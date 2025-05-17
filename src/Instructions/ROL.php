<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\AddressingModes\AccumulatorAddressing;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * ROL - Rotate Left
 *
 * 将累加器或内存位置的值左移一位，最低位由进位标志填充，最高位移入进位标志
 *
 * 操作: A/M = A/M << 1 | C
 * 标志位: N Z C
 */
class ROL extends InstructionBase
{
    /**
     * 执行ROL指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取当前进位状态
        $status = $cpu->getRegister('P');
        if (!($status instanceof StatusRegister)) {
            return $this->cycles;
        }

        $currentCarry = $status->getFlag(StatusRegister::FLAG_CARRY) ? 1 : 0;

        // 检查寻址模式是否为累加器寻址（操作累加器）
        if ($this->addressingMode instanceof AccumulatorAddressing) {
            // 获取累加器的值
            $a = $cpu->getRegister('A')->getValue();

            // 检查第7位（将移入进位标志）
            $newCarry = ($a & 0x80) !== 0;

            // 左移一位，并将当前进位放入最低位
            $result = (($a << 1) | $currentCarry) & 0xFF;

            // 更新累加器
            $cpu->getRegister('A')->setValue($result);

            // 更新状态标志位
            $status->setFlag(StatusRegister::FLAG_CARRY, $newCarry);
            $status->updateZeroFlag($result);
            $status->updateNegativeFlag($result);
        } else {
            // 获取操作数地址
            $address = $this->addressingMode->getOperandAddress($cpu, $bus);

            // 获取内存中的值
            $value = $bus->read($address);

            // 检查第7位（将移入进位标志）
            $newCarry = ($value & 0x80) !== 0;

            // 左移一位，并将当前进位放入最低位
            $result = (($value << 1) | $currentCarry) & 0xFF;

            // 将结果写回内存
            $bus->write($address, $result);

            // 更新状态标志位
            $status->setFlag(StatusRegister::FLAG_CARRY, $newCarry);
            $status->updateZeroFlag($result);
            $status->updateNegativeFlag($result);
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1);

        return $this->cycles;
    }
}
