<?php

declare(strict_types=1);

namespace Tourze\MOS6502\InstructionInitializers;

use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\InstructionInitializer;
use Tourze\MOS6502\Instructions;
use Tourze\MOS6502\InstructionSet;

/**
 * 比较指令初始化器
 */
class CompareInitializer implements InstructionInitializer
{
    /**
     * 注册比较指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // CMP - Compare Memory with Accumulator
        $instructionSet->registerInstruction(0xC9, new Instructions\CMP(0xC9, 'CMP', 2, AddressingModeFactory::immediate(), 'Compare Memory with Accumulator (Immediate)'));
        $instructionSet->registerInstruction(0xC5, new Instructions\CMP(0xC5, 'CMP', 3, AddressingModeFactory::zeroPage(), 'Compare Memory with Accumulator (Zero Page)'));
        $instructionSet->registerInstruction(0xD5, new Instructions\CMP(0xD5, 'CMP', 4, AddressingModeFactory::zeroPageX(), 'Compare Memory with Accumulator (Zero Page, X)'));
        $instructionSet->registerInstruction(0xCD, new Instructions\CMP(0xCD, 'CMP', 4, AddressingModeFactory::absolute(), 'Compare Memory with Accumulator (Absolute)'));
        $instructionSet->registerInstruction(0xDD, new Instructions\CMP(0xDD, 'CMP', 4, AddressingModeFactory::absoluteX(), 'Compare Memory with Accumulator (Absolute, X)'));
        $instructionSet->registerInstruction(0xD9, new Instructions\CMP(0xD9, 'CMP', 4, AddressingModeFactory::absoluteY(), 'Compare Memory with Accumulator (Absolute, Y)'));
        $instructionSet->registerInstruction(0xC1, new Instructions\CMP(0xC1, 'CMP', 6, AddressingModeFactory::indirectX(), 'Compare Memory with Accumulator (Indirect, X)'));
        $instructionSet->registerInstruction(0xD1, new Instructions\CMP(0xD1, 'CMP', 5, AddressingModeFactory::indirectY(), 'Compare Memory with Accumulator (Indirect, Y)'));

        // CPX - Compare Memory with X Register
        $instructionSet->registerInstruction(0xE0, new Instructions\CPX(0xE0, 'CPX', 2, AddressingModeFactory::immediate(), 'Compare Memory with X Register (Immediate)'));
        $instructionSet->registerInstruction(0xE4, new Instructions\CPX(0xE4, 'CPX', 3, AddressingModeFactory::zeroPage(), 'Compare Memory with X Register (Zero Page)'));
        $instructionSet->registerInstruction(0xEC, new Instructions\CPX(0xEC, 'CPX', 4, AddressingModeFactory::absolute(), 'Compare Memory with X Register (Absolute)'));

        // CPY - Compare Memory with Y Register
        $instructionSet->registerInstruction(0xC0, new Instructions\CPY(0xC0, 'CPY', 2, AddressingModeFactory::immediate(), 'Compare Memory with Y Register (Immediate)'));
        $instructionSet->registerInstruction(0xC4, new Instructions\CPY(0xC4, 'CPY', 3, AddressingModeFactory::zeroPage(), 'Compare Memory with Y Register (Zero Page)'));
        $instructionSet->registerInstruction(0xCC, new Instructions\CPY(0xCC, 'CPY', 4, AddressingModeFactory::absolute(), 'Compare Memory with Y Register (Absolute)'));
    }
}
