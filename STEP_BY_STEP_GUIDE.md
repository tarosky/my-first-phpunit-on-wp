# WordPressユニットテスト ステップバイステップガイド

このガイドでは、実際にコードを書きながらWordPressユニットテストを学習できます。各ステップで具体的なコードを提供し、段階的にテストスキルを身につけます。

## 前提条件

- Node.js がインストールされていること
- 基本的なPHPの知識があること

## セットアップ

### 1. 環境の準備

```bash
# 依存関係をインストール
npm install

# WordPress環境を起動
npm run start
```

## Step 1: 最初のプラグインとテスト

### 1.1 シンプルなプラグインファイルを作成

まず、テスト対象となるシンプルなプラグインを作成します。

**ファイル: `my-first-phpunit-on-wp.php`**
```php
<?php
/**
 * Plugin Name: My First PHPUnit on WordPress
 * Description: WordPressでユニットテストを学ぶためのサンプルプラグイン
 * Version: 1.0.0
 */

// 直接アクセスを防ぐ
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hello World関数
 * 
 * @return string
 */
function mfpow_hello_world() {
    return 'Hello, WordPress Unit Testing!';
}

/**
 * 数値を2倍にする関数
 * 
 * @param int $number 入力数値
 * @return int 2倍した値
 */
function mfpow_double_number( $number ) {
    return $number * 2;
}

/**
 * 配列の要素数を返す関数
 * 
 * @param array $array 入力配列
 * @return int 要素数
 */
function mfpow_count_array( $array ) {
    if ( ! is_array( $array ) ) {
        return 0;
    }
    return count( $array );
}
```

### 1.2 最初のテストケースを作成

**ファイル: `tests/test-step1-basic.php`**
```php
<?php
/**
 * Step 1: 基本的な関数のテスト
 */

class Step1_Basic_Test extends WP_UnitTestCase {

    /**
     * Hello World関数のテスト
     */
    public function test_hello_world() {
        $result = mfpow_hello_world();
        $this->assertEquals( 'Hello, WordPress Unit Testing!', $result );
    }

    /**
     * 数値を2倍にする関数のテスト
     */
    public function test_double_number() {
        // 正の数
        $this->assertEquals( 10, mfpow_double_number( 5 ) );
        
        // 負の数
        $this->assertEquals( -6, mfpow_double_number( -3 ) );
        
        // ゼロ
        $this->assertEquals( 0, mfpow_double_number( 0 ) );
    }

    /**
     * 配列カウント関数のテスト
     */
    public function test_count_array() {
        // 空の配列
        $this->assertEquals( 0, mfpow_count_array( [] ) );
        
        // 要素がある配列
        $this->assertEquals( 3, mfpow_count_array( ['a', 'b', 'c'] ) );
        
        // 配列でない値
        $this->assertEquals( 0, mfpow_count_array( 'not an array' ) );
        $this->assertEquals( 0, mfpow_count_array( null ) );
    }
}
```

### 1.3 テストを実行

```bash
npm run test
```

**期待される結果**: すべてのテストが成功すること

## Step 2: WordPressの投稿機能を使ったテスト

### 2.1 投稿関連の関数を追加

**ファイル: `my-first-phpunit-on-wp.php` に追加**
```php
/**
 * 公開済み投稿の数を取得する
 * 
 * @return int 公開済み投稿数
 */
function mfpow_get_published_post_count() {
    $posts = get_posts([
        'post_status' => 'publish',
        'numberposts' => -1,
        'post_type' => 'post'
    ]);
    return count( $posts );
}

/**
 * 投稿にカスタムメタを追加する
 * 
 * @param int $post_id 投稿ID
 * @param string $key メタキー
 * @param string $value メタ値
 * @return bool 成功/失敗
 */
function mfpow_add_post_meta( $post_id, $key, $value ) {
    if ( ! $post_id || ! $key ) {
        return false;
    }
    
    return update_post_meta( $post_id, $key, $value );
}

/**
 * 特定の投稿タイトルを大文字に変換する
 * 
 * @param int $post_id 投稿ID
 * @return string|false 大文字のタイトル、失敗時はfalse
 */
function mfpow_uppercase_post_title( $post_id ) {
    $post = get_post( $post_id );
    
    if ( ! $post ) {
        return false;
    }
    
    return strtoupper( $post->post_title );
}
```

