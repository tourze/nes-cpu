<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\AddressingModes;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\CPU;
use Tourze\MOS6502\Memory;

/**
 * 寻址模式单元测试
 */
class AddressingModesTest extends TestCase
{
    private CPU $cpu;
    private Bus $bus;
    private Memory $memory;

    protected function setUp(): void
    {
        $this->memory = new Memory();
        $this->bus = new Bus();
        $this->bus->connect($this->memory, 'ram', 0x0000, 0xFFFF);
        $this->cpu = new CPU($this->bus);
    }

    public function testImpliedAddressing(): void
    {
        $addressing = AddressingModeFactory::implied();

        $this->assertEquals(1, $addressing->getBytes());
        $this->assertEquals(0, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("implied", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testAccumulatorAddressing(): void
    {
        $addressing = AddressingModeFactory::accumulator();

        // 设置A寄存器的值
        $this->cpu->setRegister('A', 0x42);

        $this->assertEquals(1, $addressing->getBytes());
        $this->assertEquals(-1, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("accumulator", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testImmediateAddressing(): void
    {
        $addressing = AddressingModeFactory::immediate();

        // 设置PC寄存器指向测试数据
        $this->cpu->setRegister('PC', 0x0200);
        $this->memory->write(0x0200, 0x42); // 立即数

        $this->assertEquals(2, $addressing->getBytes());
        $this->assertEquals(0x0200, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("immediate", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testZeroPageAddressing(): void
    {
        $addressing = AddressingModeFactory::zeroPage();

        // 设置PC寄存器指向测试数据
        $this->cpu->setRegister('PC', 0x0200);
        $this->memory->write(0x0200, 0x42); // 零页地址
        $this->memory->write(0x0042, 0x84); // 零页地址的内容

        $this->assertEquals(2, $addressing->getBytes());
        $this->assertEquals(0x42, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x84, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("zeropage", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testZeroPageXAddressing(): void
    {
        $addressing = AddressingModeFactory::zeroPageX();

        // 设置PC寄存器和X寄存器
        $this->cpu->setRegister('PC', 0x0200);
        $this->cpu->setRegister('X', 0x05);
        $this->memory->write(0x0200, 0x40); // 零页基址
        $this->memory->write(0x0045, 0x84); // 零页基址+X的内容

        $this->assertEquals(2, $addressing->getBytes());
        $this->assertEquals(0x45, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x84, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("zeropage,X", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testZeroPageYAddressing(): void
    {
        $addressing = AddressingModeFactory::zeroPageY();

        // 设置PC寄存器和Y寄存器
        $this->cpu->setRegister('PC', 0x0200);
        $this->cpu->setRegister('Y', 0x05);
        $this->memory->write(0x0200, 0x40); // 零页基址
        $this->memory->write(0x0045, 0x84); // 零页基址+Y的内容

        $this->assertEquals(2, $addressing->getBytes());
        $this->assertEquals(0x45, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x84, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("zeropage,Y", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testAbsoluteAddressing(): void
    {
        $addressing = AddressingModeFactory::absolute();

        // 设置PC寄存器指向测试数据
        $this->cpu->setRegister('PC', 0x0200);
        $this->memory->write(0x0200, 0x34); // 低字节
        $this->memory->write(0x0201, 0x12); // 高字节
        $this->memory->write(0x1234, 0x42); // 绝对地址的内容

        $this->assertEquals(3, $addressing->getBytes());
        $this->assertEquals(0x1234, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("absolute", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testAbsoluteXAddressing(): void
    {
        $addressing = AddressingModeFactory::absoluteX();

        // 设置PC寄存器和X寄存器
        $this->cpu->setRegister('PC', 0x0200);
        $this->cpu->setRegister('X', 0x05);
        $this->memory->write(0x0200, 0x34); // 低字节
        $this->memory->write(0x0201, 0x12); // 高字节
        $this->memory->write(0x1239, 0x42); // 绝对地址+X的内容

        $this->assertEquals(3, $addressing->getBytes());
        $this->assertEquals(0x1239, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("absolute,X", $addressing->getName());
        // 没有跨页
        $this->assertFalse($addressing->getCrossesPageBoundary());

        // 测试跨页情况
        $this->cpu->setRegister('PC', 0x0300);
        $this->cpu->setRegister('X', 0x05);
        $this->memory->write(0x0300, 0xFE); // 低字节
        $this->memory->write(0x0301, 0x12); // 高字节
        $this->memory->write(0x1303, 0x43); // 绝对地址+X的内容（跨页）

        $this->assertEquals(0x1303, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x43, $addressing->getOperandValue($this->cpu, $this->bus));
        // 跨页
        $this->assertTrue($addressing->getCrossesPageBoundary());
    }

    public function testAbsoluteYAddressing(): void
    {
        $addressing = AddressingModeFactory::absoluteY();

        // 设置PC寄存器和Y寄存器
        $this->cpu->setRegister('PC', 0x0200);
        $this->cpu->setRegister('Y', 0x05);
        $this->memory->write(0x0200, 0x34); // 低字节
        $this->memory->write(0x0201, 0x12); // 高字节
        $this->memory->write(0x1239, 0x42); // 绝对地址+Y的内容

        $this->assertEquals(3, $addressing->getBytes());
        $this->assertEquals(0x1239, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("absolute,Y", $addressing->getName());
        // (0x1234 + 0x05) 不跨页
        $this->assertFalse($addressing->getCrossesPageBoundary());

        // 测试跨页情况
        $this->cpu->setRegister('PC', 0x0300);
        $this->cpu->setRegister('Y', 0x05);
        $this->memory->write(0x0300, 0xFE); // 低字节
        $this->memory->write(0x0301, 0x12); // 高字节
        $this->memory->write(0x1303, 0x43); // 绝对地址+Y的内容（跨页）

        $this->assertEquals(0x1303, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x43, $addressing->getOperandValue($this->cpu, $this->bus));
        // (0x12FE + 0x05) 跨页
        $this->assertTrue($addressing->getCrossesPageBoundary());
    }

    public function testIndirectAddressing(): void
    {
        $addressing = AddressingModeFactory::indirect();

        // 设置PC寄存器和间接地址内容
        $this->cpu->setRegister('PC', 0x0200);
        $this->memory->write(0x0200, 0x34); // 低字节
        $this->memory->write(0x0201, 0x12); // 高字节
        $this->memory->write(0x1234, 0x78); // 间接地址的低字节
        $this->memory->write(0x1235, 0x56); // 间接地址的高字节

        $this->assertEquals(3, $addressing->getBytes());
        $this->assertEquals(0x5678, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals("indirect", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());

        // 测试间接地址在页面边界上的bug情况
        $this->cpu->setRegister('PC', 0x0300);
        $this->memory->write(0x0300, 0xFF); // 低字节
        $this->memory->write(0x0301, 0x12); // 高字节
        $this->memory->write(0x12FF, 0x78); // 间接地址的低字节
        $this->memory->write(0x1200, 0x56); // 应该从下一页读取，但由于bug会从同一页的开始处读取

        // 由于JMP ($12FF)的bug，高字节会从$1200而不是$1300读取
        $this->assertEquals(0x5678, $addressing->getOperandAddress($this->cpu, $this->bus));
    }

    public function testIndirectXAddressing(): void
    {
        $addressing = AddressingModeFactory::indirectX();

        // 设置PC寄存器和X寄存器
        $this->cpu->setRegister('PC', 0x0200);
        $this->cpu->setRegister('X', 0x05);
        $this->memory->write(0x0200, 0x40); // 零页基址
        $this->memory->write(0x0045, 0x34); // (零页基址+X)的低字节内容
        $this->memory->write(0x0046, 0x12); // (零页基址+X)的高字节内容
        $this->memory->write(0x1234, 0x42); // 最终地址的内容

        $this->assertEquals(2, $addressing->getBytes());
        $this->assertEquals(0x1234, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("(indirect,X)", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());
    }

    public function testIndirectYAddressing(): void
    {
        $addressing = AddressingModeFactory::indirectY();

        // 设置PC寄存器和Y寄存器
        $this->cpu->setRegister('PC', 0x0200);
        $this->cpu->setRegister('Y', 0x05);
        $this->memory->write(0x0200, 0x40); // 零页地址
        $this->memory->write(0x0040, 0x34); // 零页地址的低字节内容
        $this->memory->write(0x0041, 0x12); // 零页地址的高字节内容
        $this->memory->write(0x1239, 0x42); // (间接地址+Y)的内容

        $this->assertEquals(2, $addressing->getBytes());
        $this->assertEquals(0x1239, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x42, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("(indirect),Y", $addressing->getName());
        // (0x1234 + 0x05) 不跨页
        $this->assertFalse($addressing->getCrossesPageBoundary());

        // 测试跨页情况
        $this->cpu->setRegister('PC', 0x0300);
        $this->cpu->setRegister('Y', 0x05);
        $this->memory->write(0x0300, 0x50); // 零页地址
        $this->memory->write(0x0050, 0xFE); // 零页地址的低字节内容
        $this->memory->write(0x0051, 0x12); // 零页地址的高字节内容
        $this->memory->write(0x1303, 0x43); // (间接地址+Y)的内容（跨页）

        $this->assertEquals(0x1303, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x43, $addressing->getOperandValue($this->cpu, $this->bus));
        // (0x12FE + 0x05) 跨页
        $this->assertTrue($addressing->getCrossesPageBoundary());
    }

    public function testRelativeAddressing(): void
    {
        $addressing = AddressingModeFactory::relative();

        // 设置PC寄存器和相对偏移
        $this->cpu->setRegister('PC', 0x0200);
        $this->memory->write(0x0200, 0x10); // 正偏移 +16

        $this->assertEquals(2, $addressing->getBytes());
        // 目标地址 = 当前PC + 偏移 + 2（指令字节数）= 0x0200 + 0x10 + 2 = 0x0212
        $this->assertEquals(0x0212, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0x10, $addressing->getOperandValue($this->cpu, $this->bus));
        $this->assertEquals("relative", $addressing->getName());
        $this->assertFalse($addressing->getCrossesPageBoundary());

        // 测试负偏移
        $this->cpu->setRegister('PC', 0x0210);
        $this->memory->write(0x0210, 0xF0); // 负偏移 -16 (0xF0 = -16 as signed byte)

        // 目标地址 = 当前PC + 偏移 + 2 = 0x0210 - 16 + 2 = 0x0202
        $this->assertEquals(0x0202, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertEquals(0xF0, $addressing->getOperandValue($this->cpu, $this->bus));

        // 测试跨页情况 - 使用跨页的例子
        $this->cpu->setRegister('PC', 0x02F0);
        $this->memory->write(0x02F0, 0x20); // 正偏移 +32，导致跨页

        // 目标地址 = 当前PC + 偏移 + 2 = 0x02F0 + 32 + 2 = 0x0312
        $this->assertEquals(0x0312, $addressing->getOperandAddress($this->cpu, $this->bus));
        $this->assertTrue($addressing->getCrossesPageBoundary());
    }
}
