<?php

declare(strict_types=1);

namespace Tourze\NES\CPU\Tests\Instructions;

use PHPUnit\Framework\TestCase;
use Tourze\NES\CPU\Bus;
use Tourze\NES\CPU\CPU;
use Tourze\NES\CPU\Memory;
use Tourze\NES\CPU\StatusRegister;

/**
 * 跳转和调用指令测试
 */
class JumpCallInstructionsTest extends TestCase
{
    private CPU $cpu;
    private Bus $bus;
    private Memory $memory;

    protected function setUp(): void
    {
        $this->memory = new Memory();
        $this->bus = new Bus();
        $this->bus->connect($this->memory, 'ram', 0x0000, 0xFFFF);
        $this->cpu = new CPU($this->bus);
    }

    /**
     * 测试JMP绝对寻址指令
     */
    public function testJMP_Absolute(): void
    {
        // 设置JMP指令
        $this->memory->write(0x0200, 0x4C); // JMP绝对寻址操作码
        $this->memory->write(0x0201, 0x34); // 低字节
        $this->memory->write(0x0202, 0x12); // 高字节

        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证PC跳转到正确地址
        $this->assertEquals(0x1234, $this->cpu->getRegister('PC')->getValue(), '程序计数器应该跳转到$1234');
    }

    /**
     * 测试JMP间接寻址指令
     */
    public function testJMP_Indirect(): void
    {
        // 设置JMP指令
        $this->memory->write(0x0200, 0x6C); // JMP间接寻址操作码
        $this->memory->write(0x0201, 0x34); // 低字节
        $this->memory->write(0x0202, 0x12); // 高字节

        // 在间接地址处设置实际目标地址
        $this->memory->write(0x1234, 0x78);
        $this->memory->write(0x1235, 0x56);

        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 验证PC跳转到间接寻址的地址
        $this->assertEquals(0x5678, $this->cpu->getRegister('PC')->getValue(), '程序计数器应该跳转到$5678');
    }

    /**
     * 测试JMP间接寻址跨页边界bug
     * JMP ($xxFF) 在6502 CPU中存在一个bug，当地址为页边界时高字节会从错误的地址获取
     */
    public function testJMP_IndirectPageBoundaryBug(): void
    {
        // 设置JMP指令，指向页边界
        $this->memory->write(0x0200, 0x6C); // JMP间接寻址操作码
        $this->memory->write(0x0201, 0xFF); // 低字节 (边界)
        $this->memory->write(0x0202, 0x12); // 高字节

        // 在错误的间接地址和正确的间接地址都设置值
        $this->memory->write(0x12FF, 0x78); // 低字节来自 $12FF
        $this->memory->write(0x1300, 0xAB); // 如果没有bug，高字节应该来自 $1300
        $this->memory->write(0x1200, 0x56); // 但由于bug，高字节会来自 $1200

        $this->cpu->getRegister('PC')->setValue(0x0200);
        $this->cpu->step();

        // 由于bug，应该跳转到 $5678 而不是 $AB78
        $this->assertEquals(0x5678, $this->cpu->getRegister('PC')->getValue(), '应当展示JMP间接寻址的页边界bug');
    }

    /**
     * 测试JSR指令
     */
    public function testJSR(): void
    {
        // 设置初始PC和SP值
        $this->cpu->getRegister('PC')->setValue(0x0200);
        $initialSP = $this->cpu->getRegister('SP')->getValue();

        // 设置JSR指令
        $this->memory->write(0x0200, 0x20); // JSR操作码
        $this->memory->write(0x0201, 0x34); // 低字节
        $this->memory->write(0x0202, 0x12); // 高字节

        $this->cpu->step();

        // 验证跳转到正确的地址
        $this->assertEquals(0x1234, $this->cpu->getRegister('PC')->getValue(), '程序计数器应该跳转到$1234');

        // 验证SP正确减少
        $this->assertEquals($initialSP - 2, $this->cpu->getRegister('SP')->getValue(), '堆栈指针应该减少2');

        // 验证返回地址正确保存到堆栈(存储的是JSR指令的最后一个字节的地址)
        $returnAddressLow = $this->memory->read(0x0100 + $initialSP);
        $returnAddressHigh = $this->memory->read(0x0100 + $initialSP - 1);
        $returnAddress = ($returnAddressHigh << 8) | $returnAddressLow;

        $this->assertEquals(0x0202, $returnAddress, '返回地址应该是JSR指令之后的地址');
    }

