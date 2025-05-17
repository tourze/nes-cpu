<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 间接X索引寻址模式（前索引间接）
 *
 * 零页地址先与X寄存器相加（不考虑进位），然后从结果地址处读取16位操作数地址，如LDA ($10,X)
 */
class IndirectXAddressing extends AddressingModeBase
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

        // 加上X寄存器的值（不考虑进位，保持在零页范围内）
        $xValue = $cpu->getRegister('X')->getValue();
        $indirectAddress = ($zeroPageAddress + $xValue) & 0xFF;

        // 从计算出的地址读取16位操作数地址（跨零页边界时需要特殊处理）
        $lowByte = $bus->read($indirectAddress);
        $highByte = $bus->read(($indirectAddress + 1) & 0xFF); // 高字节可能跨越零页边界

        return ($highByte << 8) | $lowByte;
    }

    public function getName(): string
    {
        return "(indirect,X)";
    }
}
