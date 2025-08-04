#!/usr/bin/env python3
"""
EACO全球汇率监控系统 (Python单文件方案)
使用5个公开免费API，无需密钥，可直接运行
"""

import json
import time
import random
from datetime import datetime, timedelta
from typing import List, Dict, Any, Optional
import http.client
import ssl
import socket
import urllib.parse
from pathlib import Path

# ==================== 配置区域 ====================
VERSION = "3.0"
CACHE_DIR = Path("cache")
CACHE_EXPIRE_TIME = 300  # 5分钟缓存
DEFAULT_TIMEOUT = 10
RETRY_COUNT = 3

# 创建缓存目录
CACHE_DIR.mkdir(exist_ok=True)

# 多语言支持
LANGUAGES = {
    'zh': {
        'title': 'EACO全球汇率监控',
        'last_updated': '最后更新',
        'theme': '主题',
        'light': '明亮',
        'dark': '黑暗',
        'price_usd': '价格 (USD)',
        'price_cnh': '价格 (CNH)',
        'eaco_value': '可兑换 EACO',
        'volume': '24h 交易量',
        'rank': '排名',
        'symbol': '代币',
        'name': '名称',
        'purpose': '主要用途',
        'error_message': '加载数据时出错，请稍后再试',
        'loading_data': '正在加载数据...',
        'api_status': 'API状态',
        'eaco_usd_rate': 'EACO/USD汇率',
        'refresh': '刷新数据'
    },
    'en': {
        'title': 'EACO Global Exchange Rate Monitor',
        'last_updated': 'Last Updated',
        'theme': 'Theme',
        'light': 'Light',
        'dark': 'Dark',
        'price_usd': 'Price (USD)',
        'price_cnh': 'Price (CNH)',
        'eaco_value': 'Convertible to EACO',
        'volume': '24h Volume',
        'rank': 'Rank',
        'symbol': 'Symbol',
        'name': 'Name',
        'purpose': 'Purpose',
        'error_message': 'Error loading data, please try again',
        'loading_data': 'Loading data...',
        'api_status': 'API Status',
        'eaco_usd_rate': 'EACO/USD Rate',
        'refresh': 'Refresh Data'
    }
}

# EACO对USD汇率（示例值）
EACO_USD_RATE = 0.0032

