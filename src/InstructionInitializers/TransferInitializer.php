<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\InstructionInitializers;

use Tourze\NES\CPU\AddressingModeFactory;
use Tourze\NES\CPU\InstructionInitializer;
use Tourze\NES\CPU\Instructions;
use Tourze\NES\CPU\InstructionSet;

/**
 * 传送指令初始化器
 */
class TransferInitializer implements InstructionInitializer
{
    /**
     * 注册传送指令
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // TAX - Transfer Accumulator to X
        $instructionSet->registerInstruction(0xAA, new Instructions\TAX(0xAA, 'TAX', 2, AddressingModeFactory::implied(), 'Transfer Accumulator to X'));

        // TAY - Transfer Accumulator to Y
        $instructionSet->registerInstruction(0xA8, new Instructions\TAY(0xA8, 'TAY', 2, AddressingModeFactory::implied(), 'Transfer Accumulator to Y'));

        // TXA - Transfer X to Accumulator
        $instructionSet->registerInstruction(0x8A, new Instructions\TXA(0x8A, 'TXA', 2, AddressingModeFactory::implied(), 'Transfer X to Accumulator'));

        // TYA - Transfer Y to Accumulator
        $instructionSet->registerInstruction(0x98, new Instructions\TYA(0x98, 'TYA', 2, AddressingModeFactory::implied(), 'Transfer Y to Accumulator'));

        // TSX - Transfer Stack Pointer to X
        $instructionSet->registerInstruction(0xBA, new Instructions\TSX(0xBA, 'TSX', 2, AddressingModeFactory::implied(), 'Transfer Stack Pointer to X'));

        // TXS - Transfer X to Stack Pointer
        $instructionSet->registerInstruction(0x9A, new Instructions\TXS(0x9A, 'TXS', 2, AddressingModeFactory::implied(), 'Transfer X to Stack Pointer'));
    }
}
