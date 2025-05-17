<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * EOR - Exclusive OR
 *
 * 将累加器与内存进行逻辑异或操作
 *
 * 操作: A = A ^ M
 * 标志位: N Z
 */
class EOR extends InstructionBase
{
    /**
     * 执行EOR指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 获取当前累加器值
        $a = $cpu->getRegister('A')->getValue();

        // 执行逻辑异或操作
        $result = $a ^ $value;

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