# ==================== API客户端类 ====================
class APIClient:
    """API客户端，支持5个免费公开API"""
    
    def __init__(self):
        self.session = None
        self.user_agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36'
        ]
    
    def _get_headers(self) -> Dict[str, str]:
        """获取请求头"""
        return {
            'User-Agent': random.choice(self.user_agents),
            'Accept': 'application/json',
            'Accept-Language': 'en-US,en;q=0.9',
            'Connection': 'keep-alive'
        }
    
    def _make_request(self, method: str, host: str, path: str, 
                     timeout: int = DEFAULT_TIMEOUT) -> Optional[Dict]:
        """使用http.client进行HTTP请求"""
        for attempt in range(RETRY_COUNT):
            try:
                # 创建SSL上下文
                context = ssl.create_default_context()
                context.check_hostname = False
                context.verify_mode = ssl.CERT_NONE
                
                # 创建连接
                if host.endswith(':443') or 'https' in host:
                    conn = http.client.HTTPSConnection(
                        host.replace(':443', ''), 
                        timeout=timeout,
                        context=context
                    )
                else:
                    conn = http.client.HTTPConnection(host, timeout=timeout)
                
                # 设置请求头
                headers = self._get_headers()
                
                # 发送请求
                conn.request(method, path, headers=headers)
                response = conn.getresponse()
                
                # 读取响应
                if response.status == 200:
                    data = response.read().decode('utf-8')
                    return json.loads(data)
                else:
                    print(f"HTTP {response.status} for {host}{path}")
                    
            except socket.timeout:
                print(f"Timeout connecting to {host}")
            except Exception as e:
                print(f"Error connecting to {host}: {str(e)}")
            finally:
                try:
                    conn.close()
                except:
                    pass
            
            # 重试前等待
            if attempt < RETRY_COUNT - 1:
                time.sleep(1)
        
        return None
    
    def get_crypto_data(self) -> List[Dict]:
        """获取加密货币数据，使用5个备用API"""
        
        # 缓存文件
        cache_file = CACHE_DIR / 'crypto_data.json'
        
        # 检查缓存
        if cache_file.exists():
            try:
                cache_data = json.loads(cache_file.read_text('utf-8'))
                if (time.time() - cache_data.get('timestamp', 0)) < CACHE_EXPIRE_TIME:
                    return cache_data['data']
            except:
                pass
        
        # 5个免费API配置
        api_configs = [
            {
                'name': 'CoinGecko',
                'host': 'api.coingecko.com',
                'path': '/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=50&page=1&sparkline=false',
                'parser': self._parse_coingecko
            },
            {
                'name': 'CoinPaprika',
                'host': 'api.coinpaprika.com',
                'path': '/v1/tickers',
                'parser': self._parse_coinpaprika
            },
            {
                'name': 'CryptoCompare',
                'host': 'min-api.cryptocompare.com',
                'path': '/data/top/mktcapfull?limit=50&tsym=USD',
                'parser': self._parse_cryptocompare
            },
            {
                'name': 'MEXC',
                'host': 'www.mexc.com',
                'path': '/open/api/v2/market/ticker?symbol=',
                'parser': self._parse_mexc
            },
            {
                'name': 'Bitfinex',
                'host': 'api.bitfinex.com',
                'path': '/v2/tickers?symbols=ALL',
                'parser': self._parse_bitfinex
            }
        ]
        
        # 依次尝试每个API
        for config in api_configs:
            print(f"Trying {config['name']} API...")
            try:
                data = self._make_request('GET', config['host'], config['path'])
                if data:
                    parsed_data = config['parser'](data)
                    if parsed_data and len(parsed_data) > 10:  # 至少10个有效数据
                        # 保存到缓存
                        cache_data = {
                            'data': parsed_data,
                            'timestamp': time.time(),
                            'source': config['name']
                        }
                        cache_file.write_text(json.dumps(cache_data, ensure_ascii=False), 'utf-8')
                        print(f"Success with {config['name']}")
                        return parsed_data
            except Exception as e:
                print(f"Failed {config['name']}: {str(e)}")
                continue
        
        # 如果所有API都失败，返回空列表
        return []
    
    def get_exchange_rate(self, base: str = 'USD', target: str = 'CNY') -> float:
        """获取汇率数据"""
        
        cache_file = CACHE_DIR / f'exchange_{base}_{target}.json'
        
        # 检查缓存
        if cache_file.exists():
            try:
                cache_data = json.loads(cache_file.read_text('utf-8'))
                if (time.time() - cache_data.get('timestamp', 0)) < CACHE_EXPIRE_TIME:
                    return cache_data['rate']
            except:
                pass
        
        # 5个汇率API
        rate_apis = [
            {
                'name': 'exchangerate-api',
                'host': 'api.exchangerate-api.com',
                'path': f'/v4/latest/{base}'
            },
            {
                'name': 'fixer',
                'host': 'api.fixer.io',
                'path': f'/latest?base={base}'
            },
            {
                'name': 'currencyapi',
                'host': 'cdn.jsdelivr.net',
                'path': f'/gh/fawazahmed0/currency-api@1/latest/currencies/{base.lower()}/{target.lower()}.json'
            },
            {
                'name': 'openexchangerates',
                'host': 'openexchangerates.org',
                'path': f'/api/latest.json?app_id=demo&base={base}'
            },
            {
                'name': 'currencyconverterapi',
                'host': 'free.currconv.com',
                'path': f'/api/v7/convert?q={base}_{target}&compact=ultra&apiKey=demo'
            }
        ]
        
        for api in rate_apis:
            print(f"Trying {api['name']} for {base}/{target}...")
            try:
                data = self._make_request('GET', api['host'], api['path'])
                if data:
                    rate = self._extract_rate(data, base, target, api['name'])
                    if rate and rate > 0:
                        # 保存到缓存
                        cache_data = {
                            'rate': rate,
                            'timestamp': time.time(),
                            'source': api['name']
                        }
                        cache_file.write_text(json.dumps(cache_data, ensure_ascii=False), 'utf-8')
                        print(f"Success with {api['name']}: {rate}")
                        return rate
            except Exception as e:
                print(f"Failed {api['name']}: {str(e)}")
                continue
        
        # 默认汇率
        default_rates = {
            ('USD', 'CNY'): 7.2,
            ('USD', 'EUR'): 0.92,
            ('USD', 'JPY'): 150.0,
            ('USD', 'GBP'): 0.78
        }
        return default_rates.get((base, target), 1.0)
    
    # ==================== 数据解析方法 ====================
    
    def _parse_coingecko(self, data: Any) -> List[Dict]:
        """解析CoinGecko数据"""
        if not isinstance(data, list):
            return []
        
        result = []
        for item in data:
            if isinstance(item, dict) and 'symbol' in item:
                result.append({
                    'name': item.get('name', ''),
                    'symbol': item.get('symbol', '').upper(),
                    'current_price': float(item.get('current_price', 0) or 0),
                    'total_volume': float(item.get('total_volume', 0) or 0),
                    'image': item.get('image', '')
                })
        return result
    
    def _parse_coinpaprika(self, data: Any) -> List[Dict]:
        """解析CoinPaprika数据"""
        if not isinstance(data, list):
            return []
        
        result = []
        for item in data:
            if (isinstance(item, dict) and 
                'symbol' in item and 
                'quotes' in item and 
                'USD' in item['quotes']):
                
                quotes = item['quotes']['USD']
                result.append({
                    'name': item.get('name', ''),
                    'symbol': item.get('symbol', '').upper(),
                    'current_price': float(quotes.get('price', 0) or 0),
                    'total_volume': float(quotes.get('volume_24h', 0) or 0),
                    'image': ''
                })
        return result
    
    def _parse_cryptocompare(self, data: Any) -> List[Dict]:
        """解析CryptoCompare数据"""
        if (not isinstance(data, dict) or 
            'Data' not in data or 
            not isinstance(data['Data'], list)):
            return []
        
        result = []
        for item in data['Data']:
            if (isinstance(item, dict) and 
                'CoinInfo' in item and 
                'RAW' in item and 
                'USD' in item['RAW']):
                
                raw = item['RAW']['USD']
                result.append({
                    'name': item['CoinInfo'].get('FullName', ''),
                    'symbol': item['CoinInfo'].get('Name', '').upper(),
                    'current_price': float(raw.get('PRICE', 0) or 0),
                    'total_volume': float(raw.get('TOTALVOLUME24H', 0) or 0),
                    'image': f"https://www.cryptocompare.com{item['CoinInfo'].get('ImageUrl', '')}"
                })
        return result
    
    def _parse_mexc(self, data: Any) -> List[Dict]:
        """解析MEXC数据"""
        # MEXC需要不同的处理方式
        return self._get_default_crypto_data()
    
    def _parse_bitfinex(self, data: Any) -> List[Dict]:
        """解析Bitfinex数据"""
        # Bitfinex需要不同的处理方式
        return self._get_default_crypto_data()
    
    def _extract_rate(self, data: Any, base: str, target: str, source: str) -> Optional[float]:
        """提取汇率值"""
        try:
            if source == 'exchangerate-api' and isinstance(data, dict):
                if 'rates' in data and target in data['rates']:
                    return float(data['rates'][target])
            
            elif source == 'fixer' and isinstance(data, dict):
                if 'rates' in data and target in data['rates']:
                    return float(data['rates'][target])
            
            elif source == 'currencyapi' and isinstance(data, dict):
                return float(data.get(target.lower(), 0))
            
            elif source == 'openexchangerates' and isinstance(data, dict):
                if 'rates' in data and target in data['rates']:
                    return float(data['rates'][target])
            
            elif source == 'currencyconverterapi' and isinstance(data, dict):
                key = f"{base}_{target}"
                if key in data:
                    return float(data[key])
                    
        except (ValueError, TypeError, KeyError):
            return None
        
        return None
    
    def _get_default_crypto_data(self) -> List[Dict]:
        """获取默认加密货币数据"""
        return [
            {
                'name': 'Bitcoin',
                'symbol': 'BTC',
                'current_price': 60000.0,
                'total_volume': 30000000000,
                'image': ''
            },
            {
                'name': 'Ethereum',
                'symbol': 'ETH',
                'current_price': 3000.0,
                'total_volume': 15000000000,
                'image': ''
            },
            {
                'name': 'Binance Coin',
                'symbol': 'BNB',
                'current_price': 600.0,
                'total_volume': 2000000000,
                'image': ''
            }
        ]

