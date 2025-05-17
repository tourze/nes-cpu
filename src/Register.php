<?php

declare(strict_types=1);

namespace Tourze\NES\CPU;

/**
 * CPU寄存器基类
 *
 * 表示CPU的通用寄存器，支持8位和16位寄存器
 */
class Register
{
    /**
     * 寄存器值
     */
    protected int $value;

    /**
     * 寄存器位数
     */
    protected int $bitCount;

    /**
     * 寄存器名称
     */
    protected string $name;

    /**
     * 寄存器最小值
     */
    protected int $minValue;

    /**
     * 寄存器最大值
     */
    protected int $maxValue;

    /**
     * 寄存器初始值
     */
    protected int $initialValue;

    /**
     * 构造函数
     *
     * @param string $name 寄存器名称
     * @param int $bitCount 寄存器位数（8或16）
     * @param int $initialValue 初始值
     */
    public function __construct(string $name, int $bitCount = 8, int $initialValue = 0)
    {
        $this->name = $name;
        $this->bitCount = $bitCount;
        $this->initialValue = $initialValue;

        // 设置最小值和最大值
        $this->minValue = 0;
        $this->maxValue = (1 << $bitCount) - 1;

        // 验证初始值
        if ($initialValue < $this->minValue || $initialValue > $this->maxValue) {
            throw new \InvalidArgumentException(
                "初始值 {$initialValue} 超出寄存器 {$name} 的有效范围 ({$this->minValue}-{$this->maxValue})"
            );
        }

        $this->value = $initialValue;
    }

    /**
     * 获取寄存器值
     *
     * @return int 寄存器当前值
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * 设置寄存器值
     *
     * @param int $value 要设置的值
     * @return int 实际设置的值（已限制在有效范围内）
     */
    public function setValue(int $value): int
    {
        // 确保值在有效范围内
        $value = max($this->minValue, min($value, $this->maxValue));
        $this->value = $value;
        return $value;
    }

    /**
     * 增加寄存器值
     *
     * @param int $amount 增加的数量
     * @return int 增加后的值
     */
    public function increment(int $amount = 1): int
    {
        return $this->setValue($this->value + $amount);
    }

    /**
     * 减少寄存器值
     *
     * @param int $amount 减少的数量
     * @return int 减少后的值
     */
    public function decrement(int $amount = 1): int
    {
        return $this->setValue($this->value - $amount);
    }

    /**
     * 获取寄存器位数
     *
     * @return int 寄存器位数
     */
    public function getBitCount(): int
    {
        return $this->bitCount;
    }

    /**
     * 获取寄存器名称
     *
     * @return string 寄存器名称
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 重置寄存器到初始值
     *
     * @return void
     */
    public function reset(): void
    {
        $this->value = $this->initialValue;
    }

    /**
     * 设置寄存器的特定位
     *
     * @param int $bit 要设置的位（0-7对于8位寄存器，0-15对于16位寄存器）
     * @param bool $value 位的值（true为1，false为0）
     * @return void
     */
    public function setBit(int $bit, bool $value): void
    {
        if ($bit < 0 || $bit >= $this->bitCount) {
            throw new \InvalidArgumentException(
                "位索引 {$bit} 超出寄存器 {$this->name} 的有效范围 (0-" . ($this->bitCount - 1) . ")"
            );
        }

        if ($value) {
            // 设置位
            $this->value |= (1 << $bit);
        } else {
            // 清除位
            $this->value &= ~(1 << $bit);
        }
    }

    /**
     * 获取寄存器的特定位
     *
     * @param int $bit 要获取的位
     * @return bool 位的值（true为1，false为0）
     */
    public function getBit(int $bit): bool
    {
        if ($bit < 0 || $bit >= $this->bitCount) {
            throw new \InvalidArgumentException(
                "位索引 {$bit} 超出寄存器 {$this->name} 的有效范围 (0-" . ($this->bitCount - 1) . ")"
            );
        }

        return ($this->value & (1 << $bit)) !== 0;
    }

    /**
     * 将寄存器转为字符串表示
     *
     * @return string 字符串表示
     */
    public function __toString(): string
    {
        $format = $this->bitCount <= 8 ? '%02X' : '%04X';
        return $this->name . '=$' . sprintf($format, $this->value);
    }
}
