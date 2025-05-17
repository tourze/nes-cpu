<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Bus;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Bus;
use Tourze\MOS6502\Memory;

/**
 * Bus类的单元测试
 */
class BusTest extends TestCase
{
    /**
     * 测试连接组件到总线
     */
    public function testConnectComponent(): void
    {
        $bus = new Bus();
        $memory = new Memory();

        // 连接内存到总线
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);

        $components = $bus->getConnectedComponents();
        $this->assertCount(1, $components);
        $this->assertArrayHasKey('ram', $components);
        $this->assertSame($memory, $components['ram']['component']);
        $this->assertEquals(0x0000, $components['ram']['start']);
        $this->assertEquals(0xFFFF, $components['ram']['end']);
    }

    /**
     * 测试地址映射
     */
    public function testAddressMapping(): void
    {
        $bus = new Bus();
        $memory = new Memory();

        // 连接内存到部分地址范围
        $bus->connect($memory, 'ram', 0x0000, 0x7FFF);

        // 测试地址映射
        $this->assertTrue($bus->isAddressMapped(0x0000));
        $this->assertTrue($bus->isAddressMapped(0x7FFF));
        $this->assertFalse($bus->isAddressMapped(0x8000));
        $this->assertFalse($bus->isAddressMapped(0xFFFF));

        // 测试获取组件ID
        $this->assertEquals('ram', $bus->getComponentIdAtAddress(0x0000));
        $this->assertEquals('ram', $bus->getComponentIdAtAddress(0x1234));
        $this->assertNull($bus->getComponentIdAtAddress(0x8000));
    }

    /**
     * 测试总线读写操作
     */
    public function testReadWrite(): void
    {
        $bus = new Bus();
        $memory = new Memory();

        // 连接内存到总线
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);

        // 测试写入和读取
        $bus->write(0x1000, 0x42);
        $this->assertEquals(0x42, $bus->read(0x1000));

        // 测试16位读写
        $bus->writeWord(0x2000, 0xABCD);
        $this->assertEquals(0xABCD, $bus->readWord(0x2000));

        // 验证内存中的值
        $this->assertEquals(0x42, $memory->read(0x1000));
        $this->assertEquals(0xCD, $memory->read(0x2000)); // 低字节
        $this->assertEquals(0xAB, $memory->read(0x2001)); // 高字节
    }

    /**
     * 测试未映射地址的异常
     */
    public function testUnmappedAddressException(): void
    {
        $bus = new Bus();
        $memory = new Memory();

        // 只连接部分地址范围
        $bus->connect($memory, 'ram', 0x0000, 0x7FFF);

        // 测试读取未映射地址的异常
        $this->expectException(\RuntimeException::class);
        $bus->read(0x8000);
    }

    /**
     * 测试断开组件
     */
    public function testDisconnectComponent(): void
    {
        $bus = new Bus();
        $memory = new Memory();

        // 连接内存到总线
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);

        // 验证连接是否成功
        $this->assertTrue($bus->isAddressMapped(0x1000));

        // 断开组件
        $bus->disconnect('ram');

        // 验证断开是否成功
        $this->assertFalse($bus->isAddressMapped(0x1000));
        $this->assertCount(0, $bus->getConnectedComponents());
    }

    /**
     * 测试地址冲突异常
     */
    public function testAddressConflictException(): void
    {
        $bus = new Bus();
        $memory1 = new Memory();
        $memory2 = new Memory();

        // 连接第一个内存组件
        $bus->connect($memory1, 'ram1', 0x0000, 0x7FFF);

        // 测试完全重叠的地址范围
        $this->expectException(\InvalidArgumentException::class);
        $bus->connect($memory2, 'ram2', 0x0000, 0x7FFF);
    }

    /**
     * 测试部分地址冲突
     */
    public function testPartialAddressConflict(): void
    {
        $bus = new Bus();
        $memory1 = new Memory();
        $memory2 = new Memory();

        // 连接第一个内存组件
        $bus->connect($memory1, 'ram1', 0x0000, 0x7FFF);

        // 测试部分重叠的地址范围
        $this->expectException(\InvalidArgumentException::class);
        $bus->connect($memory2, 'ram2', 0x7000, 0x8FFF);
    }

    /**
     * 测试总线重置
     */
    public function testReset(): void
    {
        $bus = new Bus();
        $memory = new Memory();

        // 连接内存到总线
        $bus->connect($memory, 'ram', 0x0000, 0xFFFF);

        // 写入一些数据
        $bus->write(0x1000, 0x42);
        $this->assertEquals(0x42, $bus->read(0x1000));

        // 重置总线
        $bus->reset();

        // 验证内存被重置
        $this->assertEquals(0, $bus->read(0x1000));
    }
}
