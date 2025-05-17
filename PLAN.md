# MOS 6502 CPU 模拟器 - 开发计划

## 项目概述

使用PHP实现完整的MOS 6502 CPU模拟器，提供精确的CPU行为模拟，包括指令执行、寻址模式、寄存器操作等功能。

### 项目目标

1. 实现周期精确的MOS 6502 CPU模拟
2. 提供友好的API以便集成到其他项目中
3. 达到足够的性能以实时模拟经典系统
4. 提供调试和分析工具

### 环境需求

- PHP 8.1+
- Composer
- PHPUnit 10.0+
- PHPStan 2.1+

## CPU架构概述

MOS 6502是一个8位微处理器，具有以下特点：

- 8位数据总线
- 16位地址总线（可寻址64KB内存）
- 简单的寄存器集合
- 56个基本指令（包括13个非法操作码共151个总操作码）
- 多种寻址模式
- 1-7个时钟周期的指令执行
- 缺页惩罚（跨页面边界增加额外周期）
- 硬件栈限制在页面1 ($0100-$01FF)
- 特殊硬件行为和已知硬件bug

### 中断和向量

- 重置向量 ($FFFC-$FFFD)：系统启动时CPU读取的地址
- 不可屏蔽中断(NMI)向量 ($FFFA-$FFFB)
- 中断请求(IRQ)和中断指令(BRK)向量 ($FFFE-$FFFF)
- 中断处理有固定的周期计数和状态变更序列

### 主要寄存器

- 累加器(A): 8位，主要算术运算寄存器
- 索引寄存器X(X): 8位，用于索引寻址和循环计数
- 索引寄存器Y(Y): 8位，用于索引寻址和循环计数
- 程序计数器(PC): 16位，指向下一条要执行的指令
- 堆栈指针(SP): 8位，指向堆栈中的下一个可用位置（堆栈限制在页面1，即$0100-$01FF）
- 状态寄存器(P): 8位，包含标志位（N, V, B, D, I, Z, C）
  - N: 负数标志 (bit 7)
  - V: 溢出标志 (bit 6)
  - -: 未使用，总是1 (bit 5)
  - B: 中断标志 (bit 4)
  - D: 十进制模式标志 (bit 3)
  - I: 中断禁用标志 (bit 2)
  - Z: 零标志 (bit 1)
  - C: 进位标志 (bit 0)

## 指令集详情

### 指令分类

1. **数据传输指令**
   - LDA, LDX, LDY：加载到寄存器
   - STA, STX, STY：从寄存器存储
   - TAX, TAY, TSX, TXA, TXS, TYA：寄存器之间传输

2. **算术指令**
   - ADC：带进位加法（支持十进制模式）
   - SBC：带借位减法（支持十进制模式）
   - INC, INX, INY：增量操作
   - DEC, DEX, DEY：减量操作

3. **逻辑指令**
   - AND：位与
   - EOR：位异或
   - ORA：位或
   - BIT：位测试

4. **移位指令**
   - ASL：算术左移
   - LSR：逻辑右移
   - ROL：带进位循环左移
   - ROR：带进位循环右移

5. **标志操作指令** ✅
   - CLC, CLD, CLI, CLV：清除标志
   - SEC, SED, SEI：设置标志

6. **比较指令**
   - CMP, CPX, CPY：寄存器与内存比较

7. **分支指令** ✅
   - BCC, BCS, BEQ, BMI, BNE, BPL, BVC, BVS：条件分支
   - 分支成功时增加1-2个周期（跨页时+2）

8. **跳转指令** ✅
   - JMP：无条件跳转（存在间接寻址bug）
   - JSR：跳转到子程序
   - RTS：从子程序返回
   - RTI：从中断返回

9. **其他指令** ✅
   - BRK：软件中断
   - NOP：无操作
   - PHA, PHP：压栈
   - PLA, PLP：出栈

10. **非法操作码**
    - 未公开但可用的指令（如LAX, SAX, DCP, ISC等）
    - 组合了多个合法指令的行为
    - 需模拟读-修改-写指令的特殊硬件行为

### 特殊硬件行为

1. **页面边界错误**
   - JMP ($xxFF) 间接寻址在跨页时的错误实现
   - 不正确地从 ($xx00) 而不是 ($xx00 + 1) 获取高字节

2. **读-修改-写操作**
   - 某些指令（如INC, DEC, ASL, LSR, ROL, ROR）
   - 执行顺序：读取、修改、写回（有特殊时序）
   - 可能对硬件寄存器产生副作用

