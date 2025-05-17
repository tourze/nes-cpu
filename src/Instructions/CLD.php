<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * CLD - 清除十进制模式标志 (Clear Decimal Mode)
 *
 * 将状态寄存器中的十进制模式标志(D)设置为0，使CPU使用二进制算术模式。
 */
class CLD extends InstructionBase
{
    /**
     * 执行CLD指令
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
            // 清除十进制模式标志(D)
            $status->setFlag(StatusRegister::FLAG_DECIMAL, false);
        }

        return $this->cycles;
    }
}
