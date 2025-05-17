<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * NOP - 无操作 (No Operation)
 *
 * 不做任何事情，只消耗周期。
 */
class NOP extends InstructionBase
{
    /**
     * 执行NOP指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 不做任何事情，只消耗周期
        return $this->cycles;
    }
}
