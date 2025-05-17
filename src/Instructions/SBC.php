<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * SBC - Subtract Memory from Accumulator with Borrow
 *
 * 从累加器中减去内存值和借位标志(1-C)
 *
 * 操作: A = A - M - (1 - C)
 * 标志位: N Z C V
 */
class SBC extends InstructionBase
{
    /**
     * 执行SBC指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数的值
        $value = $this->addressingMode->getOperandValue($cpu, $bus);

        // 获取当前累加器值
        $a = $cpu->getRegister('A')->getValue();

        // 获取状态寄存器
        $status = $cpu->getRegister('P');

        // 确保状态寄存器是正确的类型
        if (!($status instanceof StatusRegister)) {
            return $this->cycles;
        }

        // 获取当前进位标志（在SBC中，进位标志的反转表示借位）
        $carry = $status->getFlag(StatusRegister::FLAG_CARRY);

        // 检查是否为十进制模式
        if ($status->getFlag(StatusRegister::FLAG_DECIMAL)) {
            // 使用CPU的十进制模式处理方法
            $result = $cpu->handleDecimalModeSbc($a, $value, $carry);
            $diff = $result['result'];
            $newCarry = $result['carry'];

            // 更新进位标志
            $status->setFlag(StatusRegister::FLAG_CARRY, $newCarry);
        } else {
            // 二进制模式 (A - M - (1 - C))
            // 注意: 在6502中，借位是进位标志的反转
            $diff = $a - $value - ($carry ? 0 : 1);

            // 检查是否有进位（无借位）
            $status->setFlag(StatusRegister::FLAG_CARRY, $diff >= 0);

            // 检查是否有溢出（正-负=负 或 负-正=正）
            $overflowed = ((($a ^ $diff) & (~$value ^ $diff) & 0x80) !== 0);
            $status->setFlag(StatusRegister::FLAG_OVERFLOW, $overflowed);

            // 8位结果
            $diff = $diff & 0xFF;
        }

        // 更新累加器
        $cpu->getRegister('A')->setValue($diff);

        // 更新零标志和负标志
        $status->updateZeroFlag($diff);
        $status->updateNegativeFlag($diff);

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
