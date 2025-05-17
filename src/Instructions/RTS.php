<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * RTS - 从子程序返回 (Return from Subroutine)
 *
 * 从堆栈中拉出返回地址并跳转到该地址+1
 * 操作: 堆栈 -> PC, PC+1 -> PC
 * 注意: JSR在存储返回地址时减去了1，所以RTS需要加回来
 */
class RTS extends InstructionBase
{
    /**
     * 执行RTS指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 从堆栈中拉出返回地址
        $returnAddress = $cpu->pullWord();

        // 返回地址需要+1（因为JSR存储时-1）
        $returnAddress++;

        // 设置程序计数器指向返回地址
        $cpu->getRegister('PC')->setValue($returnAddress);

        return $this->cycles;
    }
}
