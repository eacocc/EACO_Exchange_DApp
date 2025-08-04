<?php
// eaco_exchange.php
// EACO Exchange DApp - 单文件集成版
// 包含前端展示 + API 数据获取 + Web3 交互逻辑
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EACO Swap - 地球村民的去中心化交易所</title>
  <script src="https://cdn.jsdelivr.net/npm/@solana/web3.js@latest/lib/index.iife.min.js"></script>
  <script src="https://unpkg.com/@solflare-wallet/sdk@1.0.12/dist/index.min.js"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; background: #f8f9fa; color: #333; }
    .container { max-width: 1000px; margin: 0 auto; }
    header { text-align: center; margin-bottom: 30px; }
    h1 { color: #2c3e50; }
    .tagline { color: #7f8c8d; font-size: 1.1em; margin-bottom: 20px; }
    .card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px; margin: 15px 0; }
    .price-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .price-item { padding: 12px; background: #f1f8ff; border-left: 4px solid #3498db; }
    .price-label { font-weight: bold; color: #2980b9; }
    .price-value { font-size: 1.2em; color: #2c3e50; }
    button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1em; }
    button:hover { background: #2980b9; }
    .wallet-info { margin: 10px 0; font-size: 0.9em; color: #555; }
    footer { text-align: center; margin-top: 50px; color: #95a5a6; font-size: 0.9em; }
    .loading { color: #7f8c8d; font-style: italic; }
    .success { color: #27ae60; }
    .error { color: #e74c3c; }
  </style>
</head>
<body>

<div class="container">
  <header>
    <h1>EACO Swap Beta 0.101</h1>
    <p class="tagline">代码即财富，共识即边界 —— 让地球村民平等参与经济建设</p>
    <button id="connectWallet">连接钱包</button>
    <div class="wallet-info" id="walletStatus">未连接钱包</div>
  </header>

  <!-- EACO 基本信息 -->
  <div class="card">
    <h2>🌍 EACO ($e) 信息</h2>
    <p><strong>合约地址 (Solana):</strong> <code>DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH</code></p>
    <p><strong>总供应量:</strong> 1.35 亿 EACO</p>
    <p><strong>当前流通量:</strong> ~1.35 亿 EACO (2025)</p>
    <p><strong>区块链:</strong> Solana</p>
    <p><strong>官网:</strong> <a href="https://linktr.ee/web3eaco" target="_blank">linktr.ee/web3eaco</a></p>
  </div>

  <!-- 实时价格显示 -->
  <div class="card">
    <h2>📊 EACO 实时价格</h2>
    <div class="price-grid">
      <div class="price-item">
        <div class="price-label">EACO/USDT</div>
        <div class="price-value" id="price-usdt" class="loading">加载中...</div>
      </div>
      <div class="price-item">
        <div class="price-label">EACO/USDC</div>
        <div class="price-value" id="price-usdc" class="loading">加载中...</div>
      </div>
      <div class="price-item">
        <div class="price-label">EACO/SOL</div>
        <div class="price-value" id="price-sol" class="loading">加载中...</div>
      </div>
      <div class="price-item">
        <div class="price-label">EACO/USD</div>
        <div class="price-value" id="price-usd" class="loading">加载中...</div>
      </div>
      <div class="price-item">
        <div class="price-label">EACO/CNY</div>
        <div class="price-value" id="price-cny" class="loading">加载中...</div>
      </div>
      <div class="price-item">
        <div class="price-label">EACO/EUR</div>
        <div class="price-value" id="price-eur" class="loading">加载中...</div>
      </div>
    </div>
  </div>

  <!-- 兑换功能（预留） -->
  <div class="card">
    <h2>🔁 兑换功能（开发中）</h2>
    <p>支持 EACO 与全球前 100 大数字货币/法币兑换</p>
    <p><strong>手续费:</strong> 0.5% - 5%（可选支付方式：SOL / USDC / USDT / EACO）</p>
    <p><strong>目标平台:</strong> Meteora, Orca, Raydium</p>
    <button id="swapBtn" disabled>兑换 (开发中)</button>
  </div>

  <!-- 交易链接 -->
  <div class="card">
    <h2>🔗 快速交易链接</h2>
    <ul>
      <li><a href="https://app.meteora.ag/dlmm/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE" target="_blank">EACO/USDT @ Meteora</a></li>
      <li><a href="https://www.orca.so/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr" target="_blank">EACO/USDC @ Orca</a></li>
      <li><a href="https://raydium.io/swap/?inputMint=DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH&outputMint=sol" target="_blank">EACO/SOL @ Raydium</a></li>
    </ul>
  </div>

  <footer>
    <p>© 2025 EACO Community. <a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank">GitHub 开源</a></p>
    <p>合规发展，正能量驱动。支持地区：新加坡、瑞士、日本、美国（合规州）等</p>
  </footer>
</div>

<script>
// ============ 智能合约交互逻辑 ============

// Solana 连接
const network = "https://api.mainnet-beta.solana.com";
const connection = new solanaWeb3.Connection(network);
let wallet;

// 连接钱包（支持 Solflare 或 Phantom）
document.getElementById('connectWallet').onclick = async () => {
  if (!window.solflare) {
    alert("请安装 Solflare 或 Phantom 钱包扩展");
    return;
  }
  try {
    await window.solflare.connect();
    wallet = window.solflare.publicKey;
    document.getElementById('walletStatus').innerHTML = `已连接: ${wallet.toBase58().slice(0,8)}...`;
    document.getElementById('connectWallet').disabled = true;
  } catch (err) {
    console.error(err);
    document.getElementById('walletStatus').innerHTML = `<span class="error">连接失败</span>`;
  }
};

// ============ 价格获取逻辑 ============

// 1. 从 Meteora 获取 EACO/USDT 价格
async function getEACO_USDT() {
  try {
    const res = await fetch('https://api.meteora.ag/dlmm/pools/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE');
    const data = await res.json();
    // 简化计算：当前价格 = sqrtPrice^2 / 10^(差值)
    const price = Math.pow(data.current_sqrt_price / 1e6, 2); // 假设 USDT 为 6 位
    return price;
  } catch (e) {
    console.error("Meteora USDT API Error:", e);
    return null;
  }
}

// 2. 从 Orca 获取 EACO/USDC 价格
async function getEACO_USDC() {
  try {
    const res = await fetch('https://api.orca.so/v1/pools/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr');
    const data = await res.json();
    return data.price; // 假设返回结构中有 price 字段
  } catch (e) {
    console.error("Orca USDC API Error:", e);
    return null;
  }
}

// 3. 从 Raydium 获取 EACO/SOL 价格
async function getEACO_SOL() {
  try {
    const res = await fetch('https://api.raydium.io/pairs');
    const pairs = await res.json();
    const pair = pairs.find(p => 
      (p.baseMint === 'DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH' && p.quoteName === 'SOL') ||
      (p.quoteMint === 'DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH' && p.baseName === 'SOL')
    );
    return pair ? pair.price : null;
  } catch (e) {
    console.error("Raydium SOL API Error:", e);
    return null;
  }
}

// 4. 获取法定货币汇率（USD 为基准）
async function getFiatRates() {
  try {
    const res = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
    const data = await res.json();
    return {
      CNY: data.rates.CNY,
      EUR: data.rates.EUR,
      JPY: data.rates.JPY,
      KRW: data.rates.KRW,
      INR: data.rates.INR
    };
  } catch (e) {
    console.error("Fiat API Error:", e);
    return { CNY: 7.2, EUR: 0.93, JPY: 150, KRW: 1350, INR: 83 }; // fallback
  }
}

// 5. 更新所有价格
async function updatePrices() {
  const usdt = await getEACO_USDT();
  const usdc = await getEACO_USDC();
  const sol = await getEACO_SOL();
  const fiat = await getFiatRates();

  const eacoUsd = usdt || 0.0032; // fallback

  document.getElementById('price-usdt').textContent = usdt ? `${usdt.toFixed(6)} USDT` : 'N/A';
  document.getElementById('price-usdc').textContent = usdc ? `${usdc.toFixed(6)} USDC` : 'N/A';
  document.getElementById('price-sol').textContent = sol ? `${sol} SOL` : 'N/A';
  document.getElementById('price-usd').textContent = `${eacoUsd} USD`;
  document.getElementById('price-cny').textContent = `${(eacoUsd * fiat.CNY).toFixed(4)} CNY`;
  document.getElementById('price-eur').textContent = `${(eacoUsd * fiat.EUR).toFixed(4)} EUR`;
}

// 初始化
updatePrices();
setInterval(updatePrices, 30000); // 每 30 秒更新

// ============ 手续费逻辑（预留） ============
// 后续可在此添加：根据用户选择的支付币种计算手续费
function calculateFee(amount, rate = 0.01) { // 默认 1%
  return amount * rate;
}

</script>

</body>
</html>