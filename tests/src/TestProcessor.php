<?php
/**
 * 抽象クラステスト用のテストプロセッサ実装
 *
 * @package MyFirstPHPUnitOnWP\Tests
 */

namespace MyFirstPHPUnitOnWP\Tests;

use MyFirstPHPUnitOnWP\AbstractProcessor;

/**
 * AbstractProcessor のテスト用具象実装クラス
 */
class TestProcessor extends AbstractProcessor {

    /**
     * @var int 処理されたアイテム数
     */
    private $processedCount = 0;

    /**
     * @var array 処理されたアイテムのログ
     */
    private $processedItems = [];

    /**
     * データをサニタイズしてログを記録して処理する
     *
     * @param mixed $data 処理するデータ
     * @return string 処理されたデータ
     */
    public function process( $data ) {
        if ( ! $this->validate( $data ) ) {
            throw new \InvalidArgumentException( 'データが処理用として有効ではありません' );
        }

        $sanitized = $this->sanitize( (string) $data );
        $this->processedCount++;
        $this->processedItems[] = $sanitized;
        
        $this->log( "処理完了: {$sanitized}" );

        return $sanitized;
    }

    /**
     * プロセッサ名を取得する
     *
     * @return string プロセッサ名
     */
    public function getName() {
        return 'テストプロセッサ';
    }

    /**
     * 処理統計を取得する（親クラスをオーバーライド）
     *
     * @return array 処理統計を含む配列
     */
    public function getStats() {
        return [
            'processed_count' => $this->processedCount,
            'error_count' => 0,
            'last_processed' => end( $this->processedItems ) ?: null,
        ];
    }

    /**
     * protectedなsanitizeメソッドをテスト用に公開するpublicメソッド
     *
     * @param string $input サニタイズする入力
     * @return string サニタイズされた入力
     */
    public function testSanitize( $input ) {
        return $this->sanitize( $input );
    }

    /**
     * protectedなvalidateメソッドをテスト用に公開するpublicメソッド
     *
     * @param mixed $data 検証するデータ
     * @return bool 検証結果
     */
    public function testValidate( $data ) {
        return $this->validate( $data );
    }

    /**
     * protectedなlogメソッドをテスト用に公開するpublicメソッド
     *
     * @param string $message ログメッセージ
     * @param string $level ログレベル
     * @return bool ログ結果
     */
    public function testLog( $message, $level = 'info' ) {
        return $this->log( $message, $level );
    }
}
