<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Tests\Instructions;

use Tourze\NES\CPU\StatusRegister;

/**
 * 算术指令测试
 *
 * 测试ADC, SBC指令
 */
class ArithmeticInstructionsTest extends InstructionTestCase
{
    /**
     * 测试ADC立即寻址 - 基本加法
     */
    public function testADC_Immediate_Basic(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x10);
        $this->setFlag(StatusRegister::FLAG_CARRY, false);

        // 设置ADC指令
        $this->loadProgram([0x69, 0x20], 0x0200); // ADC #$20

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x30, $this->cpu->getRegister('A')->getValue(), '累加器应为0x30');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_OVERFLOW), '溢出标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试ADC带进位
     */
    public function testADC_WithCarry(): void
    {
        // 设置初始值和进位标志
        $this->cpu->getRegister('A')->setValue(0x10);
        $this->setFlag(StatusRegister::FLAG_CARRY, true);

        // 设置ADC指令
        $this->loadProgram([0x69, 0x20], 0x0200); // ADC #$20

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x31, $this->cpu->getRegister('A')->getValue(), '累加器应为0x31 (0x10 + 0x20 + 1)');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应清除');
    }

    /**
     * 测试ADC产生进位
     */
    public function testADC_CarryOut(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0xF0);
        $this->setFlag(StatusRegister::FLAG_CARRY, false);

        // 设置ADC指令
        $this->loadProgram([0x69, 0x20], 0x0200); // ADC #$20

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x10, $this->cpu->getRegister('A')->getValue(), '累加器应为0x10 (0xF0 + 0x20 = 0x110, 低8位为0x10)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
    }

    /**
     * 测试ADC零结果
     */
    public function testADC_ZeroResult(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0xF0);
        $this->setFlag(StatusRegister::FLAG_CARRY, false);

        // 设置ADC指令
        $this->loadProgram([0x69, 0x10], 0x0200); // ADC #$10

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x00, $this->cpu->getRegister('A')->getValue(), '累加器应为0x00 (0xF0 + 0x10 = 0x100, 低8位为0x00)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
    }

    /**
     * 测试ADC溢出情况 - 两个正数相加变成负数
     */
    public function testADC_OverflowPositive(): void
    {
        // 设置初始值 - 正数
        $this->cpu->getRegister('A')->setValue(0x50); // 01010000
        $this->setFlag(StatusRegister::FLAG_OVERFLOW, false);

        // 设置ADC指令 - 另一个正数
        $this->loadProgram([0x69, 0x50], 0x0200); // ADC #$50 (01010000)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 得到负数结果，产生溢出
        $this->assertEquals(0xA0, $this->cpu->getRegister('A')->getValue(), '累加器应为0xA0 (10100000)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_OVERFLOW), '溢出标志应设置，因为两个正数相加得到负数');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
    }

    /**
     * 测试ADC溢出情况 - 两个负数相加变成正数
     */
    public function testADC_OverflowNegative(): void
    {
        // 设置初始值 - 负数
        $this->cpu->getRegister('A')->setValue(0x90); // 10010000
        $this->setFlag(StatusRegister::FLAG_OVERFLOW, false);

        // 设置ADC指令 - 另一个负数
        $this->loadProgram([0x69, 0x90], 0x0200); // ADC #$90 (10010000)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 得到正数结果(加进位)，产生溢出
        $this->assertEquals(0x20, $this->cpu->getRegister('A')->getValue(), '累加器应为0x20 (00100000)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_OVERFLOW), '溢出标志应设置，因为两个负数相加得到正数');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
    }

    /**
     * 测试ADC十进制模式
     */
    public function testADC_DecimalMode(): void
    {
        // 允许BCD模式（禁用disableBCD）
        $this->cpu->setDisableBCD(false);

        // 设置十进制模式
        $this->setFlag(StatusRegister::FLAG_DECIMAL, true);

        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x09); // 9
        $this->setFlag(StatusRegister::FLAG_CARRY, false);

        // 设置ADC指令
        $this->loadProgram([0x69, 0x09], 0x0200); // ADC #$09 (9)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 十进制调整
        $this->assertEquals(0x18, $this->cpu->getRegister('A')->getValue(), '累加器应为0x18 (BCD为18)');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应清除');

        // 清除十进制模式
        $this->setFlag(StatusRegister::FLAG_DECIMAL, false);

        // 重新启用disableBCD（恢复NES兼容模式）
        $this->cpu->setDisableBCD(true);
    }

    /**
     * 测试SBC立即寻址 - 基本减法
     */
    public function testSBC_Immediate_Basic(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x50);
        $this->setFlag(StatusRegister::FLAG_CARRY, true); // 借位的取反，1表示没有借位

        // 设置SBC指令
        $this->loadProgram([0xE9, 0x20], 0x0200); // SBC #$20

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x30, $this->cpu->getRegister('A')->getValue(), '累加器应为0x30 (0x50 - 0x20)');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置(没有借位)');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_OVERFLOW), '溢出标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试SBC带借位
     */
    public function testSBC_WithBorrow(): void
    {
        // 设置初始值，进位标志为0(表示需要借位)
        $this->cpu->getRegister('A')->setValue(0x50);
        $this->setFlag(StatusRegister::FLAG_CARRY, false);

        // 设置SBC指令
        $this->loadProgram([0xE9, 0x20], 0x0200); // SBC #$20

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x2F, $this->cpu->getRegister('A')->getValue(), '累加器应为0x2F (0x50 - 0x20 - 1)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置(没有借位)');
    }

    /**
     * 测试SBC产生借位
     */
    public function testSBC_BorrowOut(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x20);
        $this->setFlag(StatusRegister::FLAG_CARRY, true);

        // 设置SBC指令
        $this->loadProgram([0xE9, 0x30], 0x0200); // SBC #$30

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0xF0, $this->cpu->getRegister('A')->getValue(), '累加器应为0xF0 (0x20 - 0x30 = -16, 补码为0xF0)');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应清除(有借位)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
    }

    /**
     * 测试SBC溢出情况
     */
    public function testSBC_Overflow(): void
    {
        // 设置初始值 - 正数
        $this->cpu->getRegister('A')->setValue(0x50); // 01010000
        $this->setFlag(StatusRegister::FLAG_CARRY, true);
        $this->setFlag(StatusRegister::FLAG_OVERFLOW, false);

        // 设置SBC指令 - 减去负数
        $this->loadProgram([0xE9, 0x90], 0x0200); // SBC #$90 (10010000, -112)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 溢出发生
        $this->assertEquals(0xC0, $this->cpu->getRegister('A')->getValue(), '累加器应为0xC0 (11000000)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_OVERFLOW), '溢出标志应设置，因为正-负=负，符号不正确');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应清除(有借位)');
    }

    /**
     * 测试SBC十进制模式
     */
    public function testSBC_DecimalMode(): void
    {
        // 允许BCD模式（禁用disableBCD）
        $this->cpu->setDisableBCD(false);

        // 设置十进制模式
        $this->setFlag(StatusRegister::FLAG_DECIMAL, true);

        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x50); // 50 BCD
        $this->setFlag(StatusRegister::FLAG_CARRY, true); // 无借位

        // 设置SBC指令
        $this->loadProgram([0xE9, 0x18], 0x0200); // SBC #$18 (18 BCD)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 十进制调整
        $this->assertEquals(0x32, $this->cpu->getRegister('A')->getValue(), '累加器应为0x32 (BCD为32，即50-18=32)');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置(无借位)');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');

        // 清除十进制模式
        $this->setFlag(StatusRegister::FLAG_DECIMAL, false);

        // 重新启用disableBCD（恢复NES兼容模式）
        $this->cpu->setDisableBCD(true);
    }
}
