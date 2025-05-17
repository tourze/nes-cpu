<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Instructions;

use Tourze\NES\CPU\AddressingMode;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\StatusRegister;

/**
 * BCC - 无进位时分支 (Branch if Carry Clear)
 * 
 * 当进位标志(C)清除时，程序跳转到指定地址。
 */
class BCC extends BranchBase
{
    /**
     * 构造函数
     *
     * @param AddressingMode $addressingMode 寻址模式，只能是相对寻址
     * @param int $opcode 操作码
     * @param string $mnemonic 助记符
     * @param int $cycles 基础周期数
     */
    public function __construct(
        AddressingMode $addressingMode,
        int $opcode = 0x90,
        string $mnemonic = 'BCC',
        int $cycles = 2
    ) {
        parent::__construct(
            $opcode,
            $mnemonic,
            $cycles,
            $addressingMode,
            '无进位时分支'
        );
    }

    /**
     * 检查是否应该分支
     * 
     * 当进位标志(C)为0时分支
     * 
     * @param CPU $cpu CPU实例
     * @return bool 是否应该分支
     */
    protected function shouldBranch(CPU $cpu): bool
    {
        // 获取状态寄存器
        $status = $cpu->getRegister('P');
        
        // 检查进位标志是否为0
        return !$status->getFlag(StatusRegister::FLAG_CARRY);
    }
}
