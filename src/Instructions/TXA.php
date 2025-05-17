<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * TXA - Transfer X to Accumulator
 *
 * 将X寄存器的值传送到累加器(A)
 *
 * 操作: A = X
 * 标志位: N Z
 */
class TXA extends InstructionBase
{
    /**
     * 执行TXA指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取X寄存器的值
        $value = $cpu->getRegister('X')->getValue();

        // 将其设置到累加器
        $cpu->getRegister('A')->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // TXA是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
