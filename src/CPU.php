<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

use Exception;
use InvalidArgumentException;

/**
 * MOS 6502 CPU模拟器
 *
 * 实现6502 CPU的核心功能，包括寄存器、指令执行和中断处理
 */
class CPU
{
    /**
     * 累加器 (A) 寄存器
     */
    private Register $a;

    /**
     * X索引寄存器
     */
    private Register $x;

    /**
     * Y索引寄存器
     */
    private Register $y;

    /**
     * 程序计数器 (PC)
     */
    private Register $pc;

    /**
     * 堆栈指针 (SP)
     */
    private Register $sp;

    /**
     * 状态寄存器 (P)
     */
    private StatusRegister $status;

    /**
     * 当前指令周期计数
     */
    private int $currentCycles = 0;

    /**
     * 总周期计数
     */
    private int $totalCycles = 0;

    /**
     * 已执行指令数
     */
    private int $instructionsExecuted = 0;

    /**
     * 中断挂起标志
     */
    private bool $irqPending = false;

    /**
     * NMI中断挂起标志
     */
    private bool $nmiPending = false;

    /**
     * 总线实例
     */
    private Bus $bus;

    /**
     * 指令集实例
     */
    private InstructionSet $instructionSet;

    /**
     * 堆栈页面基地址
     */
    private const STACK_PAGE = 0x0100;

    /**
     * 重置向量地址
     */
    private const RESET_VECTOR = 0xFFFC;

    /**
     * IRQ/BRK向量地址
     */
    private const IRQ_VECTOR = 0xFFFE;

    /**
     * NMI向量地址
     */
    private const NMI_VECTOR = 0xFFFA;

    /**
     * 构造函数
     *
     * @param Bus $bus 总线实例
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
        $this->instructionSet = new InstructionSet();

        // 初始化寄存器
        $this->a = new Register('A');
        $this->x = new Register('X');
        $this->y = new Register('Y');
        $this->pc = new Register('PC', 16);
        $this->sp = new Register('SP', 8, 0xFF); // 堆栈指针初始值为0xFF（空栈）
        $this->status = new StatusRegister();

        // 设置状态寄存器的初始状态
        $this->status->setFlag(StatusRegister::FLAG_INTERRUPT, true); // 中断禁用
        $this->status->setFlag(StatusRegister::FLAG_UNUSED, true); // 未使用位始终为1

        // 重置CPU状态
        $this->reset();
    }

    /**
     * 重置CPU到初始状态
     *
     * @return void
     */
    public function reset(): void
    {
        // 重置寄存器
        $this->a->reset();
        $this->x->reset();
        $this->y->reset();

        // SP在复位时设置为0xFD
        $this->sp->setValue(0xFD);

        // 状态寄存器初始化
        $this->status->reset();
        $this->status->setFlag(StatusRegister::FLAG_INTERRUPT, true); // 中断禁用

        // 从复位向量获取PC初始值
        $resetVector = $this->bus->readWord(self::RESET_VECTOR);
        $this->pc->setValue($resetVector);

        // 重置周期计数和指令计数
        $this->currentCycles = 0;
        $this->totalCycles = 0;
        $this->instructionsExecuted = 0;

        // 清除中断挂起标志
        $this->irqPending = false;
        $this->nmiPending = false;
    }

    /**
     * 执行一条指令
     *
     * @return int 执行指令所需的周期数
     */
    public function step(): int
    {
        // 处理中断（将在后续实现）
        if ($this->nmiPending) {
            $this->handleNMI();
        } elseif ($this->irqPending && !$this->status->getFlag(StatusRegister::FLAG_INTERRUPT)) {
            $this->handleIRQ();
        }

        // 获取当前指令的操作码
        $opcode = $this->bus->read($this->pc->getValue());

        // 增加PC以指向下一字节
        $this->pc->increment();

        // 执行指令（实际实现将在后续完成）
        $this->currentCycles = 0; // 将由具体指令设置
        $this->executeInstruction($opcode);

        // 更新计数器
        $this->totalCycles += $this->currentCycles;
        $this->instructionsExecuted++;

        return $this->currentCycles;
    }

