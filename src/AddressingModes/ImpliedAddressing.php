<?php

declare(strict_types=1);

namespace Tourze\MOS6502\AddressingModes;

use Tourze\MOS6502\AddressingModeBase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;

/**
 * 隐含寻址模式
 * 
 * 用于不需要操作数的指令，如CLC, SEC, INX等
 */
class ImpliedAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 1; // 只有操作码，没有操作数
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 隐含寻址不使用内存操作数，返回0作为占位符
        return 0;
    }

    public function getOperandValue(CPU $cpu, Bus $bus): int
    {
        // 隐含寻址不使用内存操作数，返回0作为占位符
        return 0;
    }

    public function getName(): string
    {
        return "implied";
    }
}
