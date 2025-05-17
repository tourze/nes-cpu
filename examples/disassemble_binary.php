<?php

// 6502二进制文件反汇编工具

require __DIR__ . '/../../../vendor/autoload.php';

use Tourze\MOS6502\Disassembler;
use Tourze\MOS6502\InstructionSet;
use Tourze\MOS6502\Memory;

// 解析命令行参数
$options = getopt('f:a:l:o:h', ['file:', 'address:', 'length:', 'output:', 'help']);

// 显示帮助信息
if (isset($options['h']) || isset($options['help']) || !isset($options['f']) && !isset($options['file'])) {
    echo "6502二进制文件反汇编工具\n";
    echo "用法: php disassemble_binary.php [选项]\n\n";
    echo "选项:\n";
    echo "  -f, --file=FILE       要反汇编的二进制文件\n";
    echo "  -a, --address=ADDR    加载地址（十六进制，默认为0x0600）\n";
    echo "  -l, --length=LEN      要反汇编的长度（十六进制，默认为整个文件）\n";
    echo "  -o, --output=FILE     输出文件（默认为标准输出）\n";
    echo "  -h, --help            显示此帮助信息\n";
    exit(0);
}

// 获取参数
$filename = $options['f'] ?? $options['file'] ?? null;
$addressHex = $options['a'] ?? $options['address'] ?? '0600';
$lengthHex = $options['l'] ?? $options['length'] ?? null;
$outputFile = $options['o'] ?? $options['output'] ?? null;

// 转换地址为整数
$address = hexdec($addressHex);

// 检查文件存在
if (!file_exists($filename)) {
    echo "错误: 文件 '$filename' 不存在\n";
    exit(1);
}

// 读取二进制文件
$binaryData = file_get_contents($filename);
if ($binaryData === false) {
    echo "错误: 无法读取文件 '$filename'\n";
    exit(1);
}

// 如果未指定长度，使用文件大小
$length = $lengthHex !== null ? hexdec($lengthHex) : strlen($binaryData);

// 设置输出
if ($outputFile !== null) {
    $outputHandle = fopen($outputFile, 'w');
    if ($outputHandle === false) {
        echo "错误: 无法打开输出文件 '$outputFile'\n";
        exit(1);
    }
} else {
    $outputHandle = STDOUT;
}

// 创建内存、指令集和反汇编器
$memory = new Memory();
$instructionSet = new InstructionSet();
$disassembler = new Disassembler($instructionSet);

// 加载二进制数据到内存
for ($i = 0; $i < min($length, strlen($binaryData)); $i++) {
    $memory->write($address + $i, ord($binaryData[$i]));
}

// 设置格式化选项
$disassembler->setFormatOptions([
    'spacingAfterBytes' => 16,
    'uppercase' => true,
]);

// 反汇编
$disassembled = $disassembler->disassemble($memory, $address, $length);

// 输出反汇编结果
fprintf($outputHandle, "; 6502反汇编 - 文件: %s\n", $filename);
fprintf($outputHandle, "; 加载地址: $%04X, 长度: $%04X\n\n", $address, $length);

// 写入反汇编结果
foreach ($disassembled as $instruction) {
    fprintf($outputHandle, "%s\n", $instruction['formatted']);
}

// 如果输出到文件，关闭文件句柄
if ($outputFile !== null) {
    fclose($outputHandle);
    echo "反汇编结果已写入 '$outputFile'\n";
} 