3. **周期计数差异**
   - 跨页面边界的额外周期
   - 分支成功/失败的周期差异
   - 各指令在不同寻址模式下的精确周期数

## 类设计

### 核心类

#### `CPU`

- ✅ 职责：表示6502 CPU的核心功能
- ✅ 属性：
  - 寄存器（A, X, Y, PC, SP）
  - 状态寄存器
  - 当前周期计数
  - 总周期计数
  - 中断状态标志
  - 时钟周期精确计数器
- ✅ 方法：
  - `reset()`: 重置CPU到初始状态
  - `step()`: 执行一条指令（基础结构已完成）
  - `interrupt()`: 处理中断（基础结构已完成）
  - `nmi()`: 处理不可屏蔽中断（基础结构已完成）
  - `irq()`: 处理可屏蔽中断（基础结构已完成）
  - `getRegister($name)`: 获取指定寄存器
  - `getCycleCount()`: 获取当前周期计数
  - `getInstructionsExecuted()`: 获取已执行指令数
  - `push($value)`: 将值推入堆栈
  - `pull()`: 从堆栈拉出值
  - `pushWord($value)`: 将16位值推入堆栈
  - `pullWord()`: 从堆栈拉出16位值
  - `handleDecimalMode($a, $b, $carry)`: 处理十进制模式运算
- ⏳ 待实现：
  - 具体指令执行逻辑
  - ✅ 各种寻址模式的处理
  - 完整的中断处理机制

#### `Memory`

- ✅ 职责：管理和模拟64KB内存空间
- ✅ 属性：
  - 内存数组（表示64KB内存）
  - 内存访问计数器
- ✅ 方法：
  - `read($address)`: 从指定地址读取一个字节
  - `write($address, $value)`: 向指定地址写入一个字节
  - `readWord($address)`: 读取一个16位字（小端序）
  - `writeWord($address, $value)`: 写入一个16位字（小端序）
  - `load($address, array $data)`: 加载一块数据到内存
  - `dump($startAddress, $length)`: 转储一块内存区域
  - `reset()`: 重置内存为初始状态

#### `Bus`

- ✅ 职责：连接CPU和其他组件（如内存和I/O设备）
- ✅ 属性：
  - 已连接的组件列表
  - 地址映射表
- ✅ 方法：
  - `read($address)`: 从总线上的地址读取数据
  - `write($address, $value)`: 向总线上的地址写入数据
  - `connect($component, $addressRange)`: 将组件连接到总线上的特定地址范围
  - `disconnect($component)`: 从总线上断开组件
  - `getConnectedDevices()`: 获取所有已连接设备列表

### 指令和寻址模式

#### `InstructionSet`

- ✅ 职责：定义和管理所有CPU指令
- ✅ 属性：
  - 指令映射表 (操作码 => 指令实例)
  - 指令统计信息
- ✅ 方法：
  - `getInstruction($opcode)`: 根据操作码获取指令处理器
  - `execute($opcode, $cpu, $bus)`: 执行特定操作码的指令
  - `getInstructionInfo($opcode)`: 获取指令信息
  - `getInstructionStats()`: 获取指令使用统计
  - `registerInstruction($opcode, $instruction)`: 注册自定义指令

#### `Instruction`

- ✅ 职责：表示单个CPU指令
- ✅ 属性：
  - 操作码
  - 助记符
  - 字节大小
  - 基础周期数
  - 寻址模式
  - 指令描述
- ✅ 方法：
  - `execute($cpu, $bus)`: 执行指令
  - `getCycles()`: 获取指令需要的周期数
  - `addCycles($condition)`: 根据条件增加周期数
  - `getOpcode()`: 获取操作码
  - `getMnemonic()`: 获取助记符
  - `getDescription()`: 获取指令描述

#### `AddressingMode` (接口)

- ✅ 职责：定义寻址模式的通用接口
- ✅ 方法：
  - `getOperandAddress($cpu, $bus)`: 获取操作数地址
  - `getBytes()`: 获取此寻址模式使用的字节数
  - `getOperandValue($cpu, $bus)`: 获取操作数值
  - `getCrossesPageBoundary()`: 检查是否跨页边界
  - `getName()`: 获取寻址模式名称

以下是具体的寻址模式实现类：

