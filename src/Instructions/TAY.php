<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * TAY - Transfer Accumulator to Y
 *
 * 将累加器(A)的值传送到Y寄存器
 *
 * 操作: Y = A
 * 标志位: N Z
 */
class TAY extends InstructionBase
{
    /**
     * 执行TAY指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取累加器的值
        $value = $cpu->getRegister('A')->getValue();

        // 将其设置到Y寄存器
        $cpu->getRegister('Y')->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // TAY是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
