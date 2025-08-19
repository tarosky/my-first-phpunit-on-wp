<?php
/**
 * WordPress投稿解析機能
 *
 * このファイルには、投稿内容を解析してデータベース操作を行う機能が含まれています。
 * WordPressの統合テスト学習用のサンプル機能です。
 *
 * @package My_First_PHPUnit_On_WP
 */

// 直接アクセスを防ぐ
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ========================================
 * 投稿解析機能
 * ========================================
 */

/**
 * 投稿内容中の「WordPress」キーワード数をカウントしてカスタムフィールドに保存
 *
 * この関数は投稿のタイトルと本文から「WordPress」という単語の出現回数を
 * 大文字小文字を区別せずにカウントし、カスタムフィールド '_how_many_wordpress' に保存します。
 *
 * WordPressの統合テスト学習用の機能として、以下を学習できます：
 * - get_post() によるデータベースからの投稿取得
 * - update_post_meta() によるカスタムフィールドの更新
 * - 正規表現による文字列処理
 *
 * @param int $post_id 投稿ID
 * @return int カウントした数値
 */
function mfpow_count_wordpress_keywords( $post_id ) {
	// 投稿IDの検証
	if ( ! $post_id ) {
		return 0;
	}

	// 投稿データを取得
	$post = get_post( $post_id );

	// 投稿が存在しない場合
	if ( ! $post ) {
		return 0;
	}

	// タイトルと本文を結合
	$content = $post->post_title . ' ' . $post->post_content;

	// 「WordPress」の出現回数を大文字小文字を区別せずにカウント
	$count = preg_match_all( '/wordpress/i', $content );

	// カスタムフィールドに保存
	update_post_meta( $post_id, '_how_many_wordpress', $count );

	return $count;
}

/**
 * 投稿のWordPressキーワード数を取得する
 *
 * カスタムフィールド '_how_many_wordpress' からキーワード数を取得します。
 * 投稿IDが指定されない場合は現在の投稿から取得します。
 *
 * この関数でWordPress統合テストにおける以下を学習できます：
 * - get_post_meta() によるカスタムフィールド取得
 * - get_the_ID() による現在投稿IDの取得
 * - 型キャストによるデータの安全な処理
 *
 * @param int|null $post_id 投稿ID（nullの場合は現在の投稿）
 * @return int キーワード数（存在しない場合は0）
 */
function how_many_wordpress( $post_id = null ) {
	// 投稿IDが指定されていない場合は現在の投稿IDを取得
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// 投稿IDが取得できない場合
	if ( ! $post_id ) {
		return 0;
	}

	// カスタムフィールドから値を取得
	$count = get_post_meta( $post_id, '_how_many_wordpress', true );

	// 整数に変換して返す（存在しない場合は0）
	return (int) $count;
}

/**
 * ========================================
 * フック統合機能
 * ========================================
 */

/**
 * 投稿解析機能のフックを登録する
 *
 * save_postフックに投稿解析機能を登録します。
 * この関数により、投稿が保存されるたびに自動的にキーワード数がカウントされます。
 *
 * WordPressフック学習として以下を習得できます：
 * - save_postアクションフックの使用方法
 * - 自動処理の実装パターン
 * - フック登録の管理方法
 */
function mfpow_register_post_analyzer_hooks() {
	add_action( 'save_post', 'mfpow_handle_post_save', 10, 1 );
}

/**
 * 投稿保存時の処理
 *
 * save_postフックから呼び出される関数です。
 * 自動保存やリビジョンを除外し、通常の投稿保存時のみ解析を実行します。
 *
 * WordPressフック処理のベストプラクティスとして以下を学習できます：
 * - wp_is_post_autosave() による自動保存の除外
 * - wp_is_post_revision() によるリビジョンの除外
 * - 投稿タイプの確認
 * - フック処理での適切な条件分岐
 *
 * @param int $post_id 保存された投稿のID
 */
function mfpow_handle_post_save( $post_id ) {
	// 自動保存の場合はスキップ
	if ( wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// リビジョンの場合はスキップ
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// 投稿タイプが 'post' の場合のみ処理
	if ( 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	// キーワードカウントを実行
	mfpow_count_wordpress_keywords( $post_id );
}

/**
 * ========================================
 * 管理機能
 * ========================================
 */

/**
 * 投稿解析機能のユーティリティ関数群
 *
 * テストや管理で使用するためのヘルパー関数です。
 */

/**
 * 指定した投稿のキーワード数を再計算する
 *
 * 既存の投稿に対してキーワード数を再計算します。
 * バッチ処理やデータ修正時に使用します。
 *
 * @param int $post_id 投稿ID
 * @return bool 成功した場合true
 */
function mfpow_recalculate_wordpress_count( $post_id ) {
	$count = mfpow_count_wordpress_keywords( $post_id );
	return false !== $count;
}

/**
 * 投稿解析データをクリアする
 *
 * 指定した投稿のキーワード数データを削除します。
 * テストのクリーンアップや機能無効化時に使用します。
 *
 * @param int $post_id 投稿ID
 * @return bool 削除が成功した場合true
 */
function mfpow_clear_wordpress_count( $post_id ) {
	return delete_post_meta( $post_id, '_how_many_wordpress' );
}

/**
 * 投稿にキーワード数データが存在するかチェック
 *
 * 指定した投稿にキーワード数データが保存されているかを確認します。
 * テストや条件分岐で使用します。
 *
 * @param int $post_id 投稿ID
 * @return bool データが存在する場合true
 */
function mfpow_has_wordpress_count( $post_id ) {
	$meta = get_post_meta( $post_id, '_how_many_wordpress', true );
	return '' !== $meta;
}
