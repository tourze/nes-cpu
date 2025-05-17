<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\InstructionBase;
use Tourze\NES\CPU\StatusRegister;

/**
 * INY - Increment Y Register
 *
 * 将Y寄存器的值增加1
 *
 * 操作: Y = Y + 1
 * 标志位: N Z
 */
class INY extends InstructionBase
{
    /**
     * 执行INY指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取Y寄存器
        $yRegister = $cpu->getRegister('Y');

        // 获取当前Y寄存器值并增加1
        $value = ($yRegister->getValue() + 1) & 0xFF; // 保证结果在0-255范围内

        // 更新Y寄存器
        $yRegister->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // INY是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
