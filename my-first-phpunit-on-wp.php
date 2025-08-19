<?php
/**
 * Plugin Name: My First PHPUnit on WordPress
 * Plugin URI: https://github.com/tarosky/my-first-phpunit-on-wp
 * Description: WordPressでユニットテストを学ぶためのサンプルプラグイン
 * Version: 1.0.0
 * Author: Tarosky
 * Author URI: https://tarosky.co.jp/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: my-first-phpunit-on-wp
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 */

// 直接アクセスを防ぐ
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 定数定義
define( 'MFPOW_VERSION', '1.0.0' );
define( 'MFPOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MFPOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MFPOW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// vendorを読み込み
require_once __DIR__ . '/vendor/autoload.php';

// 関数ファイルの読み込み
require_once __DIR__ . '/includes/functions.php';
