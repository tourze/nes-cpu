<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

/**
 * 指令初始化器接口
 *
 * 定义指令初始化器需要实现的方法
 */
interface InstructionInitializer
{
    /**
     * 初始化指令，将其注册到指令集中
     */
    public function initialize(InstructionSet $instructionSet): void;
}
