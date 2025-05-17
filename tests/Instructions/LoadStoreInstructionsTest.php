<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Tests\Instructions;

use Tourze\NES\CPU\StatusRegister;

/**
 * 加载和存储指令测试
 *
 * 测试LDA, LDX, LDY, STA, STX, STY指令
 */
class LoadStoreInstructionsTest extends InstructionTestCase
{
    /**
     * 测试LDA立即寻址
     */
    public function testLDA_Immediate(): void
    {
        // 设置LDA指令，加载值42
        $this->loadProgram([0xA9, 0x2A], 0x0200); // LDA #$2A

        // 确保初始状态
        $this->cpu->getRegister('A')->setValue(0);
        $this->setFlag(StatusRegister::FLAG_ZERO, true);
        $this->setFlag(StatusRegister::FLAG_NEGATIVE, false);

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x2A, $this->cpu->getRegister('A')->getValue(), '累加器应为0x2A');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试LDA零页寻址
     */
    public function testLDA_ZeroPage(): void
    {
        // 在零页地址0x42设置值0x37
        $this->memory->write(0x42, 0x37);

        // 设置LDA指令，从零页地址0x42加载
        $this->loadProgram([0xA5, 0x42], 0x0200); // LDA $42

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x37, $this->cpu->getRegister('A')->getValue(), '累加器应为0x37');
        $this->assertEquals(3, $cycles, '应消耗3个周期');
    }

    /**
     * 测试LDA零页X变址寻址
     */
    public function testLDA_ZeroPageX(): void
    {
        // 设置X寄存器为5
        $this->cpu->getRegister('X')->setValue(5);

        // 在零页地址0x47(0x42+5)设置值0x3F
        $this->memory->write(0x47, 0x3F);

        // 设置LDA指令，从零页X变址地址0x42+X加载
        $this->loadProgram([0xB5, 0x42], 0x0200); // LDA $42,X

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x3F, $this->cpu->getRegister('A')->getValue(), '累加器应为0x3F');
        $this->assertEquals(4, $cycles, '应消耗4个周期');
    }

    /**
     * 测试LDA绝对寻址
     */
    public function testLDA_Absolute(): void
    {
        // 在地址0x1234设置值0x42
        $this->memory->write(0x1234, 0x42);

        // 设置LDA指令，从绝对地址0x1234加载
        $this->loadProgram([0xAD, 0x34, 0x12], 0x0200); // LDA $1234

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('A')->getValue(), '累加器应为0x42');
        $this->assertEquals(4, $cycles, '应消耗4个周期');
    }

    /**
     * 测试LDA加载零值时设置零标志
     */
    public function testLDA_ZeroFlag(): void
    {
        // 设置LDA指令，加载值0
        $this->loadProgram([0xA9, 0x00], 0x0200); // LDA #$00

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0, $this->cpu->getRegister('A')->getValue(), '累加器应为0');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
    }

    /**
     * 测试LDA加载负值时设置负标志
     */
    public function testLDA_NegativeFlag(): void
    {
        // 设置LDA指令，加载负值(0x80-0xFF)
        $this->loadProgram([0xA9, 0x80], 0x0200); // LDA #$80

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x80, $this->cpu->getRegister('A')->getValue(), '累加器应为0x80');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
    }

    /**
     * 测试LDX立即寻址
     */
    public function testLDX_Immediate(): void
    {
        // 设置LDX指令，加载值42
        $this->loadProgram([0xA2, 0x2A], 0x0200); // LDX #$2A

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x2A, $this->cpu->getRegister('X')->getValue(), 'X寄存器应为0x2A');
    }

    /**
     * 测试LDY立即寻址
     */
    public function testLDY_Immediate(): void
    {
        // 设置LDY指令，加载值42
        $this->loadProgram([0xA0, 0x2A], 0x0200); // LDY #$2A

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x2A, $this->cpu->getRegister('Y')->getValue(), 'Y寄存器应为0x2A');
    }

    /**
     * 测试STA零页寻址
     */
    public function testSTA_ZeroPage(): void
    {
        // 设置A寄存器值
        $this->cpu->getRegister('A')->setValue(0x42);

        // 设置STA指令，存储到零页地址0x30
        $this->loadProgram([0x85, 0x30], 0x0200); // STA $30

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->memory->read(0x30), '内存地址0x30应为0x42');
        $this->assertEquals(3, $cycles, '应消耗3个周期');
    }

    /**
     * 测试STX零页寻址
     */
    public function testSTX_ZeroPage(): void
    {
        // 设置X寄存器值
        $this->cpu->getRegister('X')->setValue(0x42);

        // 设置STX指令，存储到零页地址0x30
        $this->loadProgram([0x86, 0x30], 0x0200); // STX $30

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->memory->read(0x30), '内存地址0x30应为0x42');
    }

    /**
     * 测试STY零页寻址
     */
    public function testSTY_ZeroPage(): void
    {
        // 设置Y寄存器值
        $this->cpu->getRegister('Y')->setValue(0x42);

        // 设置STY指令，存储到零页地址0x30
        $this->loadProgram([0x84, 0x30], 0x0200); // STY $30

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->memory->read(0x30), '内存地址0x30应为0x42');
    }
}
