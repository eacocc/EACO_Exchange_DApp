<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EACO - 地球数字资产监控</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind配置 -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        accent: '#8B5CF6',
                        danger: '#EF4444',
                        warning: '#F59E0B',
                        dark: {
                            100: '#374151',
                            200: '#1F2937',
                            300: '#111827',
                            400: '#030712'
                        },
                        light: {
                            100: '#FFFFFF',
                            200: '#F9FAFB',
                            300: '#F3F4F6',
                            400: '#E5E7EB'
                        }
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .text-shadow {
                text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .transition-custom {
                transition: all 0.3s ease;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
        }
    </style>
</head>

<body class="font-inter bg-light-200 text-dark-300 dark:bg-dark-300 dark:text-light-200 transition-custom">
    <!-- 顶部通知栏 -->
    <div class="bg-primary/10 dark:bg-primary/20 border-b border-primary/20 py-2 px-4 text-center text-sm">
        <p>【EACO代码重构e文明】 代码e即财富，EACO地球E连接地球和宇宙的一切，e连接地球和宇宙万物。</p>
    </div>

    <!-- 导航栏 -->
    <header class="sticky top-0 z-50 bg-light-100/90 dark:bg-dark-300/90 backdrop-blur-md shadow-sm transition-custom">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo和名称 -->
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-accent flex items-center justify-center">
                        <span class="text-white font-bold text-xl">E</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent">EACO</h1>
                        <p class="text-xs text-dark-100 dark:text-light-400 hidden sm:block">Earth's Best Coin</p>
                    </div>
                </div>

                <!-- 主导航 - 桌面版 -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#markets" class="font-medium hover:text-primary transition-custom">市场</a>
                    <a href="#eaco" class="font-medium hover:text-primary transition-custom">EACO</a>
                    <a href="#converter" class="font-medium hover:text-primary transition-custom">转换器</a>
                    <a href="#api" class="font-medium hover:text-primary transition-custom">API</a>
                    <a href="#community" class="font-medium hover:text-primary transition-custom">社区</a>
                </nav>

                <!-- 右侧功能区 -->
                <div class="flex items-center space-x-4">
                    <!-- 搜索按钮 -->
                    <button id="searchBtn" class="p-2 rounded-full hover:bg-light-300 dark:hover:bg-dark-200 transition-custom">
                        <i class="fa fa-search"></i>
                    </button>
                    
                    <!-- 主题切换 -->
                    <button id="themeToggle" class="p-2 rounded-full hover:bg-light-300 dark:hover:bg-dark-200 transition-custom">
                        <i class="fa fa-moon-o dark:hidden"></i>
                        <i class="fa fa-sun-o hidden dark:inline-block"></i>
                    </button>
                    
                    <!-- 语言选择 -->
                    <div class="relative group">
                        <button class="flex items-center space-x-1 p-1 rounded-full hover:bg-light-300 dark:hover:bg-dark-200 transition-custom">
                            <i class="fa fa-globe"></i>
                            <span class="hidden sm:inline">中文</span>
                            <i class="fa fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-light-100 dark:bg-dark-200 rounded-lg shadow-lg overflow-hidden z-50 hidden group-hover:block transition-custom">
                            <div class="max-h-60 overflow-y-auto scrollbar-hide">
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="zh-CN">中文</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="en">English</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="es">Español</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="fr">Français</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="ru">Русский</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="ar">العربية</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="hi">हिन्दी</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="pt">Português</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="ja">日本語</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="bn">বাংলা</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="de">Deutsch</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="it">Italiano</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="ko">한국어</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="vi">Tiếng Việt</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="id">Bahasa Indonesia</button>
                                <button class="language-btn w-full text-left px-4 py-2 hover:bg-light-300 dark:hover:bg-dark-100 transition-custom" data-lang="ms">Bahasa Melayu</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 登录/注册按钮 -->
                    <button class="hidden sm:block bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg transition-custom">
                        连接钱包
                    </button>
                    
                    <!-- 移动端菜单按钮 -->
                    <button id="mobileMenuBtn" class="md:hidden p-2 rounded-full hover:bg-light-300 dark:hover:bg-dark-200 transition-custom">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- 移动端导航菜单 -->
        <div id="mobileMenu" class="md:hidden hidden bg-light-100 dark:bg-dark-200 border-t dark:border-dark-100">
            <div class="container mx-auto px-4 py-3 space-y-3">
                <a href="#markets" class="block py-2 font-medium hover:text-primary transition-custom">市场</a>
                <a href="#eaco" class="block py-2 font-medium hover:text-primary transition-custom">EACO</a>
                <a href="#converter" class="block py-2 font-medium hover:text-primary transition-custom">转换器</a>
                <a href="#api" class="block py-2 font-medium hover:text-primary transition-custom">API</a>
                <a href="#community" class="block py-2 font-medium hover:text-primary transition-custom">社区</a>
                <button class="w-full bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg transition-custom">
                    连接钱包
                </button>
            </div>
        </div>
        
        <!-- 搜索框 -->
        <div id="searchBox" class="hidden bg-light-100 dark:bg-dark-200 border-t dark:border-dark-100 py-3">
            <div class="container mx-auto px-4">
                <div class="relative">
                    <input type="text" placeholder="搜索数字货币或交易对..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg bg-light-300 dark:bg-dark-100 border-0 focus:ring-2 focus:ring-primary outline-none transition-custom">
                    <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-dark-100 dark:text-light-400"></i>
                </div>
                <div class="mt-2 text-sm text-dark-100 dark:text-light-400">
                    热门搜索: EACO, BTC, ETH, SOL, USDT, CNH
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- EACO重点展示区 -->
        <section id="eaco" class="py-10 bg-gradient-to-b from-primary/5 to-transparent dark:from-primary/10">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-2">EACO 实时价格</h2>
                    <p class="text-dark-100 dark:text-light-400">EACO (Earth's Best Coin) 多平台价格监控</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <!-- Meteora (E-USDT) -->
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6 transition-custom hover:shadow-lg hover:-translate-y-1">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-semibold text-lg">Meteora (E-USDT)</h3>
                                <p class="text-sm text-dark-100 dark:text-light-400">
                                    <a href="https://app.meteora.ag/dlmm/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE" target="_blank" class="hover:text-primary">查看市场</a>
                                </p>
                            </div>
                            <div class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs rounded-full">
                                实时
                            </div>
                        </div>
                        <div class="flex items-end space-x-2 mb-2">
                            <span id="meteora-price" class="text-3xl font-bold">0.00</span>
                            <span class="text-dark-100 dark:text-light-400 mb-1">USDT</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span id="meteora-change" class="text-secondary flex items-center">
                                <i class="fa fa-arrow-up mr-1"></i>0.00%
                            </span>
                            <span class="text-xs text-dark-100 dark:text-light-400">24小时</span>
                        </div>
                    </div>
                    
                    <!-- Orca (E-USDC) -->
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6 transition-custom hover:shadow-lg hover:-translate-y-1">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-semibold text-lg">Orca (E-USDC)</h3>
                                <p class="text-sm text-dark-100 dark:text-light-400">
                                    <a href="https://www.orca.so/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr" target="_blank" class="hover:text-primary">查看市场</a>
                                </p>
                            </div>
                            <div class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs rounded-full">
                                实时
                            </div>
                        </div>
                        <div class="flex items-end space-x-2 mb-2">
                            <span id="orca-price" class="text-3xl font-bold">0.00</span>
                            <span class="text-dark-100 dark:text-light-400 mb-1">USDC</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span id="orca-change" class="text-secondary flex items-center">
                                <i class="fa fa-arrow-up mr-1"></i>0.00%
                            </span>
                            <span class="text-xs text-dark-100 dark:text-light-400">24小时</span>
                        </div>
                    </div>
                    
                    <!-- Raydium (E-SOL) -->
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6 transition-custom hover:shadow-lg hover:-translate-y-1">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-semibold text-lg">Raydium (E-SOL)</h3>
                                <p class="text-sm text-dark-100 dark:text-light-400">
                                    <a href="https://raydium.io/swap/?inputMint=DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH&outputMint=sol" target="_blank" class="hover:text-primary">查看市场</a>
                                </p>
                            </div>
                            <div class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs rounded-full">
                                实时
                            </div>
                        </div>
                        <div class="flex items-end space-x-2 mb-2">
                            <span id="raydium-price" class="text-3xl font-bold">0.00</span>
                            <span class="text-dark-100 dark:text-light-400 mb-1">SOL</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span id="raydium-change" class="text-secondary flex items-center">
                                <i class="fa fa-arrow-up mr-1"></i>0.00%
                            </span>
                            <span class="text-xs text-dark-100 dark:text-light-400">24小时</span>
                        </div>
                    </div>
                </div>
                
                <!-- EACO价格图表 -->
                <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6 mb-6">
                    <div class="flex flex-wrap justify-between items-center mb-6">
                        <h3 class="font-semibold text-xl">EACO 价格趋势</h3>
                        <div class="flex space-x-2 mt-2 sm:mt-0">
                            <button class="time-filter px-3 py-1 text-sm rounded-md bg-primary text-white">24小时</button>
                            <button class="time-filter px-3 py-1 text-sm rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">7天</button>
                            <button class="time-filter px-3 py-1 text-sm rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">1个月</button>
                            <button class="time-filter px-3 py-1 text-sm rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">1年</button>
                            <button class="time-filter px-3 py-1 text-sm rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">全部</button>
                        </div>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="eacoChart"></canvas>
                    </div>
                </div>
                
                <!-- EACO基本信息 -->
                <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6">
                    <h3 class="font-semibold text-xl mb-4">EACO 基本信息</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">代币名称</p>
                            <p class="font-medium">EACO</p>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">代币符号</p>
                            <p class="font-medium">$e / $eaco</p>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">Solana地址</p>
                            <p class="font-medium text-sm break-all">DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH</p>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">Solana浏览器</p>
                            <a href="https://solscan.io/token/DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH" target="_blank" class="font-medium text-primary hover:underline">查看</a>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">市值</p>
                            <p id="eaco-marketcap" class="font-medium">--</p>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">24小时交易量</p>
                            <p id="eaco-volume" class="font-medium">--</p>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">流通供应量</p>
                            <p id="eaco-supply" class="font-medium">--</p>
                        </div>
                        <div class="p-4 bg-light-200 dark:bg-dark-300 rounded-lg">
                            <p class="text-sm text-dark-100 dark:text-light-400 mb-1">官网</p>
                            <a href="https://linktr.ee/web3eaco" target="_blank" class="font-medium text-primary hover:underline">linktr.ee/web3eaco</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- 市场排行榜 -->
        <section id="markets" class="py-10">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-2">全球数字货币市场</h2>
                    <p class="text-dark-100 dark:text-light-400">实时监控全球前1000名数字货币价格与趋势</p>
                </div>
                
                <!-- 市场概览 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-5">
                        <p class="text-dark-100 dark:text-light-400 text-sm mb-1">总市值</p>
                        <p id="total-marketcap" class="text-xl font-bold">$0.00</p>
                        <p id="marketcap-change" class="text-secondary text-sm mt-1 flex items-center">
                            <i class="fa fa-arrow-up mr-1"></i>0.00%
                        </p>
                    </div>
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-5">
                        <p class="text-dark-100 dark:text-light-400 text-sm mb-1">24小时交易量</p>
                        <p id="total-volume" class="text-xl font-bold">$0.00</p>
                        <p id="volume-change" class="text-secondary text-sm mt-1 flex items-center">
                            <i class="fa fa-arrow-up mr-1"></i>0.00%
                        </p>
                    </div>
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-5">
                        <p class="text-dark-100 dark:text-light-400 text-sm mb-1">BTC  dominance</p>
                        <p id="btc-dominance" class="text-xl font-bold">0.00%</p>
                        <p id="dominance-change" class="text-danger text-sm mt-1 flex items-center">
                            <i class="fa fa-arrow-down mr-1"></i>0.00%
                        </p>
                    </div>
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-5">
                        <p class="text-dark-100 dark:text-light-400 text-sm mb-1">活跃加密货币</p>
                        <p id="active-cryptos" class="text-xl font-bold">0</p>
                        <p class="text-dark-100 dark:text-light-400 text-sm mt-1">全球市场</p>
                    </div>
                </div>
                
                <!-- 筛选和控制 -->
                <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-dark-100 dark:text-light-400">显示:</span>
                            <select class="bg-light-300 dark:bg-dark-100 border-0 rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-primary outline-none">
                                <option>前100名</option>
                                <option>前200名</option>
                                <option>前500名</option>
                                <option>前1000名</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-dark-100 dark:text-light-400">排序方式:</span>
                            <select class="bg-light-300 dark:bg-dark-100 border-0 rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-primary outline-none">
                                <option>市值排名</option>
                                <option>涨幅 (24h)</option>
                                <option>跌幅 (24h)</option>
                                <option>交易量 (24h)</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-dark-100 dark:text-light-400">API来源:</span>
                            <div class="flex flex-wrap gap-1">
                                <button class="api-source-btn px-2 py-1 text-xs rounded bg-primary text-white">CoinGecko</button>
                                <button class="api-source-btn px-2 py-1 text-xs rounded hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">CoinCap</button>
                                <button class="api-source-btn px-2 py-1 text-xs rounded hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">Nomics</button>
                                <button class="api-source-btn px-2 py-1 text-xs rounded hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">CoinMarketCap</button>
                                <button class="api-source-btn px-2 py-1 text-xs rounded hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">CryptoCompare</button>
                                <button class="api-source-btn px-2 py-1 text-xs rounded hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">更多</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 数字货币表格 -->
                <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b dark:border-dark-100">
                                    <th class="px-4 py-3 text-left text-sm font-semibold">排名</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">名称</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold">价格</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold">24小时</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold">7天</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold hidden md:table-cell">市值</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold hidden lg:table-cell">24小时交易量</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold hidden xl:table-cell">流通供应量</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold">操作</th>
                                </tr>
                            </thead>
                            <tbody id="crypto-table-body">
                                <!-- 表格内容将通过JavaScript动态生成 -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- 加载状态 -->
                    <div id="table-loading" class="py-10 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary mb-2"></div>
                        <p>加载市场数据中...</p>
                    </div>
                    
                    <!-- 分页 -->
                    <div class="px-4 py-3 flex justify-between items-center border-t dark:border-dark-100">
                        <div class="text-sm text-dark-100 dark:text-light-400">
                            显示 1-10 条，共 1000 条
                        </div>
                        <div class="flex space-x-1">
                            <button class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom disabled:opacity-50" disabled>
                                <i class="fa fa-chevron-left text-xs"></i>
                            </button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md bg-primary text-white">1</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">2</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">3</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">...</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">100</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-light-300 dark:hover:bg-dark-100 transition-custom">
                                <i class="fa fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- 汇率转换器 -->
        <section id="converter" class="py-10 bg-gradient-to-b from-transparent to-primary/5 dark:to-primary/10">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-2">数字货币转换器</h2>
                    <p class="text-dark-100 dark:text-light-400">实时转换全球前100大法币与前1000大数字货币</p>
                </div>
                
                <div class="max-w-2xl mx-auto bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6">
                    <div class="space-y-6">
                        <!-- 转换表单 -->
                        <div class="space-y-4">
                            <!-- 从 -->
                            <div>
                                <label class="block text-sm font-medium mb-1">从</label>
                                <div class="flex">
                                    <input type="number" id="convert-amount" value="1" 
                                        class="flex-1 bg-light-300 dark:bg-dark-100 border-0 rounded-l-lg px-4 py-2 focus:ring-1 focus:ring-primary outline-none">
                                    <div class="relative">
                                        <select id="from-currency" class="bg-light-300 dark:bg-dark-100 border-0 rounded-r-lg px-4 py-2 pr-8 focus:ring-1 focus:ring-primary outline-none appearance-none">
                                            <option value="eaco">EACO</option>
                                            <option value="btc">Bitcoin (BTC)</option>
                                            <option value="eth">Ethereum (ETH)</option>
                                            <option value="sol">Solana (SOL)</option>
                                            <option value="usdt">Tether (USDT)</option>
                                            <option value="cnh">CNH</option>
                                            <option value="usd">USD</option>
                                        </select>
                                        <i class="fa fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-dark-100 dark:text-light-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 交换按钮 -->
                            <div class="flex justify-center">
                                <button id="swap-currencies" class="w-10 h-10 rounded-full bg-light-300 dark:bg-dark-100 flex items-center justify-center hover:bg-light-400 dark:hover:bg-dark-100 transition-custom">
                                    <i class="fa fa-exchange text-primary"></i>
                                </button>
                            </div>
                            
                            <!-- 到 -->
                            <div>
                                <label class="block text-sm font-medium mb-1">到</label>
                                <div class="flex">
                                    <input type="number" id="result-amount" readonly 
                                        class="flex-1 bg-light-300 dark:bg-dark-100 border-0 rounded-l-lg px-4 py-2 focus:ring-1 focus:ring-primary outline-none">
                                    <div class="relative">
                                        <select id="to-currency" class="bg-light-300 dark:bg-dark-100 border-0 rounded-r-lg px-4 py-2 pr-8 focus:ring-1 focus:ring-primary outline-none appearance-none">
                                            <option value="cnh">CNH</option>
                                            <option value="usd">USD</option>
                                            <option value="eaco">EACO</option>
                                            <option value="btc">Bitcoin (BTC)</option>
                                            <option value="eth">Ethereum (ETH)</option>
                                            <option value="sol">Solana (SOL)</option>
                                            <option value="usdt">Tether (USDT)</option>
                                        </select>
                                        <i class="fa fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-dark-100 dark:text-light-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 转换结果信息 -->
                            <div class="text-sm text-dark-100 dark:text-light-400">
                                <p id="conversion-info">1 EACO = 0.00 CNH</p>
                                <p class="text-xs mt-1">最后更新: <span id="last-updated">刚刚</span></p>
                            </div>
                        </div>
                        
                        <!-- 常用转换对 -->
                        <div>
                            <h3 class="text-sm font-medium mb-2">常用转换</h3>
                            <div class="flex flex-wrap gap-2">
                                <button class="quick-convert-btn px-3 py-1 text-sm bg-light-300 dark:bg-dark-100 rounded-md hover:bg-light-400 dark:hover:bg-dark-100 transition-custom" data-from="eaco" data-to="cnh">EACO → CNH</button>
                                <button class="quick-convert-btn px-3 py-1 text-sm bg-light-300 dark:bg-dark-100 rounded-md hover:bg-light-400 dark:hover:bg-dark-100 transition-custom" data-from="btc" data-to="cnh">BTC → CNH</button>
                                <button class="quick-convert-btn px-3 py-1 text-sm bg-light-300 dark:bg-dark-100 rounded-md hover:bg-light-400 dark:hover:bg-dark-100 transition-custom" data-from="eth" data-to="cnh">ETH → CNH</button>
                                <button class="quick-convert-btn px-3 py-1 text-sm bg-light-300 dark:bg-dark-100 rounded-md hover:bg-light-400 dark:hover:bg-dark-100 transition-custom" data-from="cnh" data-to="eaco">CNH → EACO</button>
                                <button class="quick-convert-btn px-3 py-1 text-sm bg-light-300 dark:bg-dark-100 rounded-md hover:bg-light-400 dark:hover:bg-dark-100 transition-custom" data-from="usdt" data-to="cnh">USDT → CNH</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- API资源 -->
        <section id="api" class="py-10">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-2">数字货币API资源</h2>
                    <p class="text-dark-100 dark:text-light-400">全球Top 10免费API，支持EACO汇率计算</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full bg-light-100 dark:bg-dark-200 rounded-xl shadow-md">
                        <thead>
                            <tr class="border-b dark:border-dark-100">
                                <th class="px-4 py-3 text-left text-sm font-semibold">排名</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">API名称</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">免费额度</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">特点</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">链接</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">1</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">CoinGecko API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">50次/分钟</td>
                                <td class="px-4 py-3 text-sm">覆盖13,000+币种，含交易量数据</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://www.coingecko.com/en/api" target="_blank" class="text-primary hover:underline">coingecko.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">2</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">CoinCap API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制</td>
                                <td class="px-4 py-3 text-sm">实时价格+历史数据</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://docs.coincap.io/" target="_blank" class="text-primary hover:underline">coincap.io</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">3</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">Nomics API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">1次/秒</td>
                                <td class="px-4 py-3 text-sm">机构级数据质量</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://nomics.com/docs" target="_blank" class="text-primary hover:underline">nomics.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">4</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">CoinMarketCap API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">10,000次/月</td>
                                <td class="px-4 py-3 text-sm">行业标准数据源</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://coinmarketcap.com/api/" target="_blank" class="text-primary hover:underline">coinmarketcap.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">5</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">CryptoCompare API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制(基础版)</td>
                                <td class="px-4 py-3 text-sm">包含200+交易所数据</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://min-api.cryptocompare.com/" target="_blank" class="text-primary hover:underline">cryptocompare.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">6</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">Binance API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制</td>
                                <td class="px-4 py-3 text-sm">深度流动性数据</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://binance-docs.github.io/apidocs/" target="_blank" class="text-primary hover:underline">binance.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">7</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">KuCoin API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制</td>
                                <td class="px-4 py-3 text-sm">新兴币种覆盖全</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://docs.kucoin.com/" target="_blank" class="text-primary hover:underline">docs.kucoin.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">8</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">OKX API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制</td>
                                <td class="px-4 py-3 text-sm">衍生品数据丰富</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://www.okx.com/docs/" target="_blank" class="text-primary hover:underline">okx.com</a>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">9</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">Coinlore API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制</td>
                                <td class="px-4 py-3 text-sm">轻量级简单API</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://www.coinlore.com/cryptocurrency-data-api" target="_blank" class="text-primary hover:underline">coinlore.com</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-light-200 dark:hover:bg-dark-300 transition-custom">
                                <td class="px-4 py-3 text-sm">10</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">CoinPaprika API</div>
                                </td>
                                <td class="px-4 py-3 text-sm">无限制</td>
                                <td class="px-4 py-3 text-sm">包含defi数据</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="https://api.coinpaprika.com/" target="_blank" class="text-primary hover:underline">api.coinpaprika.com</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
        <!-- 社区部分 -->
        <section id="community" class="py-10 bg-gradient-to-b from-transparent to-primary/5 dark:to-primary/10">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-2">EACO社区与生态</h2>
                    <p class="text-dark-100 dark:text-light-400">加入我们的全球社区，了解最新动态</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- 官方链接 -->
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6">
                        <h3 class="font-semibold text-xl mb-4 flex items-center">
                            <i class="fa fa-link text-primary mr-2"></i>官方链接
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="https://linktr.ee/web3eaco" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>Web3 EACO</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://solscan.io/token/DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>Solana浏览器</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://x.com/eacocc" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>Twitter (X)</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>GitHub</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://linktr.ee/eacocc" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>备用链接</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- 社区群组 -->
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6">
                        <h3 class="font-semibold text-xl mb-4 flex items-center">
                            <i class="fa fa-comments text-primary mr-2"></i>社区群组
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="https://t.me/e_eacocc" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>英文Telegram</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/aieaco" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>华语Telegram</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/eacoespanish" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>西班牙语Telegram</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/e_vietnam" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>越南语Telegram</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/e_indonesia" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>印尼语Telegram</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/e_usdc" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>USDC讨论组</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://t.me/eacocny" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-telegram text-xs mr-2"></i>
                                    <span>CNH场外交易</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- 交易市场 -->
                    <div class="bg-light-100 dark:bg-dark-200 rounded-xl shadow-md p-6">
                        <h3 class="font-semibold text-xl mb-4 flex items-center">
                            <i class="fa fa-exchange text-primary mr-2"></i>交易市场
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="https://app.meteora.ag/dlmm/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>Meteora (E-USDT)</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.orca.so/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>Orca (E-USDC)</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://raydium.io/swap/?inputMint=DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH&outputMint=sol" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-external-link text-xs mr-2"></i>
                                    <span>Raydium (E-SOL)</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank" class="flex items-center hover:text-primary transition-custom">
                                    <i class="fa fa-github text-xs mr-2"></i>
                                    <span>E swap DApp</span>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="mt-6 pt-6 border-t dark:border-dark-100">
                            <h4 class="font-medium mb-3">EACO 理念</h4>
                            <p class="text-sm text-dark-100 dark:text-light-400 italic">
                                EACO is Earth's best friend;<br>
                                $e=$eaco.计算劳动价值，量化地球资源
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 页脚 -->
    <footer class="bg-dark-300 text-light-200 py-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-accent flex items-center justify-center">
                            <span class="text-white font-bold text-xl">E</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">EACO</h3>
                            <p class="text-xs text-light-400">Earth's Best Coin</p>
                        </div>
                    </div>
                    <p class="text-sm text-light-400 mb-4">
                        连接地球和宇宙的一切，量化地球资源，计算劳动价值。
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://x.com/eacocc" target="_blank" class="text-light-400 hover:text-white transition-custom">
                            <i class="fa fa-twitter"></i>
                        </a>
                        <a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank" class="text-light-400 hover:text-white transition-custom">
                            <i class="fa fa-github"></i>
                        </a>
                        <a href="https://t.me/e_eacocc" target="_blank" class="text-light-400 hover:text-white transition-custom">
                            <i class="fa fa-telegram"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">快速链接</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#markets" class="text-light-400 hover:text-white transition-custom">市场</a></li>
                        <li><a href="#eaco" class="text-light-400 hover:text-white transition-custom">EACO</a></li>
                        <li><a href="#converter" class="text-light-400 hover:text-white transition-custom">转换器</a></li>
                        <li><a href="#api" class="text-light-400 hover:text-white transition-custom">API资源</a></li>
                        <li><a href="#community" class="text-light-400 hover:text-white transition-custom">社区</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">资源</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank" class="text-light-400 hover:text-white transition-custom">开发者API</a></li>
                        <li><a href="#" class="text-light-400 hover:text-white transition-custom">市场数据</a></li>
                        <li><a href="#" class="text-light-400 hover:text-white transition-custom">学习中心</a></li>
                        <li><a href="#" class="text-light-400 hover:text-white transition-custom">白皮书</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">法律</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-light-400 hover:text-white transition-custom">隐私政策</a></li>
                        <li><a href="#" class="text-light-400 hover:text-white transition-custom">服务条款</a></li>
                        <li><a href="#" class="text-light-400 hover:text-white transition-custom">免责声明</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-6 border-t border-dark-100 text-center text-sm text-light-400">
                <p class="mb-2">替天容人，正心正念；一切福田，不离方寸；从心而觅，感无不通。</p>
                <p>&copy; 2023 EACO. 保留所有权利。只使用CNH离岸人民币，为了安全不使用CNY人民币字样。</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // 示例数据 - 实际应用中会从API获取
        const cryptoData = [
            {
                rank: 1,
                name: "Bitcoin",
                symbol: "BTC",
                price: 42567.89,
                change24h: 2.34,
                change7d: 5.67,
                marketCap: 812345678901,
                volume24h: 28345678901,
                supply: 18987654,
                logo: "https://picsum.photos/32/32?random=1"
            },
            {
                rank: 2,
                name: "Ethereum",
                symbol: "ETH",
                price: 2234.56,
                change24h: -1.23,
                change7d: 3.45,
                marketCap: 267890123456,
                volume24h: 15678901234,
                supply: 120345678,
                logo: "https://picsum.photos/32/32?random=2"
            },
            {
                rank: 3,
                name: "Solana",
                symbol: "SOL",
                price: 107.89,
                change24h: 5.67,
                change7d: -2.34,
                marketCap: 41234567890,
                volume24h: 3456789012,
                supply: 382345678,
                logo: "https://picsum.photos/32/32?random=3"
            },
            {
                rank: 4,
                name: "Tether",
                symbol: "USDT",
                price: 1.00,
                change24h: 0.01,
                change7d: 0.02,
                marketCap: 83234567890,
                volume24h: 67890123456,
                supply: 83234567890,
                logo: "https://picsum.photos/32/32?random=4"
            },
            {
                rank: 5,
                name: "EACO",
                symbol: "EACO",
                price: 0.0567,
                change24h: 12.34,
                change7d: 34.56,
                marketCap: 5678901234,
                volume24h: 345678901,
                supply: 100000000000,
                logo: "https://picsum.photos/32/32?random=5"
            },
            {
                rank: 6,
                name: "USD Coin",
                symbol: "USDC",
                price: 1.00,
                change24h: 0.00,
                change7d: 0.01,
                marketCap: 32123456789,
                volume24h: 12345678901,
                supply: 32123456789,
                logo: "https://picsum.photos/32/32?random=6"
            },
            {
                rank: 7,
                name: "Binance Coin",
                symbol: "BNB",
                price: 356.78,
                change24h: -0.56,
                change7d: 2.34,
                marketCap: 56789012345,
                volume24h: 2345678901,
                supply: 159123456,
                logo: "https://picsum.photos/32/32?random=7"
            },
            {
                rank: 8,
                name: "Cardano",
                symbol: "ADA",
                price: 0.56,
                change24h: 1.23,
                change7d: -0.89,
                marketCap: 19234567890,
                volume24h: 890123456,
                supply: 345678901234,
                logo: "https://picsum.photos/32/32?random=8"
            },
            {
                rank: 9,
                name: "Ripple",
                symbol: "XRP",
                price: 0.67,
                change24h: -2.34,
                change7d: 1.23,
                marketCap: 34567890123,
                volume24h: 1234567890,
                supply: 50000000000,
                logo: "https://picsum.photos/32/32?random=9"
            },
            {
                rank: 10,
                name: "Dogecoin",
                symbol: "DOGE",
                price: 0.12,
                change24h: 3.45,
                change7d: -1.23,
                marketCap: 16789012345,
                volume24h: 901234567,
                supply: 132670764298,
                logo: "https://picsum.photos/32/32?random=10"
            }
        ];
        
        // 汇率数据
        const exchangeRates = {
            eaco: { cnh: 0.345, usd: 0.048, btc: 0.0000011, eth: 0.00016, sol: 0.0032, usdt: 0.048 },
            btc: { cnh: 312000, usd: 43200, eaco: 909090, eth: 18.5, sol: 398, usdt: 43200 },
            eth: { cnh: 16800, usd: 2320, eaco: 6250, btc: 0.054, sol: 21.5, usdt: 2320 },
            sol: { cnh: 780, usd: 108, eaco: 312, btc: 0.0025, eth: 0.046, usdt: 108 },
            usdt: { cnh: 7.2, usd: 1, eaco: 20.8, btc: 0.000023, eth: 0.00043, sol: 0.0093 },
            cnh: { usd: 0.139, eaco: 2.89, btc: 0.0000032, eth: 0.00006, sol: 0.00128, usdt: 0.139 },
            usd: { cnh: 7.2, eaco: 20.8, btc: 0.000023, eth: 0.00043, sol: 0.0093, usdt: 1 }
        };
        
        // DOM元素加载完成后执行
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化主题
            initTheme();
            
            // 初始化导航交互
            initNavigation();
            
            // 加载数字货币表格
            loadCryptoTable();
            
            // 初始化转换器
            initConverter();
            
            // 初始化EACO价格图表
            initEacoChart();
            
            // 模拟API数据加载
            simulateDataLoading();
        });
        
        // 初始化主题
        function initTheme() {
            // 检查本地存储中的主题偏好
            if (localStorage.getItem('theme') === 'dark' || 
                (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // 主题切换按钮事件
            document.getElementById('themeToggle').addEventListener('click', function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            });
        }
        
        // 初始化导航交互
        function initNavigation() {
            // 移动端菜单切换
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
            
            // 搜索框切换
            const searchBtn = document.getElementById('searchBtn');
            const searchBox = document.getElementById('searchBox');
            
            searchBtn.addEventListener('click', function() {
                searchBox.classList.toggle('hidden');
                if (!searchBox.classList.contains('hidden')) {
                    searchBox.querySelector('input').focus();
                }
            });
            
            // 语言切换
            const languageBtns = document.querySelectorAll('.language-btn');
            languageBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const lang = this.getAttribute('data-lang');
                    // 这里可以添加语言切换逻辑
                    document.querySelector('.language-btn[data-lang="' + lang + '"]').parentNode.parentNode.previousElementSibling.querySelector('span').textContent = this.textContent;
                });
            });
            
            // 平滑滚动
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                        
                        // 关闭移动菜单
                        if (!mobileMenu.classList.contains('hidden')) {
                            mobileMenu.classList.add('hidden');
                        }
                    }
                });
            });
        }
        
        // 加载数字货币表格
        function loadCryptoTable() {
            const tableBody = document.getElementById('crypto-table-body');
            const loadingIndicator = document.getElementById('table-loading');
            
            // 清空表格
            tableBody.innerHTML = '';
            
            // 添加数据行
            cryptoData.forEach(crypto => {
                const row = document.createElement('tr');
                row.className = 'border-b dark:border-dark-100 hover:bg-light-200 dark:hover:bg-dark-300 transition-custom';
                
                // 确定价格变化的颜色
                const change24hClass = crypto.change24h >= 0 ? 'text-secondary' : 'text-danger';
                const change7dClass = crypto.change7d >= 0 ? 'text-secondary' : 'text-danger';
                const change24hIcon = crypto.change24h >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                const change7dIcon = crypto.change7d >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                
                row.innerHTML = `
                    <td class="px-4 py-3 text-sm">${crypto.rank}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <img src="${crypto.logo}" alt="${crypto.name}" class="w-8 h-8 rounded-full mr-3">
                            <div>
                                <div class="font-medium">${crypto.name}</div>
                                <div class="text-sm text-dark-100 dark:text-light-400">${crypto.symbol}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-medium">$${crypto.price.toFixed(2)}</td>
                    <td class="px-4 py-3 text-right ${change24hClass}">
                        <i class="fa ${change24hIcon} text-xs mr-1"></i>${Math.abs(crypto.change24h).toFixed(2)}%
                    </td>
                    <td class="px-4 py-3 text-right ${change7dClass}">
                        <i class="fa ${change7dIcon} text-xs mr-1"></i>${Math.abs(crypto.change7d).toFixed(2)}%
                    </td>
                    <td class="px-4 py-3 text-right text-sm hidden md:table-cell">
                        $${formatLargeNumber(crypto.marketCap)}
                    </td>
                    <td class="px-4 py-3 text-right text-sm hidden lg:table-cell">
                        $${formatLargeNumber(crypto.volume24h)}
                    </td>
                    <td class="px-4 py-3 text-right text-sm hidden xl:table-cell">
                        ${formatLargeNumber(crypto.supply)} ${crypto.symbol}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button class="px-2 py-1 text-xs bg-primary/10 hover:bg-primary/20 text-primary rounded transition-custom">
                            详情
                        </button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
            
            // 隐藏加载指示器
            loadingIndicator.classList.add('hidden');
        }
        
        // 初始化转换器
        function initConverter() {
            const amountInput = document.getElementById('convert-amount');
            const resultInput = document.getElementById('result-amount');
            const fromCurrency = document.getElementById('from-currency');
            const toCurrency = document.getElementById('to-currency');
            const swapBtn = document.getElementById('swap-currencies');
            const conversionInfo = document.getElementById('conversion-info');
            const lastUpdated = document.getElementById('last-updated');
            const quickConvertBtns = document.querySelectorAll('.quick-convert-btn');
            
            // 转换函数
            function convertCurrency() {
                const amount = parseFloat(amountInput.value) || 0;
                const from = fromCurrency.value;
                const to = toCurrency.value;
                
                if (from === to) {
                    resultInput.value = amount.toFixed(6);
                    conversionInfo.textContent = `1 ${from.toUpperCase()} = 1 ${to.toUpperCase()}`;
                    return;
                }
                
                let rate;
                if (from === 'usd' && to === 'cnh') {
                    rate = 7.2; // 示例汇率
                } else if (from === 'cnh' && to === 'usd') {
                    rate = 1 / 7.2;
                } else {
                    rate = exchangeRates[from][to];
                }
                
                const result = amount * rate;
                resultInput.value = result.toFixed(6);
                conversionInfo.textContent = `1 ${from.toUpperCase()} = ${rate.toFixed(6)} ${to.toUpperCase()}`;
                
                // 更新最后更新时间
                lastUpdated.textContent = '刚刚';
            }
            
            // 交换货币
            swapBtn.addEventListener('click', function() {
                const temp = fromCurrency.value;
                fromCurrency.value = toCurrency.value;
                toCurrency.value = temp;
                convertCurrency();
            });
            
            // 输入变化时转换
            amountInput.addEventListener('input', convertCurrency);
            fromCurrency.addEventListener('change', convertCurrency);
            toCurrency.addEventListener('change', convertCurrency);
            
            // 快速转换按钮
            quickConvertBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const from = this.getAttribute('data-from');
                    const to = this.getAttribute('data-to');
                    
                    fromCurrency.value = from;
                    toCurrency.value = to;
                    amountInput.value = 1;
                    convertCurrency();
                });
            });
            
            // 初始转换
            convertCurrency();
        }
        
        // 初始化EACO价格图表
        function initEacoChart() {
            const ctx = document.getElementById('eacoChart').getContext('2d');
            
            // 生成过去24小时的时间标签
            const labels = [];
            const now = new Date();
            for (let i = 23; i >= 0; i--) {
                const hour = new Date(now.getTime() - i * 60 * 60 * 1000);
                labels.push(hour.getHours() + ':00');
            }
            
            // 生成随机价格数据
            const basePrice = 0.056;
            const eacoData = [];
            const meteoraData = [];
            const orcaData = [];
            const raydiumData = [];
            
            for (let i = 0; i < 24; i++) {
                const randomChange = (Math.random() - 0.45) * 0.01;
                const price = basePrice + randomChange;
                
                eacoData.push(price.toFixed(6));
                meteoraData.push((price + (Math.random() * 0.001 - 0.0005)).toFixed(6));
                orcaData.push((price + (Math.random() * 0.001 - 0.0005)).toFixed(6));
                raydiumData.push((price + (Math.random() * 0.001 - 0.0005)).toFixed(6));
            }
            
            // 创建图表
            const eacoChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'EACO (平均)',
                            data: eacoData,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 4
                        },
                        {
                            label: 'Meteora',
                            data: meteoraData,
                            borderColor: '#10B981',
                            borderWidth: 1.5,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 3
                        },
                        {
                            label: 'Orca',
                            data: orcaData,
                            borderColor: '#8B5CF6',
                            borderWidth: 1.5,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 3
                        },
                        {
                            label: 'Raydium',
                            data: raydiumData,
                            borderColor: '#F59E0B',
                            borderWidth: 1.5,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 6
                            }
                        },
                        tooltip: {
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                borderDash: [2, 4],
                                color: document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // 时间筛选按钮事件
            const timeFilters = document.querySelectorAll('.time-filter');
            timeFilters.forEach(btn => {
                btn.addEventListener('click', function() {
                    // 移除所有按钮的活跃状态
                    timeFilters.forEach(b => {
                        b.classList.remove('bg-primary', 'text-white');
                        b.classList.add('hover:bg-light-300', 'dark:hover:bg-dark-100');
                    });
                    
                    // 设置当前按钮为活跃状态
                    this.classList.add('bg-primary', 'text-white');
                    this.classList.remove('hover:bg-light-300', 'dark:hover:bg-dark-100');
                    
                    // 在实际应用中，这里会根据选择的时间范围更新图表数据
                });
            });
        }
        
        // 模拟数据加载
        function simulateDataLoading() {
            // 模拟EACO价格数据
            const eacoPrice = 0.0567;
            const meteoraPrice = (eacoPrice + (Math.random() * 0.001 - 0.0005)).toFixed(6);
            const orcaPrice = (eacoPrice + (Math.random() * 0.001 - 0.0005)).toFixed(6);
            const raydiumPrice = (eacoPrice + (Math.random() * 0.001 - 0.0005)).toFixed(6);
            
            const change24h = (Math.random() * 5 + 8).toFixed(2); // 8-13% 随机涨幅
            
            document.getElementById('meteora-price').textContent = meteoraPrice;
            document.getElementById('orca-price').textContent = orcaPrice;
            document.getElementById('raydium-price').textContent = raydiumPrice;
            
            document.getElementById('meteora-change').innerHTML = `<i class="fa fa-arrow-up mr-1"></i>${change24h}%`;
            document.getElementById('orca-change').innerHTML = `<i class="fa fa-arrow-up mr-1"></i>${(parseFloat(change24h) - 0.5).toFixed(2)}%`;
            document.getElementById('raydium-change').innerHTML = `<i class="fa fa-arrow-up mr-1"></i>${(parseFloat(change24h) + 0.3).toFixed(2)}%`;
            
            // 模拟市场概览数据
            document.getElementById('total-marketcap').textContent = '$' + formatLargeNumber(1892345678901);
            document.getElementById('marketcap-change').innerHTML = `<i class="fa fa-arrow-up mr-1"></i>2.34%`;
            
            document.getElementById('total-volume').textContent = '$' + formatLargeNumber(892345678901);
            document.getElementById('total-volume').textContent = '$' + formatLargeNumber(892345678901);
            document.getElementById('volume-change').innerHTML = `<i class="fa fa-arrow-up mr-1"></i>5.67%`;
            
            document.getElementById('btc-dominance').textContent = '42.89%';
            document.getElementById('dominance-change').innerHTML = `<i class="fa fa-arrow-down mr-1"></i>0.23%`;
            
            document.getElementById('active-cryptos').textContent = '10432';
            
            // 模拟EACO基本信息
            document.getElementById('eaco-marketcap').textContent = '$' + formatLargeNumber(5678901234);
            document.getElementById('eaco-volume').textContent = '$' + formatLargeNumber(345678901);
            document.getElementById('eaco-supply').textContent = formatLargeNumber(100000000000) + ' EACO';
            
            // API来源切换
            const apiSourceBtns = document.querySelectorAll('.api-source-btn');
            apiSourceBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // 移除所有按钮的活跃状态
                    apiSourceBtns.forEach(b => {
                        b.classList.remove('bg-primary', 'text-white');
                        b.classList.add('hover:bg-light-300', 'dark:hover:bg-dark-100');
                    });
                    
                    // 设置当前按钮为活跃状态
                    this.classList.add('bg-primary', 'text-white');
                    this.classList.remove('hover:bg-light-300', 'dark:hover:bg-dark-100');
                    
                    // 在实际应用中，这里会切换数据源并重新加载数据
                    document.getElementById('table-loading').classList.remove('hidden');
                    setTimeout(loadCryptoTable, 800);
                });
            });
        }
        
        // 格式化大数字显示
        function formatLargeNumber(num) {
            if (num >= 1000000000000) {
                return (num / 1000000000000).toFixed(2) + 'T';
            } else if (num >= 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B';
            } else if (num >= 1000000) {
                return (num / 1000000).toFixed(2) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(2) + 'K';
            }
            return num.toFixed(0);
        }
    </script>
</body>
</html>
