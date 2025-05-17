<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * JSR - 跳转到子程序 (Jump to Subroutine)
 *
 * 将返回地址（下一个指令的地址减1）压入堆栈，然后跳转到目标地址
 * 操作: PC+2 -> 堆栈, 操作数 -> PC
 * 注意: 返回地址指向JSR指令之后的字节减1，需要RTS指令补偿+1
 */
class JSR extends InstructionBase
{
    /**
     * 执行JSR指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 获取当前程序计数器值
        $pc = $cpu->getRegister('PC')->getValue();

        // 获取目标地址
        $targetAddress = $this->addressingMode->getOperandAddress($cpu, $bus);

        // 计算返回地址 (JSR指令后的下一个指令地址-1)
        // JSR是3字节指令，所以返回地址是PC+2-1=PC+1
        $returnAddress = $pc + 1;

        // 将返回地址压入堆栈 (先高字节后低字节)
        $cpu->pushWord($returnAddress);

        // 设置程序计数器指向目标地址
        $cpu->getRegister('PC')->setValue($targetAddress);

        return $this->cycles;
    }
}
