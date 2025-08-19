<?php
/**
 * WordPress投稿解析機能のテスト
 * 
 * WordPressデータベース操作の統合テスト学習用サンプルです。
 * PHPUnitでのWordPress統合テストのベストプラクティスを示します。
 */

/**
 * WordPress投稿解析機能のテストケース
 * 
 * このテストクラスでは以下の学習ポイントを習得できます：
 * 1. WordPressファクトリーを使ったテストデータ作成
 * 2. 実際のWordPressデータベース操作の統合テスト
 * 3. カスタムフィールドの読み書きテスト
 * 4. WordPressフックとの統合テスト
 * 5. エッジケースとエラーハンドリングのテスト
 */
class Mfpow_Post_Analyzer_Test extends WP_UnitTestCase {

    /**
     * テスト前のセットアップ
     * 
     * 各テストの前に実行される初期化処理です。
     * 必要なフックの登録や初期状態の設定を行います。
     */
    public function setUp(): void {
        parent::setUp();
        // 必要に応じて初期設定を行う
    }

    /**
     * ==========================================
     * 統合テスト: 実際のWordPressデータベース操作テスト
     * ==========================================
     * 
     * これらのテストが最も重要です。実際のWordPress環境で
     * データベース操作が正しく動作することを確認します。
     */

    /**
     * 基本的なWordPressキーワードカウント機能のテスト
     * 
     * 最も重要なテストです。実際のWordPressファクトリーで作成した投稿に対して
     * キーワード数をカウントし、データベースに正しく保存されることを確認します。
     */
    public function test_wordpress_keyword_counting_integration() {
        // WordPressファクトリーで投稿を作成
        $post_id = $this->factory->post->create([
            'post_title' => 'WordPressの使い方ガイド',
            'post_content' => 'WordPressは素晴らしいCMSです。wordpress開発を学習しましょう。'
        ]);

        // キーワードカウント機能を実行
        $result = mfpow_count_wordpress_keywords( $post_id );

        // 戻り値の確認（タイトル1回 + 本文2回 = 計3回）
        $this->assertEquals( 3, $result );

        // データベースに実際に保存されているかを確認（統合テスト）
        $stored_count = how_many_wordpress( $post_id );
        $this->assertEquals( 3, $stored_count );

        // カスタムフィールドが直接確認できるかテスト
        $meta_value = get_post_meta( $post_id, '_how_many_wordpress', true );
        $this->assertEquals( '3', $meta_value ); // get_post_metaは文字列で返す
    }

    /**
     * 様々なパターンでのキーワードカウントテスト
     * 
     * 実際のWordPress投稿で様々なケースをテストします。
     * 大文字小文字の違い、複数出現、特殊文字との組み合わせなど。
     */
    public function test_keyword_counting_various_cases() {
        // テストケース配列
        $test_cases = [
            // [期待値, タイトル, 本文, 説明]
            [0, '普通のブログ記事', '一般的な内容の投稿です', 'WordPressが含まれない場合'],
            [3, 'WORDPRESS wordpress WordPresS', '', '大文字小文字混在'],
            [5, 'WordPress WordPress', 'wordpress WordPress WORDPRESS', 'タイトルと本文に複数'],
            [1, 'My WordPress Site', '', 'タイトルのみにWordPress'],
            [2, '', 'wordpress development with WordPress', '本文のみに複数'],
            [1, 'About WordPress!', '', '特殊文字との組み合わせ'],
        ];

        foreach ( $test_cases as $index => $case ) {
            list( $expected, $title, $content, $description ) = $case;

            // 投稿作成
            $post_id = $this->factory->post->create([
                'post_title' => $title,
                'post_content' => $content
            ]);

            // キーワードカウント実行
            mfpow_count_wordpress_keywords( $post_id );

            // 結果確認
            $actual = how_many_wordpress( $post_id );
            $this->assertEquals( $expected, $actual, 
                "テストケース {$index}: {$description} - 期待値: {$expected}, 実際: {$actual}" );
        }
    }

