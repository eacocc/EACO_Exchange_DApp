eaco api
以下是获取EACO在三个平台（Meteora、Orca、Raydium）的实时价格API实现方案，支持自动更新并转换为USDT、CNH等格式展示：





### 功能说明

这个Python工具实现了从三个指定平台获取EACO实时价格的功能，并提供多格式展示：

1. **数据来源**：
   - Meteora：获取EACO/USDT价格（通过`6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE`池）
   - Orca：获取EACO/USDC价格（通过`Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr`池）
   - Raydium：获取EACO/SOL价格（通过SOL和EACO的交易对）

2. **核心功能**：
   - 自动计算并转换为USDT计价
   - 获取USD/CNH汇率，计算EACO的人民币价格
   - 计算三个平台的平均价格作为参考
   - 定时更新（默认30秒一次）
   - 支持JSON格式输出，便于前端展示

3. **使用方法**：
   - 直接运行脚本即可启动价格监控
   - 程序会自动打印格式化的价格信息
   - 可通过`get_price_json()`函数获取JSON数据用于网页展示
   - 可调整更新间隔（默认30秒）

4. **扩展建议**：
   - 可将数据存储到数据库，用于历史价格分析
   - 增加价格变动告警功能
   - 集成到Web框架（如Flask/Django）提供API接口
   - 添加异常处理和重试机制，提高稳定性

该工具通过各平台的公开API获取数据，无需API密钥即可使用，适合作为前端展示EACO实时价格的后端支持。
