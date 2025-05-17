<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\InstructionInitializers;

use Tourze\NES\CPU\AddressingModeFactory;
use Tourze\NES\CPU\InstructionInitializer;
use Tourze\NES\CPU\Instructions\BRK;
use Tourze\NES\CPU\Instructions\NOP;
use Tourze\NES\CPU\Instructions\PHA;
use Tourze\NES\CPU\Instructions\PHP;
use Tourze\NES\CPU\Instructions\PLA;
use Tourze\NES\CPU\Instructions\PLP;
use Tourze\NES\CPU\InstructionSet;

/**
 * 系统指令初始化器
 *
 * 负责注册所有系统指令(BRK, NOP, PHA, PHP, PLA, PLP)
 */
class SystemInitializer implements InstructionInitializer
{
    /**
     * 初始化系统指令
     *
     * @param InstructionSet $instructionSet 指令集实例
     * @return void
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // BRK - 强制中断 (Force Interrupt)
        $instructionSet->registerInstruction(0x00, new BRK(
            0x00,
            'BRK',
            7,
            AddressingModeFactory::implied(),
            '强制中断'
        ));

        // NOP - 无操作 (No Operation)
        $instructionSet->registerInstruction(0xEA, new NOP(
            0xEA,
            'NOP',
            2,
            AddressingModeFactory::implied(),
            '无操作'
        ));

        // PHA - 压入累加器 (Push Accumulator)
        $instructionSet->registerInstruction(0x48, new PHA(
            0x48,
            'PHA',
            3,
            AddressingModeFactory::implied(),
            '压入累加器'
        ));

        // PHP - 压入处理器状态 (Push Processor Status)
        $instructionSet->registerInstruction(0x08, new PHP(
            0x08,
            'PHP',
            3,
            AddressingModeFactory::implied(),
            '压入处理器状态'
        ));

        // PLA - 拉出累加器 (Pull Accumulator)
        $instructionSet->registerInstruction(0x68, new PLA(
            0x68,
            'PLA',
            4,
            AddressingModeFactory::implied(),
            '拉出累加器'
        ));

        // PLP - 拉出处理器状态 (Pull Processor Status)
        $instructionSet->registerInstruction(0x28, new PLP(
            0x28,
            'PLP',
            4,
            AddressingModeFactory::implied(),
            '拉出处理器状态'
        ));
    }
}
