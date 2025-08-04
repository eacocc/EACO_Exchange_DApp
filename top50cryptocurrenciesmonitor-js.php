<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EACO全球汇率监控</title>
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
        }
        
        .dark-theme {
            --bg-color: #1a1a1a;
            --text-color: #e0e0e0;
            --border-color: #444;
            --header-bg: #2c3e50;
            --card-bg: #2d3436;
            --accent-color: #00b894;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        header {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .theme-toggle button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            background-color: var(--header-bg);
            color: var(--header-text);
        }
        
        .chart-container {
            height: 400px;
            margin: 30px 0;
            background-color: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="light-theme">
    <div class="container">
        <header>
            <div class="controls">
                <h1>EACO全球汇率监控</h1>
                <div class="theme-toggle">
                    <button onclick="setTheme('light')">明亮</button>
                    <button onclick="setTheme('dark')">黑暗</button>
                </div>
            </div>
        </header>

        <div class="info-box">
            <p><strong>EACO/USD:</strong> <span id="eaco-rate">$0.0032</span></p>
            <p>最后更新: <span id="update-time">--</span></p>
        </div>

        <div class="chart-container">
            <canvas id="priceChart"></canvas>
        </div>

        <table id="crypto-table">
            <thead>
                <tr>
                    <th>排名</th>
                    <th>代币</th>
                    <th>名称</th>
                    <th>价格 (USD)</th>
                    <th>价格 (CNH)</th>
                    <th>可兑换 EACO</th>
                    <th>24h 交易量</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" id="loading">正在加载数据...</td>
                </tr>
            </tbody>
        </table>

        <footer>
            <p>EACO is Earth's best friend. $e = $eaco.</p>
        </footer>
    </div>

    <script>
        const EACO_USD_RATE = 0.0032;
        let cryptoData = [];
        let cnhRate = 7.2;

        // 主要API客户端
        class APIClient {
            static async fetchWithTimeout(url, timeout = 10000) {
                const controller = new AbortController();
                const id = setTimeout(() => controller.abort(), timeout);
                
                try {
                    const response = await fetch(url, {
                        signal: controller.signal,
                        headers: {
                            'Accept': 'application/json',
                            'User-Agent': 'EACO-Monitor/1.0'
                        }
                    });
                    clearTimeout(id);
                    return response.ok ? await response.json() : null;
                } catch (error) {
                    clearTimeout(id);
                    return null;
                }
            }

            static async getCryptoData() {
                const sources = [
                    'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=50&page=1&sparkline=false',
                    'https://api.coinpaprika.com/v1/tickers?quotes=USD'
                ];

                for (const url of sources) {
                    const data = await this.fetchWithTimeout(url);
                    if (data && data.length > 0) {
                        return this.formatCryptoData(data, url);
                    }
                }
                return this.getDefaultData();
            }

            static async getExchangeRate() {
                const sources = [
                    'https://api.exchangerate-api.com/v4/latest/USD'
                ];

                for (const url of sources) {
                    const data = await this.fetchWithTimeout(url);
                    if (data && data.rates && data.rates.CNY) {
                        return data.rates.CNY;
                    }
                }
                return 7.2;
            }

            static formatCryptoData(data, source) {
                if (source.includes('coingecko')) {
                    return data.map(item => ({
                        name: item.name,
                        symbol: item.symbol,
                        current_price: item.current_price,
                        total_volume: item.total_volume,
                        image: item.image
                    }));
                }
                return data.slice(0, 50).map(item => ({
                    name: item.name,
                    symbol: item.symbol.toLowerCase(),
                    current_price: item.quotes.USD.price,
                    total_volume: item.quotes.USD.volume_24h,
                    image: ''
                }));
            }

            static getDefaultData() {
                return [
                    { name: 'Bitcoin', symbol: 'btc', current_price: 60000, total_volume: 20000000000, image: '' },
                    { name: 'Ethereum', symbol: 'eth', current_price: 3000, total_volume: 15000000000, image: '' }
                ];
            }
        }

        // 初始化应用
        async function initApp() {
            try {
                // 获取数据
                [cryptoData, cnhRate] = await Promise.all([
                    APIClient.getCryptoData(),
                    APIClient.getExchangeRate()
                ]);

                // 更新UI
                updateUI();
                createChart();
                
                // 设置自动更新
                setInterval(refreshData, 300000); // 5分钟
            } catch (error) {
                console.error('初始化失败:', error);
                document.getElementById('loading').textContent = '加载数据失败，请稍后重试';
            }
        }

        function updateUI() {
            // 更新EACO汇率
            document.getElementById('eaco-rate').textContent = `$${EACO_USD_RATE.toFixed(6)}`;
            
            // 更新时间
            document.getElementById('update-time').textContent = new Date().toLocaleString();
            
            // 更新表格
            const tbody = document.querySelector('#crypto-table tbody');
            tbody.innerHTML = '';
            
            cryptoData.forEach((coin, index) => {
                const eacoValue = coin.current_price / EACO_USD_RATE;
                const priceCnh = coin.current_price * cnhRate;
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        ${coin.image ? `<img src="${coin.image}" alt="${coin.symbol}" width="20">` : ''}
                        ${coin.symbol.toUpperCase()}
                    </td>
                    <td>${coin.name}</td>
                    <td>$${coin.current_price.toFixed(6)}</td>
                    <td>¥${priceCnh.toFixed(4)}</td>
                    <td>${eacoValue.toFixed(6)} EACO</td>
                    <td>$${coin.total_volume.toLocaleString()}</td>
                `;
                tbody.appendChild(row);
            });
        }

        function createChart() {
            const ctx = document.getElementById('priceChart').getContext('2d');
            const top10 = cryptoData.slice(0, 10);
            const labels = top10.map(coin => coin.name);
            const data = top10.map(coin => coin.current_price);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Price (USD)',
                        data: data,
                        backgroundColor: 'rgba(74, 144, 226, 0.6)'
                    }]
                }
            });
        }

        function refreshData() {
            initApp();
        }

        function setTheme(theme) {
            document.body.className = theme + '-theme';
            localStorage.setItem('theme', theme);
        }

        // 页面加载
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
            initApp();
        });
    </script>
</body>
</html>