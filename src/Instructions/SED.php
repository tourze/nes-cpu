<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * SED - 设置十进制模式标志 (Set Decimal Flag)
 *
 * 将状态寄存器中的十进制模式标志(D)设置为1，使CPU使用BCD算术模式。
 */
class SED extends InstructionBase
{
    /**
     * 执行SED指令
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
            // 设置十进制模式标志(D)
            $status->setFlag(StatusRegister::FLAG_DECIMAL, true);
        }

        return $this->cycles;
    }
}