### 2.2 投稿機能のテストを作成

**ファイル: `tests/test-step2-posts.php`**
```php
<?php
/**
 * Step 2: WordPress投稿機能のテスト
 */

class Step2_Posts_Test extends WP_UnitTestCase {

    /**
     * 公開済み投稿数取得のテスト
     */
    public function test_get_published_post_count() {
        // 初期状態では投稿がない
        $this->assertEquals( 0, mfpow_get_published_post_count() );
        
        // 投稿を2つ作成
        $this->factory()->post->create([
            'post_status' => 'publish'
        ]);
        $this->factory()->post->create([
            'post_status' => 'publish'
        ]);
        
        // 投稿数が2になることを確認
        $this->assertEquals( 2, mfpow_get_published_post_count() );
        
        // 下書き投稿は含まれないことを確認
        $this->factory()->post->create([
            'post_status' => 'draft'
        ]);
        
        $this->assertEquals( 2, mfpow_get_published_post_count() );
    }

    /**
     * 投稿メタ追加のテスト
     */
    public function test_add_post_meta() {
        // テスト用投稿を作成
        $post_id = $this->factory()->post->create();
        
        // メタデータを追加
        $result = mfpow_add_post_meta( $post_id, 'test_key', 'test_value' );
        
        // 追加が成功したことを確認
        $this->assertTrue( $result );
        
        // メタデータが正しく保存されたことを確認
        $this->assertEquals( 'test_value', get_post_meta( $post_id, 'test_key', true ) );
        
        // 無効な値での失敗テスト
        $this->assertFalse( mfpow_add_post_meta( 0, 'key', 'value' ) );
        $this->assertFalse( mfpow_add_post_meta( $post_id, '', 'value' ) );
    }

    /**
     * 投稿タイトル大文字化のテスト
     */
    public function test_uppercase_post_title() {
        // テスト用投稿を作成
        $post_id = $this->factory()->post->create([
            'post_title' => 'hello world'
        ]);
        
        // タイトルが大文字化されることを確認
        $this->assertEquals( 'HELLO WORLD', mfpow_uppercase_post_title( $post_id ) );
        
        // 存在しない投稿IDでのテスト
        $this->assertFalse( mfpow_uppercase_post_title( 99999 ) );
        
        // 日本語タイトルでのテスト
        $jp_post_id = $this->factory()->post->create([
            'post_title' => 'こんにちは世界'
        ]);
        
        $this->assertEquals( 'こんにちは世界', mfpow_uppercase_post_title( $jp_post_id ) );
    }
}
```

### 2.3 テストを実行

```bash
npm run test
```

## Step 3: WordPressフック（アクション・フィルター）のテスト

### 3.1 フック機能を追加

**ファイル: `my-first-phpunit-on-wp.php` に追加**
```php
/**
 * 投稿保存時に実行されるアクション
 */
add_action( 'save_post', 'mfpow_on_save_post' );

/**
 * 投稿保存時の処理
 * 
 * @param int $post_id 投稿ID
 */
function mfpow_on_save_post( $post_id ) {
    // 自動保存やリビジョンをスキップ
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }
    
    // カスタムメタに保存日時を記録
    update_post_meta( $post_id, '_mfpow_last_saved', current_time( 'mysql' ) );
}

/**
 * 投稿タイトルにプレフィックスを追加するフィルター
 */
add_filter( 'the_title', 'mfpow_add_title_prefix' );

/**
 * タイトルにプレフィックスを追加
 * 
 * @param string $title 元のタイトル
 * @return string プレフィックス付きタイトル
 */
function mfpow_add_title_prefix( $title ) {
    if ( is_admin() ) {
        return $title;
    }
    
    return '[MFPOW] ' . $title;
}

/**
 * 設定値を取得する（デフォルト値付き）
 * 
 * @param string $option_name オプション名
 * @param mixed $default デフォルト値
 * @return mixed オプション値
 */
function mfpow_get_option( $option_name, $default = '' ) {
    return get_option( 'mfpow_' . $option_name, $default );
}

/**
 * 設定値を保存する
 * 
 * @param string $option_name オプション名
 * @param mixed $value 保存する値
 * @return bool 成功/失敗
 */
function mfpow_update_option( $option_name, $value ) {
    return update_option( 'mfpow_' . $option_name, $value );
}
```

