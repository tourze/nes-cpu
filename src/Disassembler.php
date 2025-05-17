<?php

declare(strict_types=1);

namespace Tourze\NES\CPU;

/**
 * 反汇编器
 *
 * 将机器码转换为可读的汇编代码
 */
class Disassembler
{
    /**
     * 指令集引用
     */
    private InstructionSet $instructionSet;

    /**
     * 格式化选项
     */
    private array $formatOptions = [
        'showAddress' => true,        // 是否显示地址
        'showBytes' => true,          // 是否显示机器码字节
        'addressFormat' => '$%04X',   // 地址格式
        'byteFormat' => '%02X',       // 字节格式
        'uppercase' => true,          // 助记符是否大写
        'spacingAfterBytes' => 12,    // 字节后的空格数量
        'operandFormat' => [          // 各种寻址模式的操作数格式
            'implied' => '',
            'accumulator' => 'A',
            'immediate' => '#$%02X',
            'zeroPage' => '$%02X',
            'zeroPageX' => '$%02X,X',
            'zeroPageY' => '$%02X,Y',
            'absolute' => '$%04X',
            'absoluteX' => '$%04X,X',
            'absoluteY' => '$%04X,Y',
            'indirect' => '($%04X)',
            'indirectX' => '($%02X,X)',
            'indirectY' => '($%02X),Y',
            'relative' => '$%04X',
        ],
    ];

    /**
     * 构造函数
     *
     * @param InstructionSet $instructionSet 指令集实例
     */
    public function __construct(InstructionSet $instructionSet)
    {
        $this->instructionSet = $instructionSet;
    }

    /**
     * 设置格式化选项
     *
     * @param array $options 格式化选项
     * @return void
     */
    public function setFormatOptions(array $options): void
    {
        $this->formatOptions = array_merge($this->formatOptions, $options);
    }

    /**
     * 反汇编指定内存区域的代码
     *
     * @param Memory $memory 内存实例
     * @param int $startAddress 起始地址
     * @param int $length 长度（字节数或指令数，由指定$byteLength决定）
     * @param bool $byteLength 是否按字节长度计算（默认为true）
     * @return array 反汇编结果数组
     */
    public function disassemble(Memory $memory, int $startAddress, int $length, bool $byteLength = true): array
    {
        $result = [];
        $address = $startAddress;
        $endAddress = $byteLength ? $startAddress + $length : PHP_INT_MAX;
        $instructionCount = 0;

        while ($address < $endAddress && ($byteLength || $instructionCount < $length)) {
            $instruction = $this->disassembleInstruction($memory, $address);
            $result[] = $instruction;

            $address += $instruction['bytes'];
            $instructionCount++;

            // 如果达到内存边界，终止循环
            if ($address >= Memory::MEMORY_SIZE) {
                break;
            }
        }

        return $result;
    }

    /**
     * 反汇编单条指令
     *
     * @param Memory $memory 内存实例
     * @param int $address 指令地址
     * @return array 指令信息数组
     */
    public function disassembleInstruction(Memory $memory, int $address): array
    {
        $opcode = $memory->read($address);

        try {
            // 获取指令信息
            $instructionInfo = $this->instructionSet->getInstructionInfo($opcode);

            $mnemonic = $instructionInfo['mnemonic'];
            $addressingMode = $instructionInfo['addressing_mode'];
            $bytes = $instructionInfo['bytes'];

            // 读取指令的所有字节
            $instructionBytes = [$opcode];
            for ($i = 1; $i < $bytes; $i++) {
                $instructionBytes[] = $memory->read($address + $i);
            }

            // 提取操作数
            $operand = $this->extractOperand($memory, $address, $addressingMode, $bytes);

            // 格式化指令输出
            $formattedInstruction = $this->formatInstruction($address, $instructionBytes, $mnemonic, $addressingMode, $operand);

            return [
                'address' => $address,
                'bytes' => $bytes,
                'opcode' => $opcode,
                'mnemonic' => $mnemonic,
                'addressing_mode' => $addressingMode,
                'operand' => $operand,
                'instruction_bytes' => $instructionBytes,
                'formatted' => $formattedInstruction,
            ];
        } catch (\InvalidArgumentException $e) {
            // 处理未知操作码
            return [
                'address' => $address,
                'bytes' => 1,
                'opcode' => $opcode,
                'mnemonic' => '???',
                'addressing_mode' => '',
                'operand' => [],
                'instruction_bytes' => [$opcode],
                'formatted' => $this->formatInvalidInstruction($address, $opcode),
            ];
        }
    }

