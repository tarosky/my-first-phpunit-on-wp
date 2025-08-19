<?php
/**
 * 抽象クラステスト学習用の抽象プロセッサクラス
 *
 * @package MyFirstPHPUnitOnWP
 */

namespace MyFirstPHPUnitOnWP;

/**
 * 抽象プロセッサクラス - 抽象クラスのテスト方法を学習するためのクラス
 */
abstract class AbstractProcessor {

	/**
	 * データを処理する - 具象クラスで必ず実装する必要がある抽象メソッド
	 *
	 * @param mixed $data 処理するデータ
	 * @return mixed 処理されたデータ
	 */
	abstract public function process( $data );

	/**
	 * 入力データをサニタイズする（protectedメソッド - 具象実装経由でテスト可能）
	 *
	 * @param string $input サニタイズする入力文字列
	 * @return string サニタイズされた文字列
	 */
	protected function sanitize( $input ) {
		if ( ! is_string( $input ) ) {
			return '';
		}

		return trim( strip_tags( $input ) );
	}

	/**
	 * 処理前にデータを検証する
	 *
	 * @param mixed $data 検証するデータ
	 * @return bool 有効な場合true、そうでなければfalse
	 */
	protected function validate( $data ) {
		return ! empty( $data );
	}

	/**
	 * プロセッサ名を取得する - 具象クラスでオーバーライド可能
	 *
	 * @return string プロセッサ名
	 */
	public function getName() {
		return 'Abstract Processor';
	}

	/**
	 * 処理活動をログに記録する（protectedメソッド）
	 *
	 * @param string $message ログメッセージ
	 * @param string $level ログレベル
	 * @return bool このデモでは常にtrueを返す
	 */
	protected function log( $message, $level = 'info' ) {
		// 実際のアプリケーションでは、これはログファイルやデータベースに書き込む
		// テスト用には単純にtrueを返すだけ
		return ! empty( $message ) && ! empty( $level );
	}

	/**
	 * 処理統計を取得する
	 *
	 * @return array 処理統計を含む配列
	 */
	public function getStats() {
		return [
			'processed_count' => 0,
			'error_count'     => 0,
			'last_processed'  => null,
		];
	}
}
