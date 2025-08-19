<?php
/**
 * WordPressフック機能のテスト
 */

/**
 * WordPressフィルターフックのテストケース
 */
class Mfpow_Hooks_Test extends WP_UnitTestCase {

    /**
     * テスト前のセットアップ
     */
    public function setUp(): void {
        parent::setUp();
        
        // テスト開始前にフィルターが登録されていることを確認
        if ( ! mfpow_is_blogname_filter_registered() ) {
            mfpow_register_hooks();
        }
    }

    /**
     * ==========================================
     * 統合テスト: 実際のWordPress関数でフックが動作するかテスト
     * ==========================================
     * 
     * これらのテストが最も重要です。実際のWordPress環境で
     * フィルターが期待通りに動作することを確認します。
     */

    /**
     * get_bloginfo('name') でフィルターが動作することをテスト
     * 
     * これが最も重要なテストです。実際のWordPress関数である
     * get_bloginfo('name') が "- mfpow" を含んで返すことを確認します。
     */
    public function test_get_bloginfo_name_integration() {
        // テスト用のサイト名を設定
        update_option( 'blogname', 'My WordPress Site' );
        
        // 実際のget_bloginfo('name')呼び出しでフィルターが動作することを確認
        $site_name = get_bloginfo( 'name' );
        
        // フィルターにより " - mfpow" が追加されることを確認
        $this->assertEquals( 'My WordPress Site - mfpow', $site_name );
        $this->assertStringContainsString( 'mfpow', $site_name );
        $this->assertStringContainsString( 'My WordPress Site', $site_name );
    }

    /**
     * 異なるサイト名でのget_bloginfo('name')テスト
     */
    public function test_get_bloginfo_name_with_various_names() {
        $test_cases = [
            'シンプルなサイト名',
            'Site with English',
            '日本語とEnglishの混合サイト',
            'Special-Characters & Symbols!'
        ];

        foreach ( $test_cases as $test_name ) {
            update_option( 'blogname', $test_name );
            $result = get_bloginfo( 'name' );
            
            // 各ケースで " - mfpow" が追加されることを確認
            $expected = $test_name . ' - mfpow';
            $this->assertEquals( $expected, $result, 
                "サイト名「{$test_name}」でフィルターが正しく動作していません" );
        }
    }

    /**
     * ==========================================
     * 単体テスト: フィルター関数の個別動作テスト  
     * ==========================================
     * 
     * これらは補助的なテストです。フィルター関数が
     * 期待通りに動作することを個別に確認します。
     */

    /**
     * フィルター関数の直接テスト（単体テスト）
     */
    public function test_filter_function_unit_test() {
        // $show が 'name' の場合のテスト
        $result = mfpow_modify_blogname_filter( 'テストサイト', 'name' );
        $this->assertEquals( 'テストサイト - mfpow', $result );

        // $show が空文字の場合のテスト  
        $result = mfpow_modify_blogname_filter( 'テストサイト', '' );
        $this->assertEquals( 'テストサイト - mfpow', $result );

        // $show が 'description' など他の値の場合はフィルターが適用されない
        $result = mfpow_modify_blogname_filter( 'テストサイト', 'description' );
        $this->assertEquals( 'テストサイト', $result );
    }

    /**
     * フィルターフックが正しく登録されているかのテスト
     */
    public function test_blogname_filter_is_registered() {
        // フィルターが登録されていることを確認
        $this->assertTrue( 
            has_filter( 'bloginfo', 'mfpow_modify_blogname_filter' ),
            'ブログ名フィルターが登録されていません'
        );
        
        // ユーティリティ関数でも確認
        $this->assertTrue( 
            mfpow_is_blogname_filter_registered(),
            'ユーティリティ関数でフィルター登録が確認できません'
        );
    }

    /**
     * フィルターの優先度テスト
     */
    public function test_blogname_filter_priority() {
        $priority = mfpow_get_blogname_filter_priority();
        
        // デフォルトの優先度（10）で登録されていることを確認
        $this->assertEquals( 10, $priority );
        $this->assertIsInt( $priority );
    }

