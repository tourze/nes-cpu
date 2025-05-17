<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\AddressingModeFactory;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\AddressingModeFactory;
use Tourze\MOS6502\AddressingModes\AbsoluteAddressing;
use Tourze\MOS6502\AddressingModes\AbsoluteXAddressing;
use Tourze\MOS6502\AddressingModes\AbsoluteYAddressing;
use Tourze\MOS6502\AddressingModes\AccumulatorAddressing;
use Tourze\MOS6502\AddressingModes\ImmediateAddressing;
use Tourze\MOS6502\AddressingModes\ImpliedAddressing;
use Tourze\MOS6502\AddressingModes\IndirectAddressing;
use Tourze\MOS6502\AddressingModes\IndirectXAddressing;
use Tourze\MOS6502\AddressingModes\IndirectYAddressing;
use Tourze\MOS6502\AddressingModes\RelativeAddressing;
use Tourze\MOS6502\AddressingModes\ZeroPageAddressing;
use Tourze\MOS6502\AddressingModes\ZeroPageXAddressing;
use Tourze\MOS6502\AddressingModes\ZeroPageYAddressing;

/**
 * 寻址模式工厂测试
 */
class AddressingModeFactoryTest extends TestCase
{
    /**
     * 测试静态工厂方法
     */
    public function testFactoryMethods(): void
    {
        $this->assertInstanceOf(ImpliedAddressing::class, AddressingModeFactory::implied());
        $this->assertInstanceOf(AccumulatorAddressing::class, AddressingModeFactory::accumulator());
        $this->assertInstanceOf(ImmediateAddressing::class, AddressingModeFactory::immediate());
        $this->assertInstanceOf(ZeroPageAddressing::class, AddressingModeFactory::zeroPage());
        $this->assertInstanceOf(ZeroPageXAddressing::class, AddressingModeFactory::zeroPageX());
        $this->assertInstanceOf(ZeroPageYAddressing::class, AddressingModeFactory::zeroPageY());
        $this->assertInstanceOf(AbsoluteAddressing::class, AddressingModeFactory::absolute());
        $this->assertInstanceOf(AbsoluteXAddressing::class, AddressingModeFactory::absoluteX());
        $this->assertInstanceOf(AbsoluteYAddressing::class, AddressingModeFactory::absoluteY());
        $this->assertInstanceOf(IndirectAddressing::class, AddressingModeFactory::indirect());
        $this->assertInstanceOf(IndirectXAddressing::class, AddressingModeFactory::indirectX());
        $this->assertInstanceOf(IndirectYAddressing::class, AddressingModeFactory::indirectY());
        $this->assertInstanceOf(RelativeAddressing::class, AddressingModeFactory::relative());
    }
    
    /**
     * 测试按名称获取寻址模式
     */
    public function testGetByName(): void
    {
        // 测试标准名称
        $this->assertInstanceOf(ImpliedAddressing::class, AddressingModeFactory::getByName('implied'));
        $this->assertInstanceOf(AccumulatorAddressing::class, AddressingModeFactory::getByName('accumulator'));
        $this->assertInstanceOf(ImmediateAddressing::class, AddressingModeFactory::getByName('immediate'));
        $this->assertInstanceOf(ZeroPageAddressing::class, AddressingModeFactory::getByName('zeropage'));
        $this->assertInstanceOf(ZeroPageXAddressing::class, AddressingModeFactory::getByName('zeropage,x'));
        $this->assertInstanceOf(ZeroPageYAddressing::class, AddressingModeFactory::getByName('zeropage,y'));
        $this->assertInstanceOf(AbsoluteAddressing::class, AddressingModeFactory::getByName('absolute'));
        $this->assertInstanceOf(AbsoluteXAddressing::class, AddressingModeFactory::getByName('absolute,x'));
        $this->assertInstanceOf(AbsoluteYAddressing::class, AddressingModeFactory::getByName('absolute,y'));
        $this->assertInstanceOf(IndirectAddressing::class, AddressingModeFactory::getByName('indirect'));
        $this->assertInstanceOf(IndirectXAddressing::class, AddressingModeFactory::getByName('(indirect,x)'));
        $this->assertInstanceOf(IndirectYAddressing::class, AddressingModeFactory::getByName('(indirect),y'));
        $this->assertInstanceOf(RelativeAddressing::class, AddressingModeFactory::getByName('relative'));
        
        // 测试简写名称
        $this->assertInstanceOf(ImpliedAddressing::class, AddressingModeFactory::getByName('imp'));
        $this->assertInstanceOf(AccumulatorAddressing::class, AddressingModeFactory::getByName('acc'));
        $this->assertInstanceOf(ImmediateAddressing::class, AddressingModeFactory::getByName('imm'));
        $this->assertInstanceOf(ZeroPageAddressing::class, AddressingModeFactory::getByName('zpg'));
        $this->assertInstanceOf(ZeroPageXAddressing::class, AddressingModeFactory::getByName('zpg,x'));
        $this->assertInstanceOf(ZeroPageYAddressing::class, AddressingModeFactory::getByName('zpg,y'));
        $this->assertInstanceOf(AbsoluteAddressing::class, AddressingModeFactory::getByName('abs'));
        $this->assertInstanceOf(AbsoluteXAddressing::class, AddressingModeFactory::getByName('abs,x'));
        $this->assertInstanceOf(AbsoluteYAddressing::class, AddressingModeFactory::getByName('abs,y'));
        $this->assertInstanceOf(IndirectAddressing::class, AddressingModeFactory::getByName('ind'));
        $this->assertInstanceOf(IndirectXAddressing::class, AddressingModeFactory::getByName('(ind,x)'));
        $this->assertInstanceOf(IndirectYAddressing::class, AddressingModeFactory::getByName('(ind),y'));
        $this->assertInstanceOf(RelativeAddressing::class, AddressingModeFactory::getByName('rel'));
        
        // 测试无效名称
        $this->assertNull(AddressingModeFactory::getByName('invalid_mode'));
    }
    
    /**
     * 测试实例缓存
     */
    public function testInstanceCaching(): void
    {
        // 获取同一类型的两个实例应该是同一个对象
        $instance1 = AddressingModeFactory::implied();
        $instance2 = AddressingModeFactory::implied();
        $this->assertSame($instance1, $instance2);
        
        // 不同类型的实例应该是不同的对象
        $instance3 = AddressingModeFactory::absolute();
        $this->assertNotSame($instance1, $instance3);
    }
}
