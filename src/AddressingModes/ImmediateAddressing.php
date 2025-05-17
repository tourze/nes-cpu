<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 立即寻址模式
 *
 * 操作数直接存储在指令之后，如LDA #$10
 */
class ImmediateAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 2; // 操作码 + 1字节操作数
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 立即寻址的操作数存储在PC之后的一个字节
        return $cpu->getRegister('PC')->getValue();
    }

    public function getOperandValue(CPU $cpu, Bus $bus): int
    {
        // 读取PC指向的值（立即数）
        $address = $this->getOperandAddress($cpu, $bus);
        return $bus->read($address);
    }

    public function getName(): string
    {
        return "immediate";
    }
}
