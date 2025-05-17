<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

/**
 * CPU状态寄存器
 * 
 * 管理CPU的状态标志（N, V, B, D, I, Z, C等）
 */
class StatusRegister extends Register
{
    /**
     * 负数标志位 (bit 7)
     */
    public const FLAG_NEGATIVE = 0x80;
    
    /**
     * 溢出标志位 (bit 6)
     */
    public const FLAG_OVERFLOW = 0x40;
    
    /**
     * 未使用标志位，总是1 (bit 5)
     */
    public const FLAG_UNUSED = 0x20;
    
    /**
     * 中断标志位 (bit 4)
     */
    public const FLAG_BREAK = 0x10;
    
    /**
     * 十进制模式标志位 (bit 3)
     */
    public const FLAG_DECIMAL = 0x08;
    
    /**
     * 中断禁用标志位 (bit 2)
     */
    public const FLAG_INTERRUPT = 0x04;
    
    /**
     * 零标志位 (bit 1)
     */
    public const FLAG_ZERO = 0x02;
    
    /**
     * 进位标志位 (bit 0)
     */
    public const FLAG_CARRY = 0x01;
    
    /**
     * 构造函数
     * 
     * @param int $initialValue 初始值（默认：未使用位设为1）
     */
    public function __construct(int $initialValue = self::FLAG_UNUSED)
    {
        parent::__construct('P', 8, $initialValue);
    }
    
    /**
     * 获取特定标志的状态
     * 
     * @param int $flag 标志常量
     * @return bool 标志状态
     */
    public function getFlag(int $flag): bool
    {
        return ($this->value & $flag) !== 0;
    }
    
    /**
     * 设置特定标志的状态
     * 
     * @param int $flag 标志常量
     * @param bool $status 要设置的状态
     * @return void
     */
    public function setFlag(int $flag, bool $status): void
    {
        if ($status) {
            $this->value |= $flag;
        } else {
            $this->value &= ~$flag;
        }
        
        // 确保未使用位始终为1
        $this->value |= self::FLAG_UNUSED;
    }
    
    /**
     * 根据值更新负数标志
     * 
     * @param int $value 用于更新标志的值
     * @return void
     */
    public function updateNegativeFlag(int $value): void
    {
        // 检查最高位（bit 7）是否为1
        $this->setFlag(self::FLAG_NEGATIVE, ($value & 0x80) !== 0);
    }
    
    /**
     * 根据值更新零标志
     * 
     * @param int $value 用于更新标志的值
     * @return void
     */
    public function updateZeroFlag(int $value): void
    {
        // 检查值是否为0
        $this->setFlag(self::FLAG_ZERO, ($value & 0xFF) === 0);
    }
    
    /**
     * 更新溢出标志
     * 
     * 当两个正数相加得到负数或两个负数相加得到正数时，设置溢出标志
     * 
     * @param int $a 操作数1
     * @param int $b 操作数2
     * @param int $result 运算结果
     * @return void
     */
    public function updateOverflowFlag(int $a, int $b, int $result): void
    {
        // 溢出发生在:
        // 1. 两个正数相加得到负数 - 正溢出
        // 2. 两个负数相加得到正数 - 负溢出
        // 检查a和b的符号位是否相同，且结果的符号位与它们不同
        $hasOverflow = ((~($a ^ $b) & ($a ^ $result) & 0x80) !== 0);
        $this->setFlag(self::FLAG_OVERFLOW, $hasOverflow);
    }
    
    /**
     * 更新进位标志
     * 
     * @param int $result 未截断的运算结果
     * @return void
     */
    public function updateCarryFlag(int $result): void
    {
        // 检查是否有进位（结果超过8位）
        $this->setFlag(self::FLAG_CARRY, $result > 0xFF);
    }
    
    /**
     * 获取所有标志的名称映射
     * 
     * @return array<int, string> 标志映射 [常量 => 名称]
     */
    public function getFlagNames(): array
    {
        return [
            self::FLAG_NEGATIVE => 'N',
            self::FLAG_OVERFLOW => 'V',
            self::FLAG_UNUSED => '-',
            self::FLAG_BREAK => 'B',
            self::FLAG_DECIMAL => 'D',
            self::FLAG_INTERRUPT => 'I',
            self::FLAG_ZERO => 'Z',
            self::FLAG_CARRY => 'C',
        ];
    }
    
    /**
     * 获取状态寄存器的格式化表示
     * 
     * @return string 格式化的状态字符串（例如：NV-BDIZC）
     */
    public function getFormattedStatus(): string
    {
        $result = '';
        
        foreach ($this->getFlagNames() as $flag => $name) {
            $result .= $this->getFlag($flag) ? $name : strtolower($name);
        }
        
        return $result;
    }
    
    /**
     * 将状态寄存器转为字符串表示
     * 
     * @return string 字符串表示
     */
    public function __toString(): string
    {
        return 'P=' . sprintf('%02X', $this->value) . ' [' . $this->getFormattedStatus() . ']';
    }
}
