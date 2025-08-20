# WordPress Ajax テスト学習ガイド

このドキュメントでは、WordPressのAjax機能をテストする方法を段階的に学習します。
Ajax処理は現代のWebアプリケーションにおいて重要な技術であり、適切なテスト手法を理解することが必要です。

## Ajax機能の概要

WordPress Ajax機能は、ページをリロードすることなくサーバーとデータのやり取りを行う仕組みです。
`admin-ajax.php`を通じて、ログインユーザーと非ログインユーザーの両方に対応した処理を実装できます。

### 基本的なAjax処理の流れ

1. **フック登録**: `wp_ajax_` と `wp_ajax_nopriv_` フックを使用
2. **セキュリティ検証**: nonce（ワンタイムトークン）による認証
3. **入力値検証**: サニタイズと妥当性チェック
4. **レスポンス返却**: JSON形式での結果送信

## 実装例解説

### 1. Ajax ハンドラーの登録

```php
function mfpow_register_ajax_hooks() {
    // ログインユーザー向け
    add_action( 'wp_ajax_mfpow_calculate', 'mfpow_handle_ajax_calculate' );
    
    // 非ログインユーザー向け
    add_action( 'wp_ajax_nopriv_mfpow_calculate', 'mfpow_handle_ajax_calculate' );
}
```

**学習ポイント:**
- `wp_ajax_{action}` : ログインユーザー用のフック
- `wp_ajax_nopriv_{action}` : 非ログインユーザー用のフック
- 同一関数で両方を処理可能

### 2. Ajax処理関数の実装

```php
function mfpow_handle_ajax_calculate() {
    // 1. セキュリティ検証
    if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'mfpow_ajax_nonce' ) ) {
        wp_send_json_error( array(
            'message' => 'Security check failed.',
        ) );
    }

    // 2. 入力値の取得と検証
    $number1 = sanitize_text_field( $_POST['number1'] ?? '' );
    $number2 = sanitize_text_field( $_POST['number2'] ?? '' );

    // 3. 処理実行
    $result = $number1 + $number2;

    // 4. レスポンス送信
    wp_send_json_success( array(
        'result' => $result,
    ) );
}
```

**学習ポイント:**
- `wp_verify_nonce()` : セキュリティトークン検証
- `sanitize_text_field()` : 入力値サニタイズ
- `wp_send_json_success()` : 成功レスポンス
- `wp_send_json_error()` : エラーレスポンス

## テスト手法詳解

### 1. Ajax ハンドラー登録テスト

```php
public function test_ajax_handlers_are_registered() {
    // ログインユーザー向けハンドラーの確認
    $this->assertTrue( has_action( 'wp_ajax_mfpow_calculate' ) );
    
    // 非ログインユーザー向けハンドラーの確認
    $this->assertTrue( has_action( 'wp_ajax_nopriv_mfpow_calculate' ) );
}
```

**テスト対象:**
- フックが適切に登録されているか
- ログイン・非ログイン両方の対応

### 2. Ajax 処理実行テスト

```php
public function test_ajax_calculate_addition_success() {
    // テスト用POSTデータ設定
    $_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
    $_POST['number1'] = '10';
    $_POST['number2'] = '5';
    $_POST['operation'] = 'add';

    // Ajax処理実行
    try {
        $this->_handleAjax( 'mfpow_calculate' );
    } catch ( WPAjaxDieContinueException $e ) {
        // Ajax処理の正常終了をキャッチ
    }

    // レスポンス確認
    $response = json_decode( $this->_last_response, true );
    $this->assertTrue( $response['success'] );
    $this->assertEquals( 15, $response['data']['result'] );
}
```

**テスト技法:**
- `$_POST` データの模擬設定
- `_handleAjax()` による処理実行
- `WPAjaxDieContinueException` による終了処理キャッチ
- `$_last_response` からレスポンス取得

### 3. セキュリティテスト

```php
public function test_ajax_calculate_invalid_nonce() {
    $_POST['nonce'] = 'invalid_nonce';
    $_POST['number1'] = '10';
    $_POST['number2'] = '5';

    try {
        $this->_handleAjax( 'mfpow_calculate' );
    } catch ( WPAjaxDieContinueException $e ) {
    }

    $response = json_decode( $this->_last_response, true );
    $this->assertFalse( $response['success'] );
    $this->assertEquals( 'Security check failed.', $response['data']['message'] );
}
```

**セキュリティ検証項目:**
- 無効なnonce
- 不正な入力値
- 権限チェック

### 4. エラーハンドリングテスト

```php
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
```

**エラー処理テスト項目:**
- 数値エラー（ゼロ除算等）
- バリデーションエラー
- 想定外の操作

## 高度なテスト手法

