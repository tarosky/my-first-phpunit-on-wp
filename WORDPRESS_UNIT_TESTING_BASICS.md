# WordPressユニットテスト 基本概念ガイド

## 1. WordPressユニットテストとは

WordPressユニットテストは、WordPressプラグインやテーマの個別機能を自動的にテストする手法です。PHPUnitフレームワークを使用し、WordPressの機能と統合されたテスト環境で実行されます。

## 2. テスト環境の構成要素

### 2.1 @wordpress/env
- DockerベースのローカルWordPress環境
- テスト専用のWordPressインスタンスを自動構築
- PHPバージョンやプラグインの設定が可能

### 2.2 PHPUnit
- PHP用のユニットテストフレームワーク
- アサーション（検証）機能を提供
- テストの自動実行とレポート生成

### 2.3 WordPress Test Suite
- WordPressが提供するテスト用のライブラリ
- `WP_UnitTestCase`クラスを含む
- WordPressの標準機能をテストで使用可能

## 3. 重要なファイルと役割

### 3.1 phpunit.xml.dist
```xml
<?xml version="1.0"?>
<phpunit
    bootstrap="tests/bootstrap.php"
    backupGlobals="false"
    colors="true">
    <testsuites>
        <testsuite name="Plugin Basic Test">
            <directory suffix="Test.php">./tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**主な設定項目**:
- `bootstrap`: テスト開始前に読み込むファイル
- `testsuites`: テストスイート（テストグループ）の定義
- `directory`: テストファイルの場所とパターン

### 3.2 tests/bootstrap.php
```php
<?php
// WordPress Test Suiteの読み込み
$_tests_dir = getenv( 'WP_TESTS_DIR' );
require_once $_tests_dir . '/includes/functions.php';

