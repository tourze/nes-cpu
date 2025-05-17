<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * CPX - Compare Memory with X Register
 *
 * 比较X寄存器与内存值，影响标志位但不改变X寄存器值
 *
 * 操作: X - M
 * 标志位: N Z C
 */
class CPX extends InstructionBase
{
    /**
     * 执行CPX指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 获取当前X寄存器值
        $x = $cpu->getRegister('X')->getValue();

        // 计算差值（不存储）
        $result = ($x - $value) & 0xFF;

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            // 如果X >= M，则设置进位标志（相当于无借位）
            $status->setFlag(StatusRegister::FLAG_CARRY, $x >= $value);

            // 更新零标志（如果相等）
            $status->setFlag(StatusRegister::FLAG_ZERO, $x === $value);

            // 更新负标志
            $status->updateNegativeFlag($result);
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1);

        return $this->cycles;
    }
}