### 3.2 フック機能のテストを作成

**ファイル: `tests/test-step3-hooks.php`**
```php
<?php
/**
 * Step 3: WordPressフック機能のテスト
 */

class Step3_Hooks_Test extends WP_UnitTestCase {

    /**
     * 投稿保存アクションのテスト
     */
    public function test_on_save_post_action() {
        // 投稿を作成（save_postアクションが実行される）
        $post_id = wp_insert_post([
            'post_title' => 'Test Post',
            'post_content' => 'Test Content',
            'post_status' => 'publish'
        ]);
        
        // アクションによって保存日時が記録されることを確認
        $saved_time = get_post_meta( $post_id, '_mfpow_last_saved', true );
        $this->assertNotEmpty( $saved_time );
        
        // 保存された時刻が有効な日時形式であることを確認
        $this->assertNotFalse( strtotime( $saved_time ) );
    }

    /**
     * タイトルフィルターのテスト
     */
    public function test_title_prefix_filter() {
        // フロントエンドでタイトルフィルターが動作することを確認
        $original_title = 'Sample Title';
        
        // is_admin() が false の場合（フロントエンド）
        set_current_screen( 'front' );
        $filtered_title = apply_filters( 'the_title', $original_title );
        $this->assertEquals( '[MFPOW] Sample Title', $filtered_title );
        
        // 管理画面では フィルターが適用されないことを確認
        set_current_screen( 'edit.php' );
        $admin_title = apply_filters( 'the_title', $original_title );
        $this->assertEquals( 'Sample Title', $admin_title );
    }

    /**
     * カスタムオプション機能のテスト
     */
    public function test_custom_options() {
        // デフォルト値のテスト
        $this->assertEquals( 'default', mfpow_get_option( 'test_option', 'default' ) );
        $this->assertEquals( '', mfpow_get_option( 'nonexistent_option' ) );
        
        // オプション保存のテスト
        $this->assertTrue( mfpow_update_option( 'test_option', 'test_value' ) );
        $this->assertEquals( 'test_value', mfpow_get_option( 'test_option' ) );
        
        // 配列値の保存テスト
        $array_value = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertTrue( mfpow_update_option( 'array_option', $array_value ) );
        $this->assertEquals( $array_value, mfpow_get_option( 'array_option' ) );
    }

    /**
     * カスタムアクションのテスト
     */
    public function test_custom_action() {
        // アクションが実行されたかを追跡する変数
        $action_executed = false;
        $received_data = null;
        
        // テスト用のアクションハンドラーを追加
        add_action( 'mfpow_custom_action', function( $data ) use ( &$action_executed, &$received_data ) {
            $action_executed = true;
            $received_data = $data;
        });
        
        // アクションを実行
        $test_data = 'test_data';
        do_action( 'mfpow_custom_action', $test_data );
        
        // アクションが実行され、データが正しく渡されたことを確認
        $this->assertTrue( $action_executed );
        $this->assertEquals( $test_data, $received_data );
    }

    /**
     * テスト間での状態クリーンアップを確認
     */
    public function test_database_cleanup() {
        // 前のテストで作成されたオプションがクリーンアップされていることを確認
        $this->assertEquals( '', mfpow_get_option( 'test_option' ) );
        $this->assertEquals( [], mfpow_get_option( 'array_option', [] ) );
    }
}
```

### 3.3 テストを実行

```bash
npm run test
```

## Step 4: エラーハンドリングとEdge Caseのテスト

