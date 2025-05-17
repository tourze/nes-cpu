<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\InstructionInitializers;

use Tourze\NES\CPU\AddressingModeFactory;
use Tourze\NES\CPU\InstructionInitializer;
use Tourze\NES\CPU\Instructions\LDA;
use Tourze\NES\CPU\Instructions\LDX;
use Tourze\NES\CPU\Instructions\LDY;
use Tourze\NES\CPU\Instructions\STA;
use Tourze\NES\CPU\Instructions\STX;
use Tourze\NES\CPU\Instructions\STY;
use Tourze\NES\CPU\InstructionSet;

/**
 * 加载和存储指令初始化器
 */
class LoadStoreInitializer implements InstructionInitializer
{
    /**
     * 注册加载和存储指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        $this->initializeLoadInstructions($instructionSet);
        $this->initializeStoreInstructions($instructionSet);
    }

    /**
     * 初始化加载指令
     */
    private function initializeLoadInstructions(InstructionSet $instructionSet): void
    {
        // LDA - Load Accumulator
        $instructionSet->registerInstruction(0xA9, new LDA(0xA9, 'LDA', 2, AddressingModeFactory::immediate(), 'Load Accumulator with immediate value'));
        $instructionSet->registerInstruction(0xA5, new LDA(0xA5, 'LDA', 3, AddressingModeFactory::zeroPage(), 'Load Accumulator from zero page'));
        $instructionSet->registerInstruction(0xB5, new LDA(0xB5, 'LDA', 4, AddressingModeFactory::zeroPageX(), 'Load Accumulator from zero page, X'));
        $instructionSet->registerInstruction(0xAD, new LDA(0xAD, 'LDA', 4, AddressingModeFactory::absolute(), 'Load Accumulator from absolute address'));
        $instructionSet->registerInstruction(0xBD, new LDA(0xBD, 'LDA', 4, AddressingModeFactory::absoluteX(), 'Load Accumulator from absolute address, X'));
        $instructionSet->registerInstruction(0xB9, new LDA(0xB9, 'LDA', 4, AddressingModeFactory::absoluteY(), 'Load Accumulator from absolute address, Y'));
        $instructionSet->registerInstruction(0xA1, new LDA(0xA1, 'LDA', 6, AddressingModeFactory::indirectX(), 'Load Accumulator from (indirect, X)'));
        $instructionSet->registerInstruction(0xB1, new LDA(0xB1, 'LDA', 5, AddressingModeFactory::indirectY(), 'Load Accumulator from (indirect), Y'));

        // LDX - Load X Register
        $instructionSet->registerInstruction(0xA2, new LDX(0xA2, 'LDX', 2, AddressingModeFactory::immediate(), 'Load X Register with immediate value'));
        $instructionSet->registerInstruction(0xA6, new LDX(0xA6, 'LDX', 3, AddressingModeFactory::zeroPage(), 'Load X Register from zero page'));
        $instructionSet->registerInstruction(0xB6, new LDX(0xB6, 'LDX', 4, AddressingModeFactory::zeroPageY(), 'Load X Register from zero page, Y'));
        $instructionSet->registerInstruction(0xAE, new LDX(0xAE, 'LDX', 4, AddressingModeFactory::absolute(), 'Load X Register from absolute address'));
        $instructionSet->registerInstruction(0xBE, new LDX(0xBE, 'LDX', 4, AddressingModeFactory::absoluteY(), 'Load X Register from absolute address, Y'));

        // LDY - Load Y Register
        $instructionSet->registerInstruction(0xA0, new LDY(0xA0, 'LDY', 2, AddressingModeFactory::immediate(), 'Load Y Register with immediate value'));
        $instructionSet->registerInstruction(0xA4, new LDY(0xA4, 'LDY', 3, AddressingModeFactory::zeroPage(), 'Load Y Register from zero page'));
        $instructionSet->registerInstruction(0xB4, new LDY(0xB4, 'LDY', 4, AddressingModeFactory::zeroPageX(), 'Load Y Register from zero page, X'));
        $instructionSet->registerInstruction(0xAC, new LDY(0xAC, 'LDY', 4, AddressingModeFactory::absolute(), 'Load Y Register from absolute address'));
        $instructionSet->registerInstruction(0xBC, new LDY(0xBC, 'LDY', 4, AddressingModeFactory::absoluteX(), 'Load Y Register from absolute address, X'));
    }

    /**
     * 初始化存储指令
     */
    private function initializeStoreInstructions(InstructionSet $instructionSet): void
    {
        // STA - Store Accumulator
        $instructionSet->registerInstruction(0x85, new STA(0x85, 'STA', 3, AddressingModeFactory::zeroPage(), 'Store Accumulator in zero page'));
        $instructionSet->registerInstruction(0x95, new STA(0x95, 'STA', 4, AddressingModeFactory::zeroPageX(), 'Store Accumulator in zero page, X'));
        $instructionSet->registerInstruction(0x8D, new STA(0x8D, 'STA', 4, AddressingModeFactory::absolute(), 'Store Accumulator in absolute address'));
        $instructionSet->registerInstruction(0x9D, new STA(0x9D, 'STA', 5, AddressingModeFactory::absoluteX(), 'Store Accumulator in absolute address, X'));
        $instructionSet->registerInstruction(0x99, new STA(0x99, 'STA', 5, AddressingModeFactory::absoluteY(), 'Store Accumulator in absolute address, Y'));
        $instructionSet->registerInstruction(0x81, new STA(0x81, 'STA', 6, AddressingModeFactory::indirectX(), 'Store Accumulator in (indirect, X)'));
        $instructionSet->registerInstruction(0x91, new STA(0x91, 'STA', 6, AddressingModeFactory::indirectY(), 'Store Accumulator in (indirect), Y'));
        
        // STX - Store X Register
        $instructionSet->registerInstruction(0x86, new STX(0x86, 'STX', 3, AddressingModeFactory::zeroPage(), 'Store X Register in zero page'));
        $instructionSet->registerInstruction(0x96, new STX(0x96, 'STX', 4, AddressingModeFactory::zeroPageY(), 'Store X Register in zero page, Y'));
        $instructionSet->registerInstruction(0x8E, new STX(0x8E, 'STX', 4, AddressingModeFactory::absolute(), 'Store X Register in absolute address'));
        
        // STY - Store Y Register
        $instructionSet->registerInstruction(0x84, new STY(0x84, 'STY', 3, AddressingModeFactory::zeroPage(), 'Store Y Register in zero page'));
        $instructionSet->registerInstruction(0x94, new STY(0x94, 'STY', 4, AddressingModeFactory::zeroPageX(), 'Store Y Register in zero page, X'));
        $instructionSet->registerInstruction(0x8C, new STY(0x8C, 'STY', 4, AddressingModeFactory::absolute(), 'Store Y Register in absolute address'));
    }
}
