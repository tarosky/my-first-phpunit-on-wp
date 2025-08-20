<?php
/**
 * WordPress Ajax機能
 *
 * このファイルには、WordPress Ajax処理を行う機能が含まれています。
 * Ajax テスト学習用のサンプル機能です。
 *
 * @package My_First_PHPUnit_On_WP
 */

// 直接アクセスを防ぐ
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ========================================
 * Ajax機能の実装
 * ========================================
 */

/**
 * Ajax機能のフックを登録する
 *
 * wp_ajax_ と wp_ajax_nopriv_ フックを登録します。
 * この関数により、Ajax処理が有効になります。
 *
 * WordPress Ajax学習として以下を習得できます：
 * - wp_ajax_ アクションフックの使用方法
 * - wp_ajax_nopriv_ による非ログインユーザー対応
 * - Ajax処理の基本パターン
 */
function mfpow_register_ajax_hooks() {
	// ログインユーザー向けAjaxハンドラー
	add_action( 'wp_ajax_mfpow_calculate', 'mfpow_handle_ajax_calculate' );
	add_action( 'wp_ajax_mfpow_get_post_count', 'mfpow_handle_ajax_get_post_count' );

	// 非ログインユーザー向けAjaxハンドラー（公開機能）
	add_action( 'wp_ajax_nopriv_mfpow_calculate', 'mfpow_handle_ajax_calculate' );
	add_action( 'wp_ajax_nopriv_mfpow_get_post_count', 'mfpow_handle_ajax_get_post_count' );
}

/**
 * Ajax計算処理ハンドラー
 *
 * Ajaxで送信された数値の計算を行う処理です。
 * nonce検証、入力値検証、適切なレスポンス返却を学習できます。
 *
 * WordPress Ajax処理のベストプラクティスとして以下を学習できます：
 * - wp_verify_nonce() によるセキュリティ検証
 * - sanitize_text_field() による入力値サニタイズ
 * - wp_send_json_success() / wp_send_json_error() による適切なレスポンス
 */
function mfpow_handle_ajax_calculate() {
	// nonceの検証
	if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'mfpow_ajax_nonce' ) ) {
		wp_send_json_error( array(
			'message' => 'Security check failed.',
		) );
	}

	// 入力値の取得と検証
	$number1   = sanitize_text_field( $_POST['number1'] ?? '' );
	$number2   = sanitize_text_field( $_POST['number2'] ?? '' );
	$operation = sanitize_text_field( $_POST['operation'] ?? '' );

	// 数値の検証
	if ( ! is_numeric( $number1 ) || ! is_numeric( $number2 ) ) {
		wp_send_json_error( array(
			'message' => 'Invalid numbers provided.',
		) );
	}

	$num1   = floatval( $number1 );
	$num2   = floatval( $number2 );
	$result = 0;

	// 計算処理
	switch ( $operation ) {
		case 'add':
			$result = $num1 + $num2;
			break;
		case 'subtract':
			$result = $num1 - $num2;
			break;
		case 'multiply':
			$result = $num1 * $num2;
			break;
		case 'divide':
			if ( 0 === $num2 ) {
				wp_send_json_error( array(
					'message' => 'Division by zero is not allowed.',
				) );
			}
			$result = $num1 / $num2;
			break;
		default:
			wp_send_json_error( array(
				'message' => 'Invalid operation specified.',
			) );
	}

	// 成功レスポンス
	wp_send_json_success( array(
		'result'      => $result,
		'calculation' => $num1 . ' ' . $operation . ' ' . $num2 . ' = ' . $result,
	) );
}

/**
 * Ajax投稿数取得ハンドラー
 *
 * Ajaxで投稿数を取得する処理です。
 * WordPressのデータベース操作とAjax処理の組み合わせを学習できます。
 *
 * WordPress統合処理として以下を学習できます：
 * - wp_count_posts() による投稿数取得
 * - 投稿タイプの動的指定
 * - キャッシュを考慮したデータ取得
 */
function mfpow_handle_ajax_get_post_count() {
	// nonceの検証
	if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'mfpow_ajax_nonce' ) ) {
		wp_send_json_error( array(
			'message' => 'Security check failed.',
		) );
	}

	// 投稿タイプの取得（デフォルトは'post'）
	$post_type = sanitize_text_field( $_POST['post_type'] ?? 'post' );

	// 投稿タイプの存在確認
	if ( ! post_type_exists( $post_type ) ) {
		wp_send_json_error( array(
			'message' => 'Invalid post type specified.',
		) );
	}

	// 投稿数を取得
	$counts = wp_count_posts( $post_type );

	// レスポンスデータの準備
	$response_data = array(
		'post_type' => $post_type,
		'published' => $counts->publish ?? 0,
		'draft'     => $counts->draft ?? 0,
		'private'   => $counts->private ?? 0,
		'total'     => ( $counts->publish ?? 0 ) + ( $counts->draft ?? 0 ) + ( $counts->private ?? 0 ),
	);

	// 成功レスポンス
	wp_send_json_success( $response_data );
}

/**
 * ========================================
 * ユーティリティ機能
 * ========================================
 */

/**
 * Ajax nonceを生成する
 *
 * Ajaxリクエスト用のnonceを生成します。
 * セキュリティトークンの生成を学習できます。
 *
 * @return string nonce値
 */
function mfpow_get_ajax_nonce() {
	return wp_create_nonce( 'mfpow_ajax_nonce' );
}

/**
 * Ajax用のJavaScript変数を出力する
 *
 * Ajax処理に必要なURL、nonceなどをJavaScriptで使用できるように出力します。
 * wp_localize_script() の使用方法を学習できます。
 */
function mfpow_enqueue_ajax_scripts() {
	// Ajax用のJavaScript変数を設定
	wp_localize_script( 'jquery', 'mfpow_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => mfpow_get_ajax_nonce(),
	) );
}

/**
 * Ajax機能が正しく登録されているかチェックする
 *
 * テスト用のユーティリティ関数です。
 * フックが適切に登録されているかを確認します。
 *
 * @param string $action Ajax action name
 * @param bool $include_nopriv 非ログインユーザー向けもチェックするか
 * @return bool フックが登録されている場合true
 */
function mfpow_is_ajax_handler_registered( $action, $include_nopriv = false ) {
	$registered = has_action( 'wp_ajax_' . $action );

	if ( $include_nopriv ) {
		$registered = $registered && has_action( 'wp_ajax_nopriv_' . $action );
	}

	return (bool) $registered;
}

/**
 * Ajaxレスポンスデータの検証ヘルパー
 *
 * Ajaxレスポンスが期待する形式かをチェックするヘルパー関数です。
 * テストで使用します。
 *
 * @param array $response_data レスポンスデータ
 * @return bool 妥当なレスポンスデータの場合true
 */
function mfpow_validate_ajax_response( $response_data ) {
	if ( ! is_array( $response_data ) ) {
		return false;
	}

	// 成功レスポンスまたはエラーレスポンスの形式をチェック
	$has_success = isset( $response_data['success'] ) && isset( $response_data['data'] );

	return $has_success;
}