- ✅ `ImpliedAddressing`: 隐含寻址，如CLC
- ✅ `AccumulatorAddressing`: 累加器寻址，如ASL A
- ✅ `ImmediateAddressing`: 立即寻址，如LDA #$10
- ✅ `ZeroPageAddressing`: 零页寻址，如LDA $10
- ✅ `ZeroPageXAddressing`: 零页X索引，如LDA $10,X
- ✅ `ZeroPageYAddressing`: 零页Y索引，如LDX $10,Y
- ✅ `AbsoluteAddressing`: 绝对寻址，如JMP $1234
- ✅ `AbsoluteXAddressing`: 绝对X索引，如LDA $1234,X
- ✅ `AbsoluteYAddressing`: 绝对Y索引，如LDA $1234,Y
- ✅ `IndirectAddressing`: 间接寻址，如JMP ($1234)
- ✅ `IndirectXAddressing`: 前索引间接，如LDA ($10,X)
- ✅ `IndirectYAddressing`: 后索引间接，如LDA ($10),Y
- ✅ `RelativeAddressing`: 相对寻址，如BNE $10

### 寄存器和标志

#### `Register`

- ✅ 职责：表示CPU的通用寄存器
- ✅ 属性：
  - 值
  - 位数（8或16）
  - 最小值
  - 最大值
  - 名称
- ✅ 方法：
  - `getValue()`: 获取寄存器值
  - `setValue($value)`: 设置寄存器值
  - `increment($amount = 1)`: 增加寄存器值
  - `decrement($amount = 1)`: 减少寄存器值
  - `getBitCount()`: 获取寄存器位数
  - `reset()`: 重置寄存器到初始值

#### `StatusRegister`

- ✅ 职责：管理CPU的状态标志
- ✅ 属性：
  - 标志位（N, V, -, B, D, I, Z, C）
  - 标志位掩码常量
- ✅ 方法：
  - `getFlag($flag)`: 获取特定标志的状态
  - `setFlag($flag, $value)`: 设置特定标志的状态
  - `updateNegativeFlag($value)`: 根据值更新负标志
  - `updateZeroFlag($value)`: 根据值更新零标志
  - `updateOverflowFlag($value, $operand, $result)`: 更新溢出标志
  - `updateCarryFlag($result)`: 更新进位标志
  - `getValue()`: 获取完整的状态寄存器值
  - `setValue($value)`: 设置完整的状态寄存器值
  - `reset()`: 重置所有标志为初始状态
  - `getFlagNames()`: 获取所有标志名称

### 辅助类

#### `Emulator`

- ⏳ 职责：组合和协调所有组件以提供完整的模拟体验
- ⏳ 属性：
  - CPU实例
  - 内存实例
  - 总线实例
  - 运行状态
  - 事件监听器
- ⏳ 方法：
  - `loadProgram($memoryAddress, $program)`: 将程序加载到内存
  - `loadProgramFile($memoryAddress, $filePath)`: 从文件加载程序
  - `start()`: 开始模拟
  - `stop()`: 停止模拟
  - `step()`: 执行单个指令
  - `run($cycles)`: 运行指定的周期数
  - `runUntil($address)`: 运行直到PC到达指定地址
  - `getState()`: 获取当前CPU和内存状态
  - `addEventListener($eventType, $callback)`: 添加事件监听器
  - `removeEventListener($eventType, $callback)`: 移除事件监听器
  - `reset()`: 重置模拟器状态

#### `Disassembler`

- ⏳ 职责：将机器码转换为可读的汇编代码
- ⏳ 属性：
  - 指令集引用
  - 格式化选项
- ⏳ 方法：
  - `disassemble($memory, $startAddress, $length)`: 反汇编指定内存区域的代码
  - `disassembleInstruction($memory, $address)`: 反汇编单条指令
  - `formatInstruction($address, $bytes, $mnemonic, $operand)`: 格式化指令输出
  - `setFormatOptions(array $options)`: 设置格式化选项

#### `Debugger`

- ⏳ 职责：提供调试功能，如断点和状态检查
- ⏳ 属性：
  - 断点列表
  - CPU引用
  - 内存引用
  - 监视变量
  - 日志
- ⏳ 方法：
  - `addBreakpoint($address)`: 添加断点
  - `removeBreakpoint($address)`: 移除断点
  - `hasBreakpoint($address)`: 检查地址是否有断点
  - `addWatchpoint($address, $callback)`: 添加内存监视点
  - `step()`: 单步执行
  - `stepOver()`: 单步跳过
  - `stepOut()`: 单步跳出
  - `run()`: 运行直到断点
  - `pause()`: 暂停运行
  - `getCPUState()`: 获取CPU状态（寄存器、标志等）
  - `getMemoryDump($startAddress, $length)`: 获取内存转储
  - `getCallStack()`: 获取调用堆栈
  - `getExecutionTrace($length)`: 获取执行轨迹