    /**
     * get_bloginfo('name')の実際の動作テスト
     */
    public function test_get_bloginfo_name_with_filter() {
        // WordPressのデフォルトサイト名を設定
        update_option( 'blogname', 'WordPress テストサイト' );
        
        // get_bloginfo('name') の結果に "mfpow" が含まれることを確認
        $site_name = get_bloginfo( 'name' );
        
        $this->assertStringContainsString( 'mfpow', $site_name );
        $this->assertStringContainsString( 'WordPress テストサイト', $site_name );
        $this->assertEquals( 'WordPress テストサイト - mfpow', $site_name );
    }

    /**
     * フィルター削除機能のテスト
     */
    public function test_remove_blogname_filter() {
        // フィルターが最初に登録されていることを確認
        $this->assertTrue( mfpow_is_blogname_filter_registered() );
        
        // フィルターを削除
        $removal_result = mfpow_remove_blogname_filter();
        $this->assertTrue( $removal_result );
        
        // フィルターが削除されたことを確認
        $this->assertFalse( mfpow_is_blogname_filter_registered() );
        
        // get_bloginfo('name') の結果に "mfpow" が含まれないことを確認
        update_option( 'blogname', 'クリーンなサイト名' );
        $site_name = get_bloginfo( 'name' );
        
        $this->assertStringNotContainsString( 'mfpow', $site_name );
        $this->assertEquals( 'クリーンなサイト名', $site_name );
    }

    /**
     * フィルター再登録機能のテスト
     */
    public function test_re_register_blogname_filter() {
        // まずフィルターを削除
        mfpow_remove_blogname_filter();
        $this->assertFalse( mfpow_is_blogname_filter_registered() );
        
        // フィルターを再登録
        mfpow_re_register_blogname_filter();
        
        // フィルターが再登録されたことを確認
        $this->assertTrue( mfpow_is_blogname_filter_registered() );
        
        // 動作も正常に復活していることを確認
        update_option( 'blogname', '復活テストサイト' );
        $site_name = get_bloginfo( 'name' );
        
        $this->assertStringContainsString( 'mfpow', $site_name );
        $this->assertEquals( '復活テストサイト - mfpow', $site_name );
    }

    /**
     * 複数のフィルター関数を追加した場合のテスト
     */
    public function test_multiple_filters_on_blogname() {
        // 追加のフィルターを登録 - bloginfoフィルターに対して
        add_filter( 'bloginfo', function( $output, $show ) {
            if ( $show === 'name' ) {
                return '[TEST] ' . $output;
            }
            return $output;
        }, 5, 2 ); // より高い優先度で実行
        
        update_option( 'blogname', '複数フィルターサイト' );
        $site_name = get_bloginfo( 'name' );
        
        // 両方のフィルターが適用されることを確認
        $this->assertStringContainsString( '[TEST]', $site_name );
        $this->assertStringContainsString( 'mfpow', $site_name );
        $this->assertEquals( '[TEST] 複数フィルターサイト - mfpow', $site_name );
        
        // テスト用フィルターを削除
        remove_all_filters( 'bloginfo' );
        mfpow_register_hooks(); // 元のフィルターを再登録
    }

    /**
     * WordPress環境でのフック統合テスト
     */
    public function test_wordpress_hook_integration() {
        // 実際のWordPress環境でのフィルター動作を確認
        
        // 1. 元の設定を保存
        $original_blogname = get_option( 'blogname' );
        
        // 2. テスト用のサイト名を設定
        update_option( 'blogname', 'フック統合テスト' );
        
        // 3. 各種WordPress関数でフィルターが動作することを確認
        $methods = [
            'get_bloginfo("name")',
            'wp_title()'
        ];
        
        foreach ( $methods as $method ) {
            switch ( $method ) {
                case 'get_bloginfo("name")':
                    $result = get_bloginfo( 'name' );
                    break;
                // 必要に応じて他のメソッドも追加
            }
            
            $this->assertStringContainsString( 'mfpow', $result, 
                "{$method} でフィルターが動作していません" );
        }
        
        // 4. 元の設定を復元
        update_option( 'blogname', $original_blogname );
    }

    /**
     * テスト後のクリーンアップ
     */
    public function tearDown(): void {
        // フィルターが確実に登録された状態でテストを終了
        if ( ! mfpow_is_blogname_filter_registered() ) {
            mfpow_register_hooks();
        }
        
        // 他のテストフィルターを削除
        remove_all_filters( 'bloginfo' );
        mfpow_register_hooks();
        
        parent::tearDown();
    }
}
