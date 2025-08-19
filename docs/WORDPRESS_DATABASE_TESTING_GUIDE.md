# WordPressデータベース操作の統合テスト学習ガイド

このガイドでは、WordPressでのデータベース操作を含む機能のテスト方法を学習できます。

## 🎯 学習目標

- **統合テストの重要性**を理解する
- **WordPressファクトリー**の使用方法を習得する
- **実際のデータベース操作**をテストする方法を学ぶ
- **単体テストと統合テスト**の使い分けを理解する

## 📚 実装した機能：投稿解析システム

### 機能概要
投稿のタイトルと本文から「WordPress」というキーワードの数をカウントし、カスタムフィールドに保存する機能です。

```php
// 基本的な使用例
$post_id = 123;
$count = mfpow_count_wordpress_keywords($post_id);  // 投稿を解析
$stored_count = how_many_wordpress($post_id);       // カスタムフィールドから取得
```

### ファイル構成
- `includes/post-analyzer.php` - 機能実装
- `tests/mfpow-post-analyzer-test.php` - テストケース

## 🏗️ WordPressテストにおける重要な概念

### 1. **統合テスト優先のアプローチ**

❌ **避けるべきテスト（単体テストのみ）**
```php
// 関数の個別テストのみ - 実際のWordPress環境での動作を確認していない
public function test_keyword_count_function_only() {
    $result = count_keywords_in_text('WordPress wordpress', 'wordpress');
    $this->assertEquals(2, $result);
}
```

✅ **推奨されるテスト（統合テスト）**
```php
// 実際のWordPress環境での統合テスト
public function test_wordpress_keyword_counting_integration() {
    // WordPressファクトリーで実際の投稿を作成
    $post_id = $this->factory->post->create([
        'post_title' => 'WordPressの使い方',
        'post_content' => 'WordPress開発について'
    ]);

    // 実際の機能を実行
    mfpow_count_wordpress_keywords($post_id);

    // データベースから実際の値を確認
    $count = how_many_wordpress($post_id);
    $this->assertEquals(2, $count);
}
```

### 2. **WordPressファクトリーの活用**

WordPressのテストファクトリーを使用して、実際のデータベースに投稿を作成します：

```php
// 基本的な投稿作成
$post_id = $this->factory->post->create();

// 詳細指定の投稿作成
$post_id = $this->factory->post->create([
    'post_title' => 'テストタイトル',
    'post_content' => 'テスト本文',
    'post_status' => 'publish',
    'post_type' => 'post'
]);

// 他にも利用可能
$user_id = $this->factory->user->create();
$category_id = $this->factory->category->create();
$tag_id = $this->factory->tag->create();
```

### 3. **実際のWordPress関数での検証**

テストでは実際のWordPress関数を使用してデータベースの状態を確認します：

```php
// カスタムフィールドの確認
$meta_value = get_post_meta($post_id, '_how_many_wordpress', true);

// 投稿データの確認
$post = get_post($post_id);

// 投稿の存在確認
$this->assertInstanceOf('WP_Post', $post);
```

## 🧪 テストパターンの分類

### **1. 基本統合テスト**
最も重要なテスト。実際のWordPress環境での動作確認。

```php
public function test_basic_integration() {
    $post_id = $this->factory->post->create([...]);
    mfpow_count_wordpress_keywords($post_id);
    $this->assertEquals(expected, how_many_wordpress($post_id));
}
```

### **2. 様々なケースでの統合テスト**
様々な入力パターンでの動作確認。

```php
public function test_various_cases() {
    $test_cases = [
        [expected_count, 'title', 'content', 'description'],
        // 複数のテストケース
    ];
    
    foreach ($test_cases as $case) {
        // テスト実行
    }
}
```

### **3. フック統合テスト**
WordPressのフックシステムとの統合確認。

```php
public function test_hook_integration() {
    mfpow_register_post_analyzer_hooks();
    $post_id = $this->factory->post->create([...]);
    // フックにより自動実行される
    $this->assertEquals(expected, how_many_wordpress($post_id));
}
```

### **4. エラーハンドリングテスト**
異常系での動作確認。

```php
public function test_error_handling() {
    // 存在しない投稿ID
    $count = mfpow_count_wordpress_keywords(99999);
    $this->assertEquals(0, $count);
}
```

### **5. ユーティリティ関数テスト**
管理機能の動作確認。

```php
public function test_utility_functions() {
    // データクリア、再計算などの機能テスト
}
```

## 🔄 テストの実行

### 環境での実行
```bash
# wp-env環境でのテスト実行
npx wp-env run tests-wordpress "cd /var/www/html/wp-content/plugins/my-first-phpunit-on-wp && vendor/bin/phpunit tests/mfpow-post-analyzer-test.php"

# ローカルでのテスト実行（WordPress test suiteが必要）
vendor/bin/phpunit tests/mfpow-post-analyzer-test.php
```

## 📋 学習のポイント

### ✅ **WordPressテストで学べること**

1. **実際のデータベース操作**: `update_post_meta()`, `get_post_meta()`
2. **WordPressファクトリー**: テストデータの効率的な作成
3. **統合テストの価値**: 個別機能ではなく全体の動作確認
4. **フックシステム**: `save_post`などのアクションフックとの連携
5. **エラーハンドリング**: WordPressでの適切な例外処理

### ✅ **一般的なPHPUnitテストとの違い**

| 項目 | 一般的なPHPUnitテスト | WordPressテスト |
|------|----------------------|-----------------|
| データ作成 | モックやスタブ | WordPressファクトリー |
| データベース | インメモリDB/モック | 実際のWordPressデータベース |
| 検証対象 | 戻り値のみ | データベースの状態変化 |
| テスト分離 | 手動クリーンアップ | WordPressが自動処理 |

### ✅ **避けるべきテスト内容**

- **ブラウザ操作** → Playwright/Selenium
- **REST API** → Jest/Postman
- **JavaScript** → Jest
- **UI/UX** → Cypress/Playwright

## 🚀 次のステップ

1. **実際にテストを実行**してみる
2. **新しいテストケース**を追加してみる
3. **他のWordPress機能**（タクソノミー、ユーザー管理など）のテストを実装してみる
4. **カスタム投稿タイプ**での動作をテストしてみる

## 💡 実践演習

以下の機能を実装してテストを書いてみましょう：

1. **投稿カテゴリー数カウンター**: 投稿に付与されたカテゴリー数をカスタムフィールドに保存
2. **関連記事の自動リンク**: 同じカテゴリーの他の記事へのリンクを本文末尾に自動追加
3. **投稿読了時間計算**: 文字数から読了時間を計算してメタデータとして保存

これらの機能を実装することで、WordPressデータベース操作テストのより深い理解が得られます。

---

**🎓 学習効果**

このガイドを通じて、WordPressにおける統合テストの重要性と、実際のデータベース操作をテストする方法を習得できます。これは実際の WordPress プロジェクトで高品質なコードを書くための重要なスキルです。
