<?php
/**
 * My First PHPUnit on WordPress - Functions
 * 
 * このファイルには、プラグインの主要な機能を実装した関数が含まれています。
 * ユニットテストの学習に適した様々な関数パターンを提供します。
 *
 * @package My_First_PHPUnit_On_WP
 */

// 直接アクセスを防ぐ
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ========================================
 * 基本的な関数（Step 1で使用）
 * ========================================
 */

/**
 * Hello World関数
 * 
 * @return string Hello Worldメッセージ
 */
function mfpow_hello_world() {
    return 'Hello, WordPress Unit Testing!';
}

/**
 * 数値を2倍にする関数
 * 
 * @param int|float $number 入力数値
 * @return int|float 2倍した値
 */
function mfpow_double_number( $number ) {
    return $number * 2;
}

/**
 * 配列の要素数を返す関数
 * 
 * @param mixed $array 入力配列
 * @return int 要素数（配列でない場合は0）
 */
function mfpow_count_array( $array ) {
    if ( ! is_array( $array ) ) {
        return 0;
    }
    return count( $array );
}