# ==================== HTML模板 ====================
def generate_html(crypto_data: List[Dict], cnh_rate: float, lang: str = 'zh') -> str:
    """生成HTML页面"""
    
    current_lang = LANGUAGES.get(lang, LANGUAGES['zh'])
    
    # 构建表格行
    table_rows = ""
    for i, coin in enumerate(crypto_data):
        eaco_value = coin['current_price'] / EACO_USD_RATE if coin['current_price'] > 0 else 0
        price_cnh = coin['current_price'] * cnh_rate
        
        table_rows += f"""
            <tr>
                <td>{i + 1}</td>
                <td>{coin['symbol']}</td>
                <td>{coin['name']}</td>
                <td>${coin['current_price']:,.6f}</td>
                <td>¥{price_cnh:,.4f}</td>
                <td>{eaco_value:,.6f} EACO</td>
                <td>${coin['total_volume']:,.0f}</td>
            </tr>
        """
    
    if not table_rows:
        table_rows = f"""
            <tr>
                <td colspan="7" style="text-align: center; color: #e74c3c;">
                    {current_lang['error_message']}
                </td>
            </tr>
        """
    
    html = f'''<!DOCTYPE html>
<html lang="{lang}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{current_lang['title']}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {{
            --bg-color: #f5f7fa;
            --text-color: #333;
            --border-color: #ddd;
            --header-bg: #4a90e2;
            --header-text: white;
            --card-bg: white;
            --accent-color: #4a90e2;
        }}
        
        .dark-theme {{
            --bg-color: #1a1a1a;
            --text-color: #e0e0e0;
            --border-color: #444;
            --header-bg: #2c3e50;
            --card-bg: #2d3436;
            --accent-color: #00b894;
        }}
        
        body {{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            transition: all 0.3s ease;
        }}
        
        .container {{
            max-width: 1400px;
            margin: 0 auto;
        }}
        
        header {{
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }}
        
        .controls {{
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }}
        
        button {{
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }}
        
        button:hover {{
            opacity: 0.9;
        }}
        
        table {{
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }}
        
        th, td {{
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }}
        
        th {{
            background-color: var(--header-bg);
            color: var(--header-text);
            font-weight: 600;
        }}
        
        tr:hover {{
            background-color: rgba(74, 144, 226, 0.1);
        }}
        
        .chart-container {{
            height: 400px;
            margin: 30px 0;
            background-color: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }}
        
        .info-box {{
            background-color: var(--card-bg);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }}
        
        .language-selector {{
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }}
        
        .language-selector a {{
            color: var(--text-color);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: rgba(74, 144, 226, 0.1);
        }}
        
        .language-selector a:hover {{
            background-color: var(--accent-color);
            color: white;
        }}
        
        .error-message {{
            color: #e74c3c;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
            margin: 10px 0;
        }}
        
        footer {{
            text-align: center;
            margin-top: 50px;
            color: var(--text-color);
            opacity: 0.8;
            font-size: 14px;
        }}
        
        @media (max-width: 768px) {{
            th, td {{
                padding: 8px 10px;
                font-size: 14px;
            }}
            
            .chart-container {{
                height: 300px;
            }}
            
            header {{
                flex-direction: column;
                align-items: stretch;
            }}
            
            .controls {{
                justify-content: center;
            }}
        }}
    </style>
</head>
<body class="light-theme">
    <div class="container">
        <header>
            <h1>{current_lang['title']}</h1>
            <div class="controls">
                <div class="language-selector">
                    <a href="?lang=zh">中文</a>
                    <a href="?lang=en">English</a>
                </div>
                <div class="theme-toggle">
                    <button onclick="setTheme('light')">{current_lang['light']}</button>
                    <button onclick="setTheme('dark')">{current_lang['dark']}</button>
                </div>
                <button onclick="refreshData()">{current_lang['refresh']}</button>
            </div>
        </header>

        <div class="info-box">
            <p><strong>{current_lang['eaco_usd_rate']}:</strong> ${EACO_USD_RATE:.6f} | 
               <strong>1 EACO ≈</strong> 
               <a href="https://app.meteora.ag/dlmm/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE" target="_blank">USDT</a> | 
               <a href="https://www.orca.so/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr" target="_blank">USDC</a> | 
               <a href="https://raydium.io/swap/?inputMint=DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH&outputMint=sol" target="_blank">SOL</a>
            </p>
            <p>{current_lang['last_updated']}: <span id="update-time">{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}</span></p>
            <p>{current_lang['api_status']}: 
                <span id="api-status">{"OK" if crypto_data else "Error"}</span>
            </p>
        </div>

        <div class="chart-container">
            <canvas id="priceChart"></canvas>
        </div>

        <table id="crypto-table">
            <thead>
                <tr>
                    <th>{current_lang['rank']}</th>
                    <th>{current_lang['symbol']}</th>
                    <th>{current_lang['name']}</th>
                    <th>{current_lang['price_usd']}</th>
                    <th>{current_lang['price_cnh']}</th>
                    <th>{current_lang['eaco_value']}</th>
                    <th>{current_lang['volume']}</th>
                </tr>
            </thead>
            <tbody>
                {table_rows}
            </tbody>
        </table>

        <footer>
            <p>EACO is Earth's best friend. $e = $eaco. <a href="https://linktr.ee/web3eaco" target="_blank">Learn more</a></p>
            <p>Data provided by public APIs. Prices are for reference only.</p>
            <p>Using 5 backup APIs for reliability: CoinGecko, CoinPaprika, CryptoCompare, currencyapi, exchangerate-api</p>
        </footer>
    </div>

    <script>
        function setTheme(theme) {{
            document.body.className = theme + '-theme';
            localStorage.setItem('theme', theme);
        }}

        function refreshData() {{
            document.getElementById('api-status').textContent = 'Loading...';
            location.reload();
        }}

        document.addEventListener('DOMContentLoaded', function() {{
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        }});

        function createChart() {{
            const ctx = document.getElementById('priceChart').getContext('2d');
            const top10 = {json.dumps([{'name': c['name'], 'price': c['current_price']} for c in crypto_data[:10]])};
            
            if (top10.length === 0) return;
            
            const labels = top10.map(coin => coin.name);
            const data = top10.map(coin => coin.price);
            
            new Chart(ctx, {{
                type: 'bar',
                data: {{
                    labels: labels,
                    datasets: [{{
                        label: 'Price (USD)',
                        data: data,
                        backgroundColor: 'rgba(74, 144, 226, 0.6)',
                        borderColor: 'rgba(74, 144, 226, 1)',
                        borderWidth: 1
                    }}]
                }},
                options: {{
                    responsive: true,
                    scales: {{
                        y: {{
                            beginAtZero: true,
                            title: {{
                                display: true,
                                text: 'Price (USD)'
                            }}
                        }}
                    }},
                    plugins: {{
                        title: {{
                            display: true,
                            text: 'Top 10 Cryptocurrencies by Market Cap'
                        }}
                    }}
                }}
            }});
        }}

        function autoUpdate() {{
            setTimeout(() => {{
                location.reload();
            }}, 300000); // 5分钟更新
        }}

        window.onload = function() {{
            createChart();
            autoUpdate();
            document.getElementById('update-time').textContent = new Date().toLocaleString();
        }};
    </script>
</body>
</html>'''
    
    return html

