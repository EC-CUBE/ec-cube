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

namespace Eccube\Stream\Filter;

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 suin
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @see https://github.com/suin/php-playground/blob/master/ReadingSjisCsvWithStreamFilter/SjisToUtf8EncodingFilter.php
 */
final class SjisToUtf8EncodingFilter extends \php_user_filter
{
    /**
     * Buffer size limit (bytes)
     *
     * @var int
     */
    private static $bufferSizeLimit = 1024;

    /**
     * @var string
     */
    private $buffer = '';

    public static function setBufferSizeLimit(int $bufferSizeLimit): void
    {
        self::$bufferSizeLimit = $bufferSizeLimit;
    }

    /**
     * @param resource $in
     * @param resource $out
     * @param int $consumed
     * @param bool $closing
     */
    public function filter($in, $out, &$consumed, $closing): int
    {
        $isBucketAppended = false;
        $previousData = $this->buffer;
        $deferredData = '';

        while ($bucket = \stream_bucket_make_writeable($in)) {
            $data = $previousData.$bucket->data;
            $consumed += $bucket->datalen;

            while ($this->needsToNarrowEncodingDataScope($data)) {
                $deferredData = \substr($data, -1).$deferredData;
                $data = \substr($data, 0, -1);
            }

            if ($data) {
                $bucket->data = $this->encode($data);
                \stream_bucket_append($out, $bucket);
                $isBucketAppended = true;
            }
        }

        $this->buffer = $deferredData;
        $this->assertBufferSizeIsSmallEnough();

        return $isBucketAppended ? \PSFS_PASS_ON : \PSFS_FEED_ME;
    }

    private function needsToNarrowEncodingDataScope(string $string): bool
    {
        return !($string === '' || $this->isValidEncoding($string));
    }

    private function isValidEncoding(string $string): bool
    {
        return \mb_check_encoding($string, 'SJIS-win');
    }

    private function encode(string $string): string
    {
        return \mb_convert_encoding($string, 'UTF-8', 'SJIS-win');
    }

    private function assertBufferSizeIsSmallEnough(): void
    {
        \assert(
            \strlen($this->buffer) <= self::$bufferSizeLimit,
            \sprintf(
                'Streaming buffer size must less than or equal to %u bytes, but %u bytes allocated',
                self::$bufferSizeLimit,
                \strlen($this->buffer)
            )
        );
    }
}