// プラグインの読み込み
function _manually_load_plugin() {
    require dirname( dirname( __FILE__ ) ) . '/plugin-main.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// テスト環境の起動
require $_tests_dir . '/includes/bootstrap.php';
```

**役割**:
- WordPressテスト環境の初期化
- テスト対象プラグインの読み込み
- テスト用のフィルターやアクションの設定

## 4. WP_UnitTestCaseクラス

### 4.1 基本的な使い方
```php
class Sample_Test extends WP_UnitTestCase {
    
    public function test_basic_functionality() {
        // テストロジックをここに書く
        $result = my_plugin_function();
        $this->assertEquals( 'expected_value', $result );
    }
}
```

### 4.2 よく使用するメソッド

#### セットアップとクリーンアップ
```php
public function setUp() {
    parent::setUp();
    // テスト開始前の準備処理
}

public function tearDown() {
    // テスト終了後のクリーンアップ
    parent::tearDown();
}
```

#### WordPressオブジェクトの作成
```php
// 投稿の作成
$post_id = $this->factory()->post->create([
    'post_title' => 'Test Post',
    'post_content' => 'Test Content'
]);

// ユーザーの作成
$user_id = $this->factory()->user->create([
    'role' => 'editor'
]);
```

## 5. アサーション（検証）メソッド

### 5.1 基本的なアサーション
```php
// 値の等価性をチェック
$this->assertEquals( $expected, $actual );

// 真偽値のチェック
$this->assertTrue( $condition );
$this->assertFalse( $condition );

// 空かどうかのチェック
$this->assertEmpty( $array );
$this->assertNotEmpty( $array );
```

### 5.2 WordPress固有のアサーション
```php
// WP_Errorかどうかのチェック
$this->assertWPError( $result );
$this->assertNotWPError( $result );

// 投稿が存在するかのチェック
$this->assertInstanceOf( 'WP_Post', get_post( $post_id ) );
```

## 6. テストパターンの例

### 6.1 関数のテスト
```php
public function test_utility_function() {
    // 関数の戻り値をテスト
    $result = my_utility_function( 'input' );
    $this->assertEquals( 'expected_output', $result );
}
```

### 6.2 投稿操作のテスト
```php
public function test_post_creation() {
    // 投稿を作成
    $post_id = wp_insert_post([
        'post_title' => 'Test Post',
        'post_status' => 'publish'
    ]);
    
    // 投稿が正常に作成されたかチェック
    $this->assertGreaterThan( 0, $post_id );
    $this->assertEquals( 'Test Post', get_the_title( $post_id ) );
}
```

### 6.3 メタデータのテスト
```php
public function test_post_meta() {
    $post_id = $this->factory()->post->create();
    
    // メタデータを保存
    update_post_meta( $post_id, 'test_key', 'test_value' );
    
    // メタデータが正しく保存されたかチェック
    $this->assertEquals( 'test_value', get_post_meta( $post_id, 'test_key', true ) );
}
```

### 6.4 フックのテスト
```php
public function test_action_hook() {
    // アクションが実行されたかどうかを追跡
    $action_fired = false;
    
    // テスト用のコールバック関数を追加
    add_action( 'my_custom_action', function() use ( &$action_fired ) {
        $action_fired = true;
    });
    
    // アクションを実行
    do_action( 'my_custom_action' );
    
    // アクションが実行されたかチェック
    $this->assertTrue( $action_fired );
}
```

## 7. データベースとテストの分離

### 7.1 自動ロールバック
- 各テストメソッド実行後、データベースは自動的に初期状態に戻る
- 他のテストへの影響を心配する必要がない

### 7.2 テスト用データの作成
```php
public function test_with_test_data() {
    // テスト開始時点ではクリーンなデータベース
    $posts_before = get_posts(['numberposts' => -1]);
    $this->assertEmpty( $posts_before );
    
    // テスト用データを作成
    $this->factory()->post->create_many( 5 );
    
    // データが作成されたことを確認
    $posts_after = get_posts(['numberposts' => -1]);
    $this->assertCount( 5, $posts_after );
}
// テスト終了後、作成したデータは自動的に削除される
```

## 8. テスト実行の流れ

### 8.1 コマンドライン実行
```bash
# すべてのテストを実行
npm run test

# 特定のテストクラスのみ実行
vendor/bin/phpunit tests/test-specific.php

# 特定のテストメソッドのみ実行
vendor/bin/phpunit --filter test_method_name
```

### 8.2 実行結果の読み方
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

..F                                                                 3 / 3 (100%)

Time: 00:02.123, Memory: 50.00 MB

There was 1 failure:

1) Sample_Test::test_failing_example
Failed asserting that false is true.
```

**記号の意味**:
- `.`: テスト成功
- `F`: テスト失敗
- `E`: エラー発生
- `S`: テストスキップ

## 9. ベストプラクティス

### 9.1 テストの命名規則
```php
// 良い例：何をテストするかが明確
public function test_should_return_empty_array_when_no_posts_exist() {}

// 悪い例：何をテストするか不明
public function test_function() {}
```

### 9.2 テストの独立性
```php
// 各テストは独立して動作するべき
public function test_feature_a() {
    // このテストはtest_feature_bに依存しない
}

public function test_feature_b() {
    // このテストもtest_feature_aに依存しない
}
```

### 9.3 適切なアサーションの使用
```php
// 具体的なアサーションを使用
$this->assertEquals( 5, count( $posts ) );  // 良い
$this->assertTrue( count( $posts ) == 5 );  // 悪い
```

## 10. よくある問題と解決方法

### 10.1 テストが失敗する場合
1. エラーメッセージを詳しく読む
2. `var_dump()`や`print_r()`でデバッグ情報を出力
3. 期待値と実際の値を比較

### 10.2 環境の問題
1. WordPress環境が正しく起動しているか確認
2. プラグインが正しく読み込まれているか確認
3. 必要な依存関係がインストールされているか確認

## まとめ

WordPressユニットテストは、プラグイン開発における品質保証の重要な手段です。基本概念を理解し、実践的なテストパターンを習得することで、より安全で保守しやすいコードを書けるようになります。