### 4.1 エラーハンドリング機能を追加

**ファイル: `my-first-phpunit-on-wp.php` に追加**
```php
/**
 * ユーザー権限をチェックする関数
 * 
 * @param string $capability 必要な権限
 * @return WP_Error|true 権限がない場合はWP_Error、ある場合はtrue
 */
function mfpow_check_user_capability( $capability ) {
    if ( ! current_user_can( $capability ) ) {
        return new WP_Error( 
            'insufficient_permission', 
            'You do not have permission to perform this action.',
            ['status' => 403]
        );
    }
    
    return true;
}

/**
 * 投稿を安全に削除する
 * 
 * @param int $post_id 投稿ID
 * @return WP_Error|true 削除成功時はtrue、失敗時はWP_Error
 */
function mfpow_safe_delete_post( $post_id ) {
    // 投稿の存在チェック
    $post = get_post( $post_id );
    if ( ! $post ) {
        return new WP_Error( 'post_not_found', 'Post not found.' );
    }
    
    // 権限チェック
    $capability_check = mfpow_check_user_capability( 'delete_posts' );
    if ( is_wp_error( $capability_check ) ) {
        return $capability_check;
    }
    
    // 投稿削除
    $result = wp_delete_post( $post_id, true );
    
    if ( ! $result ) {
        return new WP_Error( 'delete_failed', 'Failed to delete post.' );
    }
    
    return true;
}

/**
 * 数値の割り算を実行（ゼロ除算エラーハンドリング付き）
 * 
 * @param float $dividend 被除数
 * @param float $divisor 除数
 * @return WP_Error|float 計算結果、エラー時はWP_Error
 */
function mfpow_safe_divide( $dividend, $divisor ) {
    if ( ! is_numeric( $dividend ) || ! is_numeric( $divisor ) ) {
        return new WP_Error( 'invalid_input', 'Both values must be numeric.' );
    }
    
    if ( $divisor == 0 ) {
        return new WP_Error( 'division_by_zero', 'Cannot divide by zero.' );
    }
    
    return $dividend / $divisor;
}
```

### 4.2 エラーハンドリングのテストを作成

