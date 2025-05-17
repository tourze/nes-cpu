<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * PHP - 压入处理器状态 (Push Processor Status)
 *
 * 将处理器状态寄存器的值压入堆栈。
 * 注意: 当状态寄存器被压入堆栈时，B标志会被设置为1。
 */
class PHP extends InstructionBase
{
    /**
     * 执行PHP指令
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
            // 获取状态值，并确保B标志和未使用标志都为1（PHP特有行为）
            $statusValue = $status->getValue();
            $statusValue |= (StatusRegister::FLAG_BREAK | StatusRegister::FLAG_UNUSED);

            // 将状态值压入堆栈
            $cpu->push($statusValue);
        }

        return $this->cycles;
    }
}
