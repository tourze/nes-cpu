<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Instructions;

use Tourze\MOS6502\StatusRegister;

/**
 * 传送指令测试
 *
 * 测试TAX, TAY, TSX, TXA, TXS, TYA指令
 */
class TransferInstructionsTest extends InstructionTestCase
{
    /**
     * 测试TAX - 传送累加器到X寄存器
     */
    public function testTAX(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x42);
        $this->cpu->getRegister('X')->setValue(0x00);

        // 设置TAX指令
        $this->loadProgram([0xAA], 0x0200); // TAX

        // 执行指令
        $cycles = $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('X')->getValue(), 'X寄存器应为0x42');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应清除');
        $this->assertFalse($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应清除');
        $this->assertEquals(2, $cycles, '应消耗2个周期');
    }

    /**
     * 测试TAX设置零标志
     */
    public function testTAX_ZeroFlag(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x00);
        $this->setFlag(StatusRegister::FLAG_ZERO, false);

        // 设置TAX指令
        $this->loadProgram([0xAA], 0x0200); // TAX

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x00, $this->cpu->getRegister('X')->getValue(), 'X寄存器应为0');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_ZERO), '零标志应设置');
    }

    /**
     * 测试TAX设置负标志
     */
    public function testTAX_NegativeFlag(): void
    {
        // 设置A为负值(0x80-0xFF)
        $this->cpu->getRegister('A')->setValue(0x80);
        $this->setFlag(StatusRegister::FLAG_NEGATIVE, false);

        // 设置TAX指令
        $this->loadProgram([0xAA], 0x0200); // TAX

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x80, $this->cpu->getRegister('X')->getValue(), 'X寄存器应为0x80');
        $this->assertTrue($this->getFlag(StatusRegister::FLAG_NEGATIVE), '负标志应设置');
    }

    /**
     * 测试TAY - 传送累加器到Y寄存器
     */
    public function testTAY(): void
    {
        // 设置初始值
        $this->cpu->getRegister('A')->setValue(0x42);
        $this->cpu->getRegister('Y')->setValue(0x00);

        // 设置TAY指令
        $this->loadProgram([0xA8], 0x0200); // TAY

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('Y')->getValue(), 'Y寄存器应为0x42');
    }

    /**
     * 测试TXA - 传送X寄存器到累加器
     */
    public function testTXA(): void
    {
        // 设置初始值
        $this->cpu->getRegister('X')->setValue(0x42);
        $this->cpu->getRegister('A')->setValue(0x00);

        // 设置TXA指令
        $this->loadProgram([0x8A], 0x0200); // TXA

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('A')->getValue(), '累加器应为0x42');
    }

    /**
     * 测试TYA - 传送Y寄存器到累加器
     */
    public function testTYA(): void
    {
        // 设置初始值
        $this->cpu->getRegister('Y')->setValue(0x42);
        $this->cpu->getRegister('A')->setValue(0x00);

        // 设置TYA指令
        $this->loadProgram([0x98], 0x0200); // TYA

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('A')->getValue(), '累加器应为0x42');
    }

    /**
     * 测试TXS - 传送X寄存器到堆栈指针
     */
    public function testTXS(): void
    {
        // 设置初始值
        $this->cpu->getRegister('X')->setValue(0x42);
        $this->cpu->getRegister('SP')->setValue(0xFD); // 默认值

        // 设置TXS指令
        $this->loadProgram([0x9A], 0x0200); // TXS

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('SP')->getValue(), '堆栈指针应为0x42');
        // 注意：TXS不影响任何标志
    }

    /**
     * 测试TSX - 传送堆栈指针到X寄存器
     */
    public function testTSX(): void
    {
        // 设置初始值
        $this->cpu->getRegister('SP')->setValue(0x42);
        $this->cpu->getRegister('X')->setValue(0x00);

        // 设置TSX指令
        $this->loadProgram([0xBA], 0x0200); // TSX

        // 执行指令
        $this->executeInstruction(0x0200);

        // 验证结果
        $this->assertEquals(0x42, $this->cpu->getRegister('X')->getValue(), 'X寄存器应为0x42');
        // TSX会影响零标志和负标志
    }
}
