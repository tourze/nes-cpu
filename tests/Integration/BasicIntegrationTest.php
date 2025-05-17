<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\Memory;
use Tourze\MOS6502\StatusRegister;

/**
 * 基本集成测试
 *
 * 测试Memory、Bus和CPU的基本交互
 */
class BasicIntegrationTest extends TestCase
{
    /**
     * 测试系统初始化和重置
     */
    public function testSystemInitialization(): void
    {
        // 创建系统组件
        $memory = new Memory();
        $bus = new Bus();

        // 连接内存到总线
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);

        // 设置复位向量（0xFFFC-0xFFFD）指向0x0400
        $memory->writeWord(0xFFFC, 0x0400);

        // 创建CPU并检查重置状态
        $cpu = new CPU($bus);

        // 验证重置后的CPU状态
        $this->assertEquals(0x0400, $cpu->getRegister('PC')->getValue(), 'PC应该指向重置向量的值');
        $this->assertEquals(0, $cpu->getRegister('A')->getValue(), 'A寄存器应该为0');
        $this->assertEquals(0, $cpu->getRegister('X')->getValue(), 'X寄存器应该为0');
        $this->assertEquals(0, $cpu->getRegister('Y')->getValue(), 'Y寄存器应该为0');
        $this->assertEquals(0xFD, $cpu->getRegister('SP')->getValue(), 'SP应该为0xFD');

        // 验证状态寄存器
        $status = $cpu->getRegister('P');
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_INTERRUPT), '中断禁用标志应该被设置');
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_UNUSED), '未使用标志应该始终为1');
    }

    /**
     * 测试堆栈操作
     */
    public function testStackOperations(): void
    {
        // 创建系统组件
        $memory = new Memory();
        $bus = new Bus();
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);
        $cpu = new CPU($bus);

        // 初始SP值
        $initialSP = $cpu->getRegister('SP')->getValue();

        // 测试压入和弹出8位值
        $cpu->push(0x42);
        $this->assertEquals($initialSP - 1, $cpu->getRegister('SP')->getValue(), 'SP应该减少1');
        $this->assertEquals(0x42, $memory->read(0x0100 | ($initialSP & 0xFF)), '值应该被正确写入堆栈');

        $value = $cpu->pull();
        $this->assertEquals($initialSP, $cpu->getRegister('SP')->getValue(), 'SP应该恢复到初始值');
        $this->assertEquals(0x42, $value, '弹出的值应该和压入的相同');

        // 测试压入和弹出16位值
        $cpu->pushWord(0xABCD);
        $this->assertEquals($initialSP - 2, $cpu->getRegister('SP')->getValue(), 'SP应该减少2');

        $word = $cpu->pullWord();
        $this->assertEquals($initialSP, $cpu->getRegister('SP')->getValue(), 'SP应该恢复到初始值');
        $this->assertEquals(0xABCD, $word, '弹出的16位值应该和压入的相同');
    }

    /**
     * 测试状态寄存器操作
     */
    public function testStatusRegisterOperations(): void
    {
        // 创建系统组件
        $memory = new Memory();
        $bus = new Bus();
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);
        $cpu = new CPU($bus);

        $status = $cpu->getRegister('P');

        // 测试设置各个标志
        $flags = [
            StatusRegister::FLAG_NEGATIVE,
            StatusRegister::FLAG_OVERFLOW,
            StatusRegister::FLAG_BREAK,
            StatusRegister::FLAG_DECIMAL,
            StatusRegister::FLAG_INTERRUPT,
            StatusRegister::FLAG_ZERO,
            StatusRegister::FLAG_CARRY
        ];

        foreach ($flags as $flag) {
            $status->setFlag($flag, true);
            $this->assertTrue($status->getFlag($flag), "标志 {$flag} 应该被设置");

            $status->setFlag($flag, false);
            $this->assertFalse($status->getFlag($flag), "标志 {$flag} 应该被清除");
        }

        // 验证更新负数标志
        $status->updateNegativeFlag(0xFF);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_NEGATIVE), "负数标志应该被设置");

        $status->updateNegativeFlag(0x01);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_NEGATIVE), "负数标志应该被清除");

        // 验证更新零标志
        $status->updateZeroFlag(0x00);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_ZERO), "零标志应该被设置");

        $status->updateZeroFlag(0x01);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_ZERO), "零标志应该被清除");
    }

    /**
     * 测试十进制模式操作
     */
    public function testDecimalModeOperations(): void
    {
        // 创建系统组件
        $memory = new Memory();
        $bus = new Bus();
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);
        $cpu = new CPU($bus);

        // 测试十进制模式加法
        $result = $cpu->handleDecimalMode(0x09, 0x01, false);
        $this->assertEquals(0x10, $result['result'], "9 + 1 应该等于 10 (0x10) 在十进制模式下");
        $this->assertFalse($result['carry'], "不应该有进位");

        // 测试有进位的加法
        $result = $cpu->handleDecimalMode(0x99, 0x01, false);
        $this->assertEquals(0x00, $result['result'], "99 + 1 应该等于 00 (进位到下一个BCD数字) 在十进制模式下");
        $this->assertTrue($result['carry'], "应该有进位");

        // 测试十进制模式减法
        $result = $cpu->handleDecimalModeSbc(0x10, 0x01, true);
        $this->assertEquals(0x09, $result['result'], "10 - 1 应该等于 9 (0x09) 在十进制模式下");
        $this->assertTrue($result['carry'], "不应该有借位");

        // 测试有借位的减法
        $result = $cpu->handleDecimalModeSbc(0x00, 0x01, true);
        $this->assertEquals(0x99, $result['result'], "0 - 1 应该等于 99 (借位从下一个BCD数字) 在十进制模式下");
        $this->assertFalse($result['carry'], "应该有借位");
    }
}
