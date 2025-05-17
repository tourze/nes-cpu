<?php

declare(strict_types=1);

namespace Tourze\MOS6502\AddressingModes;

use Tourze\MOS6502\AddressingModeBase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;

/**
 * 绝对寻址模式
 *
 * 操作数的完整16位地址跟在指令后面，如JMP $1234
 */
class AbsoluteAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 3; // 操作码 + 2字节地址
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 读取指令后的两个字节作为16位地址（小端序）
        $pcValue = $cpu->getRegister('PC')->getValue();
        return $bus->readWord($pcValue);
    }

    public function getName(): string
    {
        return "absolute";
    }
}
