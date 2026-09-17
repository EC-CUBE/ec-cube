<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Stream\Filter;

use Eccube\Stream\Filter\SjisToUtf8EncodingFilter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SjisToUtf8EncodingFilterTest extends TestCase
{
    private const FILTER_NAME = 'sjis_to_utf8_encoding_filter';

    protected function setUp(): void
    {
        \stream_filter_register(
            self::FILTER_NAME,
            SjisToUtf8EncodingFilter::class
        );
        SjisToUtf8EncodingFilter::setBufferSizeLimit(1024);
    }

    #[Test]
    public function encodeSmallData(): void
    {
        $utf8Value = 'あ,い,う';
        $sjisValue = $this->getSjisValue($utf8Value);
        $resource = $this->createReadableResource($sjisValue);
        $this->assertSame(['あ', 'い', 'う'], \fgetcsv($resource, escape: '\\'));
    }

    #[Test]
    public function encodeBigDataThatExceedsStreamChunkSize(): void
    {
        $utf8Value = 'かきくけこ,さしすせそ';
        $sjisValue = $this->getSjisValue($utf8Value);
        $resource = $this->createReadableResource($sjisValue);
        $this->changeStreamChunkSize($resource, 5);
        // SJIS string will be separated into 5 chunks like following:
        //  1 2 3 4 5   1 2 3 4 5   1 2 3 4 5   1 2 3 4 5   1 2 3 4 5
        // [k a k i k] [u k e k o] [, s a s i] [s u s e s] [o        ]
        $this->assertSame(['かきくけこ', 'さしすせそ'], \fgetcsv($resource, escape: '\\'));
    }

    #[Test]
    public function fgetcsvDoesntOccur5cProblem(): void
    {
        $utf8Value = '"表"';
        $sjisValue = $this->getSjisValue($utf8Value);
        $this->assertSame('22 95 5c 22 ', \chunk_split(\bin2hex($sjisValue), 2, ' '));
        $resource = $this->createReadableResource($sjisValue);
        // $escape は現行の既定値 '\\' を明示する（PHP 8.4 で明示指定が必須）。
        // このテストは SJIS の 2 バイト目 0x5c を escape 文字として誤認しないことの確認なので、
        // 既定値を変えずに明示することが重要
        $this->assertSame(['表'], \fgetcsv($resource, escape: '\\'));
    }

    #[Test]
    public function bufferSizeShouldNotBeTooLarge(): void
    {
        SjisToUtf8EncodingFilter::setBufferSizeLimit(1);
        $utf8Value = 'あ あ あ あ '; // 82 a0 20 * 4 (12 bytes)
        $sjisValue = $this->getSjisValue($utf8Value);
        $this->assertSame(12, \strlen($sjisValue));
        $resource = $this->createReadableResource($sjisValue);
        $this->changeStreamChunkSize($resource, 2);
        // 82 a0 / 20   82 / a0 20 / 82 a0 / 20   82 / a0 20 (chunked data)
        //       /      82 /       /       /      82 /       (buffered content)
        // 82 a0 / 20 / 82   a0 20 / 82 a0 / 20 / 82 a0 20   (encoding unit)
        $this->assertSame([$utf8Value], \fgetcsv($resource, escape: '\\'));
    }

    private function getSjisValue(string $utf8Value): string
    {
        return \mb_convert_encoding($utf8Value, 'SJIS-win', 'UTF-8');
    }

    /**
     * @return resource
     */
    private function createReadableResource(string $content)
    {
        $fp = \tmpfile();
        \fwrite($fp, $content);
        \rewind($fp);
        /* @noinspection UnusedFunctionResultInspection */
        \stream_filter_append($fp, self::FILTER_NAME);

        return $fp;
    }

    /**
     * @param resource $resource
     */
    private function changeStreamChunkSize($resource, int $chunkSize): void
    {
        $this->assertIsResource($resource);
        \stream_set_chunk_size($resource, $chunkSize);
        \stream_set_read_buffer($resource, $chunkSize);
    }
}
