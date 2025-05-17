<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\InstructionInitializers;

use Tourze\NES\CPU\AddressingModeFactory;
use Tourze\NES\CPU\InstructionInitializer;
use Tourze\NES\CPU\Instructions;
use Tourze\NES\CPU\InstructionSet;

/**
 * 增减指令初始化器
 */
class IncrementDecrementInitializer implements InstructionInitializer
{
    /**
     * 注册增减指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // INX - Increment X Register
        $instructionSet->registerInstruction(0xE8, new Instructions\INX(0xE8, 'INX', 2, AddressingModeFactory::implied(), 'Increment X Register'));

        // INY - Increment Y Register
        $instructionSet->registerInstruction(0xC8, new Instructions\INY(0xC8, 'INY', 2, AddressingModeFactory::implied(), 'Increment Y Register'));

        // DEX - Decrement X Register
        $instructionSet->registerInstruction(0xCA, new Instructions\DEX(0xCA, 'DEX', 2, AddressingModeFactory::implied(), 'Decrement X Register'));

        // DEY - Decrement Y Register
        $instructionSet->registerInstruction(0x88, new Instructions\DEY(0x88, 'DEY', 2, AddressingModeFactory::implied(), 'Decrement Y Register'));

        // INC - Increment Memory
        $instructionSet->registerInstruction(0xE6, new Instructions\INC(0xE6, 'INC', 5, AddressingModeFactory::zeroPage(), 'Increment Memory (Zero Page)'));
        $instructionSet->registerInstruction(0xF6, new Instructions\INC(0xF6, 'INC', 6, AddressingModeFactory::zeroPageX(), 'Increment Memory (Zero Page, X)'));
        $instructionSet->registerInstruction(0xEE, new Instructions\INC(0xEE, 'INC', 6, AddressingModeFactory::absolute(), 'Increment Memory (Absolute)'));
        $instructionSet->registerInstruction(0xFE, new Instructions\INC(0xFE, 'INC', 7, AddressingModeFactory::absoluteX(), 'Increment Memory (Absolute, X)'));

        // DEC - Decrement Memory
        $instructionSet->registerInstruction(0xC6, new Instructions\DEC(0xC6, 'DEC', 5, AddressingModeFactory::zeroPage(), 'Decrement Memory (Zero Page)'));
        $instructionSet->registerInstruction(0xD6, new Instructions\DEC(0xD6, 'DEC', 6, AddressingModeFactory::zeroPageX(), 'Decrement Memory (Zero Page, X)'));
        $instructionSet->registerInstruction(0xCE, new Instructions\DEC(0xCE, 'DEC', 6, AddressingModeFactory::absolute(), 'Decrement Memory (Absolute)'));
        $instructionSet->registerInstruction(0xDE, new Instructions\DEC(0xDE, 'DEC', 7, AddressingModeFactory::absoluteX(), 'Decrement Memory (Absolute, X)'));
    }
}