    /**
     * 提取操作数
     *
     * @param Memory $memory 内存实例
     * @param int $address 指令地址
     * @param string $addressingMode 寻址模式
     * @param int $bytes 指令字节数
     * @return array 操作数信息
     */
    private function extractOperand(Memory $memory, int $address, string $addressingMode, int $bytes): array
    {
        $operand = [
            'value' => 0,
            'bytes' => [],
            'target' => null,
        ];

        // 如果是单字节指令（implied或accumulator），无需提取操作数
        if ($bytes <= 1) {
            return $operand;
        }

        // 读取操作数字节
        if ($bytes >= 2) {
            $operand['bytes'][] = $memory->read($address + 1);
        }

        if ($bytes >= 3) {
            $operand['bytes'][] = $memory->read($address + 2);
        }

        // 根据不同的寻址模式提取操作数值
        switch ($addressingMode) {
            case 'implied':
            case 'accumulator':
                // 无操作数或操作数为累加器
                break;

            case 'immediate':
            case 'zeroPage':
            case 'zeroPageX':
            case 'zeroPageY':
            case 'indirectX':
            case 'indirectY':
                // 单字节操作数
                $operand['value'] = $operand['bytes'][0];
                break;

            case 'absolute':
            case 'absoluteX':
            case 'absoluteY':
            case 'indirect':
                // 双字节操作数（小端序）
                $operand['value'] = $operand['bytes'][0] | ($operand['bytes'][1] << 8);
                break;

            case 'relative':
                // 相对地址（有符号偏移量）
                $offset = $operand['bytes'][0];
                // 如果是负数（最高位为1），进行符号扩展
                if ($offset & 0x80) {
                    $offset = -((~$offset & 0xFF) + 1);
                }
                // 计算目标地址（相对于下一条指令的地址）
                $operand['value'] = $offset;
                $operand['target'] = ($address + $bytes + $offset) & 0xFFFF;
                break;
        }

        return $operand;
    }

    /**
     * 格式化指令输出
     *
     * @param int $address 指令地址
     * @param array $bytes 指令字节数组
     * @param string $mnemonic 助记符
     * @param string $addressingMode 寻址模式
     * @param array $operand 操作数信息
     * @return string 格式化后的指令字符串
     */
    public function formatInstruction(int $address, array $bytes, string $mnemonic, string $addressingMode, array $operand): string
    {
        $result = '';

        // 显示地址
        if ($this->formatOptions['showAddress']) {
            $result .= sprintf($this->formatOptions['addressFormat'], $address) . ': ';
        }

        // 显示机器码字节
        if ($this->formatOptions['showBytes']) {
            $byteStrings = [];
            foreach ($bytes as $byte) {
                $byteStrings[] = sprintf($this->formatOptions['byteFormat'], $byte);
            }
            $bytesStr = implode(' ', $byteStrings);
            $result .= str_pad($bytesStr, $this->formatOptions['spacingAfterBytes'], ' ');
        }

        // 设置助记符大小写
        if ($this->formatOptions['uppercase']) {
            $mnemonic = strtoupper($mnemonic);
        } else {
            $mnemonic = strtolower($mnemonic);
        }

        $result .= $mnemonic;

        // 添加操作数（如果有）
        if ($addressingMode !== 'implied') {
            $result .= ' ';

            // 获取指定寻址模式的操作数格式
            $operandFormat = $this->formatOptions['operandFormat'][$addressingMode] ?? '%s';

            // 对于相对寻址，使用目标地址而非偏移量
            if ($addressingMode === 'relative' && $operand['target'] !== null) {
                $result .= sprintf($operandFormat, $operand['target']);
            } else {
                // 对于其他寻址模式，使用操作数值
                $result .= sprintf($operandFormat, $operand['value']);
            }
        }

        return $result;
    }

    /**
     * 格式化无效指令输出
     *
     * @param int $address 指令地址
     * @param int $opcode 操作码
     * @return string 格式化后的指令字符串
     */
    private function formatInvalidInstruction(int $address, int $opcode): string
    {
        $result = '';

        // 显示地址
        if ($this->formatOptions['showAddress']) {
            $result .= sprintf($this->formatOptions['addressFormat'], $address) . ': ';
        }

        // 显示机器码字节
        if ($this->formatOptions['showBytes']) {
            $byteStr = sprintf($this->formatOptions['byteFormat'], $opcode);
            $result .= str_pad($byteStr, $this->formatOptions['spacingAfterBytes'], ' ');
        }

        // 显示未知指令标记
        $unknownMnemonic = $this->formatOptions['uppercase'] ? '???' : '???';
        $result .= $unknownMnemonic . ' ';

        // 显示操作码（十六进制）
        $result .= sprintf('($%02X)', $opcode);

        return $result;
    }
}
