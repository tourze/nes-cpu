<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\InstructionInitializers;

use Tourze\NES\CPU\AddressingModeFactory;
use Tourze\NES\CPU\InstructionInitializer;
use Tourze\NES\CPU\Instructions;
use Tourze\NES\CPU\InstructionSet;

/**
 * 逻辑指令初始化器
 */
class LogicalInitializer implements InstructionInitializer
{
    /**
     * 注册逻辑指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // AND - Logical AND
        $instructionSet->registerInstruction(0x29, new Instructions\LogicalAND(0x29, 'AND', 2, AddressingModeFactory::immediate(), 'Logical AND (Immediate)'));
        $instructionSet->registerInstruction(0x25, new Instructions\LogicalAND(0x25, 'AND', 3, AddressingModeFactory::zeroPage(), 'Logical AND (Zero Page)'));
        $instructionSet->registerInstruction(0x35, new Instructions\LogicalAND(0x35, 'AND', 4, AddressingModeFactory::zeroPageX(), 'Logical AND (Zero Page, X)'));
        $instructionSet->registerInstruction(0x2D, new Instructions\LogicalAND(0x2D, 'AND', 4, AddressingModeFactory::absolute(), 'Logical AND (Absolute)'));
        $instructionSet->registerInstruction(0x3D, new Instructions\LogicalAND(0x3D, 'AND', 4, AddressingModeFactory::absoluteX(), 'Logical AND (Absolute, X)'));
        $instructionSet->registerInstruction(0x39, new Instructions\LogicalAND(0x39, 'AND', 4, AddressingModeFactory::absoluteY(), 'Logical AND (Absolute, Y)'));
        $instructionSet->registerInstruction(0x21, new Instructions\LogicalAND(0x21, 'AND', 6, AddressingModeFactory::indirectX(), 'Logical AND (Indirect, X)'));
        $instructionSet->registerInstruction(0x31, new Instructions\LogicalAND(0x31, 'AND', 5, AddressingModeFactory::indirectY(), 'Logical AND (Indirect, Y)'));

        // ORA - Logical Inclusive OR
        $instructionSet->registerInstruction(0x09, new Instructions\ORA(0x09, 'ORA', 2, AddressingModeFactory::immediate(), 'Logical Inclusive OR (Immediate)'));
        $instructionSet->registerInstruction(0x05, new Instructions\ORA(0x05, 'ORA', 3, AddressingModeFactory::zeroPage(), 'Logical Inclusive OR (Zero Page)'));
        $instructionSet->registerInstruction(0x15, new Instructions\ORA(0x15, 'ORA', 4, AddressingModeFactory::zeroPageX(), 'Logical Inclusive OR (Zero Page, X)'));
        $instructionSet->registerInstruction(0x0D, new Instructions\ORA(0x0D, 'ORA', 4, AddressingModeFactory::absolute(), 'Logical Inclusive OR (Absolute)'));
        $instructionSet->registerInstruction(0x1D, new Instructions\ORA(0x1D, 'ORA', 4, AddressingModeFactory::absoluteX(), 'Logical Inclusive OR (Absolute, X)'));
        $instructionSet->registerInstruction(0x19, new Instructions\ORA(0x19, 'ORA', 4, AddressingModeFactory::absoluteY(), 'Logical Inclusive OR (Absolute, Y)'));
        $instructionSet->registerInstruction(0x01, new Instructions\ORA(0x01, 'ORA', 6, AddressingModeFactory::indirectX(), 'Logical Inclusive OR (Indirect, X)'));
        $instructionSet->registerInstruction(0x11, new Instructions\ORA(0x11, 'ORA', 5, AddressingModeFactory::indirectY(), 'Logical Inclusive OR (Indirect, Y)'));

        // EOR - Exclusive OR
        $instructionSet->registerInstruction(0x49, new Instructions\EOR(0x49, 'EOR', 2, AddressingModeFactory::immediate(), 'Exclusive OR (Immediate)'));
        $instructionSet->registerInstruction(0x45, new Instructions\EOR(0x45, 'EOR', 3, AddressingModeFactory::zeroPage(), 'Exclusive OR (Zero Page)'));
        $instructionSet->registerInstruction(0x55, new Instructions\EOR(0x55, 'EOR', 4, AddressingModeFactory::zeroPageX(), 'Exclusive OR (Zero Page, X)'));
        $instructionSet->registerInstruction(0x4D, new Instructions\EOR(0x4D, 'EOR', 4, AddressingModeFactory::absolute(), 'Exclusive OR (Absolute)'));
        $instructionSet->registerInstruction(0x5D, new Instructions\EOR(0x5D, 'EOR', 4, AddressingModeFactory::absoluteX(), 'Exclusive OR (Absolute, X)'));
        $instructionSet->registerInstruction(0x59, new Instructions\EOR(0x59, 'EOR', 4, AddressingModeFactory::absoluteY(), 'Exclusive OR (Absolute, Y)'));
        $instructionSet->registerInstruction(0x41, new Instructions\EOR(0x41, 'EOR', 6, AddressingModeFactory::indirectX(), 'Exclusive OR (Indirect, X)'));
        $instructionSet->registerInstruction(0x51, new Instructions\EOR(0x51, 'EOR', 5, AddressingModeFactory::indirectY(), 'Exclusive OR (Indirect, Y)'));
    }
}
