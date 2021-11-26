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

namespace Eccube\Tests\Util;

use Eccube\Util\FilesystemUtil;
use PHPUnit\Framework\TestCase;

class FilesystemUtilTest extends TestCase
{
    public function testSizeToHumanReadable()
    {
        $asserts = [
            '435180018' => '415 MB',
            '55548818' => '53 MB',
            '778377' => '760 KB',
            '100' => '100 B',
            '88905297099' => '83 GB',
        ];

        foreach ($asserts as $assert => $expected) {
            $this->assertEquals($expected, FilesystemUtil::sizeToHumanReadable($assert));
        }
    }
}
