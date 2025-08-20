<?php
/**
 * Ajax機能のテストクラス
 *
 * WordPressのAjax処理をテストします。
 * Ajax処理のユニットテストと統合テストの学習を目的としています。
 *
 * @package My_First_PHPUnit_On_WP
 */

/**
 * Ajax機能テストクラス
 *
 * WordPress Ajax処理のテスト手法を学習できます：
 * - Ajax ハンドラーの登録確認
 * - nonce検証のテスト
 * - 入力値検証とエラーハンドリング
 * - JSON レスポンスの検証
 * - wp_send_json_* 関数の動作確認
 */
class Mfpow_Ajax_Test extends WP_Ajax_UnitTestCase {

	/**
	 * テストセットアップ
	 *
	 * 各テスト前に実行される処理です。
	 * Ajax機能を読み込み、フックを登録します。
	 */
	public function setUp(): void {
		parent::setUp();

		// Ajax機能を読み込み
		require_once MFPOW_PLUGIN_DIR . 'includes/ajax.php';
		
		// Ajax フックを登録
		mfpow_register_ajax_hooks();
	}

	/**
	 * テストクリーンアップ
	 *
	 * 各テスト後に実行される処理です。
	 * $_POSTデータをクリアします。
	 */
	public function tearDown(): void {
		$_POST = array();
		parent::tearDown();
	}

	/**
	 * ========================================
	 * Ajax フック登録のテスト
	 * ========================================
	 */

	/**
	 * Ajaxハンドラーが正しく登録されているかテスト
	 *
	 * WordPress Ajax学習ポイント：
	 * - wp_ajax_ と wp_ajax_nopriv_ フックの登録確認
	 * - has_action() による動的なフック登録確認
	 */
	public function test_ajax_handlers_are_registered() {
		// ログインユーザー向けハンドラーの確認
		$this->assertTrue( has_action( 'wp_ajax_mfpow_calculate' ) );
		$this->assertTrue( has_action( 'wp_ajax_mfpow_get_post_count' ) );

		// 非ログインユーザー向けハンドラーの確認
		$this->assertTrue( has_action( 'wp_ajax_nopriv_mfpow_calculate' ) );
		$this->assertTrue( has_action( 'wp_ajax_nopriv_mfpow_get_post_count' ) );
	}

	/**
	 * Ajax登録チェック関数のテスト
	 *
	 * ユーティリティ関数の動作確認です。
	 */
	public function test_is_ajax_handler_registered_function() {
		// 登録されているアクション
		$this->assertTrue( mfpow_is_ajax_handler_registered( 'mfpow_calculate' ) );
		$this->assertTrue( mfpow_is_ajax_handler_registered( 'mfpow_calculate', true ) );

		// 登録されていないアクション
		$this->assertFalse( mfpow_is_ajax_handler_registered( 'nonexistent_action' ) );
		$this->assertFalse( mfpow_is_ajax_handler_registered( 'nonexistent_action', true ) );
	}

	/**
	 * ========================================
	 * Ajax 計算機能のテスト
	 * ========================================
	 */

	/**
	 * 計算Ajax - 正常な加算処理のテスト
	 *
	 * WordPress Ajax学習ポイント：
	 * - $_POSTデータの設定とテスト方法
	 * - wp_send_json_success のテスト手法
	 * - try/catch による exit() 処理の対応
	 */
	public function test_ajax_calculate_addition_success() {
		// テスト用のPOSTデータを設定
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '10';
		$_POST['number2'] = '5';
		$_POST['operation'] = 'add';

		// Ajax処理実行とレスポンス取得
		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
			// Ajax処理の正常終了をキャッチ
		}

