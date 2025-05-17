<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Instructions;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\Memory;
use Tourze\MOS6502\StatusRegister;

/**
 * 指令测试基类
 *
 * 提供指令测试的公共功能和初始化代码
 */
abstract class InstructionTestCase extends TestCase
{
    protected CPU $cpu;
    protected Bus $bus;
    protected Memory $memory;

    /**
     * 测试初始化
     */
    protected function setUp(): void
    {
        $this->memory = new Memory();
        $this->bus = new Bus();
        $this->bus->connect($this->memory, 'ram', 0x0000, 0xFFFF);
        $this->cpu = new CPU($this->bus);
    }

    /**
     * 设置状态寄存器标志
     *
     * @param int $flag 要设置的标志
     * @param bool $value 标志值
     * @return void
     */
    protected function setFlag(int $flag, bool $value): void
    {
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        $status->setFlag($flag, $value);
    }

    /**
     * 获取状态寄存器标志
     *
     * @param int $flag 要获取的标志
     * @return bool 标志值
     */
    protected function getFlag(int $flag): bool
    {
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);
        return $status->getFlag($flag);
    }

    /**
     * 加载程序到指定地址
     *
     * @param array $program 程序字节数组
     * @param int $address 起始地址
     * @return void
     */
    protected function loadProgram(array $program, int $address): void
    {
        foreach ($program as $offset => $value) {
            $this->memory->write($address + $offset, $value);
        }
    }

    /**
     * 执行一条指令
     *
     * @param int $address 指令地址
     * @return int 消耗的周期数
     */
    protected function executeInstruction(int $address): int
    {
        $this->cpu->getRegister('PC')->setValue($address);
        return $this->cpu->step();
    }
}
