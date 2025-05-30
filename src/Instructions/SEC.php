<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * SEC - 设置进位标志 (Set Carry Flag)
 *
 * 将状态寄存器中的进位标志(C)设置为1
 */
class SEC extends InstructionBase
{
    /**
     * 执行SEC指令
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
            // 设置进位标志(C)
            $status->setFlag(StatusRegister::FLAG_CARRY, true);
        }

        return $this->cycles;
    }
}