### 1. WordPress データベース連携テスト

```php
public function test_ajax_get_post_count_success() {
    // テストデータ作成
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
}
```

**WordPress 統合テスト技法:**
- `$this->factory` によるテストデータ作成
- `wp_count_posts()` 等のWordPress関数テスト
- 投稿ステータス別の検証

### 2. 統合テスト

```php
public function test_ajax_integration() {
    // 複数のAjax処理を連続実行
    // 1つ目の処理
    $_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
    $_POST['number1'] = '100';
    $_POST['number2'] = '25';
    $_POST['operation'] = 'divide';

    try {
        $this->_handleAjax( 'mfpow_calculate' );
    } catch ( WPAjaxDieContinueException $e ) {
    }

    $response1 = json_decode( $this->_last_response, true );

    // POSTデータクリア
    $_POST = array();

    // 2つ目の処理
    $_POST['nonce'] = wp_create_nonce( 'mfpow_ajax_nonce' );
    $_POST['post_type'] = 'post';

    try {
        $this->_handleAjax( 'mfpow_get_post_count' );
    } catch ( WPAjaxDieContinueException $e ) {
    }

    $response2 = json_decode( $this->_last_response, true );

    // 両方のレスポンスが正常
    $this->assertTrue( $response1['success'] );
    $this->assertTrue( $response2['success'] );
}
```

**統合テストのポイント:**
- 複数処理の干渉チェック
- データの独立性確認
- 状態管理の検証

## ベストプラクティス

### 1. テストデータの管理

```php
public function setUp(): void {
    parent::setUp();
    
    // Ajax機能を読み込み
    require_once MFPOW_PLUGIN_DIR . 'includes/ajax.php';
    
    // Ajax フックを登録
    mfpow_register_ajax_hooks();
}

public function tearDown(): void {
    $_POST = array();
    parent::tearDown();
}
```

**重要なポイント:**
- `setUp()` でAjax機能初期化
- `tearDown()` でPOSTデータクリア
- テスト間の独立性確保

### 2. レスポンス形式の統一

```php
// 成功レスポンスの標準形式
wp_send_json_success( array(
    'result' => $data,
    'message' => 'Processing completed successfully.',
) );

// エラーレスポンスの標準形式
wp_send_json_error( array(
    'message' => 'An error occurred during processing.',
    'error_code' => 'INVALID_INPUT',
) );
```

### 3. セキュリティの重要性

**必須のセキュリティ対策:**
1. **nonce検証**: `wp_verify_nonce()` による認証
2. **権限チェック**: `current_user_can()` による権限確認
3. **入力値検証**: `sanitize_*()` 関数群による検証
4. **エスケープ処理**: 出力時の適切なエスケープ

## 実践演習

### 演習1: 基本的なAjax処理

**目標**: 文字列を受け取って大文字に変換するAjax処理を実装・テストする

**実装内容:**
1. `mfpow_handle_ajax_uppercase` 関数の作成
2. 入力値検証とサニタイズ
3. `strtoupper()` による変換処理
4. 包括的なテストケース作成

### 演習2: データベース操作Ajax

**目標**: ユーザー情報を取得・更新するAjax処理を実装・テストする

**実装内容:**
1. `get_userdata()` を使用した情報取得
2. `wp_update_user()` による更新処理
3. 権限チェックの実装
4. エラーハンドリングの充実

### 演習3: 複合Ajax処理

**目標**: 複数のAjax処理を組み合わせた機能を実装・テストする

**実装内容:**
1. ファイルアップロード処理
2. 画像リサイズ処理
3. メタデータ保存処理
4. 統合テストの実装

## トラブルシューティング

### よくある問題と解決策

**1. Ajax処理が実行されない**
```php
// 原因: フックが登録されていない
// 解決: has_action() で登録確認
$this->assertTrue( has_action( 'wp_ajax_your_action' ) );
```

**2. nonceエラーが発生する**
```php
// 原因: nonce生成と検証のaction名不一致
// 解決: action名の統一
$nonce = wp_create_nonce( 'same_action_name' );
wp_verify_nonce( $nonce, 'same_action_name' );
```

**3. レスポンスが期待と異なる**
```php
// 原因: JSON形式の問題
// 解決: レスポンス形式の確認
var_dump( $this->_last_response );
$response = json_decode( $this->_last_response, true );
var_dump( $response );
```

## まとめ

WordPress Ajax機能のテストでは以下の点が重要です：

1. **セキュリティファースト**: nonce検証を必ず実装
2. **包括的テスト**: 正常系・異常系両方をカバー
3. **WordPress統合**: WordPress APIとの適切な連携
4. **保守性**: テストコードの可読性と保守性

この知識を活用して、安全で確実なAjax機能を開発してください。
