<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * RTI - 从中断返回 (Return from Interrupt)
 *
 * 从堆栈中拉出状态寄存器和返回地址，然后跳转到返回地址
 * 操作: 堆栈 -> P, 堆栈 -> PC
 * 注意: 与RTS不同，RTI不需要对返回地址进行+1操作
 */
class RTI extends InstructionBase
{
    /**
     * 执行RTI指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 从堆栈中拉出状态寄存器值
        $statusValue = $cpu->pull();

        // 获取状态寄存器实例并设置其值
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            // 设置状态寄存器值，但确保第5位为1，第4位(B标志)为0
            // 这是6502 CPU的特性，在RTI时总是清除B标志
            $statusValue = ($statusValue | 0x20) & 0xEF; // 设置位5为1，位4为0
            $status->setValue($statusValue);
        }

        // 从堆栈中拉出返回地址
        $returnAddress = $cpu->pullWord();

        // 设置程序计数器指向返回地址
        $cpu->getRegister('PC')->setValue($returnAddress);

        return $this->cycles;
    }
}
