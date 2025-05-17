<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * JMP - 无条件跳转 (Jump to Location)
 *
 * 无条件跳转到指定地址
 * 绝对寻址模式: 跳转到操作数指定的16位地址
 * 间接寻址模式: 跳转到操作数指定的地址处存储的16位地址
 * 注意: 间接寻址模式存在一个硬件bug，当指定地址为页边界时(如$xxFF)，
 * 高字节将从同一页的$xx00读取，而不是从下一页的$xx00读取
 */
class JMP extends InstructionBase
{
    /**
     * 执行JMP指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取目标地址 - 由寻址模式处理具体逻辑
        // IndirectAddressing已经包含了页边界bug的处理
        $targetAddress = $this->addressingMode->getOperandAddress($cpu, $bus);

        // 设置程序计数器指向目标地址
        $cpu->getRegister('PC')->setValue($targetAddress);

        return $this->cycles;
    }
}
