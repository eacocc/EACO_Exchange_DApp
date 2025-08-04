import requests
import time
import json
from datetime import datetime
import threading

# EACO的SOL合约地址
EACO_MINT = "DqfoyZH96RnvZusSp3Cdncjpyp3C74ZmJzGhjmHnDHRH"
USDT_MINT = "Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB"
USDC_MINT = "EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v"
SOL_MINT = "So11111111111111111111111111111111111111112"

# 存储最新价格的变量
latest_prices = {
    "meteora_e_usdt": 0,
    "orca_e_usdc": 0,
    "raydium_e_sol": 0,
    "sol_usdt": 0,
    "usdt_cnh": 0,
    "updated_at": ""
}

def get_sol_usdt_price():
    """从CoinGecko获取SOL/USDT价格"""
    try:
        url = f"https://api.coingecko.com/api/v3/simple/price?ids=solana&vs_currencies=usdt"
        response = requests.get(url)
        data = response.json()
        return float(data["solana"]["usdt"])
    except Exception as e:
        print(f"获取SOL/USDT价格失败: {e}")
        return 0

def get_usdt_cnh_price():
    """从API获取USDT/CNH(离岸人民币)汇率"""
    try:
        # 使用 exchangerate-api 获取USD/CNH汇率，USDT与USD近似
        url = f"https://open.er-api.com/v6/latest/USD"
        response = requests.get(url)
        data = response.json()
        return float(data["rates"]["CNH"])
    except Exception as e:
        print(f"获取USDT/CNH汇率失败: {e}")
        return 7.25  # 默认 fallback 汇率

def get_meteora_e_usdt_price():
    """从Meteora获取EACO/USDT价格"""
    try:
        # Meteora API - 获取特定池的信息
        pool_id = "6ZfCi3qzhgDN1ygHVYXvfsfrwz8ZhQ7hD5mJtjeuUDyE"
        url = f"https://api.meteora.ag/v1/pools/{pool_id}"
        
        response = requests.get(url)
        data = response.json()
        
        # 计算价格: EACO/USDT
        if data["data"]["baseMint"] == EACO_MINT:
            base_reserve = float(data["data"]["baseReserve"])
            quote_reserve = float(data["data"]["quoteReserve"])
            # 价格 = quoteReserve / baseReserve (USDT / EACO)
            return quote_reserve / base_reserve
        else:
            base_reserve = float(data["data"]["baseReserve"])
            quote_reserve = float(data["data"]["quoteReserve"])
            # 价格 = baseReserve / quoteReserve (USDT / EACO)
            return base_reserve / quote_reserve
    except Exception as e:
        print(f"获取Meteora EACO/USDT价格失败: {e}")
        return 0

def get_orca_e_usdc_price():
    """从Orca获取EACO/USDC价格"""
    try:
        # Orca API - 获取池信息
        pool_id = "Cm6EkxcYNfvxeYDBQ3TGXFqa9NCWvrFKHz4Cfju91dhr"
        url = f"https://api.orca.so/v1/pools/{pool_id}"
        
        response = requests.get(url)
        data = response.json()
        
        # 计算EACO/USDC价格
        for token in data["tokens"]:
            if token["mint"] == EACO_MINT:
                eaco_balance = float(token["balance"])
            elif token["mint"] == USDC_MINT:
                usdc_balance = float(token["balance"])
        
        # 价格 = USDC余额 / EACO余额
        return usdc_balance / eaco_balance
    except Exception as e:
        print(f"获取Orca EACO/USDC价格失败: {e}")
        return 0

