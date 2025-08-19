# WordPressユニットテスト 学習計画

## 概要
このリポジトリは、ジュニアエンジニアがWordPressでユニットテストを実践的に学べる教材として設計されています。

## 学習目標
- WordPressプラグイン開発におけるユニットテストの重要性を理解する
- PHPUnitとWordPressテスト環境のセットアップができる
- 基本的なテストケースの書き方を習得する
- WordPressの機能（投稿、メタデータ、フック等）をテストできるようになる

## ステップバイステップ学習計画

### Step 1: 環境理解と基礎知識
**目標**: WordPressユニットテストの全体像を把握する

**学習内容**:
1. **必要なツールとファイル構成**
   - `@wordpress/env`: ローカルWordPress環境の構築
   - `phpunit.xml.dist`: PHPUnitの設定ファイル
   - `tests/bootstrap.php`: テスト環境の初期化
   - `package.json`: 環境構築とテスト実行のスクリプト

2. **WordPressテスト環境の特徴**
   - `WP_UnitTestCase`クラスの継承
   - WordPressの標準機能（投稿、ユーザー、設定等）へのアクセス
   - テスト用データベースの自動リセット機能

### Step 2: 環境セットアップ
**目標**: 実際にテスト環境を構築して動かす

**実習内容**:
1. 依存関係のインストール
   ```bash
   npm install
   ```

2. WordPress環境の起動
   ```bash
   npm run start
   ```

3. 基本テストの実行
   ```bash
   npm run test
   ```

### Step 3: 簡単なプラグイン作成
**目標**: テスト対象となるシンプルなプラグインを作成する

**学習内容**:
1. **基本的なプラグイン構造**
   - プラグインヘッダーの記述
   - 基本的な関数の定義
   - WordPressフックの利用

2. **実装例**:
   - 投稿の期限切れ機能
   - カスタムメタフィールドの管理
   - 基本的なユーティリティ関数

### Step 4: 基本テストケース作成
**目標**: WordPressの標準機能を使った基本的なテストを書く

**学習内容**:
1. **テストクラスの基本構造**
   ```php
   class Basic_Test extends WP_UnitTestCase {
       public function test_example() {
           // テストロジック
       }
   }
   ```

2. **よく使うアサーション**
   - `$this->assertEquals()`
   - `$this->assertTrue()`
   - `$this->assertEmpty()`
   - `$this->assertWPError()`

3. **WordPress固有のテストパターン**
   - 投稿の作成とテスト
   - メタデータの操作テスト
   - フックとアクションのテスト

### Step 5: 高度なテストパターン
**目標**: より実践的なテストケースを作成する

**学習内容**:
1. **モックとスタブの活用**
   - 外部APIの呼び出しテスト
   - 時間に依存する処理のテスト

2. **統合テスト**
   - 複数の機能を組み合わせたテスト
   - データベースの状態変化のテスト

3. **エラーハンドリングのテスト**
   - 例外処理のテスト
   - WP_Errorのテスト

### Step 6: CI/CDの導入
**目標**: 自動テストの実行環境を構築する

**学習内容**:
1. GitHub Actionsでの自動テスト
2. 複数のPHP/WordPressバージョンでのテスト
3. コードカバレッジの測定

## 実践的な演習課題

### 初級課題
1. 「Hello World」機能のテスト
2. 設定値の保存/取得機能のテスト
3. ショートコード機能のテスト

### 中級課題
1. カスタム投稿タイプ機能のテスト
2. Ajax処理のテスト
3. 権限チェック機能のテスト

### 上級課題
1. 複雑なクエリ処理のテスト
2. キャッシュ機能のテスト
3. マルチサイト対応のテスト

## リソース

### 公式ドキュメント
- [WordPress Plugin Handbook - Unit Tests](https://developer.wordpress.org/plugins/plugin-basics/unit-tests/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

### 参考資料
- WordPress Core Tests: https://github.com/WordPress/wordpress-develop
- WP-CLI Test Suite: https://wp-cli.org/docs/plugin-unit-tests/

## 注意点とベストプラクティス
1. **テストの独立性**: 各テストは他のテストに依存しない
2. **データのクリーンアップ**: テスト用データは自動的にリセットされる
3. **実際のWordPress環境との分離**: テストは専用環境で実行
4. **命名規則**: テストメソッドは`test_`で始める
5. **アサーションの適切な使用**: 期待する結果を明確に定義

## まとめ
このリポジトリを通じて、WordPressプラグイン開発におけるユニットテストの重要性と実践方法を段階的に学ぶことができます。実際のプラグイン開発においても、テストファーストの開発手法を適用することで、より安定で保守性の高いコードを書けるようになります。
