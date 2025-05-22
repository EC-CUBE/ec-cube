<?php

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
use PHPUnit\Framework\TestCase;

class SjisToUtf8EncodingFilterTest extends TestCase
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

    /**
     * @test
     */
    public function encode_small_data(): void
    {
        $utf8Value = 'あ,い,う';
        $sjisValue = $this->getSjisValue($utf8Value);
        $resource = $this->createReadableResource($sjisValue);
        self::assertSame(['あ', 'い', 'う'], \fgetcsv($resource));
    }

    /**
     * @test
     */
    public function encode_big_data_that_exceeds_stream_chunk_size(): void
    {
        $utf8Value = 'かきくけこ,さしすせそ';
        $sjisValue = $this->getSjisValue($utf8Value);
        $resource = $this->createReadableResource($sjisValue);
        $this->changeStreamChunkSize($resource, 5);
        // SJIS string will be separated into 5 chunks like following:
        //  1 2 3 4 5   1 2 3 4 5   1 2 3 4 5   1 2 3 4 5   1 2 3 4 5
        // [k a k i k] [u k e k o] [, s a s i] [s u s e s] [o        ]
        self::assertSame(['かきくけこ', 'さしすせそ'], \fgetcsv($resource));
    }

    /**
     * @test
     */
    public function fgetcsv_doesnt_occur_5c_problem(): void
    {
        $utf8Value = '"表"';
        $sjisValue = $this->getSjisValue($utf8Value);
        self::assertSame(
            '22 95 5c 22 ',
            \chunk_split(\bin2hex($sjisValue), 2, ' ')
        );
        $resource = $this->createReadableResource($sjisValue);
        self::assertSame(['表'], \fgetcsv($resource));
    }

    /**
     * @test
     */
    public function buffer_size_should_not_be_too_large(): void
    {
        SjisToUtf8EncodingFilter::setBufferSizeLimit(1);
        $utf8Value = 'あ あ あ あ '; // 82 a0 20 * 4 (12 bytes)
        $sjisValue = $this->getSjisValue($utf8Value);
        self::assertEquals(12, \strlen($sjisValue));
        $resource = $this->createReadableResource($sjisValue);
        $this->changeStreamChunkSize($resource, 2);
        // 82 a0 / 20   82 / a0 20 / 82 a0 / 20   82 / a0 20 (chunked data)
        //       /      82 /       /       /      82 /       (buffered content)
        // 82 a0 / 20 / 82   a0 20 / 82 a0 / 20 / 82 a0 20   (encoding unit)
        self::assertSame([$utf8Value], \fgetcsv($resource));
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
        /** @noinspection UnusedFunctionResultInspection */
        \stream_filter_append($fp, self::FILTER_NAME);
        return $fp;
    }

    /**
     * @param resource $resource
     */
    private function changeStreamChunkSize($resource, int $chunkSize): void
    {
        self::assertIsResource($resource);
        \stream_set_chunk_size($resource, $chunkSize);
        \stream_set_read_buffer($resource, $chunkSize);
    }
}
