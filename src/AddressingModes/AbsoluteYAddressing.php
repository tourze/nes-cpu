<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 绝对Y索引寻址模式
 *
 * 操作数地址为绝对地址加Y寄存器的值，如LDA $1234,Y
 */
class AbsoluteYAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 3; // 操作码 + 2字节地址
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 读取指令后的两个字节作为16位地址（小端序）
        $pcValue = $cpu->getRegister('PC')->getValue();
        $absoluteAddress = $bus->readWord($pcValue);

        // 加上Y寄存器的值
        $yValue = $cpu->getRegister('Y')->getValue();
        $finalAddress = ($absoluteAddress + $yValue) & 0xFFFF;

        // 检查是否跨页边界（影响周期计数）
        $this->crossesPageBoundary = $this->checkPageCrossing($absoluteAddress, $finalAddress);

        return $finalAddress;
    }

    public function getName(): string
    {
        return "absolute,Y";
    }
}
