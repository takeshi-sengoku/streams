<?php
/**    _______       _______
 *    / ____/ |     / /__  /
 *   / /_   | | /| / / /_ <
 *  / __/   | |/ |/ /___/ /
 * /_/      |__/|__//____/
 *
 * Flywheel3: the inertia php framework
 *
 * @category    Flywheel3
 * @package     streams
 * @author      wakaba <wakabadou@gmail.com>
 * @copyright   Copyright (c) @2019  Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/). All rights reserved.
 * @license     http://opensource.org/licenses/MIT The MIT License.
 *              This software is released under the MIT License.
 * @varsion     1.0.0
 */

declare(strict_types=1);

namespace Tests\streams\filters;

use PHPUnit\Framework\TestCase;
use fw3\streams\filters\ConvertLienFeedFilter;
use fw3\tests\streams\traits\StreamFilterTestTrait;

/**
 * 行末の改行コードを変換するストリームフィルタクラスのテスト
 */
class ConvertLienFeedFilterTest extends TestCase
{
    use StreamFilterTestTrait;

    /**
     * @var string  テストデータ：空文字
     */
    protected const TEST_DATA_EMPTY       = '';

    /**
     * @var string  テストデータ：パターン1：CR
     */
    protected const TEST_DATA_ONLY_CR1    = "\r";

    /**
     * @var string  テストデータ：パターン1：LF
     */
    protected const TEST_DATA_ONLY_LF1    = "\n";

    /**
     * @var string  テストデータ：パターン1：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF1  = "\r\n";

    /**
     * @var string  テストデータ：パターン2：CR
     */
    protected const TEST_DATA_ONLY_CR2    = "\r\r";

    /**
     * @var string  テストデータ：パターン2：LF
     */
    protected const TEST_DATA_ONLY_LF2    = "\n\n";

    /**
     * @var string  テストデータ：パターン2：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF2  = "\r\n\r\n";

    /**
     * @var string  テストデータ：パターン3：CR
     */
    protected const TEST_DATA_ONLY_CR3    = "\r\r\r";

    /**
     * @var string  テストデータ：パターン3：LF
     */
    protected const TEST_DATA_ONLY_LF3    = "\n\n\n";

    /**
     * @var string  テストデータ：パターン3：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF3  = "\r\n\r\n\r\n";

    /**
     * @var string  テストデータ：パターン4：CR
     */
    protected const TEST_DATA_ONLY_CR4    = "\r\r\r\r";

    /**
     * @var string  テストデータ：パターン4：LF
     */
    protected const TEST_DATA_ONLY_LF4    = "\n\n\n\n";

    /**
     * @var string  テストデータ：パターン4：CRLF
     */
    protected const TEST_DATA_ONLY_CRLF4  = "\r\n\r\n\r\n\r\n";

    /**
     * @var string  テストデータ：複雑な組み合わせ1
     */
    protected const TEST_DATA_COMPLEX1    = "\r\n\n\r";

    /**
     * @var string  テストデータ：複雑な組み合わせ2
     */
    protected const TEST_DATA_COMPLEX2    = "\n\r\r\n";

    /**
     * @var string  テストデータ：複雑な組み合わせ3
     */
    protected const TEST_DATA_COMPLEX3    = "\r\r\n\n";

    /**
     * @var string  テストデータ：複雑な組み合わせ4
     */
    protected const TEST_DATA_COMPLEX4    = "\n\n\r\r";

    /**
     * @var string  テストデータ：複雑な組み合わせ5
     */
    protected const TEST_DATA_COMPLEX5    = "\n\r";

    /**
     * Setup
     */
    protected function setUp(): void
    {
        \stream_filter_register('line_feed.*', ConvertLienFeedFilter::class);
    }

    /**
     * フィルタ名テスト
     */
    public function testFilterName() : void
    {
        $stream_wrapper = ['write' => 'line_feed.lf:cr'];

        \stream_filter_register('aaa.*', ConvertLienFeedFilter::class);
        $this->assertWriteStreamFilterSame("\r\n\n", "\r\n\r", $stream_wrapper);

        \stream_filter_register('aaa.bbb.ccc*', ConvertLienFeedFilter::class);
        $this->assertWriteStreamFilterSame("\r\n\n", "\r\n\r", $stream_wrapper);
    }

    /**
     * 例外テスト
     */
    public function testException() : void
    {
        try {
            $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, ['write' => 'line_feed.lf:lf']);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換前後の改行コード指定が同じです。to_linefeed:LF, from_linefeed:LF', $e->getMessage());
        }

        try {
            $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, ['write' => 'line_feed.aaa:lf']);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換先の改行コード指定が無効です。to_linefeed:aaa', $e->getMessage());
        }

        try {
            $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, ['write' => 'line_feed.cr:aaa']);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('変換元の改行コード指定が無効です。from_linefeed:aaa', $e->getMessage());
        }
    }

    /**
     * LFへの変換テスト
     */
    public function testConvert2Lf() : void
    {
        $stream_wrapper = ['write' => 'line_feed.lf:cr'];
        $this->assertWriteStreamFilterSame("\r\n\n", "\r\n\r", $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.lf:cr'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.lf:crlf'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.lf:all'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.lf'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_LF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);
    }

    /**
     * CRへの変換テスト
     */
    public function testConvert2Cr() : void
    {
        $stream_wrapper = ['write' => 'line_feed.cr:lf'];
        $this->assertWriteStreamFilterSame("\n\r\r", "\n\r\n", $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.cr:lf'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.cr:crlf'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.cr:all'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.cr'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CR4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);
    }

    /**
     * CRLFへの変換テスト
     */
    public function testConvert2CrLf() : void
    {
        $stream_wrapper = ['write' => 'line_feed.crlf:cr'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.crlf:lf'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.crlf:all'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);

        $stream_wrapper = ['write' => 'line_feed.crlf'];
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CR1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CR2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CR3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CR4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_LF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_LF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_LF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_LF4, $stream_wrapper);

        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF1, static::TEST_DATA_ONLY_CRLF1, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF2, static::TEST_DATA_ONLY_CRLF2, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF3, static::TEST_DATA_ONLY_CRLF3, $stream_wrapper);
        $this->assertWriteStreamFilterSame(static::TEST_DATA_ONLY_CRLF4, static::TEST_DATA_ONLY_CRLF4, $stream_wrapper);
    }
}