<?php
/**
 * WordPressフック関連の機能
 * 
 * このファイルには、プラグインで使用するWordPressのアクション・フィルターフック
 * に関連する関数が含まれています。
 *
 * @package My_First_PHPUnit_On_WP
 */

// 直接アクセスを防ぐ
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * プラグイン初期化時にフックを登録する
 * 
 * この関数は適切なタイミングでWordPressフックを登録します。
 */
function mfpow_register_hooks() {
    // ブログ名フィルターを登録 - 正しいフィルター名を使用
    add_filter( 'bloginfo', 'mfpow_modify_blogname_filter', 10, 2 );
}

/**
 * ========================================
 * フィルターフック関連
 * ========================================
 */


/**
 * ========================================
 * アクションフック関連
 * ========================================
 */


/**
 * bloginfoフィルター用のラッパー関数
 * 
 * @param string $output フィルター前の出力
 * @param string $show   取得する情報の種類
 * @return string フィルター後の出力
 */
function mfpow_modify_blogname_filter( $output, $show ) {
    // 'name' またはから文字の場合のみフィルターを適用
    if ( in_array( $show, [ 'name', '' ], true ) ) {
        $output .= ' - mfpow';
    }
    return $output;
}

/**
 * 管理画面でのデバッグ情報表示（テスト用）
 * 
 * 管理画面フッターにフック登録状態を表示します。
 * デバッグやテスト時に便利です。
 *
 * @param string $footer_text 既存のフッターテキスト
 * @return string 修正されたフッターテキスト
 */
function mfpow_admin_footer_debug( $footer_text ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return $footer_text;
    }
    
    $hook_status = has_filter( 'bloginfo', 'mfpow_modify_blogname_filter' ) ? 'ON' : 'OFF';
    $debug_info = sprintf( 
        ' | MFPOW Hook Status: %s | Site Name: %s', 
        $hook_status,
        get_bloginfo( 'name' )
    );
    
    return $footer_text . $debug_info;
}

/**
 * ========================================
 * フック管理用のユーティリティ関数
 * ========================================
 */

/**
 * ブログ名フィルターが登録されているかチェック
 * 
 * @return bool フィルターが登録されている場合true
 */
function mfpow_is_blogname_filter_registered() {
    return (bool) has_filter( 'bloginfo', 'mfpow_modify_blogname_filter' );
}

/**
 * ブログ名フィルターを削除する
 * 
 * テストや一時的な無効化のために使用します。
 * 
 * @return bool 削除が成功した場合true
 */
function mfpow_remove_blogname_filter() {
    return remove_filter( 'bloginfo', 'mfpow_modify_blogname_filter', 10 );
}

/**
 * ブログ名フィルターを再登録する
 * 
 * 削除したフィルターを再度有効にします。
 */
function mfpow_re_register_blogname_filter() {
    if ( ! mfpow_is_blogname_filter_registered() ) {
        add_filter( 'bloginfo', 'mfpow_modify_blogname_filter', 10, 2 );
    }
}

/**
 * 現在のフック優先度を取得する
 * 
 * @return int|false フィルターの優先度、登録されていない場合false
 */
function mfpow_get_blogname_filter_priority() {
    global $wp_filter;
    
    if ( isset( $wp_filter['bloginfo'] ) ) {
        foreach ( $wp_filter['bloginfo']->callbacks as $priority => $callbacks ) {
            if ( isset( $callbacks['mfpow_modify_blogname_filter'] ) ) {
                return $priority;
            }
        }
    }
    
    return false;
}
