<?php
/**
 * EACO 短代码功能 - 在文章中嵌入宇宙价值数据
 */

// 1. 基础汇率短代码 [eaco]
add_shortcode('eaco', 'eaco_basic_shortcode');
function eaco_basic_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => 'price', // 默认为价格展示
    ], $atts);
    
    $data = eaco_get_price();
    
    ob_start();
    ?>
    <div class="eaco-widget eaco-price-widget" style="border: 1px solid #eee; border-radius: 8px; padding: 15px; margin: 15px 0; background: #f9f9f9;">
        <h3 style="margin-top: 0; color: #2c3e50;">EACO 地球链 - 宇宙价值数据</h3>
        <div class="eaco-price">
            <h4 style="color: #3498db;">实时汇率</h4>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($data['price'] as $symbol => $value): ?>
                    <li style="margin: 5px 0; padding: 5px; background: #fff; border-radius: 4px;">
                        <strong><?php echo esc_html($symbol); ?>:</strong> 
                        <?php echo esc_html($value); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <p style="font-style: italic; color: #7f8c8d; margin-top: 10px;">
            "<?php echo esc_html($data['quote']); ?>"
        </p>
        <p style="font-size: 0.8em; color: #95a5a6; text-align: right;">
            数据来源: <a href="https://solscan.io/token/DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH" target="_blank">Solscan</a>
        </p>
    </div>
    <?php
    return ob_get_clean();
}

// 2. 锁仓量短代码 [eaco_tvl]
add_shortcode('eaco_tvl', 'eaco_tvl_shortcode');
function eaco_tvl_shortcode() {
    $data = eaco_get_tvl();
    
    ob_start();
    ?>
    <div class="eaco-widget eaco-tvl-widget" style="border: 1px solid #eee; border-radius: 8px; padding: 15px; margin: 15px 0; background: #f9f9f9;">
        <h3 style="margin-top: 0; color: #2c3e50;">EACO 锁仓量 (TVL)</h3>
        <div class="eaco-total-tvl" style="font-size: 1.2em; margin: 10px 0;">
            总锁仓价值: <strong style="color: #27ae60;"><?php echo esc_html($data['tvl_usd']); ?> USD</strong>
        </div>
        <div class="eaco-pools">
            <h4 style="color: #3498db;">交易对分布</h4>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($data['pools'] as $pool): ?>
                    <li style="margin: 5px 0; padding: 5px; background: #fff; border-radius: 4px;">
                        <a href="<?php echo esc_url($pool['url']); ?>" target="_blank" style="text-decoration: none; color: #3498db;">
                            <?php echo esc_html($pool['name']); ?>
                        </a>: 
                        <?php echo esc_html($pool['tvl']); ?> USD
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <p style="font-style: italic; color: #7f8c8d; margin-top: 10px;">
            "<?php echo esc_html($data['quote']); ?>"
        </p>
    </div>
    <?php
    return ob_get_clean();
}

// 3. 兑换计算器短代码 [eaco_swap]
add_shortcode('eaco_swap', 'eaco_swap_shortcode');
function eaco_swap_shortcode() {
    ob_start();
    ?>
    <div class="eaco-widget eaco-swap-widget" style="border: 1px solid #eee; border-radius: 8px; padding: 15px; margin: 15px 0; background: #f9f9f9;">
        <h3 style="margin-top: 0; color: #2c3e50;">EACO 兑换计算器</h3>
        <form id="eaco-swap-form" style="margin: 15px 0;">
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">源代币:</label>
                <select name="from" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="USDC">USDC</option>
                    <option value="USDT">USDT</option>
                    <option value="SOL">SOL</option>
                    <option value="EACO">EACO</option>
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">目标代币:</label>
                <select name="to" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="EACO">EACO</option>
                    <option value="USDC">USDC</option>
                    <option value="USDT">USDT</option>
                    <option value="SOL">SOL</option>
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">数量:</label>
                <input type="number" name="amount" step="0.01" min="0.01" value="100" 
                       style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
            </div>
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;">
                估算兑换
            </button>
        </form>
        
        <div id="eaco-swap-result" style="margin-top: 15px; padding: 10px; border-radius: 4px; display: none;"></div>
        
        <p style="font-style: italic; color: #7f8c8d; margin-top: 10px;" id="eaco-swap-quote">
            "每一次交易，都是一次宇宙的脉动"
        </p>
        
        <script>
        document.getElementById('eaco-swap-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const from = formData.get('from');
            const to = formData.get('to');
            const amount = formData.get('amount');
            
            const resultDiv = document.getElementById('eaco-swap-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>正在计算宇宙价值流动...</p>';
            
            // 调用 WordPress REST API
            fetch('/wp-json/eaco/v1/swap/estimate?from=' + from + '&to=' + to + '&amount=' + amount)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        resultDiv.innerHTML = '<p style="color: #e74c3c;">' + data.error + '</p>';
                    } else {
                        resultDiv.innerHTML = `
                            <div style="background: #fff; padding: 10px; border-radius: 4px;">
                                <p><strong>${data.from} 数量:</strong> ${data.inputAmount}</p>
                                <p><strong>预计获得 ${data.to}:</strong> ${data.estimatedOutput.toFixed(4)}</p>
                                <p><strong>滑点:</strong> ${data.slippage}%</p>
                            </div>
                            <p style="margin-top: 10px; font-size: 0.9em;">
                                实际兑换请访问: <a href="https://github.com/eacocc/EACO_Exchange_DApp" target="_blank">EACO 兑换 DApp</a>
                            </p>
                        `;
                    }
                    
                    // 更新哲学语句
                    if (data.quote) {
                        document.getElementById('eaco-swap-quote').textContent = '"' + data.quote + '"';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = '<p style="color: #e74c3c;">兑换估算失败，请稍后再试</p>';
                    console.error('EACO 兑换错误:', error);
                });
        });
        </script>
    </div>
    <?php
    return ob_get_clean();
}
