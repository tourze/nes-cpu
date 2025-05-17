<?php

declare(strict_types=1);

namespace Tourze\MOS6502\InstructionInitializers;

use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\InstructionInitializer;
use Tourze\MOS6502\Instructions\CLC;
use Tourze\MOS6502\Instructions\CLD;
use Tourze\MOS6502\Instructions\CLI;
use Tourze\MOS6502\Instructions\CLV;
use Tourze\MOS6502\Instructions\SEC;
use Tourze\MOS6502\Instructions\SED;
use Tourze\MOS6502\Instructions\SEI;
use Tourze\MOS6502\InstructionSet;

/**
 * 状态标志操作指令初始化器
 * 
 * 负责注册所有状态标志操作指令(CLC, CLD, CLI, CLV, SEC, SED, SEI)
 */
class StatusFlagInitializer implements InstructionInitializer
{
    /**
     * 初始化状态标志操作指令
     *
     * @param InstructionSet $instructionSet 指令集实例
     * @return void
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // CLC - 清除进位标志 (Clear Carry Flag)
        $instructionSet->registerInstruction(0x18, new CLC(
            0x18,
            'CLC',
            2,
            AddressingModeFactory::implied(),
            '清除进位标志'
        ));

        // CLD - 清除十进制模式 (Clear Decimal Mode)
        $instructionSet->registerInstruction(0xD8, new CLD(
            0xD8,
            'CLD',
            2,
            AddressingModeFactory::implied(),
            '清除十进制模式标志'
        ));

        // CLI - 清除中断禁用标志 (Clear Interrupt Disable)
        $instructionSet->registerInstruction(0x58, new CLI(
            0x58,
            'CLI',
            2,
            AddressingModeFactory::implied(),
            '清除中断禁用标志'
        ));

        // CLV - 清除溢出标志 (Clear Overflow Flag)
        $instructionSet->registerInstruction(0xB8, new CLV(
            0xB8,
            'CLV',
            2,
            AddressingModeFactory::implied(),
            '清除溢出标志'
        ));

        // SEC - 设置进位标志 (Set Carry Flag)
        $instructionSet->registerInstruction(0x38, new SEC(
            0x38,
            'SEC',
            2,
            AddressingModeFactory::implied(),
            '设置进位标志'
        ));

        // SED - 设置十进制模式 (Set Decimal Flag)
        $instructionSet->registerInstruction(0xF8, new SED(
            0xF8,
            'SED',
            2,
            AddressingModeFactory::implied(),
            '设置十进制模式标志'
        ));

        // SEI - 设置中断禁用标志 (Set Interrupt Disable)
        $instructionSet->registerInstruction(0x78, new SEI(
            0x78,
            'SEI',
            2,
            AddressingModeFactory::implied(),
            '设置中断禁用标志'
        ));
    }
}
