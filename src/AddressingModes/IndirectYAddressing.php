<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 间接Y索引寻址模式（后索引间接）
 *
 * 从零页地址读取16位指针，然后与Y寄存器相加得到最终地址，如LDA ($10),Y
 */
class IndirectYAddressing extends AddressingModeBase
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

        // 从零页地址读取16位指针（跨零页边界时需要特殊处理）
        $lowByte = $bus->read($zeroPageAddress);
        $highByte = $bus->read(($zeroPageAddress + 1) & 0xFF); // 高字节可能跨越零页边界
        $pointerAddress = ($highByte << 8) | $lowByte;

        // 加上Y寄存器的值得到最终地址
        $yValue = $cpu->getRegister('Y')->getValue();
        $finalAddress = ($pointerAddress + $yValue) & 0xFFFF;

        // 检查是否跨页边界（影响周期计数）
        $this->crossesPageBoundary = $this->checkPageCrossing($pointerAddress, $finalAddress);

        return $finalAddress;
    }

    public function getName(): string
    {
        return "(indirect),Y";
    }
}
