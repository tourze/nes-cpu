<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

/**
 * 寻址模式接口
 *
 * 定义了各种寻址模式的通用接口
 */
interface AddressingMode
{
    /**
     * 获取操作数地址
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 操作数地址
     */
    public function getOperandAddress(CPU $cpu, Bus $bus): int;

    /**
     * 获取操作数值
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 操作数值
     */
    public function getOperandValue(CPU $cpu, Bus $bus): int;

    /**
     * 获取此寻址模式使用的字节数
     *
     * @return int 字节数
     */
    public function getBytes(): int;

    /**
     * 检查是否跨页边界
     *
     * @return bool 是否跨页边界
     */
    public function getCrossesPageBoundary(): bool;

    /**
     * 获取寻址模式名称
     *
     * @return string 寻址模式名称
     */
    public function getName(): string;
}