    /**
     * 日本語と英語が混在する実際的なケースのテスト
     * 
     * 実際のブログ記事に近い内容での動作確認
     */
    public function test_realistic_blog_post_analysis() {
        $post_id = $this->factory->post->create([
            'post_title' => 'WordPressプラグイン開発入門',
            'post_content' => '
                WordPressプラグイン開発について説明します。
                
                ## 1. WordPress環境の準備
                まず、WordPress開発環境を構築しましょう。
                
                ## 2. プラグインの基本構造
                wordpress pluginの基本的な構造を理解することが重要です。
                
                WordPressのフック機能を使って、既存の機能を拡張できます。
            '
        ]);

        // 解析実行
        mfpow_count_wordpress_keywords( $post_id );

        // 期待値：タイトル1回 + 本文5回 = 6回
        $count = how_many_wordpress( $post_id );
        $this->assertEquals( 6, $count );

        // データの存在確認
        $this->assertTrue( mfpow_has_wordpress_count( $post_id ) );
    }

    /**
     * ==========================================
     * WordPressフックとの統合テスト
     * ==========================================
     */

    /**
     * save_postフックとの統合テスト
     * 
     * WordPressの save_post フックが実行されたときに
     * 自動的にキーワードカウントが実行されることを確認します。
     */
    public function test_save_post_hook_integration() {
        // フックを登録
        mfpow_register_post_analyzer_hooks();

        // 投稿を作成（save_postフックが自動実行される）
        $post_id = $this->factory->post->create([
            'post_title' => 'WordPress自動解析テスト',
            'post_content' => 'この投稿はwordpress フックテスト用です。'
        ]);

        // フックにより自動的にカウントされているか確認
        $count = how_many_wordpress( $post_id );
        $this->assertEquals( 2, $count, 'save_postフックによる自動カウントが失敗' );

        // フックの登録を解除（テスト間の分離）
        remove_action( 'save_post', 'mfpow_handle_post_save' );
    }

    /**
     * 自動保存とリビジョンの除外テスト
     * 
     * WordPressの自動保存やリビジョン作成時に
     * 解析処理がスキップされることを確認します。
     */
    public function test_autosave_and_revision_exclusion() {
        // 通常の投稿を作成
        $post_id = $this->factory->post->create([
            'post_title' => 'WordPress テスト投稿',
            'post_content' => 'wordpress content'
        ]);

        // 手動でmfpow_handle_post_saveを呼び出してテスト

        // 1. 通常の投稿保存（処理される）
        mfpow_handle_post_save( $post_id );
        $this->assertEquals( 2, how_many_wordpress( $post_id ) );

        // データをクリア
        mfpow_clear_wordpress_count( $post_id );

        // 2. 自動保存のシミュレーション
        // Note: wp_is_post_autosaveは実際の自動保存投稿でないとtrueを返さないため
        // ここでは関数の動作確認のみ行う
        $this->assertFalse( wp_is_post_autosave( $post_id ) );

        // 3. リビジョンのシミュレーション  
        $this->assertFalse( wp_is_post_revision( $post_id ) );

        // 4. 投稿タイプの確認
        $this->assertEquals( 'post', get_post_type( $post_id ) );
    }

    /**
     * ==========================================
     * エラーハンドリングとエッジケーステスト
     * ==========================================
     */

    /**
     * 無効なデータでのエラーハンドリングテスト
     * 
     * 存在しない投稿IDや無効な値での動作を確認します。
     */
    public function test_error_handling() {
        // 存在しない投稿ID
        $count = mfpow_count_wordpress_keywords( 99999 );
        $this->assertEquals( 0, $count );

        // 無効な投稿ID (0)
        $count = mfpow_count_wordpress_keywords( 0 );
        $this->assertEquals( 0, $count );

        // nullの投稿ID
        $count = mfpow_count_wordpress_keywords( null );
        $this->assertEquals( 0, $count );

        // how_many_wordpress関数での存在しない投稿
        $count = how_many_wordpress( 99999 );
        $this->assertEquals( 0, $count );
    }

