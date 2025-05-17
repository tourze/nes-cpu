<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\AddressingModes\AccumulatorAddressing;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * LSR - Logical Shift Right
 *
 * 将累加器或内存位置的值右移一位，最高位置0，最低位移入进位标志
 *
 * 操作: A/M = A/M >> 1
 * 标志位: N Z C
 */
class LSR extends InstructionBase
{
    /**
     * 执行LSR指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 检查寻址模式是否为累加器寻址（操作累加器）
        if ($this->addressingMode instanceof AccumulatorAddressing) {
            // 获取累加器的值
            $a = $cpu->getRegister('A')->getValue();

            // 检查第0位（将移入进位标志）
            $carryFlag = ($a & 0x01) !== 0;

            // 右移一位
            $result = $a >> 1;

            // 更新累加器
            $cpu->getRegister('A')->setValue($result);

            // 更新状态标志位
            $status = $cpu->getRegister('P');
            if ($status instanceof StatusRegister) {
                $status->setFlag(StatusRegister::FLAG_CARRY, $carryFlag);
                $status->updateZeroFlag($result);
                // 右移后最高位为0，所以负标志始终为0
                $status->setFlag(StatusRegister::FLAG_NEGATIVE, false);
            }
        } else {
            // 获取操作数地址
            $address = $this->addressingMode->getOperandAddress($cpu, $bus);

            // 获取内存中的值
            $value = $bus->read($address);

            // 检查第0位（将移入进位标志）
            $carryFlag = ($value & 0x01) !== 0;

            // 右移一位
            $result = $value >> 1;

            // 将结果写回内存
            $bus->write($address, $result);

            // 更新状态标志位
            $status = $cpu->getRegister('P');
            if ($status instanceof StatusRegister) {
                $status->setFlag(StatusRegister::FLAG_CARRY, $carryFlag);
                $status->updateZeroFlag($result);
                // 右移后最高位为0，所以负标志始终为0
                $status->setFlag(StatusRegister::FLAG_NEGATIVE, false);
            }
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1);

        return $this->cycles;
    }
}
