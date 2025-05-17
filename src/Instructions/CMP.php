<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * CMP - Compare Memory with Accumulator
 *
 * 比较累加器与内存值，影响标志位但不改变累加器值
 *
 * 操作: A - M
 * 标志位: N Z C
 */
class CMP extends InstructionBase
{
    /**
     * 执行CMP指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 获取当前累加器值
        $a = $cpu->getRegister('A')->getValue();

        // 计算差值（不存储）
        $result = ($a - $value) & 0xFF;

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            // 如果A >= M，则设置进位标志（相当于无借位）
            $status->setFlag(StatusRegister::FLAG_CARRY, $a >= $value);

            // 更新零标志（如果相等）
            $status->setFlag(StatusRegister::FLAG_ZERO, $a === $value);

            // 更新负标志
            $status->updateNegativeFlag($result);
        }

        // 计算周期数（跨页时+1）
        $cycles = $this->cycles;
        if ($this->addressingMode->getCrossesPageBoundary()) {
            $cycles++;
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1);

        return $cycles;
    }
}
