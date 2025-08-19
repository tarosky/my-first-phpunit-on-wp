# WordPressにおけるテストカバレッジガイド

このガイドでは、WordPressプラグイン開発におけるテストカバレッジの概念と実践的な使用方法を学習できます。

## 目次
1. [テストカバレッジとは](#テストカバレッジとは)
2. [カバレッジの種類](#カバレッジの種類)
3. [実践的な使用方法](#実践的な使用方法)
4. [カバレッジレポートの読み方](#カバレッジレポートの読み方)
5. [よくある誤解と注意点](#よくある誤解と注意点)
6. [WordPress特有の考慮事項](#wordpress特有の考慮事項)

## テストカバレッジとは

**テストカバレッジ（Test Coverage）** は、テストがコードのどの部分を実行したかを測定する指標です。

### 基本概念
- **実行されたコード行数 ÷ 全コード行数 × 100 = カバレッジ率**
- 例：100行のコードで80行がテストで実行された場合、カバレッジ率は80%

### なぜ重要か？
1. **テストの網羅性の確認**: テストされていないコードを発見
2. **品質の可視化**: コードの信頼性を数値で把握
3. **リファクタリングの安全性**: 変更時の回帰テスト対象の確認

## カバレッジの種類

### 1. **ライン（行）カバレッジ**
最も一般的な指標。実行された行の割合を測定。

```php
function example_function($input) {
    if ($input > 0) {        // ← この行がテストで実行されたか？
        return $input * 2;   // ← この行がテストで実行されたか？
    }
    return 0;                // ← この行がテストで実行されたか？
}
```

### 2. **ブランチカバレッジ**
条件分岐の全パターンが実行されたかを測定。

```php
if ($input > 0) {
    // 条件が true の場合
} else {
    // 条件が false の場合  ← 両方のパターンがテストされているか？
}
```

## 実践的な使用方法

### 基本コマンド

```bash
# 1. テキスト形式でカバレッジ確認（手軽）
npm run coverage:text

# 2. HTML形式でカバレッジレポート生成（詳細）
npm run coverage

# 3. XML形式でカバレッジレポート生成（CI/CD用）
npm run coverage:xml
```

### 初回実行の流れ

```bash
# 1. WordPress環境を起動
npm start

# 2. 通常のテストを実行して動作確認
npm test

# 3. カバレッジを確認
npm run coverage:text
```

## カバレッジレポートの読み方

### テキスト出力例
```
Code Coverage Report:      
  2023-08-19 22:00:00      
                           
 Summary:                  
  Classes: 100.00% (3/3)   
  Methods:  85.71% (12/14) 
  Lines:    89.47% (68/76) 

...

MyFirstPHPUnitOnWP\Calculator
  Methods:  91.67% ( 11/ 12)   Lines:  95.24% ( 40/ 42)
```

### 読み方のポイント
- **Classes**: クラス全体のカバレッジ
- **Methods**: メソッドのカバレッジ
- **Lines**: 最も重要な指標

### HTMLレポートの活用
`npm run coverage` 実行後、**ローカルファイルシステム**の `coverage/index.html` をブラウザで開きます：

**カバレッジレポートが実際に生成されます！**

**実際のカバレッジレポート確認方法:**
- ✅ `npm run coverage:text` - ターミナルでの簡易表示
- ✅ `npm run coverage` - HTMLレポート生成 (`./coverage/html/index.html`)
- ✅ `npm run coverage:xml` - XML形式（CI/CD用）

```bash
# テキスト形式でカバレッジ確認（推奨）
npm run coverage:text

# 実行結果例：
# Code Coverage Report:
#  Summary:
#   Classes: 50.00% (1/2)
#   Methods: 81.82% (9/11)  
#   Lines:   63.16% (60/95)

# HTMLレポート生成
npm run coverage
# → ./coverage/html/index.html が生成される
```

**生成されるファイル:**
- `./coverage/html/index.html` - 詳細なHTMLレポート
- `./coverage/clover.xml` - XML形式レポート

**仕組み:**
wp-env の `--xdebug` フラグと `XDEBUG_MODE=coverage` 環境変数により動作

HTMLレポートの機能：
1. **ファイル別カバレッジ一覧**: どのファイルのカバレッジが低いかがわかる
2. **行別表示**: 実行されていない行が赤色でハイライト
3. **インタラクティブ表示**: クリックで詳細確認

## よくある誤解と注意点

### ❌ 間違った考え方
- **「100%カバレッジが絶対の目標」**
- **「カバレッジが高い = 良いテスト」**
- **「カバレッジだけで品質が判断できる」**

### ✅ 正しい考え方
- **カバレッジは品質の指標の一つに過ぎない**
- **意味のあるテストを書くことが最重要**
- **80-90%程度のカバレッジで十分な場合が多い**

### 実例：過度なカバレッジ追求の問題

```php
// この行をカバレッジ100%にするためだけのテスト
public function test_meaningless_coverage() {
    // 実際のビジネスロジックとは関係ない無意味なテスト
    $result = some_function('dummy');
    $this->assertNotNull($result); // 意味のないアサーション
}
```

### 推奨する実践方法

```php
// 意味のあるテストケース
public function test_business_logic_validation() {
    // 実際の使用シナリオをテスト
    $post_id = $this->factory->post->create([
        'post_title' => 'WordPress記事のタイトル'
    ]);
    
    $count = mfpow_count_wordpress_keywords($post_id);
    
    // ビジネスロジックを検証
    $this->assertEquals(1, $count);
    $this->assertEquals(1, how_many_wordpress($post_id));
}
```

## WordPress特有の考慮事項

### 1. **WordPress コア機能の除外**
WordPressコア関数（`get_post()`, `update_option()`など）はカバレッジ対象外

### 2. **統合テストでのカバレッジ**
```php
// WordPressの統合テストでは、内部的に多くのコードが実行される
public function test_save_post_integration() {
    $post_id = $this->factory->post->create(); // ← 内部で多くのコードが実行
    // このテストは見た目以上に多くのコードをカバーしている
}
```

### 3. **フック・フィルターでのカバレッジ**
```php
// フィルターの登録
add_filter('bloginfo', 'mfpow_modify_blogname_filter', 10, 2);

// テストで実際にフィルターを呼び出すことが重要
get_bloginfo('name', 'display'); // ← フィルター関数内のコードを実行
```

## 実際のプロジェクトでの使用例

### Step 1: 現在のカバレッジを確認
```bash
npm run coverage:text
```

### Step 2: カバレッジの低いファイルを特定
```bash
npm run coverage  # HTMLレポートで詳細確認
```

### Step 3: 必要なテストケースを追加
重要なビジネスロジックで未テストの部分にテストを追加

### Step 4: 継続的な監視
定期的にカバレッジを確認して品質を維持

## カバレッジ指標の目安

### プロジェクト別推奨カバレッジ
- **学習プロジェクト**: 70-80%（学習が目的）
- **小規模プラグイン**: 80-90%（品質重視）
- **大規模プロジェクト**: 75-85%（実用性重視）

### ファイル別優先度
1. **ビジネスロジック**: 高カバレッジを目指す（90%+）
2. **ユーティリティ関数**: 中程度カバレッジ（80%+）
3. **設定・初期化コード**: 低カバレッジでも許容（60%+）

## 実践演習

### 演習1: 現在のカバレッジ確認
```bash
# 現在のプロジェクトのカバレッジを確認してみましょう
npm run coverage:text
```

期待される結果：
- Calculator クラス: 高カバレッジ（多くのテストがある）
- hooks.php: 中程度カバレッジ（統合テストでカバー）
- post-analyzer.php: 高カバレッジ（包括的なテスト）

### 演習2: HTMLレポートでの詳細分析
```bash
# 詳細なレポートを生成
npm run coverage

# coverage/index.html をブラウザで開いて確認
```

確認ポイント：
- どのファイルのカバレッジが低いか？
- 実行されていない行は重要なロジックか？
- テストを追加すべき箇所はどこか？

### 演習3: カバレッジ改善の実践
1. HTMLレポートで未実行の行を特定
2. その行が重要なビジネスロジックかを判断
3. 必要であれば新しいテストケースを追加
4. 再度カバレッジを測定して改善を確認

## 高度なカバレッジ活用

### CI/CDでのカバレッジ監視
```bash
# XML形式でカバレッジを生成（CI/CD用）
npm run coverage:xml
```

### カバレッジしきい値の設定
```xml
<!-- phpunit.xml.dist での設定例 -->
<coverage>
    <report>
        <html outputDirectory="coverage/html"/>
        <text outputFile="coverage/coverage.txt"/>
    </report>
</coverage>
```

## まとめ

テストカバレッジは**品質向上のツール**であり、**目標ではありません**。

### 重要なポイント
1. **意味のあるテスト** > **高いカバレッジ**
2. **ビジネスロジックの検証** > **全行の実行**
3. **継続的な改善** > **一度だけの測定**

### ジュニアエンジニアへのアドバイス
- まず動作するテストを書く
- カバレッジは後から確認する
- 数値に振り回されず、コードの品質向上を目指す

カバレッジを活用して、より良いWordPressプラグインを開発していきましょう！
