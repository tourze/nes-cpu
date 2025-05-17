<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Tests\Instructions;

use Tourze\NES\CPU\StatusRegister;

/**
 * 逻辑指令测试
 *
 * 测试AND, ORA, EOR指令
 */
class LogicalInstructionsTest extends InstructionTestCase
{
    /**
     * 测试AND立即寻址 - 基本逻辑与操作
     */
    public function testAND_Immediate_Basic(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x55); // 01010101

        // 设置AND指令
        $this->loadProgram([0x29, 0xAA], 0x0200); // AND #$AA (10101010)

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果 - 按位与: 01010101 & 10101010 = 00000000
        $this->assertEquals(0x00, $this->cpu->getRegister('A')->getValue(), '累加器应为0x00');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试AND设置负标志
     */
    public function testAND_NegativeFlag(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0xFF); // 11111111

        // 设置AND指令
        $this->loadProgram([0x29, 0x80], 0x0200); // AND #$80 (10000000)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 按位与: 11111111 & 10000000 = 10000000
        $this->assertEquals(0x80, $this->cpu->getRegister('A')->getValue(), '累加器应为0x80');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
    }

    /**
     * 测试AND零页寻址
     */
    public function testAND_ZeroPage(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0xFF);

        // 在零页地址0x42设置值0x0F
        $this->memory->write(0x42, 0x0F);

        // 设置AND指令
        $this->loadProgram([0x25, 0x42], 0x0200); // AND $42

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x0F, $this->cpu->getRegister('A')->getValue(), '累加器应为0x0F');
        $this->assertEquals(3, $cycles, '应消耗3个周期');
    }

    /**
     * 测试ORA立即寻址 - 基本逻辑或操作
     */
    public function testORA_Immediate_Basic(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x55); // 01010101

        // 设置ORA指令
        $this->loadProgram([0x09, 0xAA], 0x0200); // ORA #$AA (10101010)

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果 - 按位或: 01010101 | 10101010 = 11111111
        $this->assertEquals(0xFF, $this->cpu->getRegister('A')->getValue(), '累加器应为0xFF');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试ORA设置零标志
     */
    public function testORA_ZeroFlag(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x00);
        $this->setFlag(StatusRegister::FLAG_ZERO, true);

        // 设置ORA指令
        $this->loadProgram([0x09, 0x00], 0x0200); // ORA #$00

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 按位或: 00000000 | 00000000 = 00000000
        $this->assertEquals(0x00, $this->cpu->getRegister('A')->getValue(), '累加器应为0x00');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应保持设置');
    }

    /**
     * 测试EOR立即寻址 - 基本逻辑异或操作
     */
    public function testEOR_Immediate_Basic(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x55); // 01010101

        // 设置EOR指令
        $this->loadProgram([0x49, 0xAA], 0x0200); // EOR #$AA (10101010)

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果 - 按位异或: 01010101 ^ 10101010 = 11111111
        $this->assertEquals(0xFF, $this->cpu->getRegister('A')->getValue(), '累加器应为0xFF');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试EOR设置零标志
     */
    public function testEOR_ZeroFlag(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0xAA); // 10101010

        // 设置EOR指令
        $this->loadProgram([0x49, 0xAA], 0x0200); // EOR #$AA (10101010)

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果 - 按位异或: 10101010 ^ 10101010 = 00000000
        $this->assertEquals(0x00, $this->cpu->getRegister('A')->getValue(), '累加器应为0x00');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
    }

    /**
     * 测试EOR零页寻址
     */
    public function testEOR_ZeroPage(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x55);

        // 在零页地址0x42设置值0xFF
        $this->memory->write(0x42, 0xFF);

        // 设置EOR指令
        $this->loadProgram([0x45, 0x42], 0x0200); // EOR $42

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果 - 按位异或: 01010101 ^ 11111111 = 10101010
        $this->assertEquals(0xAA, $this->cpu->getRegister('A')->getValue(), '累加器应为0xAA');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
        $this->assertEquals(3, $cycles, '应消耗3个周期');
    }
}
