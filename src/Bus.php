<?php

declare(strict_types=1);

namespace Tourze\MOS6502;

/**
 * 总线类
 *
 * 连接CPU和其他组件（如内存和I/O设备）
 */
class Bus
{
    /**
     * 已连接组件的映射
     *
     * @var array<string, array{component: object, start: int, end: int}>
     */
    private array $components = [];

    /**
     * 地址空间映射
     *
     * @var array<int, string>
     */
    private array $addressMap = [];

    /**
     * 从总线读取字节
     *
     * @param int $address 读取地址
     * @return int 读取的字节值
     * @throws \RuntimeException 如果地址没有映射到任何组件
     */
    public function read(int $address): int
    {
        $address = $address & 0xFFFF; // 确保地址在0-65535范围内

        // 查找处理该地址的组件
        $componentId = $this->addressMap[$address] ?? null;

        if ($componentId === null) {
            throw new \RuntimeException("地址 0x" . sprintf('%04X', $address) . " 未映射到任何组件");
        }

        $component = $this->components[$componentId]['component'];

        // 调用组件的read方法
        if ($component instanceof Memory) {
            return $component->read($address);
        }

        // 其他类型的组件将在后续实现
        throw new \RuntimeException("组件 {$componentId} 不支持读取操作");
    }

    /**
     * 向总线写入字节
     *
     * @param int $address 写入地址
     * @param int $value 要写入的值
     * @return void
     * @throws \RuntimeException 如果地址没有映射到任何组件
     */
    public function write(int $address, int $value): void
    {
        $address = $address & 0xFFFF; // 确保地址在0-65535范围内
        $value = $value & 0xFF; // 确保值在0-255范围内

        // 查找处理该地址的组件
        $componentId = $this->addressMap[$address] ?? null;

        if ($componentId === null) {
            throw new \RuntimeException("地址 0x" . sprintf('%04X', $address) . " 未映射到任何组件");
        }

        $component = $this->components[$componentId]['component'];

        // 调用组件的write方法
        if ($component instanceof Memory) {
            $component->write($address, $value);
            return;
        }

        // 其他类型的组件将在后续实现
        throw new \RuntimeException("组件 {$componentId} 不支持写入操作");
    }

    /**
     * 读取16位字（小端序）
     *
     * @param int $address 起始地址
     * @return int 16位值
     */
    public function readWord(int $address): int
    {
        $low = $this->read($address);
        $high = $this->read(($address + 1) & 0xFFFF);
        return $low | ($high << 8);
    }

    /**
     * 写入16位字（小端序）
     *
     * @param int $address 起始地址
     * @param int $value 16位值
     * @return void
     */
    public function writeWord(int $address, int $value): void
    {
        $this->write($address, $value & 0xFF);
        $this->write(($address + 1) & 0xFFFF, ($value >> 8) & 0xFF);
    }

    /**
     * 连接组件到总线
     *
     * @param object $component 要连接的组件
     * @param string $id 组件ID
     * @param int $startAddress 起始地址
     * @param int $endAddress 结束地址
     * @return void
     * @throws \InvalidArgumentException 如果地址范围无效或与现有映射冲突
     */
    public function connect(object $component, string $id, int $startAddress, int $endAddress): void
    {
        // 验证地址范围
        $startAddress = $startAddress & 0xFFFF;
        $endAddress = $endAddress & 0xFFFF;

        if ($startAddress > $endAddress) {
            throw new \InvalidArgumentException("无效的地址范围：起始地址 0x" . sprintf('%04X', $startAddress) .
                " 大于结束地址 0x" . sprintf('%04X', $endAddress));
        }

        // 检查地址范围是否与现有映射冲突
        for ($address = $startAddress; $address <= $endAddress; $address++) {
            if (isset($this->addressMap[$address])) {
                $existingId = $this->addressMap[$address];
                throw new \InvalidArgumentException(
                    "地址冲突：地址 0x" . sprintf('%04X', $address) .
                    " 已经映射到组件 {$existingId}"
                );
            }
        }

        // 存储组件信息
        $this->components[$id] = [
            'component' => $component,
            'start' => $startAddress,
            'end' => $endAddress
        ];

        // 更新地址映射
        for ($address = $startAddress; $address <= $endAddress; $address++) {
            $this->addressMap[$address] = $id;
        }
    }

    /**
     * 断开组件连接
     *
     * @param string $id 组件ID
     * @return void
     * @throws \InvalidArgumentException 如果组件ID不存在
     */
    public function disconnect(string $id): void
    {
        if (!isset($this->components[$id])) {
            throw new \InvalidArgumentException("组件 {$id} 不存在");
        }

        $component = $this->components[$id];
        $startAddress = $component['start'];
        $endAddress = $component['end'];

        // 清除地址映射
        for ($address = $startAddress; $address <= $endAddress; $address++) {
            if (($this->addressMap[$address] ?? null) === $id) {
                unset($this->addressMap[$address]);
            }
        }

        // 删除组件
        unset($this->components[$id]);
    }

    /**
     * 获取已连接组件的列表
     *
     * @return array<string, array{component: object, start: int, end: int}> 组件列表
     */
    public function getConnectedComponents(): array
    {
        return $this->components;
    }

    /**
     * 检查地址是否已映射到组件
     *
     * @param int $address 要检查的地址
     * @return bool 如果地址已映射则返回true
     */
    public function isAddressMapped(int $address): bool
    {
        $address = $address & 0xFFFF;
        return isset($this->addressMap[$address]);
    }

    /**
     * 获取地址映射到的组件ID
     *
     * @param int $address 地址
     * @return string|null 组件ID，如果地址未映射则返回null
     */
    public function getComponentIdAtAddress(int $address): ?string
    {
        $address = $address & 0xFFFF;
        return $this->addressMap[$address] ?? null;
    }

    /**
     * 重置总线状态
     *
     * @return void
     */
    public function reset(): void
    {
        // 保留连接的组件，但重置它们（如果支持reset方法）
        foreach ($this->components as $component) {
            $obj = $component['component'];
            if (method_exists($obj, 'reset')) {
                $obj->reset();
            }
        }
    }
}
