<?php
/**
 * EACO 地球链函数库 - 连接 WordPress 与宇宙价值网络
 */

// EACO API 基础配置
define('EACO_SWAP_API_URL', 'https://your-eaco-api.vercel.app'); // 替换为你的 Node.js API 地址
define('EACO_PHILOSOPHY', [
    'price' => "代码即财富，e 连接地球与宇宙万物",
    'tvl' => "文明的价值，在于可交换的信任",
    'swap' => "每一次交易，都是一次宇宙的脉动"
]);

/**
 * 获取 EACO 实时价格 (通过 DexScreener API)
 */
function eaco_get_price() {
    $url = 'https://api.dexscreener.com/latest/dex/pairs/solana/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr'; // EACO-USDC 交易对
    
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        return [
            'symbol' => 'EACO',
            'price' => ['USDC' => '获取失败', 'USDT' => '获取失败', 'SOL' => '获取失败'],
            'quote' => EACO_PHILOSOPHY['price']
        ];
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    return [
        'symbol' => 'EACO',
        'price' => [
            'USDC' => number_format($body['pair']['priceUsd'], 4),
            'USDT' => number_format($body['pair']['priceUsd'], 4),
            'SOL' => number_format($body['pair']['priceNative'], 6)
        ],
        'timestamp' => time(),
        'quote' => EACO_PHILOSOPHY['price']
    ];
}

/**
 * 获取 EACO 锁仓量 (TVL)
 */
function eaco_get_tvl() {
    // 从 Meteora 和 Orca 聚合 TVL 数据
    $pools = [
        [
            'name' => 'EACO-USDC (Orca)',
            'url' => 'https://api.dexscreener.com/latest/dex/pairs/solana/Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr'
        ],
        [
            'name' => 'EACO-USDT (Meteora)',
            'url' => 'https://api.dexscreener.com/latest/dex/pairs/solana/6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE'
        ],
        [
            'name' => 'EACO-SOL (Raydium)',
            'url' => 'https://api.dexscreener.com/latest/dex/pairs/solana/GsDB4iKELP7KDVjn5ZcHsJhWRY8J3HqTxvE86zyDhV34'
        ]
    ];
    
    $tvlData = ['tvl_usd' => 0, 'pools' => []];
    
    foreach ($pools as $pool) {
        $response = wp_remote_get($pool['url']);
        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            $tvl = $data['pair']['liquidity']['usd'] ?? 0;
            
            $tvlData['tvl_usd'] += $tvl;
            $tvlData['pools'][] = [
                'name' => $pool['name'],
                'tvl' => number_format($tvl, 0),
                'url' => "https://app.meteora.ag/dlmm/{$data['pair']['address']}"
            ];
        }
    }
    
    $tvlData['tvl_usd'] = number_format($tvlData['tvl_usd'], 0);
    $tvlData['quote'] = EACO_PHILOSOPHY['tvl'];
    
    return $tvlData;
}

/**
 * 代理调用 Node.js 兑换估算 API
 */
function eaco_get_swap_estimate($request) {
    $from = sanitize_text_field($request->get_param('from'));
    $to = sanitize_text_field($request->get_param('to'));
    $amount = sanitize_text_field($request->get_param('amount'));
    
    // 验证参数
    if (empty($from) || empty($to) || empty($amount) || !is_numeric($amount)) {
        return [
            'error' => '参数错误，请提供有效的代币类型和数量',
            'quote' => EACO_PHILOSOPHY['swap']
        ];
    }
    
    // 调用 Node.js API
    $url = add_query_arg([
        'from' => $from,
        'to' => $to,
        'amount' => $amount
    ], EACO_SWAP_API_URL . '/swap/estimate');
    
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        return [
            'error' => '无法连接到 EACO 兑换服务',
            'quote' => EACO_PHILOSOPHY['swap']
        ];
    }
    
    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);
    
    // 如果 API 返回错误，添加哲学语句
    if (isset($result['error'])) {
        $result['quote'] = EACO_PHILOSOPHY['swap'];
    }
    
    return $result;
}

/**
 * 获取哲学语句
 */
function eaco_get_philosophy($request) {
    $tag = sanitize_text_field($request->get_param('tag'));
    $allQuotes = [
        '交易' => [
            "每一次交易，都是一次宇宙的脉动",
            "流动是文明的语言，兑换是价值的诗歌"
        ],
        '价值' => [
            "EACO 量化劳动，记录地球的呼吸",
            "代码即财富，e 连接万物"
        ],
        '宇宙' => [
            "EACO 是地球最好的朋友，也是宇宙的使者",
            "唯一的 $e，连接地球与星辰"
        ]
    ];
    
    if ($tag && isset($allQuotes[$tag])) {
        return ['quotes' => $allQuotes[$tag]];
    }
    
    // 随机返回一条语句
    $randomCategory = array_rand($allQuotes);
    return ['quote' => $allQuotes[$randomCategory][array_rand($allQuotes[$randomCategory])]];
}