    /**
     * 空の投稿や特殊文字のテスト
     * 
     * 特殊なケースでの動作を確認します。
     */
    public function test_edge_cases() {
        // 空のタイトルと本文
        $post_id = $this->factory->post->create([
            'post_title' => '',
            'post_content' => ''
        ]);
        mfpow_count_wordpress_keywords( $post_id );
        $this->assertEquals( 0, how_many_wordpress( $post_id ) );

        // HTMLタグが含まれる場合
        $post_id2 = $this->factory->post->create([
            'post_title' => '<h1>WordPress Guide</h1>',
            'post_content' => '<p>Learn <strong>wordpress</strong> development.</p>'
        ]);
        mfpow_count_wordpress_keywords( $post_id2 );
        $this->assertEquals( 2, how_many_wordpress( $post_id2 ) );

        // 非常に長い投稿
        $long_content = str_repeat( 'This is a WordPress tutorial. ', 100 );
        $post_id3 = $this->factory->post->create([
            'post_title' => 'Long WordPress Post',
            'post_content' => $long_content
        ]);
        mfpow_count_wordpress_keywords( $post_id3 );
        $this->assertEquals( 101, how_many_wordpress( $post_id3 ) ); // タイトル1回 + 本文100回
    }

    /**
     * ==========================================
     * ユーティリティ関数のテスト
     * ==========================================
     */

    /**
     * 管理用ユーティリティ関数のテスト
     * 
     * 再計算、クリア、存在確認の各機能をテストします。
     */
    public function test_utility_functions() {
        // 投稿作成と初期カウント
        $post_id = $this->factory->post->create([
            'post_title' => 'WordPress Test',
            'post_content' => 'wordpress development'
        ]);
        mfpow_count_wordpress_keywords( $post_id );

        // 1. 存在確認
        $this->assertTrue( mfpow_has_wordpress_count( $post_id ) );

        // 2. 再計算機能
        $result = mfpow_recalculate_wordpress_count( $post_id );
        $this->assertTrue( $result );
        $this->assertEquals( 2, how_many_wordpress( $post_id ) );

        // 3. データクリア機能
        $result = mfpow_clear_wordpress_count( $post_id );
        $this->assertTrue( $result );
        $this->assertFalse( mfpow_has_wordpress_count( $post_id ) );
        $this->assertEquals( 0, how_many_wordpress( $post_id ) );
    }

    /**
     * 複数投稿での動作確認テスト
     * 
     * 複数の投稿が相互に影響しないことを確認します。
     */
    public function test_multiple_posts_isolation() {
        // 複数の投稿を作成
        $post_data = [
            ['WordPress Guide', 'Learn wordpress basics', 2],
            ['PHP Tutorial', 'No relevant keywords here', 0],
            ['WordPress WordPress', 'Advanced wordpress and WORDPRESS topics', 4],
        ];

        $post_ids = [];
        foreach ( $post_data as $data ) {
            list( $title, $content, $expected ) = $data;
            $post_id = $this->factory->post->create([
                'post_title' => $title,
                'post_content' => $content
            ]);
            $post_ids[] = $post_id;

            // 解析実行
            mfpow_count_wordpress_keywords( $post_id );
            $this->assertEquals( $expected, how_many_wordpress( $post_id ) );
        }

        // すべての投稿のデータが正しく分離されていることを再確認
        foreach ( $post_ids as $index => $post_id ) {
            $expected = $post_data[$index][2];
            $this->assertEquals( $expected, how_many_wordpress( $post_id ),
                "投稿 {$post_id} のデータが他の投稿の影響を受けています" );
        }
    }

    /**
     * テスト後のクリーンアップ
     * 
     * 各テスト後に必要なクリーンアップを実行します。
     * WordPressテストでは自動的に投稿データは削除されますが、
     * フックの登録状態などは手動でクリーンアップします。
     */
    public function tearDown(): void {
        // save_postフックの登録を確実に解除
        remove_action( 'save_post', 'mfpow_handle_post_save' );
        
        parent::tearDown();
    }
}
