<?php

declare(strict_types=1);

namespace Tourze\MOS6502\AddressingModes;

use Tourze\MOS6502\AddressingModeBase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;

/**
 * 零页寻址模式
 *
 * 操作数地址在零页（0x0000-0x00FF），如LDA $10
 */
class ZeroPageAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 2; // 操作码 + 1字节零页地址
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 读取指令后的一个字节作为零页地址
        $pcValue = $cpu->getRegister('PC')->getValue();
        // 确保地址在零页范围内

        return $bus->read($pcValue) & 0xFF;
    }

    public function getName(): string
    {
        return "zeropage";
    }
}