**ファイル: `tests/test-step4-error-handling.php`**
```php
<?php
/**
 * Step 4: エラーハンドリングとEdge Caseのテスト
 */

class Step4_Error_Handling_Test extends WP_UnitTestCase {

    /**
     * ユーザー権限チェックのテスト
     */
    public function test_user_capability_check() {
        // 管理者ユーザーでログイン
        $admin_user = $this->factory()->user->create(['role' => 'administrator']);
        wp_set_current_user( $admin_user );
        
        // 管理者は delete_posts 権限を持っている
        $result = mfpow_check_user_capability( 'delete_posts' );
        $this->assertTrue( $result );
        
        // 権限のない購読者でテスト
        $subscriber_user = $this->factory()->user->create(['role' => 'subscriber']);
        wp_set_current_user( $subscriber_user );
        
        $result = mfpow_check_user_capability( 'delete_posts' );
        $this->assertWPError( $result );
        $this->assertEquals( 'insufficient_permission', $result->get_error_code() );
        
        // ログアウト状態でテスト
        wp_set_current_user( 0 );
        
        $result = mfpow_check_user_capability( 'delete_posts' );
        $this->assertWPError( $result );
    }

    /**
     * 安全な投稿削除のテスト
     */
    public function test_safe_delete_post() {
        // 管理者でログイン
        $admin_user = $this->factory()->user->create(['role' => 'administrator']);
        wp_set_current_user( $admin_user );
        
        // 投稿を作成
        $post_id = $this->factory()->post->create();
        
        // 投稿が存在することを確認
        $this->assertNotNull( get_post( $post_id ) );
        
        // 投稿を削除
        $result = mfpow_safe_delete_post( $post_id );
        $this->assertTrue( $result );
        
        // 投稿が削除されたことを確認
        $this->assertNull( get_post( $post_id ) );
        
        // 存在しない投稿の削除を試行
        $result = mfpow_safe_delete_post( 99999 );
        $this->assertWPError( $result );
        $this->assertEquals( 'post_not_found', $result->get_error_code() );
        
        // 権限のないユーザーでテスト
        $subscriber_user = $this->factory()->user->create(['role' => 'subscriber']);
        wp_set_current_user( $subscriber_user );
        
        $post_id2 = $this->factory()->post->create();
        $result = mfpow_safe_delete_post( $post_id2 );
        $this->assertWPError( $result );
        $this->assertEquals( 'insufficient_permission', $result->get_error_code() );
    }

    /**
     * 安全な除算のテスト
     */
    public function test_safe_divide() {
        // 正常な除算
        $result = mfpow_safe_divide( 10, 2 );
        $this->assertEquals( 5, $result );
        
        // 小数点の除算
        $result = mfpow_safe_divide( 7, 2 );
        $this->assertEquals( 3.5, $result );
        
        // 負の数の除算
        $result = mfpow_safe_divide( -10, 2 );
        $this->assertEquals( -5, $result );
        
        // ゼロ除算エラー
        $result = mfpow_safe_divide( 10, 0 );
        $this->assertWPError( $result );
        $this->assertEquals( 'division_by_zero', $result->get_error_code() );
        
        // 無効な入力値
        $result = mfpow_safe_divide( 'not_a_number', 5 );
        $this->assertWPError( $result );
        $this->assertEquals( 'invalid_input', $result->get_error_code() );
        
        $result = mfpow_safe_divide( 10, 'not_a_number' );
        $this->assertWPError( $result );
        $this->assertEquals( 'invalid_input', $result->get_error_code() );
        
        // null値のテスト
        $result = mfpow_safe_divide( null, 5 );
        $this->assertWPError( $result );
        
        // boolean値のテスト（PHPでは1,0に変換される）
        $result = mfpow_safe_divide( true, 1 );
        $this->assertEquals( 1, $result );
        
        $result = mfpow_safe_divide( false, 1 );
        $this->assertEquals( 0, $result );
    }

    /**
     * Edge Case: 大きな数値の処理
     */
    public function test_large_numbers() {
        $large_number = PHP_INT_MAX;
        $result = mfpow_double_number( $large_number );
        
        // オーバーフローの可能性があるため、型をチェック
        $this->assertIsNumeric( $result );
    }

    /**
     * Edge Case: 特殊文字を含む投稿タイトル
     */
    public function test_special_characters_in_title() {
        // 特殊文字を含むタイトル
        $special_title = '!"#$%&\'()=~|`{+*}_?><';
        $post_id = $this->factory()->post->create([
            'post_title' => $special_title
        ]);
        
        $result = mfpow_uppercase_post_title( $post_id );
        $this->assertEquals( strtoupper( $special_title ), $result );
        
        // HTMLタグを含むタイトル
        $html_title = '<script>alert("test")</script>';
        $post_id2 = $this->factory()->post->create([
            'post_title' => $html_title
        ]);
        
        $result = mfpow_uppercase_post_title( $post_id2 );
        $this->assertEquals( strtoupper( $html_title ), $result );
    }
}
```

### 4.3 テストを実行

```bash
npm run test
```

## Step 5: 統合テストとパフォーマンステスト

### 5.1 統合テスト機能を追加

**ファイル: `my-first-phpunit-on-wp.php` に追加**
```php
/**
 * 投稿とそのメタデータを一括で作成する
 * 
 * @param array $post_data 投稿データ
 * @param array $meta_data メタデータ
 * @return int|WP_Error 投稿ID、失敗時はWP_Error
 */
function mfpow_create_post_with_meta( $post_data, $meta_data = [] ) {
    // 投稿を作成
    $post_id = wp_insert_post( $post_data );
    
    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }
    
    // メタデータを追加
    foreach ( $meta_data as $key => $value ) {
        update_post_meta( $post_id, $key, $value );
    }
    
    return $post_id;
}

/**
 * 複数の投稿を一括削除する
 * 
 * @param array $post_ids 投稿IDの配列
 * @return array 削除結果の配列
 */
