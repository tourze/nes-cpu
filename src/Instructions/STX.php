<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;

/**
 * STX - 存储X寄存器 (Store X Register)
 *
 * 将X寄存器的值存储到内存
 * 操作: X -> M
 */
class STX extends InstructionBase
{
    /**
     * 执行STX指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取目标地址
        $address = $this->addressingMode->getOperandAddress($cpu, $bus);
        
        // 获取X寄存器的值
        $value = $cpu->getRegister('X')->getValue();
        
        // 将值写入内存
        $bus->write($address, $value);
        
        return $this->cycles;
    }
} 