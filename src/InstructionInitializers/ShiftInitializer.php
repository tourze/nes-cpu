<?php

declare(strict_types=1);

namespace Tourze\MOS6502\InstructionInitializers;

use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\InstructionInitializer;
use Tourze\MOS6502\Instructions;
use Tourze\MOS6502\InstructionSet;

/**
 * 移位指令初始化器
 */
class ShiftInitializer implements InstructionInitializer
{
    /**
     * 注册移位指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // ASL - Arithmetic Shift Left
        $instructionSet->registerInstruction(0x0A, new Instructions\ASL(0x0A, 'ASL', 2, AddressingModeFactory::accumulator(), 'Arithmetic Shift Left (Accumulator)'));
        $instructionSet->registerInstruction(0x06, new Instructions\ASL(0x06, 'ASL', 5, AddressingModeFactory::zeroPage(), 'Arithmetic Shift Left (Zero Page)'));
        $instructionSet->registerInstruction(0x16, new Instructions\ASL(0x16, 'ASL', 6, AddressingModeFactory::zeroPageX(), 'Arithmetic Shift Left (Zero Page, X)'));
        $instructionSet->registerInstruction(0x0E, new Instructions\ASL(0x0E, 'ASL', 6, AddressingModeFactory::absolute(), 'Arithmetic Shift Left (Absolute)'));
        $instructionSet->registerInstruction(0x1E, new Instructions\ASL(0x1E, 'ASL', 7, AddressingModeFactory::absoluteX(), 'Arithmetic Shift Left (Absolute, X)'));

        // LSR - Logical Shift Right
        $instructionSet->registerInstruction(0x4A, new Instructions\LSR(0x4A, 'LSR', 2, AddressingModeFactory::accumulator(), 'Logical Shift Right (Accumulator)'));
        $instructionSet->registerInstruction(0x46, new Instructions\LSR(0x46, 'LSR', 5, AddressingModeFactory::zeroPage(), 'Logical Shift Right (Zero Page)'));
        $instructionSet->registerInstruction(0x56, new Instructions\LSR(0x56, 'LSR', 6, AddressingModeFactory::zeroPageX(), 'Logical Shift Right (Zero Page, X)'));
        $instructionSet->registerInstruction(0x4E, new Instructions\LSR(0x4E, 'LSR', 6, AddressingModeFactory::absolute(), 'Logical Shift Right (Absolute)'));
        $instructionSet->registerInstruction(0x5E, new Instructions\LSR(0x5E, 'LSR', 7, AddressingModeFactory::absoluteX(), 'Logical Shift Right (Absolute, X)'));

        // ROL - Rotate Left
        $instructionSet->registerInstruction(0x2A, new Instructions\ROL(0x2A, 'ROL', 2, AddressingModeFactory::accumulator(), 'Rotate Left (Accumulator)'));
        $instructionSet->registerInstruction(0x26, new Instructions\ROL(0x26, 'ROL', 5, AddressingModeFactory::zeroPage(), 'Rotate Left (Zero Page)'));
        $instructionSet->registerInstruction(0x36, new Instructions\ROL(0x36, 'ROL', 6, AddressingModeFactory::zeroPageX(), 'Rotate Left (Zero Page, X)'));
        $instructionSet->registerInstruction(0x2E, new Instructions\ROL(0x2E, 'ROL', 6, AddressingModeFactory::absolute(), 'Rotate Left (Absolute)'));
        $instructionSet->registerInstruction(0x3E, new Instructions\ROL(0x3E, 'ROL', 7, AddressingModeFactory::absoluteX(), 'Rotate Left (Absolute, X)'));

        // ROR - Rotate Right
        $instructionSet->registerInstruction(0x6A, new Instructions\ROR(0x6A, 'ROR', 2, AddressingModeFactory::accumulator(), 'Rotate Right (Accumulator)'));
        $instructionSet->registerInstruction(0x66, new Instructions\ROR(0x66, 'ROR', 5, AddressingModeFactory::zeroPage(), 'Rotate Right (Zero Page)'));
        $instructionSet->registerInstruction(0x76, new Instructions\ROR(0x76, 'ROR', 6, AddressingModeFactory::zeroPageX(), 'Rotate Right (Zero Page, X)'));
        $instructionSet->registerInstruction(0x6E, new Instructions\ROR(0x6E, 'ROR', 6, AddressingModeFactory::absolute(), 'Rotate Right (Absolute)'));
        $instructionSet->registerInstruction(0x7E, new Instructions\ROR(0x7E, 'ROR', 7, AddressingModeFactory::absoluteX(), 'Rotate Right (Absolute, X)'));
    }
}
