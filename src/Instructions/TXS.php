<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * TXS - Transfer X to Stack Pointer
 *
 * 将X寄存器的值传送到堆栈指针(SP)
 *
 * 操作: SP = X
 * 标志位: 无
 */
class TXS extends InstructionBase
{
    /**
     * 执行TXS指令
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取X寄存器的值
        $value = $cpu->getRegister('X')->getValue();

        // 将其设置到堆栈指针
        $cpu->getRegister('SP')->setValue($value);

        // TXS不影响任何状态标志

        // TXS是隐含寻址，不需要更新PC

        return $this->cycles;
    }
}
