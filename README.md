# My First PHPUnit on WordPress

WordPressでユニットテストを学ぶためのサンプルプラグインです。

## 概要

このリポジトリは、ジュニアエンジニアがWordPressプラグイン開発におけるユニットテストを実践的に学習できるよう設計されています。段階的な学習アプローチにより、基本的なテストから高度な統合テストまで習得できます。

## 特徴

- **段階的学習**: 5つのステップで基本から応用まで
- **実践的なコード**: 実際のプラグイン開発で使用する機能をテスト
- **WordPressに特化**: WP_UnitTestCase、ファクトリー、フックテスト
- **エラーハンドリング**: 堅牢なコードの書き方を学習
- **パフォーマンス意識**: 大量データでの動作確認

## 必要な環境

- PHP 8.0以上
- Composer
- MySQL/MariaDB
- Git
- SVN（WordPressテストスイート取得用）

## セットアップ

### 1. リポジトリのクローン

```bash
git clone https://github.com/tarosky/my-first-phpunit-on-wp.git
cd my-first-phpunit-on-wp
```

### 2. Composerでの依存関係インストール

```bash
composer install
```

### 3. WordPress環境でのテスト（@wordpress/env使用）

```bash
# 依存関係をインストール
npm install

# WordPress環境を起動
npm run start

# 初回のみ：wp-env環境内でComposer依存関係とWordPressテストライブラリをインストール
npm run test:setup

# テストを実行
npm run test

# より詳細な出力でテストを実行
npm run test:verbose
```

### 4. 従来方式でのテスト環境セットアップ

テスト用データベースとWordPressテストスイートをセットアップ：

```bash
# テスト環境をインストール（データベース名、ユーザー名、パスワードを指定）
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

# または、Composerスクリプトを使用
composer run-script install-wp-tests
```

### 5. テストの実行

```bash
# 全テストを実行
composer test
# または
vendor/bin/phpunit

# 特定のテストファイルのみ実行
vendor/bin/phpunit tests/test-basic.php

# カバレッジ付きでテスト実行
composer run-script test-coverage
```

## 学習の流れ

### Step 1: 基本的な関数テスト
- `mfpow_hello_world()` - Hello Worldメッセージ
- `mfpow_double_number()` - 数値を2倍にする
- `mfpow_count_array()` - 配列の要素数を返す

### Step 2: WordPress投稿機能テスト
- 投稿のCRUD操作
- カスタムメタフィールド
- 投稿データの検証

### Step 3: フック（アクション・フィルター）テスト
- `save_post`アクション
- タイトルフィルター
- カスタムアクション

### Step 4: エラーハンドリング
- WP_Errorクラスの使用
- 権限チェック
- 例外処理

### Step 5: 統合テスト・パフォーマンステスト
- 複数機能の組み合わせ
- 大量データ処理
- 完全ワークフロー

## ファイル構成

```
my-first-phpunit-on-wp/
├── my-first-phpunit-on-wp.php     # メインプラグインファイル
├── includes/
│   └── functions.php              # テスト対象の関数群
├── tests/
│   ├── bootstrap.php              # テスト環境の初期化
│   └── test-basic.php             # 基本テストケース
├── bin/
│   └── install-wp-tests.sh        # WordPress テスト環境セットアップ
├── composer.json                  # Composer設定
├── phpunit.xml.dist               # PHPUnit設定
├── package.json                   # npm設定（wp-env用）
├── .wp-env.json                   # WordPress環境設定
├── LEARNING_PLAN.md               # 学習計画
├── WORDPRESS_UNIT_TESTING_BASICS.md # 基本概念ガイド
└── STEP_BY_STEP_GUIDE.md          # ステップバイステップガイド
```

## テストの書き方

### 基本的なテストケース

```php
<?php
class My_Basic_Test extends WP_UnitTestCase {

    public function test_hello_world() {
        $result = mfpow_hello_world();
        $this->assertEquals( 'Hello, WordPress Unit Testing!', $result );
    }

    public function test_post_creation() {
        $post_id = $this->factory()->post->create([
            'post_title' => 'Test Post'
        ]);
        
        $this->assertGreaterThan( 0, $post_id );
        $this->assertEquals( 'Test Post', get_the_title( $post_id ) );
    }
}
```

