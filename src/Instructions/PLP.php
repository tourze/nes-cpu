<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * PLP - 拉出处理器状态 (Pull Processor Status)
 *
 * a从堆栈拉出一个值到状态寄存器。
 * 注意: 当状态从堆栈拉出时，B标志不受影响，而未使用标志总是被设为1。
 */
class PLP extends InstructionBase
{
    /**
     * 执行PLP指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 从堆栈拉出值
        $value = $cpu->pull();

        // 获取状态寄存器
        $status = $cpu->getRegister('P');

        if ($status instanceof StatusRegister) {
            // 保留当前B标志状态，设置未使用标志为1
            $bFlag = $status->getFlag(StatusRegister::FLAG_BREAK);

            // 设置状态寄存器的值
            $status->setValue($value);

            // 恢复B标志的原始状态，确保未使用标志为1
            $status->setFlag(StatusRegister::FLAG_BREAK, $bFlag);
            $status->setFlag(StatusRegister::FLAG_UNUSED, true);
        }

        return $this->cycles;
    }
}
