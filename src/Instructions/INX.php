<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * INX - Increment X Register
 *
 * 将X寄存器的值增加1
 *
 * 操作: X = X + 1
 * 标志位: N Z
 */
class INX extends InstructionBase
{
    /**
     * 执行INX指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取X寄存器
        $xRegister = $cpu->getRegister('X');

        // 获取当前X寄存器值并增加1
        $value = ($xRegister->getValue() + 1) & 0xFF; // 保证结果在0-255范围内

        // 更新X寄存器
        $xRegister->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // INX是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
