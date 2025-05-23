<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * ADC - Add Memory to Accumulator with Carry
 *
 * 将内存值和进位标志加到累加器中
 *
 * 操作: A = A + M + C
 * 标志位: N Z C V
 *
 * 注意: 在Ricoh 2A03（NES的CPU）中，十进制模式被禁用，即使设置了D标志也会被忽略
 * 本实现可以通过CPU的disableBCD设置来模拟这种行为，或者使用标准6502的BCD模式
 */
class ADC extends InstructionBase
{
    /**
     * 执行ADC指令
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

        // 获取当前进位标志
        $carry = $status->getFlag(StatusRegister::FLAG_CARRY) ? 1 : 0;

        // 检查是否为十进制模式（仅当BCD模式未被禁用时有效）
        if (!$cpu->isDisableBCD() && $status->getFlag(StatusRegister::FLAG_DECIMAL)) {
            // 使用CPU的十进制模式处理方法
            $result = $cpu->handleDecimalMode($a, $value, $carry === 1);
            $sum = $result['result'];
            $newCarry = $result['carry'];

            // 更新进位标志
            $status->setFlag(StatusRegister::FLAG_CARRY, $newCarry);
        } else {
            // 二进制模式
            $sum = $a + $value + $carry;

            // 检查是否有进位（结果超过255）
            $status->setFlag(StatusRegister::FLAG_CARRY, $sum > 0xFF);

            // 检查是否有溢出（正+正=负 或 负+负=正）
            $overflowed = ((($a ^ $sum) & ($value ^ $sum) & 0x80) !== 0);
            $status->setFlag(StatusRegister::FLAG_OVERFLOW, $overflowed);

            // 8位结果
            $sum = $sum & 0xFF;
        }

        // 更新累加器
        $cpu->getRegister('A')->setValue($sum);

        // 更新零标志和负标志
        $status->updateZeroFlag($sum);
        $status->updateNegativeFlag($sum);

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
