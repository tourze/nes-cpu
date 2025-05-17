<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * PLA - 拉出累加器 (Pull Accumulator)
 *
 * 从堆栈拉出一个值到累加器，并更新负数和零标志。
 */
class PLA extends InstructionBase
{
    /**
     * 执行PLA指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 从堆栈拉出值
        $value = $cpu->pull();

        // 将值存入累加器
        $cpu->getRegister('A')->setValue($value);

        // 获取状态寄存器，更新负数和零标志
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateNegativeFlag($value);
            $status->updateZeroFlag($value);
        }

        return $this->cycles;
    }
}
