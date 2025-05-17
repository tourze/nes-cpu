<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\InstructionBase;

/**
 * 分支指令基类
 *
 * 处理所有分支指令的共同逻辑，包括相对寻址和周期计算
 */
abstract class BranchBase extends InstructionBase
{
    /**
     * 条件分支指令的共用执行逻辑
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @param bool $condition 分支条件，由子类提供
     * @return int 消耗的周期数
     */
    protected function executeBranch(CPU $cpu, Bus $bus, bool $condition): int
    {
        // 获取当前PC值
        $pc = $cpu->getRegister('PC');
        $currentPC = $pc->getValue();

        // 默认周期数
        $cycles = $this->cycles;

        // 如果条件不满足，只需要增加PC指向下一条指令
        if (!$condition) {
            // 相对寻址模式下，需要跳过偏移量字节
            $pc->increment();
            return $cycles;
        }

        // 条件满足，计算目标地址
        $targetAddress = $this->addressingMode->getOperandAddress($cpu, $bus);

        // PC已经指向了操作数，需要再前进一字节
        $pc->increment();

        // 跳转到目标地址
        $pc->setValue($targetAddress);

        // 分支成功需要额外加1个周期
        $cycles += 1;

        // 如果跨页边界需要再加1个周期
        if ($this->addressingMode->getCrossesPageBoundary()) {
            $cycles += 1;
        }

        return $cycles;
    }

    /**
     * 检查是否应该分支
     *
     * 各子类必须实现此方法以提供具体的分支条件
     *
     * @param CPU $cpu CPU实例
     * @return bool 是否应该分支
     */
    abstract protected function shouldBranch(CPU $cpu): bool;

    /**
     * 执行指令
     *
     * @param CPU $cpu CPU实例
     * @param Bus $bus 总线实例
     * @return int 消耗的周期数
     */
    public function execute(CPU $cpu, Bus $bus): int
    {
        // 调用执行分支的通用方法，传入特定的分支条件
        return $this->executeBranch($cpu, $bus, $this->shouldBranch($cpu));
    }
}
