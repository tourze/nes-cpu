<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Instructions;

use Tourze\MOS6502\AddressingMode;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\StatusRegister;

/**
 * BCS - 有进位时分支 (Branch if Carry Set)
 * 
 * 当进位标志(C)置位时，程序跳转到指定地址。
 */
class BCS extends BranchBase
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
        int $opcode = 0xB0,
        string $mnemonic = 'BCS',
        int $cycles = 2
    ) {
        parent::__construct(
            $opcode,
            $mnemonic,
            $cycles,
            $addressingMode,
            '有进位时分支'
        );
    }

    /**
     * 检查是否应该分支
     * 
     * 当进位标志(C)为1时分支
     * 
     * @param CPU $cpu CPU实例
     * @return bool 是否应该分支
     */
    protected function shouldBranch(CPU $cpu): bool
    {
        // 获取状态寄存器
        $status = $cpu->getRegister('P');
        
        // 检查进位标志是否为1
        return $status->getFlag(StatusRegister::FLAG_CARRY);
    }
}
