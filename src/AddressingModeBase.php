<?php

declare(strict_types=1);

namespace Tourze\NES\CPU;

/**
 * 寻址模式抽象基类
 * 
 * 提供一些所有寻址模式共用的基本功能
 */
abstract class AddressingModeBase implements AddressingMode
{
    /**
     * 指示是否跨页边界
     */
    protected bool $crossesPageBoundary = false;
    
    /**
     * {@inheritdoc}
     */
    public function getCrossesPageBoundary(): bool
    {
        return $this->crossesPageBoundary;
    }
    
    /**
     * 检查两个地址是否跨页
     * 
     * @param int $addr1 第一个地址
     * @param int $addr2 第二个地址
     * @return bool 是否跨页
     */
    protected function checkPageCrossing(int $addr1, int $addr2): bool
    {
        return ($addr1 & 0xFF00) !== ($addr2 & 0xFF00);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOperandValue(CPU $cpu, Bus $bus): int
    {
        $address = $this->getOperandAddress($cpu, $bus);
        return $bus->read($address);
    }
} 