    /**
     * 执行指定的操作码
     *
     * @param int $opcode 操作码
     * @return void
     */
    private function executeInstruction(int $opcode): void
    {
        try {
            // 使用指令集执行指令
            $this->currentCycles = $this->instructionSet->execute($opcode, $this, $this->bus);
        } catch (InvalidArgumentException $e) {
            // 未知操作码
            $this->currentCycles = 2; // 默认周期数
            error_log(sprintf("Unknown opcode encountered: 0x%02X at PC=0x%04X", $opcode, $this->pc->getValue()));
        } catch (Exception $e) {
            // 执行过程中出错
            $this->currentCycles = 2; // 默认周期数
            error_log(sprintf("Error executing opcode 0x%02X at PC=0x%04X: %s", $opcode, $this->pc->getValue(), $e->getMessage()));
        }
    }

    /**
     * 触发IRQ中断
     *
     * @return void
     */
    public function irq(): void
    {
        $this->irqPending = true;
    }

    /**
     * 触发NMI中断
     *
     * @return void
     */
    public function nmi(): void
    {
        $this->nmiPending = true;
    }

    /**
     * 处理IRQ中断
     *
     * @return void
     */
    private function handleIRQ(): void
    {
        // IRQ处理逻辑将在后续实现
        $this->irqPending = false;
        $this->currentCycles += 7; // IRQ处理需要7个周期
    }

    /**
     * 处理NMI中断
     *
     * @return void
     */
    private function handleNMI(): void
    {
        // NMI处理逻辑将在后续实现
        $this->nmiPending = false;
        $this->currentCycles += 7; // NMI处理需要7个周期
    }

    /**
     * 将值推入堆栈
     *
     * @param int $value 要推入的值
     * @return void
     */
    public function push(int $value): void
    {
        $value = $value & 0xFF; // 确保值在0-255范围内
        $stackAddress = self::STACK_PAGE | $this->sp->getValue();
        $this->bus->write($stackAddress, $value);
        $this->sp->decrement(); // 堆栈向下增长
    }

    /**
     * 从堆栈拉出值
     *
     * @return int 拉出的值
     */
    public function pull(): int
    {
        $this->sp->increment(); // 堆栈向下增长，所以先增加SP
        $stackAddress = self::STACK_PAGE | $this->sp->getValue();
        return $this->bus->read($stackAddress);
    }

    /**
     * 将16位值推入堆栈（小端序）
     *
     * @param int $value 要推入的16位值
     * @return void
     */
    public function pushWord(int $value): void
    {
        $high = ($value >> 8) & 0xFF;
        $low = $value & 0xFF;

        // 6502堆栈是大端序存储的
        $this->push($high);
        $this->push($low);
    }

    /**
     * 从堆栈拉出16位值（小端序）
     *
     * @return int 拉出的16位值
     */
    public function pullWord(): int
    {
        // 6502堆栈是大端序存储的
        $low = $this->pull();
        $high = $this->pull();

        return ($high << 8) | $low;
    }

    /**
     * 获取指定的寄存器
     *
     * @param string $name 寄存器名称 (A, X, Y, PC, SP, P)
     * @return Register|StatusRegister 寄存器实例
     * @throws \InvalidArgumentException 如果寄存器名称无效
     */
    public function getRegister(string $name): Register
    {
        return match (strtoupper($name)) {
            'A' => $this->a,
            'X' => $this->x,
            'Y' => $this->y,
            'PC' => $this->pc,
            'SP' => $this->sp,
            'P' => $this->status,
            default => throw new \InvalidArgumentException("未知的寄存器名称: {$name}")
        };
    }

    /**
     * 设置寄存器值
     *
     * @param string $name 寄存器名称
     * @param int $value 要设置的值
     * @return void
     * @throws \InvalidArgumentException 如果寄存器名称无效
     */
    public function setRegister(string $name, int $value): void
    {
        $register = $this->getRegister($name);
        $register->setValue($value);
    }

    /**
     * 获取当前周期计数
     *
     * @return int 当前指令的周期数
     */
    public function getCurrentCycles(): int
    {
        return $this->currentCycles;
    }

    /**
     * 获取总周期计数
     *
     * @return int 总周期数
     */
    public function getTotalCycles(): int
    {
        return $this->totalCycles;
    }

