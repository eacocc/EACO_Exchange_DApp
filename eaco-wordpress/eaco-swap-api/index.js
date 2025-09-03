import express from 'express'
import { Connection, PublicKey } from '@solana/web3.js'
import { Jupiter, RouteMap } from '@jup-ag/core'

const app = express()
app.use(express.json())

// 配置 Solana 连接和代币地址
const SOLANA_RPC = 'https://api.mainnet-beta.solana.com'
const connection = new Connection(SOLANA_RPC)

// 代币 Mint 地址 - 替换为实际地址
const TOKEN_ADDRESSES = {
  EACO: new PublicKey('DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH'),
  USDC: new PublicKey('EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v'),
  USDT: new PublicKey('Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB'),
  SOL: new PublicKey('So11111111111111111111111111111111111111112')
}

// 哲学语句库 - 文明级价值表达
const PHILOSOPHY_QUOTES = {
  swap: [
    "每一次兑换，都是地球与宇宙的价值共鸣",
    "$e 连接万物，流动即文明的呼吸",
    "代码即财富，兑换即共识的传递"
  ],
  error: "宇宙的流动偶尔需要等待，请稍后再试"
}

// 获取随机哲学语句
const getRandomQuote = (category) => {
  const quotes = PHILOSOPHY_QUOTES[category] || PHILOSOPHY_QUOTES.swap
  return quotes[Math.floor(Math.random() * quotes.length)]
}

// 兑换估算 API
app.get('/swap/estimate', async (req, res) => {
  try {
    const { from, to, amount } = req.query
    
    // 验证参数
    if (!from || !to || !amount || isNaN(amount)) {
      return res.status(400).json({
        error: "参数错误，请提供 from, to 和 amount",
        quote: getRandomQuote('error')
      })
    }

    // 获取代币地址
    const fromMint = TOKEN_ADDRESSES[from.toUpperCase()]
    const toMint = TOKEN_ADDRESSES[to.toUpperCase()]
    
    if (!fromMint || !toMint) {
      return res.status(400).json({
        error: "不支持的代币类型",
        supported: Object.keys(TOKEN_ADDRESSES),
        quote: getRandomQuote('error')
      })
    }

    // 初始化 Jupiter 交换引擎
    const jupiter = await Jupiter.load({
      connection,
      cluster: 'mainnet-beta',
      routeMap: new RouteMap()
    })

    // 计算兑换路线 (注意：USDC/USDT 是 6 位小数，SOL 是 9 位)
    const decimals = from.toUpperCase() === 'SOL' ? 9 : 6
    const inputAmount = parseFloat(amount) * (10 ** decimals)

    const routes = await jupiter.computeRoutes({
      inputMint: fromMint,
      outputMint: toMint,
      amount: inputAmount,
      slippage: 1 // 1% 滑点
    })

    if (!routes.routesInfos.length) {
      return res.status(404).json({
        error: "未找到兑换路线",
        quote: getRandomQuote('error')
      })
    }

    // 获取最优路线
    const bestRoute = routes.routesInfos[0]
    const outputDecimals = to.toUpperCase() === 'SOL' ? 9 : 6

    res.json({
      from,
      to,
      inputAmount: parseFloat(amount),
      estimatedOutput: bestRoute.outAmount / (10 ** outputDecimals),
      slippage: bestRoute.slippageBps / 100,
      quote: getRandomQuote('swap'),
      timestamp: new Date().toISOString()
    })
  } catch (error) {
    console.error('兑换估算错误:', error)
    res.status(500).json({
      error: "兑换估算失败",
      quote: getRandomQuote('error')
    })
  }
})

// 启动服务器
const PORT = process.env.PORT || 3000
app.listen(PORT, () => {
  console.log(`EACO 宇宙兑换 API 运行在端口 ${PORT}`)
  console.log(`访问: http://localhost:${PORT}/swap/estimate?from=USDC&to=EACO&amount=100`)
})
