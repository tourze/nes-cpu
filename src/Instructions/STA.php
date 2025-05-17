<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * STA - Store Accumulator
 *
 * 将累加器(A)的值存储到内存中
 *
 * 操作: M = A
 * 标志位: 无
 */
class STA extends InstructionBase
{
    /**
     * 执行STA指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数地址
        $address = $this->addressingMode->getOperandAddress($cpu, $bus);

        // 获取累加器的值
        $value = $cpu->getRegister('A')->getValue();

        // 将值写入内存
        $bus->write($address, $value);

        // 计算指令周期数（STA不受跨页影响）
        $cycles = $this->cycles;

        // 更新PC指向下一条指令
        $cpu->getRegister('PC')->increment($this->getBytes() - 1); // 减1是因为CPU->step()中已经增加了1

        return $cycles;
    }
}
