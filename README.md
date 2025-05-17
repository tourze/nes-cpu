# MOS 6502 CPU模拟器

这个包提供了一个完整的MOS 6502 CPU的PHP模拟实现。MOS 6502是一个经典的8位微处理器，曾用于Apple II、Commodore 64、NES等众多经典计算机和游戏机。

## 特性

- 完整实现6502指令集
- 准确模拟CPU时序和周期
- 模拟所有寻址模式
- 支持中断处理
- 提供调试和反汇编工具

## 安装

```bash
composer require tourze/mos-6502
```

## 基本用法

```php
use Tourze\MOS6502\Emulator;

// 创建模拟器实例
$emulator = new Emulator();

// 加载程序到内存
$program = [
    0xA9, 0x01,     // LDA #$01
    0x69, 0x02,     // ADC #$02
    0x00           // BRK
];
$emulator->loadProgram(0x0600, $program);

// 执行程序直到BRK指令
$emulator->runUntilBreak();

// 获取CPU状态
$state = $emulator->getState();
echo "A寄存器: " . dechex($state['a']) . "\n";
```

## 文档

有关更详细的文档，请查看`docs`目录或访问[项目Wiki](https://github.com/你的用户名/mos-6502/wiki)。

## 测试

```bash
composer test
```

## 许可证

MIT
