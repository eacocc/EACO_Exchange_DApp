<?php
/*
Plugin Name: EACO 地球链 API
Plugin URI: https://linktr.ee/web3eaco
Description: 将 EACO 地球链的宇宙价值数据接入 WordPress，支持汇率、TVL、兑换估算与哲学语句展示。
Version: 1.0.0
Author: EACO Protocol
Author URI: https://x.com/eacocc
License: MIT
Text Domain: eaco
*/

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件路径
define('EACO_API_VERSION', '1.0.0');
define('EACO_API_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EACO_API_PLUGIN_URL', plugin_dir_url(__FILE__));

// 包含必要文件
require_once EACO_API_PLUGIN_DIR . 'includes/eaco-router.php';
require_once EACO_API_PLUGIN_DIR . 'includes/eaco-functions.php';
require_once EACO_API_PLUGIN_DIR . 'includes/eaco-shortcode.php';

// 激活插件时的操作
register_activation_hook(__FILE__, 'eaco_api_activate');
function eaco_api_activate() {
    // 可以在这里添加激活逻辑，如创建数据库表等
    update_option('eaco_api_version', EACO_API_VERSION);
}

// 插件初始化
add_action('plugins_loaded', 'eaco_api_init');
function eaco_api_init() {
    // 加载文本域（多语言支持）
    load_plugin_textdomain('eaco', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

// 添加插件设置页面链接
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'eaco_api_add_settings_link');
function eaco_api_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=eaco-api-settings">' . __('设置', 'eaco') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
