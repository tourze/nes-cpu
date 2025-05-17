<?php

declare(strict_types=1);

namespace Tourze\NES\CPU;

/**
 * 指令抽象基类
 *
 * 实现一些所有指令共用的基本功能
 */
abstract class InstructionBase implements Instruction
{
    /**
     * 操作码
     */
    protected int $opcode;

    /**
     * 助记符
     */
    protected string $mnemonic;

    /**
     * 基础周期数
     */
    protected int $cycles;

    /**
     * 指令描述
     */
    protected string $description;

    /**
     * 寻址模式
     */
    protected AddressingMode $addressingMode;

    /**
     * 构造函数
     *
     * @param int $opcode 操作码
     * @param string $mnemonic 助记符
     * @param int $cycles 基础周期数
     * @param AddressingMode $addressingMode 寻址模式
     * @param string $description 指令描述
     */
    public function __construct(
        int $opcode,
        string $mnemonic,
        int $cycles,
        AddressingMode $addressingMode,
        string $description = ''
    ) {
        $this->opcode = $opcode;
        $this->mnemonic = $mnemonic;
        $this->cycles = $cycles;
        $this->addressingMode = $addressingMode;
        $this->description = $description;
    }

    /**
     * 获取操作码
     */
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * 获取助记符
     */
    public function getMnemonic(): string
    {
        return $this->mnemonic;
    }

    /**
     * 获取基础周期数
     */
    public function getCycles(): int
    {
        return $this->cycles;
    }

    /**
     * 获取指令描述
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * 获取寻址模式
     */
    public function getAddressingMode(): AddressingMode
    {
        return $this->addressingMode;
    }

    /**
     * 获取指令字节大小
     */
    public function getBytes(): int
    {
        return $this->addressingMode->getBytes();
    }

    /**
     * 添加额外周期（用于跨页等情况）
     *
     * @param bool $condition 添加周期的条件
     * @param int $amount 增加的周期数量
     * @return int 实际增加的周期数
     */
    protected function addBranchCycles(bool $condition, int $amount = 1): int
    {
        return $condition ? $amount : 0;
    }

    /**
     * 设置寄存器值并更新状态标志
     *
     * @param CPU $cpu CPU实例
     * @param string $registerName 寄存器名称
     * @param int $value 新值
     */
    protected function setRegisterUpdateStatus(CPU $cpu, string $registerName, int $value): void
    {
        $cpu->getRegister($registerName)->setValue($value);

        // 获取状态寄存器进行标志位更新
        $status = $cpu->getRegister('P');
        if ($status instanceof StatusRegister) {
            $status->updateZeroFlag($value);
            $status->updateNegativeFlag($value);
        }
    }
}