		// レスポンスの確認
		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEquals( 15, $response['data']['result'] );
		$this->assertStringContainsString( '10 add 5 = 15', $response['data']['calculation'] );
	}

	/**
	 * 計算Ajax - 減算処理のテスト
	 */
	public function test_ajax_calculate_subtraction() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '20';
		$_POST['number2'] = '8';
		$_POST['operation'] = 'subtract';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEquals( 12, $response['data']['result'] );
	}

	/**
	 * 計算Ajax - 乗算処理のテスト
	 */
	public function test_ajax_calculate_multiplication() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '4';
		$_POST['number2'] = '7';
		$_POST['operation'] = 'multiply';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEquals( 28, $response['data']['result'] );
	}

	/**
	 * 計算Ajax - 除算処理のテスト
	 */
	public function test_ajax_calculate_division() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '15';
		$_POST['number2'] = '3';
		$_POST['operation'] = 'divide';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEquals( 5, $response['data']['result'] );
	}

	/**
	 * ========================================
	 * Ajax エラーハンドリングのテスト
	 * ========================================
	 */

	/**
	 * 無効なnonce - セキュリティエラーのテスト
	 *
	 * WordPress セキュリティ学習ポイント：
	 * - nonce検証の重要性
	 * - wp_send_json_error によるエラーレスポンス
	 */
	public function test_ajax_calculate_invalid_nonce() {
		$_POST['nonce'] = 'invalid_nonce';
		$_POST['number1'] = '10';
		$_POST['number2'] = '5';
		$_POST['operation'] = 'add';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'Security check failed.', $response['data']['message'] );
	}

	/**
	 * 無効な数値 - バリデーションエラーのテスト
	 */
	public function test_ajax_calculate_invalid_numbers() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = 'not_a_number';
		$_POST['number2'] = '5';
		$_POST['operation'] = 'add';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'Invalid numbers provided.', $response['data']['message'] );
	}

	/**
	 * ゼロ除算エラーのテスト
	 */
	public function test_ajax_calculate_division_by_zero() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '10';
		$_POST['number2'] = '0';
		$_POST['operation'] = 'divide';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'Division by zero is not allowed.', $response['data']['message'] );
	}

	/**
	 * 無効な演算子のテスト
	 */
	public function test_ajax_calculate_invalid_operation() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '10';
		$_POST['number2'] = '5';
		$_POST['operation'] = 'invalid_operation';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'Invalid operation specified.', $response['data']['message'] );
	}

	/**
	 * ========================================
	 * 投稿数取得Ajax のテスト
	 * ========================================
	 */

	/**
	 * 投稿数取得Ajax - 正常処理のテスト
	 *
	 * WordPress データベース学習ポイント：
	 * - wp_count_posts() の動作確認
	 * - ファクトリーパターンによるテストデータ作成
	 * - 投稿ステータスの理解
	 */
	public function test_ajax_get_post_count_success() {
		// テスト用投稿を作成
		$published_post = $this->factory->post->create( array(
			'post_status' => 'publish'
		) );
		$draft_post = $this->factory->post->create( array(
			'post_status' => 'draft'
		) );

		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['post_type'] = 'post';

		try {
			$this->_handleAjax( 'mfpow_get_post_count' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEquals( 'post', $response['data']['post_type'] );
		$this->assertGreaterThanOrEqual( 1, $response['data']['published'] );
		$this->assertGreaterThanOrEqual( 1, $response['data']['draft'] );
		$this->assertGreaterThanOrEqual( 2, $response['data']['total'] );
	}

	/**
	 * 存在しない投稿タイプのテスト
	 */
	public function test_ajax_get_post_count_invalid_post_type() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['post_type'] = 'nonexistent_post_type';

		try {
			$this->_handleAjax( 'mfpow_get_post_count' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertFalse( $response['success'] );
		$this->assertEquals( 'Invalid post type specified.', $response['data']['message'] );
	}

	/**
	 * ========================================
	 * ユーティリティ関数のテスト
	 * ========================================
	 */

	/**
	 * nonce生成機能のテスト
	 */
	public function test_get_ajax_nonce() {
		$nonce = mfpow_get_ajax_nonce();
		
		$this->assertNotEmpty( $nonce );
		$this->assertNotFalse( wp_verify_nonce( $nonce, 'mfpow_ajax_nonce' ) );
	}

	/**
	 * Ajaxレスポンス検証ヘルパーのテスト
	 */
	public function test_validate_ajax_response() {
		// 正常なレスポンス形式
		$valid_success_response = array(
			'success' => true,
			'data' => array( 'result' => 10 )
		);
		$this->assertTrue( mfpow_validate_ajax_response( $valid_success_response ) );

		$valid_error_response = array(
			'success' => false,
			'data' => array( 'message' => 'Error occurred' )
		);
		$this->assertTrue( mfpow_validate_ajax_response( $valid_error_response ) );

		// 無効なレスポンス形式
		$invalid_response = array( 'result' => 10 );
		$this->assertFalse( mfpow_validate_ajax_response( $invalid_response ) );

		$non_array_response = 'invalid response';
		$this->assertFalse( mfpow_validate_ajax_response( $non_array_response ) );
	}

	/**
	 * ========================================
	 * 統合テスト
	 * ========================================
	 */

	/**
	 * Ajax機能の複雑な計算テスト
	 *
	 * より複雑な計算でAjax機能の安定性を確認します。
	 */
	public function test_ajax_complex_calculation() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '123.456';
		$_POST['number2'] = '78.9';
		$_POST['operation'] = 'multiply';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEqualsWithDelta( 9740.6784, $response['data']['result'], 0.0001 );
		$this->assertStringContainsString( '123.456', $response['data']['calculation'] );
		$this->assertStringContainsString( 'multiply', $response['data']['calculation'] );
		$this->assertStringContainsString( '78.9', $response['data']['calculation'] );
	}

	/**
	 * 浮動小数点計算の精度テスト
	 */
	public function test_ajax_calculate_float_precision() {
		$_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
		$_POST['number1'] = '0.1';
		$_POST['number2'] = '0.2';
		$_POST['operation'] = 'add';

		try {
			$this->_handleAjax( 'mfpow_calculate' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response, true );
		
		$this->assertTrue( $response['success'] );
		$this->assertEqualsWithDelta( 0.3, $response['data']['result'], 0.0001 );
	}
}
