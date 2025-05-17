<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * AND - Logical AND
 *
 * 将累加器与内存进行逻辑与操作
 *
 * 操作: A = A & M
 * 标志位: N Z
 */
class LogicalAND extends InstructionBase
{
    /**
     * 执行AND指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 获取当前累加器值
        $a = $cpu->getRegister('A')->getValue();

        // 执行逻辑与操作
        $result = $a & $value;

        // 更新累加器
        $cpu->getRegister('A')->setValue($result);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($result);
            $status->updateNegativeFlag($result);
        }

        // 计算周期数（跨页时+1）
        $cycles = $this->cycles;
        if ($this->addressingMode->getCrossesPageBoundary()) {
            $cycles++;
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1); // 减1是因为CPU->step()中已经增加了1

        return $cycles;
    }
}