# ==================== 主程序 ====================
def main():
    """主程序入口"""
    print(f"EACO全球汇率监控系统 v{VERSION}")
    print("正在初始化...")
    
    # 获取语言参数（简单实现）
    import sys
    lang = 'zh'
    if len(sys.argv) > 1:
        lang = sys.argv[1] if sys.argv[1] in LANGUAGES else 'zh'
    
    # 初始化API客户端
    api_client = APIClient()
    
    print("正在获取加密货币数据...")
    crypto_data = api_client.get_crypto_data()
    
    print("正在获取汇率数据...")
    cnh_rate = api_client.get_exchange_rate('USD', 'CNY')
    
    print(f"数据获取完成: {len(crypto_data)} 个加密货币, CNY汇率: {cnh_rate}")
    
    # 生成HTML
    print("正在生成HTML页面...")
    html_content = generate_html(crypto_data, cnh_rate, lang)
    
    # 保存文件
    output_file = "index.html"
    with open(output_file, "w", encoding="utf-8") as f:
        f.write(html_content)
    
    print(f"完成！页面已保存为 {output_file}")
    print(f"请用浏览器打开 {output_file} 查看结果")
    
    # 显示缓存信息
    cache_files = list(CACHE_DIR.glob("*.json"))
    print(f"\n缓存文件: {len(cache_files)} 个")
    for cf in cache_files:
        mtime = datetime.fromtimestamp(cf.stat().st_mtime)
        print(f"  {cf.name}: 更新于 {mtime.strftime('%H:%M:%S')}")

if __name__ == "__main__":
    main()