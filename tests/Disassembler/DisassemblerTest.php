<?php

declare(strict_types=1);

namespace Tourze\MOS6502\Tests\Disassembler;

use PHPUnit\Framework\TestCase;
use Tourze\MOS6502\Disassembler;
use Tourze\MOS6502\InstructionSet;
use Tourze\MOS6502\Memory;

/**
 * 反汇编器测试类
 */
class DisassemblerTest extends TestCase
{
    /**
     * 指令集
     */
    private InstructionSet $instructionSet;
    
    /**
     * 内存
     */
    private Memory $memory;
    
    /**
     * 反汇编器
     */
    private Disassembler $disassembler;
    
    /**
     * 测试初始化
     */
    protected function setUp(): void
    {
        $this->instructionSet = new InstructionSet();
        $this->memory = new Memory();
        $this->disassembler = new Disassembler($this->instructionSet);
    }
    
    /**
     * 测试基本反汇编功能
     */
    public function testBasicDisassembly(): void
    {
        // 加载一段简单的程序到内存
        // LDA #$42   ; A9 42
        // STA $10    ; 85 10
        // LDX #$0A   ; A2 0A
        // STX $11    ; 86 11
        // CMP $10    ; C5 10
        // BNE $05    ; D0 05
        // CLC        ; 18
        $program = [0xA9, 0x42, 0x85, 0x10, 0xA2, 0x0A, 0x86, 0x11, 0xC5, 0x10, 0xD0, 0x05, 0x18];
        
        $address = 0x0200;
        foreach ($program as $byte) {
            $this->memory->write($address++, $byte);
        }
        
        // 反汇编程序
        $result = $this->disassembler->disassemble($this->memory, 0x0200, count($program));
        
        // 验证结果
        $this->assertCount(7, $result, '应该反汇编出7条指令'); // 13个字节，7条指令
        
        // 验证第一条指令 (LDA #$42)
        $this->assertEquals(0x0200, $result[0]['address']);
        $this->assertEquals(0xA9, $result[0]['opcode']);
        $this->assertEquals('LDA', $result[0]['mnemonic']);
        $this->assertEquals('immediate', $result[0]['addressing_mode']);
        $this->assertEquals(2, $result[0]['bytes']);
        $this->assertEquals(0x42, $result[0]['operand']['value']);
        
        // 验证最后一条指令 (CLC)
        $this->assertEquals(0x020C, $result[6]['address']);
        $this->assertEquals(0x18, $result[6]['opcode']);
        $this->assertEquals('CLC', $result[6]['mnemonic']);
        $this->assertEquals('implied', $result[6]['addressing_mode']);
        $this->assertEquals(1, $result[6]['bytes']);
    }
    
    /**
     * 测试格式化指令输出
     */
    public function testFormatInstruction(): void
    {
        // 加载一段简单的程序到内存
        // LDA #$42   ; A9 42
        $this->memory->write(0x0200, 0xA9);
        $this->memory->write(0x0201, 0x42);
        
        // 反汇编单条指令
        $instruction = $this->disassembler->disassembleInstruction($this->memory, 0x0200);
        
        // 验证格式化输出
        $this->assertStringContainsString('LDA #$42', $instruction['formatted']);
        $this->assertStringContainsString('$0200', $instruction['formatted']);
        
        // 修改格式化选项
        $this->disassembler->setFormatOptions([
            'showAddress' => false,
            'uppercase' => false,
        ]);
        
        // 再次反汇编
        $instruction = $this->disassembler->disassembleInstruction($this->memory, 0x0200);
        
        // 验证修改后的格式化输出
        $this->assertStringContainsString('lda #$42', $instruction['formatted']);
        $this->assertStringNotContainsString('$0200', $instruction['formatted']);
    }
    
    /**
     * 测试反汇编相对寻址指令
     */
    public function testRelativeAddressing(): void
    {
        // 加载一个带分支指令的程序
        // BNE $0210  ; D0 0E (分支到0x0210, 从0x0202+2计算偏移量为+14)
        $this->memory->write(0x0200, 0xD0);
        $this->memory->write(0x0201, 0x0E);
        
        // 反汇编
        $instruction = $this->disassembler->disassembleInstruction($this->memory, 0x0200);
        
        // 验证结果
        $this->assertEquals('BNE', $instruction['mnemonic']);
        $this->assertEquals('relative', $instruction['addressing_mode']);
        $this->assertEquals(0x0E, $instruction['operand']['value']); // 原始偏移量
        $this->assertEquals(0x0210, $instruction['operand']['target']); // 计算后的目标地址
        $this->assertStringContainsString('$0210', $instruction['formatted']); // 格式化输出应该包含目标地址
    }
    
    /**
     * 测试反汇编JMP间接指令
     */
    public function testIndirectJump(): void
    {
        // 加载一个JMP间接指令
        // JMP ($1234) ; 6C 34 12
        $this->memory->write(0x0200, 0x6C);
        $this->memory->write(0x0201, 0x34);
        $this->memory->write(0x0202, 0x12);
        
        // 反汇编
        $instruction = $this->disassembler->disassembleInstruction($this->memory, 0x0200);
        
        // 验证结果
        $this->assertEquals('JMP', $instruction['mnemonic']);
        $this->assertEquals('indirect', $instruction['addressing_mode']);
        $this->assertEquals(0x1234, $instruction['operand']['value']);
        $this->assertStringContainsString('($1234)', $instruction['formatted']);
    }
    
    /**
     * 测试处理未知操作码
     */
    public function testUnknownOpcode(): void
    {
        // 写入一个未知操作码
        $this->memory->write(0x0200, 0xFF); // 假设0xFF是未实现的操作码
        
        // 反汇编
        $instruction = $this->disassembler->disassembleInstruction($this->memory, 0x0200);
        
        // 验证结果
        $this->assertEquals('???', $instruction['mnemonic']);
        $this->assertEquals(1, $instruction['bytes']);
        $this->assertStringContainsString('($FF)', $instruction['formatted']); // 应该显示操作码
    }
    
    /**
     * 测试反汇编指定长度
     */
    public function testDisassembleWithLength(): void
    {
        // 加载一段程序到内存
        // LDA #$01 ; A9 01
        // LDA #$02 ; A9 02
        // LDA #$03 ; A9 03
        // LDA #$04 ; A9 04
        $program = [0xA9, 0x01, 0xA9, 0x02, 0xA9, 0x03, 0xA9, 0x04];
        
        $address = 0x0200;
        foreach ($program as $byte) {
            $this->memory->write($address++, $byte);
        }
        
        // 按字节长度反汇编 - 4个字节
        $result = $this->disassembler->disassemble($this->memory, 0x0200, 4, true);
        $this->assertCount(2, $result); // 应该得到2条指令（每条2字节）
        
        // 按指令数量反汇编 - 3条指令
        $result = $this->disassembler->disassemble($this->memory, 0x0200, 3, false);
        $this->assertCount(3, $result);
    }
} 