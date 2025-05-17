<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 相对寻址模式
 *
 * 用于分支指令，操作数是一个有符号的偏移量，相对于下一条指令的地址，如BNE $10
 */
class RelativeAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 2; // 操作码 + 1字节偏移量
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 读取指令后的一个字节作为偏移量
        $pcValue = $cpu->getRegister('PC')->getValue();
        $offset = $bus->read($pcValue);

        // 将偏移量视为有符号字节（-128到+127）
        if ($offset & 0x80) {
            $offset = $offset - 256; // 处理负偏移
        }

        // 目标地址是当前PC加上偏移量再加2（因为PC已经指向操作数）
        $targetAddress = ($pcValue + $offset + 2) & 0xFFFF;

        // 检查是否跨页边界（影响周期计数）
        // 相对寻址时，应检查指令结束后的PC（即PC+2）是否和目标地址在不同页
        $nextPC = ($pcValue + 2) & 0xFFFF;
        $this->crossesPageBoundary = $this->checkPageCrossing($nextPC, $targetAddress);

        return $targetAddress;
    }

    public function getOperandValue(CPU $cpu, Bus $bus): int
    {
        // 对于相对寻址，我们可能更关心目标地址而不是存储在该地址的值
        // 但为了接口一致性，我们返回操作数（偏移量）本身
        $pcValue = $cpu->getRegister('PC')->getValue();
        return $bus->read($pcValue);
    }

    public function getName(): string
    {
        return "relative";
    }
}