def get_raydium_e_sol_price():
    """从Raydium获取EACO/SOL价格"""
    try:
        # Raydium API - 获取所有池并筛选
        url = "https://api.raydium.io/v2/sdk/liquidity/mainnet.json"
        response = requests.get(url)
        data = response.json()
        
        # 查找EACO-SOL交易对
        for market in data["markets"]:
            if (market["baseMint"] == EACO_MINT and market["quoteMint"] == SOL_MINT) or \
               (market["baseMint"] == SOL_MINT and market["quoteMint"] == EACO_MINT):
                
                base_reserve = float(market["baseReserve"])
                quote_reserve = float(market["quoteReserve"])
                
                # 计算价格
                if market["baseMint"] == EACO_MINT:
                    # EACO/SOL价格 = quoteReserve / baseReserve
                    return quote_reserve / base_reserve
                else:
                    # EACO/SOL价格 = baseReserve / quoteReserve
                    return base_reserve / quote_reserve
        
        return 0
    except Exception as e:
        print(f"获取Raydium EACO/SOL价格失败: {e}")
        return 0

def fetch_all_prices():
    """获取所有相关价格"""
    global latest_prices
    
    # 并行获取价格以提高效率
    meteora_price = get_meteora_e_usdt_price()
    orca_price = get_orca_e_usdc_price()
    raydium_price = get_raydium_e_sol_price()
    sol_price = get_sol_usdt_price()
    usdt_cnh = get_usdt_cnh_price()
    
    # 更新价格字典
    latest_prices = {
        "meteora_e_usdt": meteora_price,
        "orca_e_usdc": orca_price,
        "raydium_e_sol": raydium_price,
        "sol_usdt": sol_price,
        # 计算EACO/SOL转换为USDT的价格
        "raydium_e_usdt": raydium_price * sol_price,
        # 计算平均价格
        "average_e_usdt": (meteora_price + orca_price + (raydium_price * sol_price)) / 3 if all([meteora_price, orca_price, raydium_price, sol_price]) else 0,
        "usdt_cnh": usdt_cnh,
        # 计算CNH价格
        "average_e_cnh": ((meteora_price + orca_price + (raydium_price * sol_price)) / 3) * usdt_cnh if all([meteora_price, orca_price, raydium_price, sol_price, usdt_cnh]) else 0,
        "updated_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    
    print(f"价格已更新: {latest_prices['updated_at']}")
    return latest_prices

def scheduled_fetch(interval=60):
    """定时获取价格，默认60秒一次"""
    while True:
        fetch_all_prices()
        time.sleep(interval)

def start_price_monitor(interval=60):
    """启动价格监控线程"""
    monitor_thread = threading.Thread(target=scheduled_fetch, args=(interval,), daemon=True)
    monitor_thread.start()
    print(f"价格监控已启动，每{interval}秒更新一次")
    return monitor_thread

def display_prices():
    """格式化显示价格"""
    if not latest_prices["updated_at"]:
        print("尚未获取价格数据，请等待更新...")
        return
    
    print("\n" + "="*50)
    print(f"EACO实时价格 ({latest_prices['updated_at']})")
    print("-"*50)
    print(f"Meteora (EACO/USDT): ${latest_prices['meteora_e_usdt']:.8f}")
    print(f"Orca (EACO/USDC):   ${latest_prices['orca_e_usdc']:.8f}")
    print(f"Raydium (EACO/SOL): {latest_prices['raydium_e_sol']:.8f} SOL")
    print(f"Raydium (EACO/USDT): ${latest_prices['raydium_e_usdt']:.8f}")
    print("-"*50)
    print(f"平均价格 (EACO/USDT): ${latest_prices['average_e_usdt']:.8f}")
    print(f"平均价格 (EACO/CNH): ¥{latest_prices['average_e_cnh']:.8f}")
    print(f"USD/CNH汇率: {latest_prices['usdt_cnh']:.2f}")
    print("="*50 + "\n")

def get_price_json():
    """返回JSON格式的价格数据"""
    return json.dumps(latest_prices, indent=2, ensure_ascii=False)

if __name__ == "__main__":
    # 启动价格监控
    start_price_monitor(30)  # 每30秒更新一次
    
    # 每隔10秒显示一次价格
    try:
        while True:
            display_prices()
            time.sleep(10)
    except KeyboardInterrupt:
        print("程序已停止")
