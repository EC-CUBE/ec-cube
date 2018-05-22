<?php

namespace Eccube\Tests\Util;

use PHPUnit\Framework\TestCase;
use Eccube\Util\FilesystemUtil;

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
