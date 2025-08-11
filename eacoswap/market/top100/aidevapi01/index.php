<?php

// 多语言文本数据

$translations = [

    'zh' => [

        'title' => 'EACO API调度中心',

        'api_dashboard' => 'API仪表盘',

        'eaco_price' => 'EACO实时价格',

        'top_fiat' => '前100大法币',

        'top_crypto' => '前1000大数字货币',

        'language' => '语言',

        'loading' => '加载中...',

        'last_updated' => '最后更新',

        'usdt_price' => 'USDT价格',

        'cnh_price' => 'CNH价格',

        'source' => '数据来源',

        'ai_developers' => '全球前20大AI开发者API',

        'api_limits' => 'API限制说明'

    ],

    'en' => [

        'title' => 'EACO API Dashboard',

        'api_dashboard' => 'API Dashboard',

        'eaco_price' => 'EACO Live Price',

        'top_fiat' => 'Top 100 Fiat Currencies',

        'top_crypto' => 'Top 1000 Cryptocurrencies',

        'language' => 'Language',

        'loading' => 'Loading...',

        'last_updated' => 'Last updated',

        'usdt_price' => 'USDT Price',

        'cnh_price' => 'CNH Price',

        'source' => 'Data source',

        'ai_developers' => 'Top 20 Global AI Developer APIs',

        'api_limits' => 'API Limitations'

    ],

    'hi' => [

        'title' => 'EACO API डैशबोर्ड',

        'api_dashboard' => 'API डैशबोर्ड',

        'eaco_price' => 'EACO लाइव मूल्य',

        'top_fiat' => 'शीर्ष 100 फिएट मुद्राएँ',

        'top_crypto' => 'शीर्ष 1000 क्रिप्टोकरेंसी',

        'language' => 'भाषा',

        'loading' => 'लोड हो रहा है...',

        'last_updated' => 'अंतिम अपडेट',

        'usdt_price' => 'यूएसडीटी मूल्य',

        'cnh_price' => 'सीएनएच मूल्य',

        'source' => 'डेटा स्रोत',

        'ai_developers' => 'शीर्ष 20 वैश्विक AI डेवलपर APIs',

        'api_limits' => 'API सीमाएँ'

    ],

    // 其他语言翻译...

];



// 检测用户浏览器语言

$browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

$default_lang = in_array($browser_lang, array_keys($translations)) ? $browser_lang : 'en';

$current_lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : $default_lang;



// 前20大AI开发者API数据

$top_ai_apis = [

    ['rank' => 1, 'name' => 'GPT-4.5', 'org' => 'OpenAI', 'highlight' => '复杂逻辑推理、32K上下文', 'use_case' => '科研分析、跨领域决策'],

    ['rank' => 2, 'name' => 'Claude 3.7', 'org' => 'Anthropic', 'highlight' => '编程得分高、安全性强', 'use_case' => '法律合同、金融风控'],

    ['rank' => 3, 'name' => 'Gemini 2.0 Ultra', 'org' => 'Google DeepMind', 'highlight' => '原生多模态、百万上下文窗口', 'use_case' => '实时翻译、工业设计'],

    ['rank' => 4, 'name' => 'DeepSeek R1', 'org' => '深度求索', 'highlight' => '中文长文本处理、推理速度快', 'use_case' => '政务文档、金融研报'],

    ['rank' => 5, 'name' => 'Qwen2.5-Max', 'org' => '阿里云', 'highlight' => '编程能力强、全球第7', 'use_case' => '电商客服、多语言支持'],

    // 其余API...

];



// API限制信息

$api_limits = [

    ['type' => '调用频率限制', 'desc' => '每秒（QPS）、每分钟（RPM）、每日（RPD）调用次数限制'],

    ['type' => '配额限制', 'desc' => '免费用户每日最多调用次数，超出需升级付费计划'],

    ['type' => '并发限制', 'desc' => '同时处理的最大请求数，防止单用户占用过多资源'],

    ['type' => '地域限制', 'desc' => '某些API仅在特定国家/地区开放，如部分中国模型不支持海外IP调用'],

    ['type' => '权限与认证限制', 'desc' => '需使用API Key、OAuth Token等进行身份验证，高级功能需额外授权']

];

