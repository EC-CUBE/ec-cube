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

use Eccube\Stream\Filter\ConvertLineFeedFilter;
use PHPUnit\Framework\TestCase;

class ConvertLineFeedFilterTest extends TestCase
{
    private const FILTER_NAME = 'convert_linefeed_filter';

    protected function setUp(): void
    {
        \stream_filter_register(
            self::FILTER_NAME,
            ConvertLineFeedFilter::class
        );
    }

    public function testApplyFilter()
    {
        $data = "あいうえお\n"
            . "かきくけこ\r"
            . "さしすせそ\r\n";

        $fp = \tmpfile();
        \fwrite($fp, $data);
        \rewind($fp);
        \stream_filter_append($fp, self::FILTER_NAME);

        $actual = '';
        if ($fp !== false) {
            while (($line = fgets($fp)) !== false) {
                $actual .= $line;
            }
        }

        $expected = "あいうえお\n"
            . "かきくけこ\n"
            . "さしすせそ\n";
        self::assertSame($expected, $actual);
    }
}
