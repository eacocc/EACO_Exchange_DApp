<?php
/**
 * EACO全球汇率监控系统 - 单文件完整版
 * 无需数据库，纯API驱动，开箱即用
 * 支持多语言、主题切换、缓存、错误处理
 */

// 设置响应头
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// 获取语言参数
$lang = $_GET['lang'] ?? 'zh';
$languages = [
    'zh' => '中文',
    'en' => 'English',
    'es' => 'Español',
    'fr' => 'Français',
    'de' => 'Deutsch',
    'ru' => 'Русский',
    'ar' => 'العربية',
    'hi' => 'हिन्दी',
    'pt' => 'Português',
    'bn' => 'বাংলা'
];

// 多语言翻译
$translations = [
    'zh' => [
        'title' => 'EACO全球汇率监控',
        'last_updated' => '最后更新',
        'theme' => '主题',
        'light' => '明亮',
        'dark' => '黑暗',
        'price_usd' => '价格 (USD)',
        'price_cnh' => '价格 (CNH)',
        'eaco_value' => '可兑换 EACO',
        'volume' => '24h 交易量',
        'rank' => '排名',
        'symbol' => '代币',
        'name' => '名称',
        'error_message' => '加载数据时出错，请稍后再试',
        'loading_data' => '正在加载数据...',
        'api_status' => 'API状态',
        'top_crypto' => '前10大加密货币',
        'refresh' => '刷新数据',
        'eaco_info' => 'EACO信息',
        'eaco_rate' => 'EACO/USD汇率',
        'network_status' => '网络状态'
    ],
    'en' => [
        'title' => 'EACO Global Exchange Rate Monitor',
        'last_updated' => 'Last Updated',
        'theme' => 'Theme',
        'light' => 'Light',
        'dark' => 'Dark',
        'price_usd' => 'Price (USD)',
        'price_cnh' => 'Price (CNH)',
        'eaco_value' => 'Convertible to EACO',
        'volume' => '24h Volume',
        'rank' => 'Rank',
        'symbol' => 'Symbol',
        'name' => 'Name',
        'error_message' => 'Error loading data, please try again',
        'loading_data' => 'Loading data...',
        'api_status' => 'API Status',
        'top_crypto' => 'Top 10 Cryptocurrencies',
        'refresh' => 'Refresh Data',
        'eaco_info' => 'EACO Information',
        'eaco_rate' => 'EACO/USD Rate',
        'network_status' => 'Network Status'
    ]
];

$currentLang = $translations[$lang] ?? $translations['zh'];

// EACO 基准汇率 (可从API获取)
$EACO_USD_RATE = 0.0032;

class EACOMonitor {
    private $cacheDir;
    private $cacheTime = 300; // 5分钟缓存
    private $apiTimeout = 10;
    private $retryAttempts = 3;
    