    /**
     * 获取已执行指令数
     *
     * @return int 已执行的指令数
     */
    public function getInstructionsExecuted(): int
    {
        return $this->instructionsExecuted;
    }

    /**
     * 设置当前周期数
     *
     * @param int $cycles 周期数
     * @return void
     */
    protected function setCurrentCycles(int $cycles): void
    {
        $this->currentCycles = $cycles;
    }

    /**
     * 增加当前周期数
     *
     * @param int $cycles 要增加的周期数
     * @return void
     */
    protected function addCycles(int $cycles): void
    {
        $this->currentCycles += $cycles;
    }

    /**
     * 获取总线实例
     *
     * @return Bus 总线实例
     */
    public function getBus(): Bus
    {
        return $this->bus;
    }

    /**
     * 处理十进制模式加法
     *
     * @param int $a 累加器值
     * @param int $b 操作数
     * @param bool $carry 进位标志
     * @return array{result: int, carry: bool} 结果和进位标志
     */
    public function handleDecimalMode(int $a, int $b, bool $carry): array
    {
        // 将十进制调整前的值保存起来，用于V标志计算
        $a &= 0xFF;
        $b &= 0xFF;
        $carryIn = $carry ? 1 : 0;

        // 先计算各个位
        $lowNibbleA = $a & 0x0F;
        $lowNibbleB = $b & 0x0F;
        $lowNibbleSum = $lowNibbleA + $lowNibbleB + $carryIn;

        // 低位调整
        $carryLow = false;
        if ($lowNibbleSum > 9) {
            $lowNibbleSum += 6;
            $carryLow = true;
        }

        // 高位计算
        $highNibbleA = ($a >> 4) & 0x0F;
        $highNibbleB = ($b >> 4) & 0x0F;
        $highNibbleSum = $highNibbleA + $highNibbleB + ($carryLow ? 1 : 0);

        // 高位调整
        $carryHigh = false;
        if ($highNibbleSum > 9) {
            $highNibbleSum += 6;
            $carryHigh = true;
        }

        // 组合结果
        $result = (($highNibbleSum & 0x0F) << 4) | ($lowNibbleSum & 0x0F);

        return [
            'result' => $result & 0xFF,
            'carry' => $carryHigh
        ];
    }

    /**
     * 处理十进制模式减法
     *
     * @param int $a 累加器值
     * @param int $b 操作数
     * @param bool $carry 进位标志（实际是借位标志的取反）
     * @return array{result: int, carry: bool} 结果和进位标志
     */
    public function handleDecimalModeSbc(int $a, int $b, bool $carry): array
    {
        $a &= 0xFF;
        $b &= 0xFF;
        $borrowIn = !$carry;

        // 先计算各个位
        $lowNibbleA = $a & 0x0F;
        $lowNibbleB = $b & 0x0F;
        $lowNibbleDiff = $lowNibbleA - $lowNibbleB - ($borrowIn ? 1 : 0);

        // 低位调整
        $borrowLow = false;
        if ($lowNibbleDiff < 0) {
            $lowNibbleDiff -= 6;
            $borrowLow = true;
        }

        // 高位计算
        $highNibbleA = ($a >> 4) & 0x0F;
        $highNibbleB = ($b >> 4) & 0x0F;
        $highNibbleDiff = $highNibbleA - $highNibbleB - ($borrowLow ? 1 : 0);

        // 高位调整
        $borrowHigh = false;
        if ($highNibbleDiff < 0) {
            $highNibbleDiff -= 6;
            $borrowHigh = true;
        }

        // 组合结果
        $result = (($highNibbleDiff & 0x0F) << 4) | ($lowNibbleDiff & 0x0F);

        return [
            'result' => $result & 0xFF,
            'carry' => !$borrowHigh
        ];
    }

    /**
     * 返回CPU状态的字符串表示
     *
     * @return string CPU状态
     */
    public function __toString(): string
    {
        return sprintf(
            "A=%02X X=%02X Y=%02X PC=%04X SP=%02X P=%02X [%s] CYC=%d",
            $this->a->getValue(),
            $this->x->getValue(),
            $this->y->getValue(),
            $this->pc->getValue(),
            $this->sp->getValue(),
            $this->status->getValue(),
            $this->status->getFormattedStatus(),
            $this->totalCycles
        );
    }
}
