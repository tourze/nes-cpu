<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * SEI - 设置中断禁用标志 (Set Interrupt Disable)
 * 
 * 将状态寄存器中的中断禁用标志(I)设置为1，禁止CPU响应可屏蔽中断(IRQ)。
 */
class SEI extends InstructionBase
{
    /**
     * 执行SEI指令
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
            // 设置中断禁用标志(I)
            $status->setFlag(StatusRegister::FLAG_INTERRUPT, true);
        }
        
        return $this->cycles;
    }
} 