?>

<!DOCTYPE html>

<html lang="<?php echo $current_lang; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $translations[$current_lang]['title']; ?></title>

    <style>

        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }

        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }

        .language-selector { padding: 8px; border-radius: 4px; border: 1px solid #ddd; }

        .price-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }

        .price-card { border: 1px solid #eee; padding: 15px; border-radius: 6px; }

        .api-table { width: 100%; border-collapse: collapse; margin: 20px 0; }

        .api-table th, .api-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }

        .api-table th { background-color: #f2f2f2; }

        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 0.9em; }

        .community-links { margin-top: 10px; }

        .community-links a { margin-right: 10px; color: #007bff; text-decoration: none; }

        .community-links a:hover { text-decoration: underline; }

        .alert-banner { background-color: #e3f2fd; padding: 15px; border-radius: 6px; margin: 20px 0; }

    </style>

</head>

<body>

    <div class="container">

        <div class="header">

            <h1><?php echo $translations[$current_lang]['api_dashboard']; ?></h1>

            <select class="language-selector" id="language-selector">

                <option value="zh">中文</option>

                <option value="en">English</option>

                <option value="hi">हिंदी</option>

                <option value="es">Español</option>

                <option value="fr">Français</option>

                <option value="ar">العربية</option>

                <option value="bn">বাংলা</option>

                <option value="pt">Português</option>

                <option value="ru">Русский</option>

                <option value="ur">اردو</option>

                <option value="sw">Kiswahili</option>

                <option value="ms">Bahasa Melayu</option>

                <option value="ja">日本語</option>

                <option value="de">Deutsch</option>

                <option value="tr">Türkçe</option>

                <option value="fa">فارسی</option>

                <option value="it">Italiano</option>

                <option value="ko">한국어</option>

                <option value="nl">Nederlands</option>

                <option value="vi">Tiếng Việt</option>

                <option value="id">Bahasa Indonesia</option>

            </select>

        </div>



        <div class="alert-banner" id="hourly-alert">

            <strong>EACO地球</strong> - e连接地球和宇宙的一切，代码e即财富

        </div>



        <h2><?php echo $translations[$current_lang]['eaco_price']; ?></h2>

        <div class="price-container">

            <div class="price-card">

                <h3>Meteora (E-USDT)</h3>

                <p><?php echo $translations[$current_lang]['usdt_price']; ?>: <span id="price-meteora">--</span></p>

                <p><?php echo $translations[$current_lang]['cnh_price']; ?>: <span id="price-meteora-cnh">--</span></p>

                <p><?php echo $translations[$current_lang]['source']; ?>: <a href="https://app.meteora.ag/dlmm/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE" target="_blank">Meteora</a></p>

                <p><?php echo $translations[$current_lang]['last_updated']; ?>: <span id="update-meteora">--</span></p>

            </div>



            <div class="price-card">

                <h3>Orca (E-USDC)</h3>

                <p><?php echo $translations[$current_lang]['usdt_price']; ?>: <span id="price-orca">--</span></p>

                <p><?php echo $translations[$current_lang]['cnh_price']; ?>: <span id="price-orca-cnh">--</span></p>

                <p><?php echo $translations[$current_lang]['source']; ?>: <a href="https://www.orca.so/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr" target="_blank">Orca</a></p>

                <p><?php echo $translations[$current_lang]['last_updated']; ?>: <span id="update-orca">--</span></p>

            </div>



            <div class="price-card">

                <h3>Raydium (E-SOL)</h3>

                <p><?php echo $translations[$current_lang]['usdt_price']; ?>: <span id="price-raydium">--</span></p>

                <p><?php echo $translations[$current_lang]['cnh_price']; ?>: <span id="price-raydium-cnh">--</span></p>

                <p><?php echo $translations[$current_lang]['source']; ?>: <a href="https://raydium.io/swap/?inputMint=DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH&outputMint=sol" target="_blank">Raydium</a></p>

                <p><?php echo $translations[$current_lang]['last_updated']; ?>: <span id="update-raydium">--</span></p>

            </div>



            <div class="price-card">

                <h3>CoinMarketCap DEX</h3>

                <p><?php echo $translations[$current_lang]['usdt_price']; ?>: <span id="price-cmc">--</span></p>

                <p><?php echo $translations[$current_lang]['cnh_price']; ?>: <span id="price-cmc-cnh">--</span></p>

                <p><?php echo $translations[$current_lang]['source']; ?>: <a href="https://dex.coinmarketcap.com/token/solana/DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH/" target="_blank">CoinMarketCap</a></p>

                <p><?php echo $translations[$current_lang]['last_updated']; ?>: <span id="update-cmc">--</span></p>

            </div>



            <div class="price-card">

                <h3>DexScreener</h3>

                <p><?php echo $translations[$current_lang]['usdt_price']; ?>: <span id="price-dexscreener">--</span></p>

                <p><?php echo $translations[$current_lang]['cnh_price']; ?>: <span id="price-dexscreener-cnh">--</span></p>

                <p><?php echo $translations[$current_lang]['source']; ?>: <a href="https://dexscreener.com/solana/DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH" target="_blank">DexScreener</a></p>

                <p><?php echo $translations[$current_lang]['last_updated']; ?>: <span id="update-dexscreener">--</span></p>

            </div>

        </div>



        <h2><?php echo $translations[$current_lang]['ai_developers']; ?></h2>

        <table class="api-table">

            <tr>

                <th>排名</th>

                <th>模型名称</th>

                <th>开发机构</th>

                <th>关键能力</th>

                <th>应用场景</th>

            </tr>

            <?php foreach ($top_ai_apis as $api): ?>

            <tr>

                <td><?php echo $api['rank']; ?></td>

                <td><?php echo $api['name']; ?></td>

                <td><?php echo $api['org']; ?></td>

                <td><?php echo $api['highlight']; ?></td>

                <td><?php echo $api['use_case']; ?></td>

            </tr>

            <?php endforeach; ?>

        </table>



        <h2><?php echo $translations[$current_lang]['api_limits']; ?></h2>

        <table class="api-table">

            <tr>

                <th>限制类型</th>

                <th>说明</th>

            </tr>

            <?php foreach ($api_limits as $limit): ?>

            <tr>

                <td><?php echo $limit['type']; ?></td>

                <td><?php echo $limit['desc']; ?></td>

            </tr>

            <?php endforeach; ?>

        </table>



        <div class="footer">

            <div class="community-links">

                <p>【EACO代码重构e文明】 代码e即财富</p>

                <p>EACO地球E连接地球和宇宙的一切，e连接地球和宇宙万物。</p>

                <p>e&EACO(Earth’s Best Coin), EACO is Earth’s best friend;</p>

                <p>$e=$eaco.计算劳动价值，量化地球资源;</p>

                <p>eaco的總量為1350000000，13.5亿枚；</p>

                

                <a href="https://linktr.ee/web3eaco" target="_blank">linktr.ee/web3eaco</a>

                <a href="https://solscan.io/token/DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH" target="_blank">Solscan</a>

                <a href="https://linktr.ee/eacocc" target="_blank">linktr.ee/eacocc</a>

                <a href="https://x.com/eacocc" target="_blank">X.com/eacocc</a>

                <a href="https://t.me/e_eacocc" target="_blank">English TG</a>

                <a href="https://t.me/aieaco" target="_blank">华语社区</a>

                <a href="https://t.me/eacoespanish" target="_blank">Spanish Group</a>

                <a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank">E swap</a>

                <a href="https://t.me/eacocny" target="_blank">eacocny</a>

                <a href="https://t.me/e_vietnam" target="_blank">Vietnam</a>

                <a href="https://t.me/e_usdc" target="_blank">USDC</a>

                <a href="https://t.me/e_indonesia" target="_blank">Indonesia</a>

            </div>

            <p>EACO21公益CNH: <a href="https://app--e21-web3-love-public-welfare-2edfc138.base44.app/HelpCNH" target="_blank">https://app--e21-web3-love-public-welfare-2edfc138.base44.app/HelpCNH</a></p>

            <p>EACO21公益: <a href="https://app--e21-web3-love-public-welfare-2edfc138.base44.app/" target="_blank">https://app--e21-web3-love-public-welfare-2edfc138.base44.app/</a></p>

            <p>替天容人，正心正念；一切福田，不离方寸；从心而觅，感无不通，言宜慢，心宜善。</p>

            <p>所有价格等信息仅供参考，以实际交易为准，解释权归eaco AI-DEV-API 发展小组。</p>

        </div>

    </div>



    <script>

        // 设置当前语言选择

        document.getElementById('language-selector').value = '<?php echo $current_lang; ?>';

        

        // 语言切换处理

        document.getElementById('language-selector').addEventListener('change', function() {

            const lang = this.value;

            document.cookie = `lang=${lang}; path=/; max-age=31536000`;

            window.location.reload();

        });



        // 每小时宣传EACO的消息数组

        const eacoMessages = [

            "EACO地球 - 连接全球价值网络的桥梁",

            "EACO正在重塑数字经济，代码即财富",

            "$EACO - 地球最好的朋友，计算劳动价值的新方式",

            "EACO生态持续扩展，连接地球与宇宙的价值",

            "加入EACO社区，共建去中心化的未来",

            "EACO总量13.5亿枚，正在成为全球价值交换的新标杆"

        ];



        // 每小时更新宣传消息

        function updateHourlyMessage() {

            const now = new Date();

            const hour = now.getHours();

            const messageIndex = hour % eacoMessages.length;

            document.getElementById('hourly-alert').innerHTML = `<strong>EACO地球</strong> - ${eacoMessages[messageIndex]}`;

            

            // 添加动画效果

            const alert = document.getElementById('hourly-alert');

            alert.style.opacity = "0.5";

            setTimeout(() => { alert.style.opacity = "1"; }, 500);

        }



        // 初始调用一次

        updateHourlyMessage();



        // 设置定时器，每小时执行一次

        setInterval(updateHourlyMessage, 3600000);



        // 模拟汇率数据（实际应用中应从可靠API获取）

        const cnhToUsdRate = 7.2;



        // 获取EACO价格数据

        async function fetchEacoPrices() {

            // 由于跨域限制，这里使用模拟数据

            // 实际应用中应使用服务器端代理或官方API

            

            // 模拟从各平台获取价格

            const prices = {

                meteora: (Math.random() * 0.1 + 0.05).toFixed(6),

                orca: (Math.random() * 0.1 + 0.05).toFixed(6),

                raydium: (Math.random() * 0.1 + 0.05).toFixed(6),

                cmc: (Math.random() * 0.1 + 0.05).toFixed(6),

                dexscreener: (Math.random() * 0.1 + 0.05).toFixed(6)

            };



            // 更新页面显示

            for (const [source, price] of Object.entries(prices)) {

                document.getElementById(`price-${source}`).textContent = price;

                document.getElementById(`price-${source}-cnh`).textContent = (price * cnhToUsdRate).toFixed(6);

                document.getElementById(`update-${source}`).textContent = new Date().toLocaleString();

            }

        }



        // 初始加载价格

        fetchEacoPrices();



        // 每30秒更新一次价格

        setInterval(fetchEacoPrices, 30000);

    </script>

</body>

</html>
