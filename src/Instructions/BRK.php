<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * BRK - 强制中断 (Force Interrupt)
 *
 * 强制生成一个中断，将程序计数器和处理器状态压入堆栈，
 * 然后从中断向量($FFFE-$FFFF)加载新的程序计数器值，
 * 并设置中断禁用标志(I)。
 *
 * 注意: BRK是2字节指令，但第二个字节会被忽略。PC压栈时加2。
 */
class BRK extends InstructionBase
{
    /**
     * 执行BRK指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取当前程序计数器值并加2（跳过操作码和未使用的第二个字节）
        $pc = $cpu->getRegister('PC')->getValue() + 1;

        // 将PC压入堆栈
        $cpu->pushWord($pc);

        // 获取状态寄存器
        $status = $cpu->getRegister('P');

        if ($status instanceof StatusRegister) {
            // 设置B标志位，然后压入状态寄存器
            $status->setFlag(StatusRegister::FLAG_BREAK, true);
            $cpu->push($status->getValue());

            // 设置中断禁用标志
            $status->setFlag(StatusRegister::FLAG_INTERRUPT, true);

            // 清除B标志位（实际硬件行为，中断处理时B位为0）
            $status->setFlag(StatusRegister::FLAG_BREAK, false);
        }

        // 从中断向量读取新的PC值
        $newPC = $bus->readWord(0xFFFE);
        $cpu->getRegister('PC')->setValue($newPC);

        return $this->cycles;
    }
}
