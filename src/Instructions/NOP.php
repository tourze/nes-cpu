<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;

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
