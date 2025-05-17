<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\AddressingModes;

use Tourze\NES\CPU\AddressingModeBase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;

/**
 * 间接寻址模式
 *
 * 操作数地址存储在指定的内存地址中，如JMP ($1234)
 * 实现了6502著名的间接寻址bug：如果指针地址在页面边界上（$xxFF），不会正确跨页读取高字节
 */
class IndirectAddressing extends AddressingModeBase
{
    public function getBytes(): int
    {
        return 3; // 操作码 + 2字节间接地址
    }

    public function getOperandAddress(CPU $cpu, Bus $bus): int
    {
        // 读取指令后的两个字节作为16位间接地址（小端序）
        $pcValue = $cpu->getRegister('PC')->getValue();
        $indirectAddress = $bus->readWord($pcValue);

        // 实现6502的间接寻址bug
        // 如果间接地址在页面边界上（如$xxFF），高字节的读取会错误地包装回同一页面（$xx00）
        if (($indirectAddress & 0xFF) === 0xFF) {
            $lowByte = $bus->read($indirectAddress);
            $highByte = $bus->read($indirectAddress & 0xFF00); // 错误地回到页面起始处
            return ($highByte << 8) | $lowByte;
        } else {
            // 正常情况：从间接地址读取16位操作数地址
            return $bus->readWord($indirectAddress);
        }
    }

    public function getName(): string
    {
        return "indirect";
    }
}