function mfpow_bulk_delete_posts( $post_ids ) {
    $results = [];
    
    foreach ( $post_ids as $post_id ) {
        $results[ $post_id ] = mfpow_safe_delete_post( $post_id );
    }
    
    return $results;
}
```

### 5.2 統合テストを作成

**ファイル: `tests/test-step5-integration.php`**
```php
<?php
/**
 * Step 5: 統合テストとパフォーマンステスト
 */

class Step5_Integration_Test extends WP_UnitTestCase {

    /**
     * 投稿とメタデータの統合テスト
     */
    public function test_post_with_meta_integration() {
        // 投稿データとメタデータを準備
        $post_data = [
            'post_title' => 'Integration Test Post',
            'post_content' => 'This is a test post for integration testing.',
            'post_status' => 'publish'
        ];
        
        $meta_data = [
            'custom_field_1' => 'value_1',
            'custom_field_2' => 'value_2',
            'custom_field_3' => ['array', 'value']
        ];
        
        // 投稿とメタデータを作成
        $post_id = mfpow_create_post_with_meta( $post_data, $meta_data );
        
        // 投稿が正常に作成されたことを確認
        $this->assertIsInt( $post_id );
        $this->assertGreaterThan( 0, $post_id );
        
        // 投稿データを検証
        $created_post = get_post( $post_id );
        $this->assertEquals( $post_data['post_title'], $created_post->post_title );
        $this->assertEquals( $post_data['post_content'], $created_post->post_content );
        $this->assertEquals( $post_data['post_status'], $created_post->post_status );
        
        // メタデータを検証
        foreach ( $meta_data as $key => $expected_value ) {
            $actual_value = get_post_meta( $post_id, $key, true );
            $this->assertEquals( $expected_value, $actual_value );
        }
        
        // 投稿数の確認（保存時アクションでメタが追加されているか）
        $this->assertEquals( 1, mfpow_get_published_post_count() );
        
        // 保存時に追加されるメタデータも確認
        $last_saved = get_post_meta( $post_id, '_mfpow_last_saved', true );
        $this->assertNotEmpty( $last_saved );
    }

    /**
     * 一括削除の統合テスト
     */
    public function test_bulk_delete_integration() {
        // 管理者権限でログイン
        $admin_user = $this->factory()->user->create(['role' => 'administrator']);
        wp_set_current_user( $admin_user );
        
        // 複数の投稿を作成
        $post_ids = [];
        for ( $i = 0; $i < 5; $i++ ) {
            $post_ids[] = $this->factory()->post->create([
                'post_title' => "Test Post {$i}"
            ]);
        }
        
        // 投稿が作成されたことを確認
        $this->assertEquals( 5, count( $post_ids ) );
        foreach ( $post_ids as $post_id ) {
            $this->assertNotNull( get_post( $post_id ) );
        }
        
        // 一括削除を実行
        $results = mfpow_bulk_delete_posts( $post_ids );
        
        // すべて成功したことを確認
        foreach ( $results as $post_id => $result ) {
            $this->assertTrue( $result );
            $this->assertNull( get_post( $post_id ) );
        }
    }

    /**
     * パフォーマンステスト: 大量データの処理
     */
    public function test_performance_large_dataset() {
        $start_time = microtime( true );
        
        // 100件の投稿を作成
        $post_ids = $this->factory()->post->create_many( 100 );
        
        // 投稿数を取得（データベースクエリのパフォーマンス）
        $count = mfpow_get_published_post_count();
        
        $end_time = microtime( true );
        $execution_time = $end_time - $start_time;
        
        // 100件の投稿が正しく作成されたことを確認
        $this->assertEquals( 100, $count );
        
        // 実行時間が妥当な範囲内であることを確認（5秒以内）
        $this->assertLessThan( 5.0, $execution_time, 'Performance test failed: execution time too long' );
    }

