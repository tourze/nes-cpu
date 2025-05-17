<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Register;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Register;

/**
 * Register类的单元测试
 */
class RegisterTest extends TestCase
{
    /**
     * 测试寄存器初始化
     */
    public function testInitialization(): void
    {
        // 默认8位寄存器，初始值为0
        $reg = new Register('A');
        $this->assertEquals(0, $reg->getValue());
        $this->assertEquals(8, $reg->getBitCount());
        $this->assertEquals('A', $reg->getName());

        // 16位寄存器，初始值为0
        $reg16 = new Register('PC', 16);
        $this->assertEquals(0, $reg16->getValue());
        $this->assertEquals(16, $reg16->getBitCount());

        // 8位寄存器，自定义初始值
        $reg8custom = new Register('X', 8, 0x42);
        $this->assertEquals(0x42, $reg8custom->getValue());
    }

    /**
     * 测试设置值和范围限制
     */
    public function testSetValueAndLimits(): void
    {
        // 8位寄存器
        $reg8 = new Register('A');

        // 设置有效值
        $reg8->setValue(0xFF);
        $this->assertEquals(0xFF, $reg8->getValue());

        // 设置超出范围的值（应被限制）
        $reg8->setValue(0x100);
        $this->assertEquals(0xFF, $reg8->getValue()); // 应被限制为0xFF

        $reg8->setValue(-1);
        $this->assertEquals(0, $reg8->getValue()); // 应被限制为0

        // 16位寄存器
        $reg16 = new Register('PC', 16);

        // 设置有效值
        $reg16->setValue(0xFFFF);
        $this->assertEquals(0xFFFF, $reg16->getValue());

        // 设置超出范围的值（应被限制）
        $reg16->setValue(0x10000);
        $this->assertEquals(0xFFFF, $reg16->getValue()); // 应被限制为0xFFFF
    }

    /**
     * 测试递增和递减操作
     */
    public function testIncrementAndDecrement(): void
    {
        $reg = new Register('A', 8, 0x10);

        // 测试递增
        $reg->increment();
        $this->assertEquals(0x11, $reg->getValue());

        $reg->increment(5);
        $this->assertEquals(0x16, $reg->getValue());

        // 测试递减
        $reg->decrement();
        $this->assertEquals(0x15, $reg->getValue());

        $reg->decrement(5);
        $this->assertEquals(0x10, $reg->getValue());

        // 测试边界递增
        $reg->setValue(0xFE);
        $reg->increment(2);
        $this->assertEquals(0xFF, $reg->getValue()); // 应被限制为0xFF

        // 测试边界递减
        $reg->setValue(0x01);
        $reg->decrement(2);
        $this->assertEquals(0x00, $reg->getValue()); // 应被限制为0
    }

    /**
     * 测试位操作
     */
    public function testBitOperations(): void
    {
        $reg = new Register('A');

        // 初始应为0
        for ($bit = 0; $bit < 8; $bit++) {
            $this->assertFalse($reg->getBit($bit), "位 {$bit} 应为0");
        }

        // 设置各个位
        for ($bit = 0; $bit < 8; $bit++) {
            $reg->setBit($bit, true);
            $this->assertTrue($reg->getBit($bit), "位 {$bit} 应为1");
            $expected = 1 << $bit;
            $this->assertEquals($expected, $reg->getValue(), "设置位 {$bit} 后值应为 " . sprintf('%02X', $expected));

            // 重置该位
            $reg->setBit($bit, false);
            $this->assertFalse($reg->getBit($bit), "位 {$bit} 应为0");
            $this->assertEquals(0, $reg->getValue());
        }

        // 测试同时设置多个位
        $reg->setBit(0, true); // 0x01
        $reg->setBit(2, true); // + 0x04 = 0x05
        $reg->setBit(4, true); // + 0x10 = 0x15
        $this->assertEquals(0x15, $reg->getValue());

        // 验证各位状态
        $this->assertTrue($reg->getBit(0));
        $this->assertFalse($reg->getBit(1));
        $this->assertTrue($reg->getBit(2));
        $this->assertFalse($reg->getBit(3));
        $this->assertTrue($reg->getBit(4));
        $this->assertFalse($reg->getBit(5));
        $this->assertFalse($reg->getBit(6));
        $this->assertFalse($reg->getBit(7));
    }

    /**
     * 测试重置操作
     */
    public function testReset(): void
    {
        // 自定义初始值
        $reg = new Register('A', 8, 0x42);

        // 修改值
        $reg->setValue(0xFF);
        $this->assertEquals(0xFF, $reg->getValue());

        // 重置
        $reg->reset();
        $this->assertEquals(0x42, $reg->getValue(), "重置后应恢复到初始值");
    }

    /**
     * 测试无效位索引的异常
     */
    public function testInvalidBitIndex(): void
    {
        $reg = new Register('A');

        $this->expectException(\InvalidArgumentException::class);
        $reg->getBit(8); // 8位寄存器的有效位索引为0-7
    }

    /**
     * 测试无效初始值的异常
     */
    public function testInvalidInitialValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Register('A', 8, 0x100); // 8位寄存器的有效值范围为0-255
    }

    /**
     * 测试字符串表示
     */
    public function testToString(): void
    {
        $reg8 = new Register('A', 8, 0x42);
        $this->assertEquals('A=$42', (string)$reg8);

        $reg16 = new Register('PC', 16, 0x1234);
        $this->assertEquals('PC=$1234', (string)$reg16);
    }
}
