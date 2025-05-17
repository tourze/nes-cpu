<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Tests\Register;

use PHPUnit\Framework\TestCase;
use Tourze\NES\CPU\StatusRegister;

/**
 * StatusRegister类的单元测试
 */
class StatusRegisterTest extends TestCase
{
    /**
     * 测试状态寄存器初始化
     */
    public function testInitialization(): void
    {
        $status = new StatusRegister();

        // 默认情况下，只有UNUSED标志应为1
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_UNUSED));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_NEGATIVE));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_OVERFLOW));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_BREAK));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_DECIMAL));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_INTERRUPT));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_ZERO));
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_CARRY));

        // 验证初始值
        $this->assertEquals(StatusRegister::FLAG_UNUSED, $status->getValue());
    }

    /**
     * 测试标志位设置和获取
     */
    public function testFlagSetAndGet(): void
    {
        $status = new StatusRegister();

        // 设置各个标志位
        $status->setFlag(StatusRegister::FLAG_NEGATIVE, true);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_NEGATIVE));

        $status->setFlag(StatusRegister::FLAG_ZERO, true);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_ZERO));

        // 验证多个标志位同时设置
        $expectedValue = StatusRegister::FLAG_NEGATIVE | StatusRegister::FLAG_ZERO | StatusRegister::FLAG_UNUSED;
        $this->assertEquals($expectedValue, $status->getValue());

        // 清除标志位
        $status->setFlag(StatusRegister::FLAG_NEGATIVE, false);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_NEGATIVE));

        // UNUSED标志应始终为1
        $status->setFlag(StatusRegister::FLAG_UNUSED, false);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_UNUSED));
    }

    /**
     * 测试负数标志更新
     */
    public function testUpdateNegativeFlag(): void
    {
        $status = new StatusRegister();

        // 正数不应设置负数标志
        $status->updateNegativeFlag(0x00);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_NEGATIVE));

        $status->updateNegativeFlag(0x7F);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_NEGATIVE));

        // 负数（最高位为1）应设置负数标志
        $status->updateNegativeFlag(0x80);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_NEGATIVE));

        $status->updateNegativeFlag(0xFF);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_NEGATIVE));
    }

    /**
     * 测试零标志更新
     */
    public function testUpdateZeroFlag(): void
    {
        $status = new StatusRegister();

        // 零值应设置零标志
        $status->updateZeroFlag(0x00);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_ZERO));

        // 非零值不应设置零标志
        $status->updateZeroFlag(0x01);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_ZERO));

        $status->updateZeroFlag(0xFF);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_ZERO));
    }

    /**
     * 测试溢出标志更新
     */
    public function testUpdateOverflowFlag(): void
    {
        $status = new StatusRegister();

        // 两个正数相加得到正数，不应设置溢出标志
        $status->updateOverflowFlag(0x01, 0x02, 0x03);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_OVERFLOW));

        // 修正：两个相同符号的数相加，如果结果与原操作数符号相反，才设置溢出标志
        // 这里0x81和0x82对应负数，相加后的0x03是正数，所以应该设置溢出标志
        $status->updateOverflowFlag(0x81, 0x82, 0x03);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_OVERFLOW));

        // 两个正数相加得到负数，应设置溢出标志
        $status->updateOverflowFlag(0x7F, 0x01, 0x80); // 127 + 1 = 128 (正溢出)
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_OVERFLOW));

        // 两个负数相加得到正数，应设置溢出标志
        $status->updateOverflowFlag(0x80, 0x80, 0x00); // -128 + -128 = -256 (被截断为0，负溢出)
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_OVERFLOW));
    }

    /**
     * 测试进位标志更新
     */
    public function testUpdateCarryFlag(): void
    {
        $status = new StatusRegister();

        // 结果在0-255范围内，不应设置进位标志
        $status->updateCarryFlag(0xFF);
        $this->assertFalse($status->getFlag(StatusRegister::FLAG_CARRY));

        // 结果超过255，应设置进位标志
        $status->updateCarryFlag(0x100);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_CARRY));

        $status->updateCarryFlag(0x1FF);
        $this->assertTrue($status->getFlag(StatusRegister::FLAG_CARRY));
    }

    /**
     * 测试重置操作
     */
    public function testReset(): void
    {
        $status = new StatusRegister();

        // 设置所有标志
        $status->setValue(0xFF);

        // 重置
        $status->reset();

        // 验证只有UNUSED标志为1
        $this->assertEquals(StatusRegister::FLAG_UNUSED, $status->getValue());
    }

    /**
     * 测试获取标志名称
     */
    public function testGetFlagNames(): void
    {
        $status = new StatusRegister();
        $names = $status->getFlagNames();

        $this->assertEquals('N', $names[StatusRegister::FLAG_NEGATIVE]);
        $this->assertEquals('V', $names[StatusRegister::FLAG_OVERFLOW]);
        $this->assertEquals('-', $names[StatusRegister::FLAG_UNUSED]);
        $this->assertEquals('B', $names[StatusRegister::FLAG_BREAK]);
        $this->assertEquals('D', $names[StatusRegister::FLAG_DECIMAL]);
        $this->assertEquals('I', $names[StatusRegister::FLAG_INTERRUPT]);
        $this->assertEquals('Z', $names[StatusRegister::FLAG_ZERO]);
        $this->assertEquals('C', $names[StatusRegister::FLAG_CARRY]);
    }

    /**
     * 测试格式化状态字符串
     */
    public function testGetFormattedStatus(): void
    {
        $status = new StatusRegister();

        // 初始情况下只有UNUSED为1
        $this->assertEquals('nv-bdizc', $status->getFormattedStatus());

        // 设置所有标志
        $status->setValue(0xFF);
        $this->assertEquals('NV-BDIZC', $status->getFormattedStatus());

        // 设置部分标志
        $status->setValue(StatusRegister::FLAG_NEGATIVE | StatusRegister::FLAG_ZERO | StatusRegister::FLAG_UNUSED);
        $this->assertEquals('Nv-bdiZc', $status->getFormattedStatus());
    }

    /**
     * 测试字符串表示
     */
    public function testToString(): void
    {
        $status = new StatusRegister();
        $status->setValue(StatusRegister::FLAG_NEGATIVE | StatusRegister::FLAG_ZERO | StatusRegister::FLAG_UNUSED);

        $expectedValue = sprintf('%02X', StatusRegister::FLAG_NEGATIVE | StatusRegister::FLAG_ZERO | StatusRegister::FLAG_UNUSED);
        $this->assertEquals("P=$expectedValue [Nv-bdiZc]", (string)$status);
    }
}