    public function __construct() {
        // 创建临时缓存目录
        $this->cacheDir = sys_get_temp_dir() . '/eaco_cache/';
        if (!file_exists($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * 获取加密货币数据
     */
    public function getCryptoData() {
        $cacheFile = $this->cacheDir . 'crypto_data.json';
        
        // 检查缓存
        if ($this->isCacheValid($cacheFile)) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        // 多个API源，实现故障转移
        $apiSources = [
            [
                'url' => 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=50&page=1&sparkline=false',
                'parser' => [$this, 'parseCoinGecko']
            ],
            [
                'url' => 'https://api.coinpaprika.com/v1/tickers?quotes=USD',
                'parser' => [$this, 'parseCoinPaprika']
            ],
            [
                'url' => 'https://api.binance.com/api/v3/ticker/24hr',
                'parser' => [$this, 'parseBinance']
            ]
        ];
        
        foreach ($apiSources as $source) {
            $data = $this->fetchWithRetry($source['url']);
            if ($data) {
                $parsedData = call_user_func($source['parser'], $data);
                if ($parsedData && count($parsedData) > 0) {
                    file_put_contents($cacheFile, json_encode($parsedData));
                    return $parsedData;
                }
            }
        }
        
        // 返回空数据或默认数据
        return $this->getDefaultCryptoData();
    }
    
    /**
     * 获取汇率数据
     */
    public function getExchangeRate($base = 'USD', $target = 'CNY') {
        $cacheFile = $this->cacheDir . "exchange_{$base}_{$target}.json";
        
        // 检查缓存
        if ($this->isCacheValid($cacheFile)) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            return $cached['rate'] ?? $this->getDefaultRate($target);
        }
        
        $apiSources = [
            "https://api.exchangerate-api.com/v4/latest/{$base}",
            "https://api.fixer.io/latest?access_key=demo&base={$base}",
            "https://free.currconv.com/api/v7/convert?q={$base}_{$target}&compact=ultra&apiKey=demo"
        ];
        
        foreach ($apiSources as $url) {
            $data = $this->fetchWithRetry($url);
            if ($data) {
                $rate = $this->extractRate($data, $base, $target, $url);
                if ($rate) {
                    $cacheData = ['rate' => $rate, 'timestamp' => time()];
                    file_put_contents($cacheFile, json_encode($cacheData));
                    return $rate;
                }
            }
        }
        
        return $this->getDefaultRate($target);
    }
    
    /**
     * 带重试机制的API请求
     */
    private function fetchWithRetry($url) {
        for ($i = 0; $i < $this->retryAttempts; $i++) {
            $result = $this->fetchData($url);
            if ($result) {
                return $result;
            }
            // 指数退避
            usleep(pow(2, $i) * 100000);
        }
        return false;
    }
    
    /**
     * 基础API请求
     */
    private function fetchData($url) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->apiTimeout,
            CURLOPT_USERAGENT => 'EACO-Monitor/1.0',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response && $httpCode === 200) {
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }
        
        return false;
    }
    
    /**
     * 解析CoinGecko数据
     */
    private function parseCoinGecko($data) {
        $result = [];
        foreach ($data as $item) {
            if (isset($item['symbol'])) {
                $result[] = [
                    'id' => $item['id'] ?? '',
                    'name' => $item['name'] ?? '',
                    'symbol' => $item['symbol'] ?? '',
                    'current_price' => $item['current_price'] ?? 0,
                    'market_cap_rank' => $item['market_cap_rank'] ?? 0,
                    'total_volume' => $item['total_volume'] ?? 0,
                    'price_change_percentage_24h' => $item['price_change_percentage_24h'] ?? 0,
                    'image' => $item['image'] ?? ''
                ];
            }
        }
        return $result;
    }
    
    /**
     * 解析CoinPaprika数据
     */
    private function parseCoinPaprika($data) {
        $result = [];
        foreach ($data as $item) {
            if (isset($item['symbol'])) {
                $result[] = [
                    'id' => $item['id'] ?? '',
                    'name' => $item['name'] ?? '',
                    'symbol' => strtolower($item['symbol']) ?? '',
                    'current_price' => $item['quotes']['USD']['price'] ?? 0,
                    'market_cap_rank' => $item['rank'] ?? 0,
                    'total_volume' => $item['quotes']['USD']['volume_24h'] ?? 0,
                    'price_change_percentage_24h' => $item['quotes']['USD']['percent_change_24h'] ?? 0,
                    'image' => ''
                ];
            }
        }
        return $result;
    }
    
    /**
     * 解析Binance数据
     */
    private function parseBinance($data) {
        $result = [];
        $usdtPairs = array_filter($data, function($item) {
            return strpos($item['symbol'], 'USDT') !== false;
        });
        
        usort($usdtPairs, function($a, $b) {
            return $b['quoteVolume'] <=> $a['quoteVolume'];
        });
        
        $count = 0;
        foreach ($usdtPairs as $item) {
            if ($count >= 50) break;
            $symbol = str_replace('USDT', '', $item['symbol']);
            $result[] = [
                'id' => $symbol,
                'name' => $symbol,
                'symbol' => strtolower($symbol),
                'current_price' => floatval($item['lastPrice']),
                'market_cap_rank' => $count + 1,
                'total_volume' => floatval($item['quoteVolume']),
                'price_change_percentage_24h' => floatval($item['priceChangePercent']),
                'image' => ''
            ];
            $count++;
        }
        
        return $result;
    }
    
