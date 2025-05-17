<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Tourze\NES\CPU\Disassembler;
use Tourze\NES\CPU\InstructionSet;
use Tourze\NES\CPU\Memory;

// 创建内存实例
$memory = new Memory();

// 创建指令集
$instructionSet = new InstructionSet();

// 创建反汇编器
$disassembler = new Disassembler($instructionSet);

// 简单的示例程序 - 从0x1000位置读取一个值，加上42，然后存储回0x2000
$program = [
    // LDA $1000
    0xAD, 0x00, 0x10,
    // CLC
    0x18,
    // ADC #$2A
    0x69, 0x2A,
    // STA $2000
    0x8D, 0x00, 0x20,
    // BNE $020A (跳转到下面的RTS指令)
    0xD0, 0x01,
    // RTS
    0x60
];

// 加载程序到内存
$address = 0x0200;
foreach ($program as $byte) {
    $memory->write($address++, $byte);
}

// 设置格式化选项
$disassembler->setFormatOptions([
    'spacingAfterBytes' => 14,
    'uppercase' => true,
]);

echo "6502汇编代码反汇编示例\n";
echo "--------------------\n\n";

// 反汇编程序
$disassembled = $disassembler->disassemble($memory, 0x0200, count($program));

// 打印反汇编结果
foreach ($disassembled as $instruction) {
    echo $instruction['formatted'] . "\n";
}

echo "\n--------------------\n";
echo "程序流程解释：\n";
echo "1. 从内存地址\$1000读取一个值\n";
echo "2. 清除进位标志\n";
echo "3. 将值加上42 (0x2A)\n";
echo "4. 存储结果到内存地址\$2000\n";
echo "5. 如果结果不为0，跳转到RTS指令\n";
echo "6. 返回（从子程序）\n"; 