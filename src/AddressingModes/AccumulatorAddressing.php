<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 累加器寻址模式
 *
 * 用于对累加器A操作的指令，如ASL A
 */
class AccumulatorAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 1; // 只有操作码，没有操作数
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 累加器寻址不使用内存地址，返回-1作为特殊标记
        return -1;
    }

    public function getOperandValue(CPU $cpu, Bus $bus): int
    {
        // 直接返回累加器的值
        return $cpu->getRegister('A')->getValue();
    }

    public function getName(): string
    {
        return "accumulator";
    }
}
