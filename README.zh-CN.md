# nes-cpu

MOS 6502 CPU模拟器，针对NES系统的Ricoh 2A03处理器优化

## 特性

- 完整实现6502指令集
- 模拟Ricoh 2A03特有的行为（如禁用BCD模式）
- 支持标准MOS 6502和Ricoh 2A03两种模式（可配置）
- 准确模拟CPU时序和周期
- 模拟所有寻址模式
- 支持中断处理

## 与标准MOS 6502的区别

Ricoh 2A03处理器与标准MOS 6502的主要区别：

- 禁用BCD（二进制编码十进制）模式 - D标志仍可设置，但ADC和SBC指令会忽略它
- 集成了定制的音频处理单元(APU)（非本模拟器的一部分）

本模拟器默认以Ricoh 2A03模式运行（禁用BCD），但可以通过API切换到标准MOS 6502模式：

```php
// 创建CPU实例时可指定是否禁用BCD模式
$cpu = new CPU($bus, true); // true: 禁用BCD (NES模式)，false: 启用BCD (标准6502模式)

// 或者在运行时修改
$cpu->setDisableBCD(false); // 启用BCD模式
$cpu->setDisableBCD(true);  // 禁用BCD模式
```

## 安装

```bash
composer require tourze/nes-cpu
```

## 使用方法

```php
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\Memory;

// 创建总线和内存
$memory = new Memory();
$bus = new Bus();
$bus->connect($memory, 'ram', 0x0000, 0xFFFF);

// 创建CPU (默认为NES/Ricoh 2A03模式)
$cpu = new CPU($bus);

// 加载程序到内存
$program = [
    0xA9, 0x01,     // LDA #$01
    0x69, 0x02,     // ADC #$02
    0x00           // BRK
];
$memory->load(0x0600, $program);

// 设置程序计数器
$cpu->getRegister('PC')->setValue(0x0600);

// 执行程序直到BRK指令
while ($cpu->getMemory()->read($cpu->getRegister('PC')->getValue()) != 0x00) {
    $cpu->step();
}

// 获取累加器值
echo "A寄存器: " . dechex($cpu->getRegister('A')->getValue()) . "\n";
```

## 配置

可以在创建CPU实例时配置模拟器的行为：

```php
// 创建标准MOS 6502模式的CPU（启用BCD模式）
$cpu = new CPU($bus, false);
```

## 参考文档

- [NESdev Wiki](https://www.nesdev.org/wiki/CPU)
- [MOS 6502 Datasheet](http://archive.6502.org/datasheets/rockwell_r650x_r651x.pdf)
