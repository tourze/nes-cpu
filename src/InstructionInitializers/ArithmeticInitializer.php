<?php

declare(strict_types=1);

namespace Tourze\MOS6502\InstructionInitializers;

use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\InstructionInitializer;
use Tourze\MOS6502\Instructions;
use Tourze\MOS6502\InstructionSet;

/**
 * 算术指令初始化器
 */
class ArithmeticInitializer implements InstructionInitializer
{
    /**
     * 注册算术指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // ADC - Add Memory to Accumulator with Carry
        $instructionSet->registerInstruction(0x69, new Instructions\ADC(0x69, 'ADC', 2, AddressingModeFactory::immediate(), 'Add Memory to Accumulator with Carry (Immediate)'));
        $instructionSet->registerInstruction(0x65, new Instructions\ADC(0x65, 'ADC', 3, AddressingModeFactory::zeroPage(), 'Add Memory to Accumulator with Carry (Zero Page)'));
        $instructionSet->registerInstruction(0x75, new Instructions\ADC(0x75, 'ADC', 4, AddressingModeFactory::zeroPageX(), 'Add Memory to Accumulator with Carry (Zero Page, X)'));
        $instructionSet->registerInstruction(0x6D, new Instructions\ADC(0x6D, 'ADC', 4, AddressingModeFactory::absolute(), 'Add Memory to Accumulator with Carry (Absolute)'));
        $instructionSet->registerInstruction(0x7D, new Instructions\ADC(0x7D, 'ADC', 4, AddressingModeFactory::absoluteX(), 'Add Memory to Accumulator with Carry (Absolute, X)'));
        $instructionSet->registerInstruction(0x79, new Instructions\ADC(0x79, 'ADC', 4, AddressingModeFactory::absoluteY(), 'Add Memory to Accumulator with Carry (Absolute, Y)'));
        $instructionSet->registerInstruction(0x61, new Instructions\ADC(0x61, 'ADC', 6, AddressingModeFactory::indirectX(), 'Add Memory to Accumulator with Carry (Indirect, X)'));
        $instructionSet->registerInstruction(0x71, new Instructions\ADC(0x71, 'ADC', 5, AddressingModeFactory::indirectY(), 'Add Memory to Accumulator with Carry (Indirect, Y)'));

        // SBC - Subtract Memory from Accumulator with Borrow
        $instructionSet->registerInstruction(0xE9, new Instructions\SBC(0xE9, 'SBC', 2, AddressingModeFactory::immediate(), 'Subtract Memory from Accumulator with Borrow (Immediate)'));
        $instructionSet->registerInstruction(0xE5, new Instructions\SBC(0xE5, 'SBC', 3, AddressingModeFactory::zeroPage(), 'Subtract Memory from Accumulator with Borrow (Zero Page)'));
        $instructionSet->registerInstruction(0xF5, new Instructions\SBC(0xF5, 'SBC', 4, AddressingModeFactory::zeroPageX(), 'Subtract Memory from Accumulator with Borrow (Zero Page, X)'));
        $instructionSet->registerInstruction(0xED, new Instructions\SBC(0xED, 'SBC', 4, AddressingModeFactory::absolute(), 'Subtract Memory from Accumulator with Borrow (Absolute)'));
        $instructionSet->registerInstruction(0xFD, new Instructions\SBC(0xFD, 'SBC', 4, AddressingModeFactory::absoluteX(), 'Subtract Memory from Accumulator with Borrow (Absolute, X)'));
        $instructionSet->registerInstruction(0xF9, new Instructions\SBC(0xF9, 'SBC', 4, AddressingModeFactory::absoluteY(), 'Subtract Memory from Accumulator with Borrow (Absolute, Y)'));
        $instructionSet->registerInstruction(0xE1, new Instructions\SBC(0xE1, 'SBC', 6, AddressingModeFactory::indirectX(), 'Subtract Memory from Accumulator with Borrow (Indirect, X)'));
        $instructionSet->registerInstruction(0xF1, new Instructions\SBC(0xF1, 'SBC', 5, AddressingModeFactory::indirectY(), 'Subtract Memory from Accumulator with Borrow (Indirect, Y)'));
    }
}
