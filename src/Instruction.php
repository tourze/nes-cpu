<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

/**
 * 指令接口
 *
 * 定义所有CPU指令需要实现的方法
 */
interface Instruction
{
    /**
     * 执行指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int;

    /**
     * 获取指令需要的基础周期数
     *
     * @return int 周期数
     */
    public function getCycles(): int;

    /**
     * 获取操作码
     *
     * @return int 操作码值
     */
    public function getOpcode(): int;

    /**
     * 获取助记符
     *
     * @return string 指令助记符
     */
    public function getMnemonic(): string;

    /**
     * 获取指令描述
     *
     * @return string 指令描述
     */
    public function getDescription(): string;

    /**
     * 获取指令使用的寻址模式
     *
     * @return AddressingMode 寻址模式
     */
    public function getAddressingMode(): AddressingMode;

    /**
     * 获取指令字节大小
     *
     * @return int 字节数
     */
    public function getBytes(): int;
}
