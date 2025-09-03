<?php
/**
 * EACO API 路由注册 - 构建 WordPress 与宇宙的接口
 */

add_action('rest_api_init', 'eaco_register_rest_routes');
function eaco_register_rest_routes() {
    // 命名空间：eaco/v1
    $namespace = 'eaco/v1';
    
    // 1. 获取价格数据
    register_rest_route($namespace, '/price', [
        'methods' => 'GET',
        'callback' => 'eaco_get_price',
        'permission_callback' => '__return_true', // 公开访问
        'description' => '获取 EACO 与 USDC、USDT、SOL 的实时汇率',
        'args' => []
    ]);
    
    // 2. 获取锁仓量数据
    register_rest_route($namespace, '/tvl', [
        'methods' => 'GET',
        'callback' => 'eaco_get_tvl',
        'permission_callback' => '__return_true',
        'description' => '获取 EACO 各交易对的总锁仓量 (TVL)',
        'args' => []
    ]);
    
    // 3. 兑换估算
    register_rest_route($namespace, '/swap/estimate', [
        'methods' => 'GET',
        'callback' => 'eaco_get_swap_estimate',
        'permission_callback' => '__return_true',
        'description' => '估算 EACO 与其他代币的兑换结果',
        'args' => [
            'from' => [
                'required' => true,
                'type' => 'string',
                'description' => '源代币 (支持: EACO, USDC, USDT, SOL)',
                'enum' => ['EACO', 'USDC', 'USDT', 'SOL']
            ],
            'to' => [
                'required' => true,
                'type' => 'string',
                'description' => '目标代币 (支持: EACO, USDC, USDT, SOL)',
                'enum' => ['EACO', 'USDC', 'USDT', 'SOL']
            ],
            'amount' => [
                'required' => true,
                'type' => 'number',
                'description' => '兑换数量'
            ]
        ]
    ]);
    
    // 4. 获取哲学语句
    register_rest_route($namespace, '/philosophy', [
        'methods' => 'GET',
        'callback' => 'eaco_get_philosophy',
        'permission_callback' => '__return_true',
        'description' => '获取 EACO 哲学语句',
        'args' => [
            'tag' => [
                'type' => 'string',
                'description' => '语句标签 (可选: 交易, 价值, 宇宙)',
                'enum' => ['交易', '价值', '宇宙']
            ]
        ]
    ]);
}
