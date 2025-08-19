<?php
/**
 * ユニットテスト学習用 Calculator クラス
 *
 * @package MyFirstPHPUnitOnWP
 */

namespace MyFirstPHPUnitOnWP;

/**
 * 計算機クラス - 様々なメソッドタイプのユニットテストを学習するためのクラス
 */
class Calculator {

	/**
	 * 2つの数値を足し算する
	 *
	 * @param int|float $a 最初の数値
	 * @param int|float $b 2番目の数値
	 * @return int|float 足し算の結果
	 */
	public function add( $a, $b ) {
		if ( ! $this->validate( $a ) || ! $this->validate( $b ) ) {
			throw new \InvalidArgumentException( '引数は両方とも数値である必要があります' );
		}

		return $a + $b;
	}

	/**
	 * 2つの数値を掛け算する
	 *
	 * @param int|float $a 最初の数値
	 * @param int|float $b 2番目の数値
	 * @return int|float 掛け算の結果
	 */
	public function multiply( $a, $b ) {
		if ( ! $this->validate( $a ) || ! $this->validate( $b ) ) {
			throw new \InvalidArgumentException( '引数は両方とも数値である必要があります' );
		}

		return $a * $b;
	}

	/**
	 * 計算機のバージョンを取得する
	 *
	 * @return string バージョン番号
	 */
	public function getVersion() {
		return '1.0.0';
	}

	/**
	 * 数値の階乗を計算する
	 *
	 * @param int $n 階乗を計算する数値
	 * @return int 階乗の結果
	 */
	public function factorial( $n ) {
		if ( ! $this->validate( $n ) || $n < 0 || ! is_int( $n ) ) {
			throw new \InvalidArgumentException( '引数は0以上の整数である必要があります' );
		}

		if ( $n <= 1 ) {
			return 1;
		}

		return $n * $this->factorial( $n - 1 );
	}

	/**
	 * 値が数値かどうかを検証する（privateメソッド - 直接テストできない）
	 *
	 * @param mixed $value 検証する値
	 * @return bool 数値の場合true、そうでなければfalse
	 */
	private function validate( $value ) {
		return is_numeric( $value );
	}

	/**
	 * 数値を指定した小数点以下の桁数でフォーマットする（protectedメソッド - 継承経由でテスト可能）
	 *
	 * @param float $number フォーマットする数値
	 * @param int $decimals 小数点以下の桁数
	 * @return string フォーマットされた数値
	 */
	protected function formatNumber( $number, $decimals = 2 ) {
		return number_format( $number, $decimals );
	}
}
