<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * CLC - 清除进位标志 (Clear Carry Flag)
 *
 * 将状态寄存器中的进位标志(C)设置为0
 */
class CLC extends InstructionBase
{
    /**
     * 执行CLC指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取状态寄存器
        $status = $cpu->getRegister('P');

        if ($status instanceof StatusRegister) {
            // 清除进位标志(C)
            $status->setFlag(StatusRegister::FLAG_CARRY, false);
        }

        return $this->cycles;
    }
}
