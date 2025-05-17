<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * DEX - Decrement X Register
 *
 * 将X寄存器的值减少1
 *
 * 操作: X = X - 1
 * 标志位: N Z
 */
class DEX extends InstructionBase
{
    /**
     * 执行DEX指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取X寄存器
        $xRegister = $cpu->getRegister('X');

        // 获取当前X寄存器值并减少1
        $value = ($xRegister->getValue() - 1) & 0xFF; // 保证结果在0-255范围内

        // 更新X寄存器
        $xRegister->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // DEX是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
