<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Memory;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Memory;

/**
 * Memory类的单元测试
 */
class MemoryTest extends TestCase
{
    /**
     * 测试内存初始化
     */
    public function testInitialization(): void
    {
        $memory = new Memory();

        // 测试内存是否被初始化为0
        for ($i = 0; $i < 10; $i++) {
            $address = rand(0, Memory::MEMORY_SIZE - 1);
            $this->assertEquals(0, $memory->read($address), "地址 {$address} 的初始值应为0");
        }

        // 测试初始访问计数
        $this->assertEquals(10, $memory->getAccessCount(), "初始访问计数应为10（上面的10次读取）");
    }

    /**
     * 测试基本的读写操作
     */
    public function testReadWrite(): void
    {
        $memory = new Memory();

        // 测试写入和读取
        $memory->write(0x1000, 0x42);
        $this->assertEquals(0x42, $memory->read(0x1000));

        // 测试边界值
        $memory->write(0x0000, 0xFF);
        $memory->write(0xFFFF, 0x55);
        $this->assertEquals(0xFF, $memory->read(0x0000));
        $this->assertEquals(0x55, $memory->read(0xFFFF));

        // 测试值的范围限制
        $memory->write(0x2000, 0x1FF); // 超出8位范围
        $this->assertEquals(0xFF, $memory->read(0x2000)); // 应截断为0xFF

        // 测试地址溢出处理
        $memory->write(0x10000, 0xAA); // 超出16位地址
        $this->assertEquals(0xAA, $memory->read(0x0000)); // 应环绕到0x0000
    }

    /**
     * 测试16位读写操作
     */
    public function testReadWriteWord(): void
    {
        $memory = new Memory();

        // 测试写入和读取16位字
        $memory->writeWord(0x1000, 0x1234);
        $this->assertEquals(0x1234, $memory->readWord(0x1000));

        // 验证小端序存储
        $this->assertEquals(0x34, $memory->read(0x1000)); // 低字节
        $this->assertEquals(0x12, $memory->read(0x1001)); // 高字节

        // 测试地址环绕
        $memory->writeWord(0xFFFF, 0xABCD);
        $this->assertEquals(0xCD, $memory->read(0xFFFF));
        $this->assertEquals(0xAB, $memory->read(0x0000)); // 环绕到0x0000

        // readWord同样应该处理环绕
        $this->assertEquals(0xABCD, $memory->readWord(0xFFFF));
    }

    /**
     * 测试加载和转储数据
     */
    public function testLoadAndDump(): void
    {
        $memory = new Memory();

        // 测试数据加载
        $data = [0x11, 0x22, 0x33, 0x44, 0x55];
        $memory->load(0x1000, $data);

        // 验证加载的数据
        for ($i = 0; $i < count($data); $i++) {
            $this->assertEquals($data[$i], $memory->read(0x1000 + $i));
        }

        // 测试数据转储
        $dumpedData = $memory->dump(0x1000, count($data));

        // 验证转储的数据
        foreach ($dumpedData as $address => $value) {
            $this->assertEquals($value, $memory->read($address));
        }

        // 测试加载时的地址环绕
        $memory->load(0xFFFE, [0xAA, 0xBB, 0xCC]);
        $this->assertEquals(0xAA, $memory->read(0xFFFE));
        $this->assertEquals(0xBB, $memory->read(0xFFFF));
        $this->assertEquals(0xCC, $memory->read(0x0000)); // 环绕到0x0000
    }

    /**
     * 测试内存重置
     */
    public function testReset(): void
    {
        $memory = new Memory();

        // 写入一些数据
        for ($i = 0; $i < 10; $i++) {
            $memory->write($i, 0x42);
        }

        // 重置内存
        $memory->reset();

        // 验证内存被清零
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals(0, $memory->read($i));
        }

        // 验证访问计数被重置
        $this->assertEquals(10, $memory->getAccessCount(), "10次读取操作");
    }
}
