<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * TAX - Transfer Accumulator to X
 *
 * 将累加器(A)的值传送到X寄存器
 *
 * 操作: X = A
 * 标志位: N Z
 */
class TAX extends InstructionBase
{
    /**
     * 执行TAX指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取累加器的值
        $value = $cpu->getRegister('A')->getValue();

        // 将其设置到X寄存器
        $cpu->getRegister('X')->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // TAX是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
