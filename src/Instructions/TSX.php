<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;
use Tourze\MOS6502\StatusRegister;

/**
 * TSX - Transfer Stack Pointer to X
 *
 * 将堆栈指针(SP)的值传送到X寄存器
 *
 * 操作: X = SP
 * 标志位: N Z
 */
class TSX extends InstructionBase
{
    /**
     * 执行TSX指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取堆栈指针的值
        $value = $cpu->getRegister('SP')->getValue();

        // 将其设置到X寄存器
        $cpu->getRegister('X')->setValue($value);

        // 更新状态标志位
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }

        // TSX是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
