<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * CLI - 清除中断禁用标志 (Clear Interrupt Disable)
 *
 * 将状态寄存器中的中断禁用标志(I)设置为0，允许CPU响应可屏蔽中断(IRQ)。
 */
class CLI extends InstructionBase
{
    /**
     * 执行CLI指令
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
            // 清除中断禁用标志(I)
            $status->setFlag(StatusRegister::FLAG_INTERRUPT, false);
        }

        return $this->cycles;
    }
}
