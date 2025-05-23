<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;

/**
 * PHA - 压入累加器 (Push Accumulator)
 *
 * 将累加器的值压入堆栈。
 */
class PHA extends InstructionBase
{
    /**
     * 执行PHA指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取累加器的值
        $accValue = $cpu->getRegister('A')->getValue();

        // 将累加器的值压入堆栈
        $cpu->push($accValue);

        return $this->cycles;
    }
}
