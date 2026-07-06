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

namespace Eccube\Tests\Rector\CodingStyle;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * Rector を実行するため rector 同梱の nikic/php-parser を読み込む。
 * pcov 有効のカバレッジ実行では本体の nikic/php-parser と衝突して
 * "Cannot redeclare class PhpParser\Node\Scalar\Float_" で fatal になるため、
 * カバレッジ実行から除外できるよう rector グループを付与する
 * (coverage.yml で --exclude-group rector)。
 */
#[Group('rector')]
final class AttributeArgumentsOrderRectorTest extends AbstractRectorTestCase
{
    #[DataProvider(methodName: 'provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