    /**
     * 提取汇率
     */
    private function extractRate($data, $base, $target, $url) {
        if (strpos($url, 'exchangerate-api') !== false && isset($data['rates'][$target])) {
            return floatval($data['rates'][$target]);
        } elseif (strpos($url, 'fixer.io') !== false && isset($data['rates'][$target])) {
            return floatval($data['rates'][$target]);
        } elseif (strpos($url, 'currconv.com') !== false && isset($data["{$base}_{$target}"])) {
            return floatval($data["{$base}_{$target}"]);
        }
        
        return null;
    }
    
    /**
     * 检查缓存是否有效
     */
    private function isCacheValid($cacheFile) {
        return file_exists($cacheFile) && 
               (time() - filemtime($cacheFile) < $this->cacheTime);
    }
    
    /**
     * 获取默认加密货币数据
     */
    private function getDefaultCryptoData() {
        return [
            [
                'id' => 'bitcoin',
                'name' => 'Bitcoin',
                'symbol' => 'btc',
                'current_price' => 60000,
                'market_cap_rank' => 1,
                'total_volume' => 20000000000,
                'price_change_percentage_24h' => 2.5,
                'image' => ''
            ],
            [
                'id' => 'ethereum',
                'name' => 'Ethereum',
                'symbol' => 'eth',
                'current_price' => 3000,
                'market_cap_rank' => 2,
                'total_volume' => 15000000000,
                'price_change_percentage_24h' => 1.8,
                'image' => ''
            ]
        ];
    }
    
    /**
     * 获取默认汇率
     */
    private function getDefaultRate($target) {
        $defaults = [
            'CNY' => 7.2,
            'EUR' => 0.92,
            'JPY' => 150,
            'KRW' => 1300
        ];
        return $defaults[$target] ?? 1.0;
    }
}

// 初始化监控器
$monitor = new EACOMonitor();

// 获取数据
$cryptoData = $monitor->getCryptoData();
$cnhRate = $monitor->getExchangeRate('USD', 'CNY');
$eurRate = $monitor->getExchangeRate('USD', 'EUR');
$jpyRate = $monitor->getExchangeRate('USD', 'JPY');

// 计算EACO相关数据
function calculateEACOValue($usdPrice, $eacoUsdRate = 0.0032) {
    return $usdPrice / $eacoUsdRate;
}

