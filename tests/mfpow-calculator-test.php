<?php
/**
 * Calculator クラスのテスト
 */

use MyFirstPHPUnitOnWP\Calculator;

/**
 * Calculator クラスの様々なテスト技法を実演するテストケース
 */
class Mfpow_Calculator_Test extends WP_UnitTestCase {

    /**
     * テスト用の Calculator インスタンス
     *
     * @var Calculator
     */
    private $calculator;

    /**
     * テストケースのセットアップ
     */
    public function setUp(): void {
        parent::setUp();
        $this->calculator = new Calculator();
    }

    /**
     * publicメソッドのテスト - add()
     */
    public function test_add_method() {
        // 正の数のテスト
        $this->assertEquals( 7, $this->calculator->add( 3, 4 ) );
        
        // 負の数のテスト
        $this->assertEquals( -1, $this->calculator->add( -3, 2 ) );
        
        // ゼロのテスト
        $this->assertEquals( 5, $this->calculator->add( 5, 0 ) );
        
        // 小数のテスト
        $this->assertEquals( 5.5, $this->calculator->add( 2.2, 3.3 ) );
    }

    /**
     * publicメソッドのテスト - multiply()
     */
    public function test_multiply_method() {
        // 正の数のテスト
        $this->assertEquals( 12, $this->calculator->multiply( 3, 4 ) );
        
        // ゼロとの掛け算テスト
        $this->assertEquals( 0, $this->calculator->multiply( 5, 0 ) );
        
        // 負の数のテスト
        $this->assertEquals( -6, $this->calculator->multiply( -2, 3 ) );
        
        // 小数のテスト（浮動小数点精度のためデルタを使用）
        $this->assertEqualsWithDelta( 6.6, $this->calculator->multiply( 2.2, 3 ), 0.0001 );
    }

    /**
     * publicメソッドのテスト - getVersion()
     */
    public function test_get_version() {
        $this->assertEquals( '1.0.0', $this->calculator->getVersion() );
        $this->assertIsString( $this->calculator->getVersion() );
    }

    /**
     * publicメソッドのテスト - factorial()
     */
    public function test_factorial() {
        // 基本的な階乗のテスト
        $this->assertEquals( 1, $this->calculator->factorial( 0 ) );
        $this->assertEquals( 1, $this->calculator->factorial( 1 ) );
        $this->assertEquals( 2, $this->calculator->factorial( 2 ) );
        $this->assertEquals( 6, $this->calculator->factorial( 3 ) );
        $this->assertEquals( 120, $this->calculator->factorial( 5 ) );
    }

    /**
     * addメソッドの例外処理テスト
     */
    public function test_add_throws_exception_with_invalid_input() {
        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessage( '引数は両方とも数値である必要があります' );
        
        $this->calculator->add( 'not_a_number', 5 );
    }

    /**
     * multiplyメソッドの例外処理テスト
     */
    public function test_multiply_throws_exception_with_invalid_input() {
        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessage( '引数は両方とも数値である必要があります' );
        
        $this->calculator->multiply( 5, [] );
    }

    /**
     * factorialメソッドの例外処理テスト
     */
    public function test_factorial_throws_exception_with_invalid_input() {
        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessage( '引数は0以上の整数である必要があります' );
        
        $this->calculator->factorial( -1 );
    }

    /**
     * factorial に整数以外を渡すと例外が発生することのテスト
     */
    public function test_factorial_throws_exception_with_float() {
        $this->expectException( InvalidArgumentException::class );
        $this->expectExceptionMessage( '引数は0以上の整数である必要があります' );
        
        $this->calculator->factorial( 3.5 );
    }

    /**
     * privateメソッドが直接テストできないことを実演する
     * このテストは、なぜprivateメソッドをテストすべきでないかを示す
     */
    public function test_private_method_cannot_be_accessed_directly() {
        // これは致命的エラーを引き起こす:
        // $this->calculator->validate( 5 );
        
        // 代わりに、privateメソッドはpublicメソッド経由で間接的にテストする
        $this->assertTrue( true, 'privateメソッドは直接テストすべきではない' );
    }

    /**
     * リフレクションを使用したprivateメソッドのテスト（教育目的）
     * 注意: これは通常本番コードでは推奨されません
     */
    public function test_private_method_using_reflection() {
        // Calculator クラスのリフレクションを作成
        $reflection = new ReflectionClass( Calculator::class );
        
        // private の validate メソッドを取得
        $validateMethod = $reflection->getMethod( 'validate' );
        $validateMethod->setAccessible( true );
        
        // リフレクション経由でprivateメソッドをテスト
        $this->assertTrue( $validateMethod->invokeArgs( $this->calculator, [ 5 ] ) );
        $this->assertTrue( $validateMethod->invokeArgs( $this->calculator, [ '10' ] ) );
        $this->assertFalse( $validateMethod->invokeArgs( $this->calculator, [ 'not_numeric' ] ) );
        $this->assertFalse( $validateMethod->invokeArgs( $this->calculator, [ [] ] ) );
        $this->assertFalse( $validateMethod->invokeArgs( $this->calculator, [ null ] ) );
    }

    /**
     * リフレクションを使用したprotectedメソッドのテスト
     * 注意: これはprotectedメソッドへのアクセスを実演するもので、推奨されません
     */
    public function test_protected_method_using_reflection() {
        $reflection = new ReflectionClass( Calculator::class );
        
        $formatMethod = $reflection->getMethod( 'formatNumber' );
        $formatMethod->setAccessible( true );
        
        // protected な formatNumber メソッドをテスト
        $this->assertEquals( '3.14', $formatMethod->invokeArgs( $this->calculator, [ 3.14159 ] ) );
        $this->assertEquals( '3.142', $formatMethod->invokeArgs( $this->calculator, [ 3.14159, 3 ] ) );
    }

    /**
     * 実装ではなく動作をテストすべき理由を実演するテスト
     */
    public function test_behavior_not_implementation() {
        // private の validate() メソッドを直接テストする代わりに、
        // それを使用する public メソッドの動作をテストする
        
        // validate() が正常に動作すれば、add() は有効な入力で動作するはず
        $this->assertEquals( 8, $this->calculator->add( 3, 5 ) );
        
        // validate() が正常に動作すれば、add() は無効な入力で例外を発生させるはず
        $this->expectException( InvalidArgumentException::class );
        $this->calculator->add( 'invalid', 5 );
    }

    /**
     * テスト後のクリーンアップ
     */
    public function tearDown(): void {
        $this->calculator = null;
        parent::tearDown();
    }
}
