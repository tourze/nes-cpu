<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * CPY - Compare Memory with Y Register
 *
 * 比较Y寄存器与内存值，影响标志位但不改变Y寄存器值
 *
 * 操作: Y - M
 * 标志位: N Z C
 */
class CPY extends InstructionBase
{
    /**
     * 执行CPY指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 获取当前Y寄存器值
        $y = $cpu->getRegister('Y')->getValue();

        // 计算差值（不存储）
        $result = ($y - $value) & 0xFF;

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            // 如果Y >= M，则设置进位标志（相当于无借位）
            $status->setFlag(StatusRegister::FLAG_CARRY, $y >= $value);

            // 更新零标志（如果相等）
            $status->setFlag(StatusRegister::FLAG_ZERO, $y === $value);

            // 更新负标志
            $status->updateNegativeFlag($result);
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1);

        return $this->cycles;
    }
}
