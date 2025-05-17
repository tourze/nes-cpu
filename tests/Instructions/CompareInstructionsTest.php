<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Tests\Instructions;

use Tourze\NES\CPU\StatusRegister;

/**
 * 比较指令测试
 *
 * 测试CMP, CPX, CPY指令
 */
class CompareInstructionsTest extends InstructionTestCase
{
    /**
     * 测试CMP立即寻址 - A大于操作数
     */
    public function testCMP_Immediate_Greater(): void
    {
        // 设置累加器为0x80
        $this->cpu->getRegister('A')->setValue(0x80);

        // 设置CMP指令比较0x40
        $this->loadProgram([0xC9, 0x40], 0x0200); // CMP #$40

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果: A > 操作数，设置进位标志，不设置零标志
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除 (结果为0x40，最高位为0)');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试CMP立即寻址 - A等于操作数
     */
    public function testCMP_Immediate_Equal(): void
    {
        // 设置累加器为0x40
        $this->cpu->getRegister('A')->setValue(0x40);

        // 设置CMP指令比较0x40
        $this->loadProgram([0xC9, 0x40], 0x0200); // CMP #$40

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果: A = 操作数，设置进位标志和零标志
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
    }

    /**
     * 测试CMP立即寻址 - A小于操作数
     */
    public function testCMP_Immediate_Less(): void
    {
        // 设置累加器为0x40
        $this->cpu->getRegister('A')->setValue(0x40);

        // 设置CMP指令比较0x80
        $this->loadProgram([0xC9, 0x80], 0x0200); // CMP #$80

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果: A < 操作数，不设置进位标志，不设置零标志
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
    }

    /**
     * 测试CMP零页寻址
     */
    public function testCMP_ZeroPage(): void
    {
        // 设置累加器为0x40
        $this->cpu->getRegister('A')->setValue(0x40);

        // 在零页地址0x50设置值0x30
        $this->memory->write(0x50, 0x30);

        // 设置CMP指令
        $this->loadProgram([0xC5, 0x50], 0x0200); // CMP $50

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置 (A >= M)');
        $this->assertEquals(3, $cycles, '应消耗3个周期');
    }

    /**
     * 测试CPX立即寻址
     */
    public function testCPX_Immediate(): void
    {
        // 设置X寄存器为0x40
        $this->cpu->getRegister('X')->setValue(0x40);

        // 设置CPX指令
        $this->loadProgram([0xE0, 0x40], 0x0200); // CPX #$40

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试CPY立即寻址
     */
    public function testCPY_Immediate(): void
    {
        // 设置Y寄存器为0x40
        $this->cpu->getRegister('Y')->setValue(0x40);

        // 设置CPY指令
        $this->loadProgram([0xC0, 0x40], 0x0200); // CPY #$40

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_CARRY), '进位标志应设置');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }
}
