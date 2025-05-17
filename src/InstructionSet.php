<?php

declare(strict_types=1);

namespace Tourze\NES\CPU;

use InvalidArgumentException;
use RuntimeException;

/**
 * 指令集类
 *
 * 管理所有CPU指令，提供指令的查找和执行功能
 */
class InstructionSet
{
    /**
     * 指令映射表 (opcode => instruction)
     */
    private array $instructions = [];

    /**
     * 指令使用统计
     */
    private array $stats = [];

    /**
     * 构造函数
     *
     * 初始化所有6502 CPU指令
     */
    public function __construct()
    {
        $this->initializeInstructions();
    }

    /**
     * 初始化所有6502指令
     */
    private function initializeInstructions(): void
    {
        // 初始化指令映射表
        $this->initializeLoadInstructions();
        $this->initializeStoreInstructions();
        $this->initializeTransferInstructions();
        $this->initializeStackInstructions();
        $this->initializeLogicalInstructions();
        $this->initializeArithmeticInstructions();
        $this->initializeIncrementDecrementInstructions();
        $this->initializeShiftInstructions();
        $this->initializeCompareInstructions();
        $this->initializeJumpCallInstructions();
        $this->initializeBranchInstructions();
        $this->initializeStatusInstructions();
        $this->initializeSystemInstructions();
    }

    /**
     * 初始化加载和存储指令
     */
    private function initializeLoadInstructions(): void
    {
        $loadStoreInitializer = new InstructionInitializers\LoadStoreInitializer();
        $loadStoreInitializer->initialize($this);
    }

    /**
     * 初始化存储指令
     */
    private function initializeStoreInstructions(): void
    {
        // 由于LoadStoreInitializer已经包含了存储指令，这里不需要重复
    }

    /**
     * 初始化传送指令
     */
    private function initializeTransferInstructions(): void
    {
        $transferInitializer = new InstructionInitializers\TransferInitializer();
        $transferInitializer->initialize($this);
    }

    /**
     * 初始化堆栈指令
     */
    private function initializeStackInstructions(): void
    {
        // 堆栈指令(PHA, PHP, PLA, PLP)已在SystemInitializer中实现
    }

    /**
     * 初始化逻辑运算指令
     */
    private function initializeLogicalInstructions(): void
    {
        $logicalInitializer = new InstructionInitializers\LogicalInitializer();
        $logicalInitializer->initialize($this);
    }

    /**
     * 初始化算术运算指令
     */
    private function initializeArithmeticInstructions(): void
    {
        $arithmeticInitializer = new InstructionInitializers\ArithmeticInitializer();
        $arithmeticInitializer->initialize($this);
    }

    /**
     * 初始化增减指令
     */
    private function initializeIncrementDecrementInstructions(): void
    {
        $incrementDecrementInitializer = new InstructionInitializers\IncrementDecrementInitializer();
        $incrementDecrementInitializer->initialize($this);
    }

    /**
     * 初始化移位指令
     */
    private function initializeShiftInstructions(): void
    {
        $shiftInitializer = new InstructionInitializers\ShiftInitializer();
        $shiftInitializer->initialize($this);
    }

    /**
     * 初始化比较指令
     */
    private function initializeCompareInstructions(): void
    {
        $compareInitializer = new InstructionInitializers\CompareInitializer();
        $compareInitializer->initialize($this);
    }

    /**
     * 初始化跳转和调用指令
     */
    private function initializeJumpCallInstructions(): void
    {
        $jumpCallInitializer = new InstructionInitializers\JumpCallInitializer();
        $jumpCallInitializer->initialize($this);
    }

    /**
     * 初始化分支指令
     */
    private function initializeBranchInstructions(): void
    {
        $branchInitializer = new InstructionInitializers\BranchInitializer();
        $branchInitializer->initialize($this);
    }

    /**
     * 初始化状态标志指令
     */
    private function initializeStatusInstructions(): void
    {
        $statusFlagInitializer = new InstructionInitializers\StatusFlagInitializer();
        $statusFlagInitializer->initialize($this);
    }

    /**
     * 初始化系统指令
     */
    private function initializeSystemInstructions(): void
    {
        $systemInitializer = new InstructionInitializers\SystemInitializer();
        $systemInitializer->initialize($this);
    }

    /**
     * 根据操作码获取指令
     *
     * @param int $opcode 操作码
     * @return Instruction 指令实例
     * @throws InvalidArgumentException 当操作码不存在时抛出
     */
    public function getInstruction(int $opcode): Instruction
    {
        if (!isset($this->instructions[$opcode])) {
            throw new InvalidArgumentException(sprintf('Unknown opcode: 0x%02X', $opcode));
        }

        // 更新使用统计
        if (!isset($this->stats[$opcode])) {
            $this->stats[$opcode] = 0;
        }

        $this->stats[$opcode]++;

        return $this->instructions[$opcode];
    }

    /**
     * 执行指定操作码的指令
     *
     * @param int $opcode 操作码
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     * @throws RuntimeException 当执行过程中出错时抛出
     */
    public function execute(int $opcode, CPU $cpu, Bus $bus): int
    {
        $instruction = $this->getInstruction($opcode);
        return $instruction->execute($cpu, $bus);
    }

    /**
     * 获取操作码的信息
     *
     * @param int $opcode 操作码
     * @return array 操作码信息
     * @throws InvalidArgumentException 当操作码不存在时抛出
     */
    public function getInstructionInfo(int $opcode): array
    {
        $instruction = $this->getInstruction($opcode);

        return [
            'opcode' => $opcode,
            'mnemonic' => $instruction->getMnemonic(),
            'addressing_mode' => $instruction->getAddressingMode()->getName(),
            'bytes' => $instruction->getBytes(),
            'cycles' => $instruction->getCycles(),
            'description' => $instruction->getDescription(),
        ];
    }

    /**
     * 获取指令使用统计
     *
     * @return array 指令使用统计
     */
    public function getInstructionStats(): array
    {
        return $this->stats;
    }

    /**
     * 注册自定义指令
     *
     * @param int $opcode 操作码
     * @param Instruction $instruction 指令实例
     * @return void
     * @throws InvalidArgumentException 当操作码已存在时抛出
     */
    public function registerInstruction(int $opcode, Instruction $instruction): void
    {
        if (isset($this->instructions[$opcode])) {
            throw new InvalidArgumentException(sprintf('Opcode already registered: 0x%02X', $opcode));
        }

        $this->instructions[$opcode] = $instruction;
    }
}
