<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

/**
 * 6502 CPU的内存模拟类
 * 
 * 管理和模拟64KB内存空间，提供读写操作
 */
class Memory
{
    /**
     * 内存大小常量 - 64KB
     */
    public const MEMORY_SIZE = 65536;
    
    /**
     * 内存数组，表示64KB内存空间
     * 
     * @var array<int, int>
     */
    private array $memory = [];
    
    /**
     * 内存访问计数器
     * 
     * @var int
     */
    private int $accessCount = 0;
    
    /**
     * 构造函数，初始化内存
     */
    public function __construct()
    {
        $this->reset();
    }
    
    /**
     * 重置内存为初始状态
     * 
     * @return void
     */
    public function reset(): void
    {
        $this->memory = array_fill(0, self::MEMORY_SIZE, 0);
        $this->accessCount = 0;
    }
    
    /**
     * 从指定地址读取一个字节
     * 
     * @param int $address 内存地址 (0-65535)
     * 
     * @return int 读取的字节值 (0-255)
     */
    public function read(int $address): int
    {
        $address = $address & 0xFFFF; // 确保地址在0-65535范围内
        $this->accessCount++;
        return $this->memory[$address];
    }
    
    /**
     * 向指定地址写入一个字节
     * 
     * @param int $address 内存地址 (0-65535)
     * @param int $value 要写入的字节值 (0-255)
     * 
     * @return void
     */
    public function write(int $address, int $value): void
    {
        $address = $address & 0xFFFF; // 确保地址在0-65535范围内
        $value = $value & 0xFF; // 确保值在0-255范围内
        $this->accessCount++;
        $this->memory[$address] = $value;
    }
    
    /**
     * 读取一个16位字（小端序）
     * 
     * @param int $address 内存地址 (0-65535)
     * 
     * @return int 读取的16位值 (0-65535)
     */
    public function readWord(int $address): int
    {
        $low = $this->read($address);
        $high = $this->read(($address + 1) & 0xFFFF);
        return $low | ($high << 8);
    }
    
    /**
     * 写入一个16位字（小端序）
     * 
     * @param int $address 内存地址 (0-65535)
     * @param int $value 要写入的16位值 (0-65535)
     * 
     * @return void
     */
    public function writeWord(int $address, int $value): void
    {
        $this->write($address, $value & 0xFF);
        $this->write(($address + 1) & 0xFFFF, ($value >> 8) & 0xFF);
    }
    
    /**
     * 加载一块数据到内存
     * 
     * @param int $address 起始内存地址
     * @param array<int, int> $data 要加载的数据数组
     * 
     * @return void
     */
    public function load(int $address, array $data): void
    {
        $address = $address & 0xFFFF;
        foreach ($data as $byte) {
            $this->write($address++, $byte);
            $address &= 0xFFFF; // 处理地址溢出
        }
    }
    
    /**
     * 转储一块内存区域
     * 
     * @param int $startAddress 起始地址
     * @param int $length 要转储的字节数
     * 
     * @return array<int, int> 内存内容数组
     */
    public function dump(int $startAddress, int $length): array
    {
        $startAddress = $startAddress & 0xFFFF;
        $result = [];
        
        for ($i = 0; $i < $length; $i++) {
            $address = ($startAddress + $i) & 0xFFFF;
            $result[$address] = $this->memory[$address];
        }
        
        return $result;
    }
    
    /**
     * 获取内存访问计数
     * 
     * @return int 内存访问次数
     */
    public function getAccessCount(): int
    {
        return $this->accessCount;
    }
}
