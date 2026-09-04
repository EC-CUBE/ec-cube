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

namespace Eccube\Tests\Functions;

use PHPUnit\Framework\TestCase;

/**
 * ECCUBE_UMASK による umask の適用を検証する.
 */
final class ApplyUmaskTest extends TestCase
{
    private int $originalUmask;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalUmask = umask();
        unset($_ENV['ECCUBE_UMASK'], $_SERVER['ECCUBE_UMASK']);
        putenv('ECCUBE_UMASK');
    }

    protected function tearDown(): void
    {
        umask($this->originalUmask);
        unset($_ENV['ECCUBE_UMASK'], $_SERVER['ECCUBE_UMASK']);
        putenv('ECCUBE_UMASK');
        parent::tearDown();
    }

    public function testUnsetKeepsCurrentUmask(): void
    {
        umask(0022);

        apply_umask();

        $this->assertSame(0022, umask());
    }

    public function testEmptyValueKeepsCurrentUmask(): void
    {
        umask(0022);
        $_ENV['ECCUBE_UMASK'] = '';

        apply_umask();

        $this->assertSame(0022, umask());
    }

    /**
     * 互換のため 4.3 以前と同じ挙動 (ディレクトリ 0777 / ファイル 0666) に戻せること.
     */
    public function testZeroUmaskIsApplied(): void
    {
        umask(0022);
        $_ENV['ECCUBE_UMASK'] = '0000';

        apply_umask();

        $this->assertSame(0000, umask());
    }

    public function testOctalValueIsApplied(): void
    {
        umask(0000);
        $_ENV['ECCUBE_UMASK'] = '0027';

        apply_umask();

        $this->assertSame(0027, umask());
    }

    /**
     * env() は先頭に 0 の無い数値を int として返すため, その経路も 8 進数として解釈すること.
     */
    public function testValueWithoutLeadingZeroIsTreatedAsOctal(): void
    {
        umask(0000);
        $_ENV['ECCUBE_UMASK'] = '22';

        apply_umask();

        $this->assertSame(0022, umask());
    }

    public function testInvalidValueIsIgnored(): void
    {
        umask(0022);
        $_ENV['ECCUBE_UMASK'] = '0999';

        apply_umask();

        $this->assertSame(0022, umask());
    }
}
