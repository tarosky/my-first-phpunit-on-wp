<?php
/**
 * Function test
 */

/**
 * Sample test case.
 */
class Mfpow_Basic_Test extends WP_UnitTestCase {

	/**
	 * Test functions
	 */
	function test_hello() {
		// Check function.
		$this->assertEquals( mfpow_hello_world(), 'Hello, WordPress Unit Testing!' );
	}

	/**
	 * Test mfpow_double_number function
	 */
	function test_double_number() {
		// 正の整数
		$this->assertEquals( 10, mfpow_double_number( 5 ) );
		
		// 負の数
		$this->assertEquals( -20, mfpow_double_number( -10 ) );
		
		// ゼロ
		$this->assertEquals( 0, mfpow_double_number( 0 ) );
		
		// 小数
		$this->assertEquals( 6.4, mfpow_double_number( 3.2 ) );
		
		// 文字列数値も処理される
		$this->assertEquals( 14, mfpow_double_number( '7' ) );
	}

	/**
	 * Test mfpow_count_array function
	 */
	function test_count_array() {
		// 通常の配列
		$this->assertEquals( 3, mfpow_count_array( ['a', 'b', 'c'] ) );
		
		// 空の配列
		$this->assertEquals( 0, mfpow_count_array( [] ) );
		
		// 配列でない値は0を返す
		$this->assertEquals( 0, mfpow_count_array( 'string' ) );
		$this->assertEquals( 0, mfpow_count_array( 123 ) );
		$this->assertEquals( 0, mfpow_count_array( null ) );
		
		// 連想配列も正しくカウント
		$this->assertEquals( 2, mfpow_count_array( ['key1' => 'val1', 'key2' => 'val2'] ) );
		
		// ネストした配列
		$this->assertEquals( 2, mfpow_count_array( [['nested1'], ['nested2']] ) );
	}
}
