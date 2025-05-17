<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;

/**
 * STY - 存储Y寄存器 (Store Y Register)
 *
 * 将Y寄存器的值存储到内存
 * 操作: Y -> M
 */
class STY extends InstructionBase
{
    /**
     * 执行STY指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取目标地址
        $address = $this->addressingMode->getOperandAddress($cpu, $bus);
        
        // 获取Y寄存器的值
        $value = $cpu->getRegister('Y')->getValue();
        
        // 将值写入内存
        $bus->write($address, $value);
        
        return $this->cycles;
    }
} 