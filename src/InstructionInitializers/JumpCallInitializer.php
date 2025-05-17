<?php

declare(strict_types=1);

namespace Tourze\MOS6502\InstructionInitializers;

use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\InstructionInitializer;
use Tourze\MOS6502\Instructions\JMP;
use Tourze\MOS6502\Instructions\JSR;
use Tourze\MOS6502\Instructions\RTI;
use Tourze\MOS6502\Instructions\RTS;
use Tourze\MOS6502\InstructionSet;

/**
 * 跳转指令初始化器
 * 
 * 负责注册所有跳转和调用指令(JMP, JSR, RTS, RTI)
 */
class JumpCallInitializer implements InstructionInitializer
{
    /**
     * 初始化跳转和调用指令
     *
     * @param InstructionSet $instructionSet 指令集实例
     * @return void
     */
    public function initialize(InstructionSet $instructionSet): void
    {
        // JMP - 无条件跳转 (Jump to Location)
        $instructionSet->registerInstruction(0x4C, new JMP(
            0x4C,
            'JMP',
            3,
            AddressingModeFactory::absolute(),
            '无条件跳转（绝对寻址）'
        ));
        
        // JMP - 无条件跳转 (间接寻址) (Jump to Location Indirect)
        $instructionSet->registerInstruction(0x6C, new JMP(
            0x6C,
            'JMP',
            5,
            AddressingModeFactory::indirect(),
            '无条件跳转（间接寻址）'
        ));
        
        // JSR - 跳转到子程序 (Jump to Subroutine)
        $instructionSet->registerInstruction(0x20, new JSR(
            0x20,
            'JSR',
            6,
            AddressingModeFactory::absolute(),
            '跳转到子程序'
        ));
        
        // RTS - 从子程序返回 (Return from Subroutine)
        $instructionSet->registerInstruction(0x60, new RTS(
            0x60,
            'RTS',
            6,
            AddressingModeFactory::implied(),
            '从子程序返回'
        ));
        
        // RTI - 从中断返回 (Return from Interrupt)
        $instructionSet->registerInstruction(0x40, new RTI(
            0x40,
            'RTI',
            6,
            AddressingModeFactory::implied(),
            '从中断返回'
        ));
    }
} 