### よく使用するアサーション

```php
// 基本的な比較
$this->assertEquals( $expected, $actual );
$this->assertTrue( $condition );
$this->assertFalse( $condition );

// WordPress固有
$this->assertWPError( $result );
$this->assertInstanceOf( 'WP_Post', get_post( $post_id ) );
```

## 学習リソース

- **[LEARNING_PLAN.md](LEARNING_PLAN.md)**: 体系的な学習計画
- **[WORDPRESS_UNIT_TESTING_BASICS.md](WORDPRESS_UNIT_TESTING_BASICS.md)**: 基本概念の詳細解説
- **[STEP_BY_STEP_GUIDE.md](STEP_BY_STEP_GUIDE.md)**: 実践的なコーディング演習

## トラブルシューティング

### よくある問題

#### 1. テストデータベース接続エラー
```bash
# データベース設定を確認
mysql -u root -p -e "SHOW DATABASES;"

# テスト環境を再セットアップ
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

#### 2. WordPress環境が起動しない
```bash
# wp-env環境を再構築
npm run stop
npm run start
```

#### 3. PHPUnitが見つからない
```bash
# Composerの依存関係を再インストール
composer install --no-dev
composer install
```

### デバッグ方法

```php
// テスト内でのデバッグ出力
public function test_debug_example() {
    $result = mfpow_hello_world();
    
    // 標準エラー出力にデバッグ情報を出力
    fwrite( STDERR, "Result: " . $result . "\n" );
    
    $this->assertEquals( 'Hello, WordPress Unit Testing!', $result );
}
```

## コマンド一覧

### Composer コマンド

```bash
# テスト環境のセットアップ
composer run-script install-wp-tests

# 全テストの実行
composer test

# カバレッジ付きテスト
composer run-script test-coverage
```

### npm コマンド

```bash
# WordPress環境の起動
npm run start

# WordPress環境の停止
npm run stop

# WordPress CLIの実行
npm run cli -- plugin list

# テストの実行
npm run test
```

### PHPUnit コマンド

```bash
# 全テストを実行
vendor/bin/phpunit

# 特定のテストクラスのみ
vendor/bin/phpunit tests/test-basic.php

# 特定のメソッドのみ
vendor/bin/phpunit --filter test_hello_world

# 詳細出力付き
vendor/bin/phpunit --verbose
```

## ベストプラクティス

1. **テストの独立性**: 各テストは他のテストに依存しない
2. **データのクリーンアップ**: テストデータは自動的にリセットされる
3. **適切なアサーション**: 具体的で意味のあるアサーションを使用
4. **テストの命名**: `test_should_return_expected_value_when_condition`
5. **setUp/tearDown**: 必要に応じてテスト前後の処理を実装

## 貢献

このプロジェクトへの貢献を歓迎します！

1. このリポジトリをフォーク
2. 機能ブランチを作成 (`git checkout -b feature/amazing-feature`)
3. 変更をコミット (`git commit -m 'Add some amazing feature'`)
4. ブランチにプッシュ (`git push origin feature/amazing-feature`)
5. Pull Requestを作成

## ライセンス

このプロジェクトはGPL-3.0ライセンスの下でライセンスされています。詳細は[LICENSE](LICENSE)ファイルを参照してください。

## サポート

- 質問やバグ報告は[Issues](https://github.com/tarosky/my-first-phpunit-on-wp/issues)で受け付けています
- 学習についての質問は[Discussions](https://github.com/tarosky/my-first-phpunit-on-wp/discussions)をご利用ください

## 参考リンク

- [WordPress Plugin Handbook - Unit Tests](https://developer.wordpress.org/plugins/plugin-basics/unit-tests/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WP-CLI Test Suite](https://wp-cli.org/docs/plugin-unit-tests/)
- [WordPress Core Tests](https://github.com/WordPress/wordpress-develop)