    /**
     * 全機能の統合ワークフローテスト
     */
    public function test_complete_workflow() {
        // 1. 管理者でログイン
        $admin_user = $this->factory()->user->create(['role' => 'administrator']);
        wp_set_current_user( $admin_user );
        
        // 2. 設定を保存
        mfpow_update_option( 'workflow_enabled', true );
        $this->assertTrue( mfpow_get_option( 'workflow_enabled' ) );
        
        // 3. 投稿とメタデータを作成
        $post_id = mfpow_create_post_with_meta(
            [
                'post_title' => 'Workflow Test',
                'post_content' => 'Testing complete workflow',
                'post_status' => 'publish'
            ],
            [
                'workflow_step' => 'created',
                'priority' => 'high'
            ]
        );
        
        // 4. 投稿が正しく作成されたことを確認
        $this->assertGreaterThan( 0, $post_id );
        $this->assertEquals( 'WORKFLOW TEST', mfpow_uppercase_post_title( $post_id ) );
        
        // 5. 数値計算処理
        $calculation = mfpow_safe_divide( 100, 5 );
        $this->assertEquals( 20, $calculation );
        
        // 6. 最終的に投稿を削除
        $delete_result = mfpow_safe_delete_post( $post_id );
        $this->assertTrue( $delete_result );
        
        // 7. ワークフロー完了確認
        $this->assertNull( get_post( $post_id ) );
        $this->assertEquals( 0, mfpow_get_published_post_count() );
    }
}
```

## テストの実行と結果確認

### 全体のテストを実行
```bash
npm run test
```

### 特定のステップのテストのみ実行
```bash
# Step 1のテストのみ
vendor/bin/phpunit tests/test-step1-basic.php

# Step 2のテストのみ
vendor/bin/phpunit tests/test-step2-posts.php
```

### テストの詳細出力
```bash
# より詳細な出力でテストを実行
vendor/bin/phpunit --verbose
```

## 学習の進め方

### 1. 各ステップの順番通りに進める
- Step 1から順番に実行し、各ステップで何が学習できるかを確認
- エラーが出た場合は、メッセージを読んで原因を考察

### 2. コードを改変して実験
- わざとテストを失敗させて、エラーメッセージを確認
- 期待値を変更してテストの動作を理解

### 3. 独自のテストケースを追加
- 既存の関数に対して、異なる入力値でのテストを追加
- 新しい機能を実装してそのテストを作成

### 4. デバッグ技術を習得
```php
// テスト内でのデバッグ出力
public function test_debug_example() {
    $result = mfpow_hello_world();
    
    // デバッグ出力
    fwrite( STDERR, "Result: " . $result . "\n" );
    
    $this->assertEquals( 'Hello, WordPress Unit Testing!', $result );
}
```

## よくある問題と解決法

### 1. テストが失敗する場合
```bash
# より詳細なエラー情報を表示
vendor/bin/phpunit --verbose --debug
```

### 2. 環境の問題
```bash
# WordPress環境を再起動
npm run stop
npm run start
```

### 3. キャッシュの問題
```bash
# Composerキャッシュをクリア
composer clear-cache

# Node.jsキャッシュをクリア
npm cache clean --force
```

## まとめ

このステップバイステップガイドを通じて、以下のスキルが身につきます：

1. **基本的なユニットテスト**: 関数の入出力をテスト
2. **WordPress統合テスト**: 投稿、メタデータ、データベースのテスト
3. **フック機能のテスト**: アクションとフィルターのテスト
4. **エラーハンドリング**: WP_Errorと例外処理のテスト
5. **統合テスト**: 複数機能を組み合わせたワークフローのテスト
6. **パフォーマンステスト**: 大量データ処理の性能測定

実際のプラグイン開発では、これらのテストパターンを組み合わせて、堅牢で保守しやすいコードを作成できるようになります。

## 次のステップ

1. **CI/CD環境の構築**: GitHub Actionsでの自動テスト
2. **コードカバレッジの測定**: テストがどの程度コードをカバーしているか
3. **モックとスタブ**: 外部依存関係のテスト
4. **テスト駆動開発（TDD）**: テストを先に書く開発手法