    /**
     * 测试RTS指令
     */
    public function testRTS(): void
    {
        // 设置初始堆栈内容，模拟JSR已经执行
        $initialSP = $this->cpu->getRegister('SP')->getValue();

        // 压入返回地址(0x0202) - 堆栈是低字节先入，后高字节
        $this->cpu->pushWord(0x0202); // 实际上JSR会保存PC-1

        // 设置当前PC为子程序地址
        $this->cpu->getRegister('PC')->setValue(0x1234);

        // 设置RTS指令
        $this->memory->write(0x1234, 0x60); // RTS操作码

        $this->cpu->step();

        // 验证PC正确恢复到返回地址+1
        $this->assertEquals(0x0203, $this->cpu->getRegister('PC')->getValue(), '程序计数器应该恢复到返回地址+1');

        // 验证SP正确恢复
        $this->assertEquals($initialSP, $this->cpu->getRegister('SP')->getValue(), '堆栈指针应该恢复初始值');
    }

    /**
     * 测试RTI指令
     */
    public function testRTI(): void
    {
        // 设置初始堆栈内容，模拟中断已经发生
        $initialSP = $this->cpu->getRegister('SP')->getValue();

        // 获取状态寄存器，设置初始状态
        $status = $this->cpu->getRegister('P');
        $this->assertInstanceOf(StatusRegister::class, $status);

        // 清除除未使用标志外的所有标志
        foreach ([
                     StatusRegister::FLAG_NEGATIVE,
                     StatusRegister::FLAG_OVERFLOW,
                     StatusRegister::FLAG_BREAK,
                     StatusRegister::FLAG_DECIMAL,
                     StatusRegister::FLAG_INTERRUPT,
                     StatusRegister::FLAG_ZERO,
                     StatusRegister::FLAG_CARRY
                 ] as $flag) {
            $status->setFlag($flag, false);
        }

        // 记录当前状态寄存器值
        $initialStatusValue = $status->getValue();

        // 正确模拟中断处理程序压栈顺序：
        // 1. 首先压入返回地址高字节
        // 2. 然后压入返回地址低字节
        // 3. 最后压入状态寄存器

        // 压入返回地址(0x0400)
        $this->memory->write(0x0100 | $initialSP, 0x04); // 返回地址高字节
        $this->memory->write(0x0100 | ($initialSP - 1), 0x00); // 返回地址低字节
        $this->memory->write(0x0100 | ($initialSP - 2), 0xFF); // 状态值，所有标志设置

        // 手动调整SP指向状态值
        $this->cpu->getRegister('SP')->setValue($initialSP - 3);

        // 设置当前PC为中断处理程序地址
        $this->cpu->getRegister('PC')->setValue(0x1000);

        // 设置RTI指令
        $this->memory->write(0x1000, 0x40); // RTI操作码

        $this->cpu->step();

        // 验证PC正确恢复到返回地址
        $this->assertEquals(0x0400, $this->cpu->getRegister('PC')->getValue(), '程序计数器应该恢复到返回地址');

        // 验证SP正确恢复
        $this->assertEquals($initialSP, $this->cpu->getRegister('SP')->getValue(), '堆栈指针应该恢复初始值');

        // 验证状态寄存器值正确恢复
        // 注意：RTI会恢复状态寄存器，但会确保B标志未设置，未使用标志始终为1
        $testStatusValue = 0xFF; // 所有标志都设置
        $expectedStatus = $testStatusValue & ~StatusRegister::FLAG_BREAK | StatusRegister::FLAG_UNUSED;
        $this->assertEquals($expectedStatus, $status->getValue(), '状态寄存器应该正确恢复，但B标志应该未设置');
    }
}
