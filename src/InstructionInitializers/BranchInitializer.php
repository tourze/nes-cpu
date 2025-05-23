<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\InstructionInitializers;

use Tourze\NES\CPU\AddressingModeFactory;
use Tourze\NES\CPU\InstructionInitializer;
use Tourze\NES\CPU\Instructions\BCC;
use Tourze\NES\CPU\Instructions\BCS;
use Tourze\NES\CPU\Instructions\BEQ;
use Tourze\NES\CPU\Instructions\BMI;
use Tourze\NES\CPU\Instructions\BNE;
use Tourze\NES\CPU\Instructions\BPL;
use Tourze\NES\CPU\Instructions\BVC;
use Tourze\NES\CPU\Instructions\BVS;
use Tourze\NES\CPU\InstructionSet;

/**
 * 分支指令初始化器
 *
 * 负责注册所有分支指令(BCC, BCS, BEQ, BMI, BNE, BPL, BVC, BVS)
 */
class BranchInitializer implements InstructionInitializer
{
    /**
     * 初始化分支指令
     *
     * @param InstructionSet $instructionSet 指令集实例
     * @return void
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // BCC - 无进位时分支 (Branch if Carry Clear)
        $instructionSet->registerInstruction(0x90, new BCC(
            AddressingModeFactory::relative(),
            0x90,
            'BCC',
            2
        ));

        // BCS - 有进位时分支 (Branch if Carry Set)
        $instructionSet->registerInstruction(0xB0, new BCS(
            AddressingModeFactory::relative(),
            0xB0,
            'BCS',
            2
        ));

        // BEQ - 相等时分支 (Branch if Equal)
        $instructionSet->registerInstruction(0xF0, new BEQ(
            AddressingModeFactory::relative(),
            0xF0,
            'BEQ',
            2
        ));

        // BMI - 结果为负时分支 (Branch if Minus)
        $instructionSet->registerInstruction(0x30, new BMI(
            AddressingModeFactory::relative(),
            0x30,
            'BMI',
            2
        ));

        // BNE - 不相等时分支 (Branch if Not Equal)
        $instructionSet->registerInstruction(0xD0, new BNE(
            AddressingModeFactory::relative(),
            0xD0,
            'BNE',
            2
        ));

        // BPL - 结果为正时分支 (Branch if Plus)
        $instructionSet->registerInstruction(0x10, new BPL(
            AddressingModeFactory::relative(),
            0x10,
            'BPL',
            2
        ));

        // BVC - 无溢出时分支 (Branch if Overflow Clear)
        $instructionSet->registerInstruction(0x50, new BVC(
            AddressingModeFactory::relative(),
            0x50,
            'BVC',
            2
        ));

        // BVS - 有溢出时分支 (Branch if Overflow Set)
        $instructionSet->registerInstruction(0x70, new BVS(
            AddressingModeFactory::relative(),
            0x70,
            'BVS',
            2
        ));
    }
}