// 检查API状态
$apiStatus = !empty($cryptoData) ? 'OK' : 'Error';
$lastUpdated = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentLang['title']); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-color: #f5f7fa;
            --text-color: #333;
            --border-color: #ddd;
            --header-bg: #4a90e2;
            --header-text: white;
            --card-bg: white;
            --accent-color: #4a90e2;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --error-color: #e74c3c;
        }
        
        .dark-theme {
            --bg-color: #1a1a1a;
            --text-color: #e0e0e0;
            --border-color: #444;
            --header-bg: #2c3e50;
            --card-bg: #2d3436;
            --accent-color: #00b894;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            transition: all 0.3s ease;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        header {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .theme-toggle {
            display: flex;
            gap: 12px;
        }
        
        button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
        }
        
        th {
            background-color: var(--header-bg);
            color: var(--header-text);
            font-weight: 600;
            font-size: 15px;
        }
        
        tr:hover {
            background-color: rgba(74, 144, 226, 0.1);
        }
        
        .chart-container {
            height: 400px;
            margin: 30px 0;
            background-color: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .info-box {
            background-color: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }
        
        .info-box h3 {
            margin-bottom: 15px;
            color: var(--accent-color);
            font-size: 18px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed var(--border-color);
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .language-selector {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .language-selector a {
            color: var(--header-text);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            background-color: rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .language-selector a:hover {
            background-color: rgba(255,255,255,0.4);
            transform: translateY(-2px);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-ok {
            background-color: var(--success-color);
            color: white;
        }
        
        .status-error {
            background-color: var(--error-color);
            color: white;
        }
        
        .refresh-btn {
            background-color: var(--warning-color);
        }
        
        .refresh-btn:hover {
            background-color: #e67e22;
        }
        
        footer {
            text-align: center;
            margin-top: 50px;
            color: var(--text-color);
            opacity: 0.8;
            font-size: 14px;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .eaco-links {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        
        .eaco-links a {
            color: var(--accent-color);
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid var(--accent-color);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .eaco-links a:hover {
            background-color: var(--accent-color);
            color: white;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }
            
            .theme-toggle, .language-selector {
                justify-content: center;
            }
            
            th, td {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .chart-container {
                height: 300px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="light-theme">
    <div class="container">
        <header>
            <div class="header-content">
                <h1><?php echo htmlspecialchars($currentLang['title']); ?></h1>
                <div class="controls">
                    <div class="language-selector">
                        <?php foreach ($languages as $code => $name): ?>
                            <a href="?lang=<?php echo $code; ?>"><?php echo $name; ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="theme-toggle">
                        <button onclick="setTheme('light')"><?php echo htmlspecialchars($currentLang['light']); ?></button>
                        <button onclick="setTheme('dark')"><?php echo htmlspecialchars($currentLang['dark']); ?></button>
                        <button onclick="refreshData()" class="refresh-btn">🔄 <?php echo htmlspecialchars($currentLang['refresh']); ?></button>
                    </div>
                </div>
            </div>
        </header>

        <?php if ($apiStatus === 'Error'): ?>
            <div class="info-box" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <p><strong>⚠️ <?php echo htmlspecialchars($currentLang['error_message']); ?></strong></p>
                <p><?php echo htmlspecialchars($currentLang['loading_data']); ?></p>
            </div>
        <?php endif; ?>

        <div class="info-grid">
            <div class="info-box">
                <h3><?php echo htmlspecialchars($currentLang['eaco_info']); ?></h3>
                <div class="info-item">
                    <span><?php echo htmlspecialchars($currentLang['eaco_rate']); ?></span>
                    <span>$<?php echo number_format($EACO_USD_RATE, 6); ?></span>
                </div>
                <div class="info-item">
                    <span>EACO/CNY</span>
                    <span>¥<?php echo number_format($EACO_USD_RATE * $cnhRate, 6); ?></span>
                </div>
                <div class="info-item">
                    <span><?php echo htmlspecialchars($currentLang['last_updated']); ?></span>
                    <span><?php echo $lastUpdated; ?></span>
                </div>
                <div class="info-item">
                    <span><?php echo htmlspecialchars($currentLang['api_status']); ?></span>
                    <span class="status-badge status-<?php echo $apiStatus === 'OK' ? 'ok' : 'error'; ?>">
                        <?php echo $apiStatus; ?>
                    </span>
                </div>
            </div>

            <div class="info-box">
                <h3><?php echo htmlspecialchars($currentLang['network_status']); ?></h3>
                <div class="info-item">
                    <span>USD/CNY</span>
                    <span>¥<?php echo number_format($cnhRate, 4); ?></span>
                </div>
                <div class="info-item">
                    <span>USD/EUR</span>
                    <span>€<?php echo number_format($eurRate, 4); ?></span>
                </div>
                <div class="info-item">
                    <span>USD/JPY</span>
                    <span>¥<?php echo number_format($jpyRate, 2); ?></span>
                </div>
                <div class="info-item">
                    <span><?php echo htmlspecialchars($currentLang['top_crypto']); ?></span>
                    <span><?php echo count($cryptoData); ?> <?php echo $lang === 'zh' ? '个' : 'coins'; ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($cryptoData)): ?>
            <div class="chart-container">
                <canvas id="priceChart"></canvas>
            </div>

            <table id="crypto-table">
                <thead>
                    <tr>
                        <th><?php echo htmlspecialchars($currentLang['rank']); ?></th>
                        <th><?php echo htmlspecialchars($currentLang['symbol']); ?></th>
                        <th><?php echo htmlspecialchars($currentLang['name']); ?></th>
                        <th><?php echo htmlspecialchars($currentLang['price_usd']); ?></th>
                        <th><?php echo htmlspecialchars($currentLang['price_cnh']); ?></th>
                        <th><?php echo htmlspecialchars($currentLang['eaco_value']); ?></th>
                        <th><?php echo htmlspecialchars($currentLang['volume']); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cryptoData as $coin): ?>
                        <?php 
                        $eacoValue = calculateEACOValue($coin['current_price'], $EACO_USD_RATE);
                        $priceCnh = $coin['current_price'] * $cnhRate;
                        ?>
                        <tr>
                            <td><?php echo $coin['market_cap_rank']; ?></td>
                            <td>
                                <?php if (!empty($coin['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($coin['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($coin['symbol']); ?>" 
                                         width="20" style="vertical-align: middle; border-radius: 50%;">
                                <?php else: ?>
                                    <span style="display: inline-block; width: 20px; height: 20px; background-color: #ddd; border-radius: 50%; text-align: center; line-height: 20px; font-size: 12px;">
                                        <?php echo strtoupper(substr($coin['symbol'], 0, 1)); ?>
                                    </span>
                                <?php endif; ?>
                                <?php echo strtoupper(htmlspecialchars($coin['symbol'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($coin['name']); ?></td>
                            <td>$<?php echo number_format($coin['current_price'], 6); ?></td>
                            <td>¥<?php echo number_format($priceCnh, 4); ?></td>
                            <td><?php echo number_format($eacoValue, 6); ?> EACO</td>
                            <td>$<?php echo number_format($coin['total_volume'], 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="info-box">
            <h3>EACO Links</h3>
            <div class="eaco-links">
                <a href="https://app.meteora.ag/dlmm/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE" target="_blank">Meteora DLMM</a>
                <a href="https://www.orca.so/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr" target="_blank">Orca Pool</a>
                <a href="https://raydium.io/swap/?inputMint=DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH&outputMint=sol" target="_blank">Raydium Swap</a>
                <a href="https://linktr.ee/web3eaco" target="_blank">Linktree</a>
            </div>
        </div>

        <footer>
            <p>EACO is Earth's best friend. $e = $eaco.</p>
            <p>Data from public APIs. Prices for reference only. <strong>Do not make financial decisions based on this data.</strong></p>
            <p>Cache system ensures stability. No database required.</p>
        </footer>
    </div>

    <script>
        function setTheme(theme) {
            document.body.className = theme + '-theme';
            localStorage.setItem('eaco_theme', theme);
        }

        function refreshData() {
            document.getElementById('api-status').textContent = 'Loading...';
            location.reload();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('eaco_theme') || 'light';
            setTheme(savedTheme);
            
            // 自动更新
            setInterval(() => {
                const now = new Date().toLocaleString();
                document.getElementById('update-time')?.textContent = now;
            }, 1000);
        });

        function createChart() {
            const ctx = document.getElementById('priceChart').getContext('2d');
            const top10 = <?php echo json_encode(array_slice($cryptoData, 0, 10)); ?>;
            
            if (top10.length === 0) return;
            
            const labels = top10.map(coin => coin.name);
            const data = top10.map(coin => coin.current_price);
            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#779ECB', '#FFB6C1'
            ];
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Market Cap',
                        data: data,
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: '<?php echo $lang === "zh" ? "市值前10加密货币" : "Top 10 Cryptocurrencies by Market Cap"; ?>',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        }

        function autoUpdate() {
            setTimeout(() => {
                location.reload();
            }, 300000); // 5分钟自动更新
        }

        window.onload = function() {
            if (typeof <?php echo json_encode($cryptoData); ?> !== 'undefined' && <?php echo json_encode($cryptoData); ?>.length > 0) {
                createChart();
            }
            autoUpdate();
        };
    </script>
</body>
</html>