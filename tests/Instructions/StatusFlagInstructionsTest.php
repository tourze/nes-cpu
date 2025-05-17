<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Instructions;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\Memory;
use Tourze\MOS6502\StatusRegister;

/**
 * 状态标志指令测试
 */
class StatusFlagInstructionsTest extends TestCase
{
    private CPU $cpu;
    private Bus $bus;
    private Memory $memory;

    protected function setUp(): void
    {
        $this->memory = new Memory();
        $this->bus = new Bus();
        $this->bus->connect($this->memory, 'ram', 0x0000, 0xFFFF);
        $this->cpu = new CPU($this->bus);
    }

    /**
     * 测试CLC指令 - 清除进位标志
     */
    public function testCLC(): void
    {
        // 设置进位标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_CARRY, true);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_CARRY), '进位标志应该被设置');

        // 执行CLC指令
        $this->memory->write(0x0200, 0x18); // CLC操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被清除
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_CARRY), 'CLC应该清除进位标志');
    }

    /**
     * 测试SEC指令 - 设置进位标志
     */
    public function testSEC(): void
    {
        // 清除进位标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_CARRY, false);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_CARRY), '进位标志应该被清除');

        // 执行SEC指令
        $this->memory->write(0x0200, 0x38); // SEC操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被设置
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_CARRY), 'SEC应该设置进位标志');
    }

    /**
     * 测试CLD指令 - 清除十进制模式标志
     */
    public function testCLD(): void
    {
        // 设置十进制模式标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_DECIMAL, true);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_DECIMAL), '十进制模式标志应该被设置');

        // 执行CLD指令
        $this->memory->write(0x0200, 0xD8); // CLD操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被清除
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_DECIMAL), 'CLD应该清除十进制模式标志');
    }

    /**
     * 测试SED指令 - 设置十进制模式标志
     */
    public function testSED(): void
    {
        // 清除十进制模式标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_DECIMAL, false);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_DECIMAL), '十进制模式标志应该被清除');

        // 执行SED指令
        $this->memory->write(0x0200, 0xF8); // SED操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被设置
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_DECIMAL), 'SED应该设置十进制模式标志');
    }

    /**
     * 测试CLI指令 - 清除中断禁用标志
     */
    public function testCLI(): void
    {
        // 设置中断禁用标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_INTERRUPT, true);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_INTERRUPT), '中断禁用标志应该被设置');

        // 执行CLI指令
        $this->memory->write(0x0200, 0x58); // CLI操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被清除
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_INTERRUPT), 'CLI应该清除中断禁用标志');
    }

    /**
     * 测试SEI指令 - 设置中断禁用标志
     */
    public function testSEI(): void
    {
        // 清除中断禁用标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_INTERRUPT, false);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_INTERRUPT), '中断禁用标志应该被清除');

        // 执行SEI指令
        $this->memory->write(0x0200, 0x78); // SEI操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被设置
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_INTERRUPT), 'SEI应该设置中断禁用标志');
    }

    /**
     * 测试CLV指令 - 清除溢出标志
     */
    public function testCLV(): void
    {
        // 设置溢出标志
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag(StatusRegister::FLAG_OVERFLOW, true);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_OVERFLOW), '溢出标志应该被设置');

        // 执行CLV指令
        $this->memory->write(0x0200, 0xB8); // CLV操作码
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证标志被清除
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_OVERFLOW), 'CLV应该清除溢出标志');
    }
}
