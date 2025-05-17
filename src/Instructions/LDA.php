<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * LDA - Load Accumulator
 *
 * 将内存中的值加载到累加器(A)中，并设置零和负标志
 *
 * 操作: A = M
 * 标志位: N Z
 */
class LDA extends InstructionBase
{
    /**
     * 执行LDA指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 更新累加器的值
        $cpu->getRegister('A')->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // 计算指令周期数（基础周期数 + 跨页额外周期）
        $cycles = $this->cycles;

        // 某些寻址模式（如AbsoluteX, AbsoluteY, IndirectY）在跨页时会增加一个周期
        if ($this->addressingMode->getCrossesPageBoundary()) {
            $cycles++;
        }

        // 更新PC指向下一条指令
        $cpu->getRegister('PC')->increment($this->getBytes() - 1); // 减1是因为CPU->step()中已经增加了1

        return $cycles;
    }
}
