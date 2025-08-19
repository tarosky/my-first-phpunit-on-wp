<?php
/**
 * 抽象プロセッサのテスト
 */

use MyFirstPHPUnitOnWP\Tests\TestProcessor;

/**
 * TestProcessor実装を通じたAbstractProcessorクラスのテストケース
 */
class Mfpow_Processor_Test extends WP_UnitTestCase {

    /**
     * テスト用のTestProcessorインスタンス
     *
     * @var TestProcessor
     */
    private $processor;

    /**
     * テストケースのセットアップ
     */
    public function setUp(): void {
        parent::setUp();
        $this->processor = new TestProcessor();
    }

    /**
     * 抽象メソッドの実装テスト
     */
    public function test_process_method() {
        // 通常の処理テスト
        $result = $this->processor->process( 'Hello World' );
        $this->assertEquals( 'Hello World', $result );
        
        // HTMLタグ付きの処理テスト（サニタイズされるはず）
        $result = $this->processor->process( '<p>Hello <strong>World</strong></p>' );
        $this->assertEquals( 'Hello World', $result );
        
        // 余分な空白を含む処理テスト
        $result = $this->processor->process( '  Spaced Text  ' );
        $this->assertEquals( 'Spaced Text', $result );
    }

    /**
     * processメソッドの例外処理テスト
     */
    public function test_process_throws_exception_with_invalid_data() {
        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessage( 'データが処理用として有効ではありません' );
        
        $this->processor->process( '' );
    }

    /**
     * nullデータでprocessが例外を発生させることのテスト
     */
    public function test_process_throws_exception_with_null_data() {
        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessage( 'データが処理用として有効ではありません' );
        
        $this->processor->process( null );
    }

    /**
     * getNameメソッドのテスト（具象クラスでオーバーライド済み）
     */
    public function test_get_name() {
        $this->assertEquals( 'テストプロセッサ', $this->processor->getName() );
        $this->assertIsString( $this->processor->getName() );
    }

    /**
     * getStatsメソッドのテスト（具象クラスでオーバーライド済み）
     */
    public function test_get_stats() {
        // 初期統計
        $stats = $this->processor->getStats();
        $this->assertIsArray( $stats );
        $this->assertArrayHasKey( 'processed_count', $stats );
        $this->assertArrayHasKey( 'error_count', $stats );
        $this->assertArrayHasKey( 'last_processed', $stats );
        $this->assertEquals( 0, $stats['processed_count'] );
        $this->assertNull( $stats['last_processed'] );
        
        // データを処理して統計を確認
        $this->processor->process( 'Test Data' );
        $stats = $this->processor->getStats();
        $this->assertEquals( 1, $stats['processed_count'] );
        $this->assertEquals( 'Test Data', $stats['last_processed'] );
    }

    /**
     * protectedなsanitizeメソッドをpublicラッパー経由でテスト
     */
    public function test_sanitize_method() {
        // 通常のテキストテスト
        $this->assertEquals( 'Hello World', $this->processor->testSanitize( 'Hello World' ) );
        
        // HTML削除テスト
        $this->assertEquals( 'Hello World', $this->processor->testSanitize( '<p>Hello <strong>World</strong></p>' ) );
        
        // 空白トリムテスト
        $this->assertEquals( 'Trimmed', $this->processor->testSanitize( '  Trimmed  ' ) );
        
        // 文字列以外の入力テスト
        $this->assertEquals( '', $this->processor->testSanitize( 123 ) );
        $this->assertEquals( '', $this->processor->testSanitize( [] ) );
        $this->assertEquals( '', $this->processor->testSanitize( null ) );
    }

    /**
     * protectedなvalidateメソッドをpublicラッパー経由でテスト
     */
    public function test_validate_method() {
        // 有効なデータのテスト
        $this->assertTrue( $this->processor->testValidate( 'valid data' ) );
        $this->assertTrue( $this->processor->testValidate( 123 ) );
        $this->assertTrue( $this->processor->testValidate( ['array'] ) );
        
        // 無効なデータのテスト
        $this->assertFalse( $this->processor->testValidate( '' ) );
        $this->assertFalse( $this->processor->testValidate( null ) );
        $this->assertFalse( $this->processor->testValidate( false ) );
        $this->assertFalse( $this->processor->testValidate( 0 ) );
    }

    /**
     * protectedなlogメソッドをpublicラッパー経由でテスト
     */
    public function test_log_method() {
        // 有効なログのテスト
        $this->assertTrue( $this->processor->testLog( 'テストメッセージ' ) );
        $this->assertTrue( $this->processor->testLog( 'エラーメッセージ', 'error' ) );
        
        // 無効なログのテスト
        $this->assertFalse( $this->processor->testLog( '' ) );
        $this->assertFalse( $this->processor->testLog( 'message', '' ) );
    }

    /**
     * 具象実装を通じて抽象クラスの機能をテストできることの実演
     */
    public function test_abstract_class_through_concrete_implementation() {
        // これは抽象クラスをテストする正しい方法を実演する
        // 抽象クラスを直接テストせず、具象実装を通じてテストする
        
        // 抽象クラスのメソッドが期待通りに動作することをテスト
        $this->assertInstanceOf( TestProcessor::class, $this->processor );
        
        // 抽象メソッドが実装されていることをテスト
        $this->assertTrue( method_exists( $this->processor, 'process' ) );
        
        // 具象クラスが抽象クラスのメソッドをオーバーライドできることをテスト
        $this->assertEquals( 'テストプロセッサ', $this->processor->getName() );
    }

    /**
     * publicインターフェース経由でprotectedメソッドの統合テスト
     */
    public function test_integration_of_protected_methods() {
        // process()を呼び出すと、内部でprotectedなvalidate()とsanitize()を使用する
        // これはこれらのメソッドの統合をテストする
        
        $result = $this->processor->process( '  <p>Test Data</p>  ' );
        
        // 結果はサニタイズされているべき（HTML削除とトリム）
        $this->assertEquals( 'Test Data', $result );
        
        // 統計が更新されているべき（内部処理が動作したことを示す）
        $stats = $this->processor->getStats();
        $this->assertEquals( 1, $stats['processed_count'] );
        $this->assertEquals( 'Test Data', $stats['last_processed'] );
    }

    /**
     * 複数の処理操作のテスト
     */
    public function test_multiple_processing_operations() {
        // 複数のアイテムを処理
        $this->processor->process( 'First' );
        $this->processor->process( 'Second' );
        $this->processor->process( 'Third' );
        
        // 最終統計を確認
        $stats = $this->processor->getStats();
        $this->assertEquals( 3, $stats['processed_count'] );
        $this->assertEquals( 'Third', $stats['last_processed'] );
    }

    /**
     * テスト後のクリーンアップ
     */
    public function tearDown(): void {
        $this->processor = null;
        parent::tearDown();
    }
}
