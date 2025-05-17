<?php

declare(strict_types=1);

namespace Tourze\NES\CPU;

use Tourze\NES\CPU\AddressingModes\AbsoluteAddressing;
use Tourze\NES\CPU\AddressingModes\AbsoluteXAddressing;
use Tourze\NES\CPU\AddressingModes\AbsoluteYAddressing;
use Tourze\NES\CPU\AddressingModes\AccumulatorAddressing;
use Tourze\NES\CPU\AddressingModes\ImmediateAddressing;
use Tourze\NES\CPU\AddressingModes\ImpliedAddressing;
use Tourze\NES\CPU\AddressingModes\IndirectAddressing;
use Tourze\NES\CPU\AddressingModes\IndirectXAddressing;
use Tourze\NES\CPU\AddressingModes\IndirectYAddressing;
use Tourze\NES\CPU\AddressingModes\RelativeAddressing;
use Tourze\NES\CPU\AddressingModes\ZeroPageAddressing;
use Tourze\NES\CPU\AddressingModes\ZeroPageXAddressing;
use Tourze\NES\CPU\AddressingModes\ZeroPageYAddressing;

/**
 * 寻址模式工厂类
 * 
 * 提供获取各种寻址模式实例的方法
 */
class AddressingModeFactory
{
    /**
     * 寻址模式缓存
     */
    private static array $instances = [];
    
    /**
     * 获取隐含寻址模式
     */
    public static function implied(): AddressingMode
    {
        return self::getInstance('implied', ImpliedAddressing::class);
    }
    
    /**
     * 获取累加器寻址模式
     */
    public static function accumulator(): AddressingMode
    {
        return self::getInstance('accumulator', AccumulatorAddressing::class);
    }
    
    /**
     * 获取立即寻址模式
     */
    public static function immediate(): AddressingMode
    {
        return self::getInstance('immediate', ImmediateAddressing::class);
    }
    
    /**
     * 获取零页寻址模式
     */
    public static function zeroPage(): AddressingMode
    {
        return self::getInstance('zeroPage', ZeroPageAddressing::class);
    }
    
    /**
     * 获取零页X索引寻址模式
     */
    public static function zeroPageX(): AddressingMode
    {
        return self::getInstance('zeroPageX', ZeroPageXAddressing::class);
    }
    
    /**
     * 获取零页Y索引寻址模式
     */
    public static function zeroPageY(): AddressingMode
    {
        return self::getInstance('zeroPageY', ZeroPageYAddressing::class);
    }
    
    /**
     * 获取绝对寻址模式
     */
    public static function absolute(): AddressingMode
    {
        return self::getInstance('absolute', AbsoluteAddressing::class);
    }
    
    /**
     * 获取绝对X索引寻址模式
     */
    public static function absoluteX(): AddressingMode
    {
        return self::getInstance('absoluteX', AbsoluteXAddressing::class);
    }
    
    /**
     * 获取绝对Y索引寻址模式
     */
    public static function absoluteY(): AddressingMode
    {
        return self::getInstance('absoluteY', AbsoluteYAddressing::class);
    }
    
    /**
     * 获取间接寻址模式
     */
    public static function indirect(): AddressingMode
    {
        return self::getInstance('indirect', IndirectAddressing::class);
    }
    
    /**
     * 获取间接X索引寻址模式
     */
    public static function indirectX(): AddressingMode
    {
        return self::getInstance('indirectX', IndirectXAddressing::class);
    }
    
    /**
     * 获取间接Y索引寻址模式
     */
    public static function indirectY(): AddressingMode
    {
        return self::getInstance('indirectY', IndirectYAddressing::class);
    }
    
    /**
     * 获取相对寻址模式
     */
    public static function relative(): AddressingMode
    {
        return self::getInstance('relative', RelativeAddressing::class);
    }
    
    /**
     * 根据名称获取寻址模式
     * 
     * @param string $name 寻址模式名称
     * @return AddressingMode|null 对应的寻址模式实例
     */
    public static function getByName(string $name): ?AddressingMode
    {
        return match ($name) {
            'implied', 'imp' => self::implied(),
            'accumulator', 'acc' => self::accumulator(),
            'immediate', 'imm' => self::immediate(),
            'zeropage', 'zpg' => self::zeroPage(),
            'zeropage,x', 'zpg,x' => self::zeroPageX(),
            'zeropage,y', 'zpg,y' => self::zeroPageY(),
            'absolute', 'abs' => self::absolute(),
            'absolute,x', 'abs,x' => self::absoluteX(),
            'absolute,y', 'abs,y' => self::absoluteY(),
            'indirect', 'ind' => self::indirect(),
            '(indirect,x)', '(ind,x)' => self::indirectX(),
            '(indirect),y', '(ind),y' => self::indirectY(),
            'relative', 'rel' => self::relative(),
            default => null,
        };
    }
    
    /**
     * 获取寻址模式实例
     * 
     * @param string $key 缓存键
     * @param string $className 类名
     * @return AddressingMode 寻址模式实例
     */
    private static function getInstance(string $key, string $className): AddressingMode
    {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new $className();
        }
        
        return self::$instances[$key];
    }
} 