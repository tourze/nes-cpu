<?php

declare(strict_types=1);

namespace Tourze\MOS6502\AddressingModes;

use Tourze\MOS6502\AddressingModeBase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;

/**
 * 零页X索引寻址模式
 *
 * 操作数地址为零页地址加X寄存器的值（不考虑进位），如LDA $10,X
 */
class ZeroPageXAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 2; // 操作码 + 1字节零页地址
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 读取指令后的一个字节作为零页地址
        $pcValue = $cpu->getRegister('PC')->getValue();
        $zeroPageAddress = $bus->read($pcValue);

        // 加上X寄存器的值，并确保仍在零页范围内（不考虑进位）
        $xValue = $cpu->getRegister('X')->getValue();
        $finalAddress = ($zeroPageAddress + $xValue) & 0xFF;

        return $finalAddress;
    }

    public function getName(): string
    {
        return "zeropage,X";
    }
}
