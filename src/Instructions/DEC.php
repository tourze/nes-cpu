<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * DEC - Decrement Memory
 *
 * 将内存位置的值减少1
 *
 * 操作: M = M - 1
 * 标志位: N Z
 */
class DEC extends InstructionBase
{
    /**
     * 执行DEC指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取操作数地址
        $address = $this->addressingMode->getOperandAddress($cpu, $bus);

        // 获取内存中的值
        $value = $bus->read($address);

        // 减少1并确保结果在0-255范围内
        $value = ($value - 1) & 0xFF;

        // 将结果写回内存
        $bus->write($address, $value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // 更新PC
        $cpu->getRegister('PC')->increment($this->getBytes() - 1);

        return $this->cycles;
    }
}
