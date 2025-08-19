# My First PHPUnit on WordPress

WordPressでユニットテストを学ぶためのサンプルプラグインです。

## 概要

このリポジトリは、ジュニアエンジニアがWordPressプラグイン開発におけるユニットテストを段階的に学習できるよう設計されています。

## クイックスタート

```bash
# リポジトリをクローン
git clone https://github.com/tarosky/my-first-phpunit-on-wp.git
cd my-first-phpunit-on-wp

# 依存関係をインストール
composer install
npm install

# WordPress環境を起動
npm run start

# テストを実行
npm test
```

## 学習の流れ

### Step 1: 基本的な関数テスト
- `mfpow_hello_world()` - Hello Worldメッセージ
- `mfpow_double_number()` - 数値を2倍にする
- `mfpow_count_array()` - 配列の要素数を返す

### Step 2: クラスベースのテスト
- **パブリックメソッドのテスト**: Calculator クラスの基本演算
- **プライベートメソッドの扱い**: テストできない理由と対処法
- **抽象クラスのテスト**: AbstractProcessor の具象実装を通じたテスト
- **例外処理のテスト**: InvalidArgumentException の適切な検証

### Step 3以降: WordPress特有の機能
今後追加予定：投稿操作、フック、データベース処理など

## ファイル構成と学習資料

```
my-first-phpunit-on-wp/
├── src/                           # PSR-4 クラス群
│   ├── Calculator.php             # 具体クラス例
│   └── AbstractProcessor.php      # 抽象クラス例
├── tests/
│   ├── src/
│   │   └── TestProcessor.php      # 抽象クラステスト用具象実装
│   ├── mfpow-basic-test.php       # 基本関数テスト
│   ├── mfpow-calculator-test.php  # クラステスト例
│   └── mfpow-processor-test.php   # 抽象クラステスト例
├── includes/
│   └── functions.php              # 基本関数群
└── 詳細ドキュメント/
    ├── LEARNING_PLAN.md           # 体系的な学習計画
    ├── WORDPRESS_UNIT_TESTING_BASICS.md # 基本概念の詳細解説
    └── STEP_BY_STEP_GUIDE.md      # 実践的なコーディング演習
```

## 現在のテスト例

### 基本関数テスト
```bash
# 3つの基本関数をテスト（13アサーション）
npm test
```

### クラステスト
- **Calculator**: 算数演算、例外処理、リフレクションAPI
- **AbstractProcessor**: 抽象クラスの具象実装を通じたテスト

## コマンド

```bash
# WordPress環境
npm start                  # 環境起動
npm stop                   # 環境停止

# テスト実行
npm test                       # 全テスト実行
npm run coverage:text          # テキスト形式カバレッジ
npm run coverage               # HTML形式カバレッジレポート
npm run coverage:xml           # XML形式カバレッジ（CI/CD用）

# 従来のComposer方式
composer test                  # PHPUnit直接実行
composer run-script test-coverage # カバレッジ付きテスト
```

### テストカバレッジについて

テストカバレッジを使用するには**Xdebug**が必要です：

```bash
# カバレッジの確認（要Xdebug）
npm run coverage:text
```

**カバレッジ機能が実際に動作します！**

```bash
# テキスト形式でカバレッジ確認（推奨）
npm run coverage:text

# 実行結果例：
# Code Coverage Report:
#  Summary:
#   Classes: 50.00% (1/2)
#   Methods: 81.82% (9/11)
#   Lines:   63.16% (60/95)
```

**カバレッジ機能について:**
- ✅ 完全に動作（wp-env --xdebug + XDEBUG_MODE=coverage）
- ✅ HTMLレポート生成: `./coverage/html/index.html`
- ✅ XML形式レポート: `./coverage/clover.xml`

**コマンド一覧:**
- `npm run coverage:text` - ターミナルでの簡易表示
- `npm run coverage` - 詳細HTMLレポート生成
- `npm run coverage:xml` - CI/CD用XML形式

詳細は **[テストカバレッジガイド](WORDPRESS_TEST_COVERAGE_GUIDE.md)** をご覧ください。

## 学習のポイント

### ✅ 学習済み
- 基本関数のユニットテスト
- クラスのテスト方法
- プライベートメソッドが直接テストできないこと
- 抽象クラスのテスト戦略
- 例外処理のテスト

### 🚀 今後の学習予定
- WordPress投稿・メタデータ操作
- フック・フィルターのテスト
- データベース操作とファクトリー
- 統合テスト・パフォーマンステスト

## 必要な環境

- PHP 8.2以上
- Composer
- Node.js & npm
- WordPress環境（wp-envで自動構築）

## 詳細情報

各トピックの詳細は以下のドキュメントを参照してください：

- **[学習計画](LEARNING_PLAN.md)**: 段階的な学習ロードマップ
- **[基本概念](WORDPRESS_UNIT_TESTING_BASICS.md)**: WordPressユニットテストの基礎
- **[実践ガイド](STEP_BY_STEP_GUIDE.md)**: ハンズオン形式の演習

## ライセンス

GPL-3.0-or-later - 詳細は[LICENSE](LICENSE)ファイルを参照

## サポート

- バグ報告・質問: [Issues](https://github.com/tarosky/my-first-phpunit-on-wp/issues)
- 学習相談: [Discussions](https://github.com/tarosky/my-first-phpunit-on-wp/discussions)
