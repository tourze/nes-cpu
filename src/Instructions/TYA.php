<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * TYA - Transfer Y to Accumulator
 *
 * 将Y寄存器的值传送到累加器(A)
 *
 * 操作: A = Y
 * 标志位: N Z
 */
class TYA extends InstructionBase
{
    /**
     * 执行TYA指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取Y寄存器的值
        $value = $cpu->getRegister('Y')->getValue();

        // 将其设置到累加器
        $cpu->getRegister('A')->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // TYA是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
