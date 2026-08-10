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

namespace Eccube\Tests\Doctrine\ORM\Query;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Eccube\Entity\Product;
use Eccube\Tests\EccubeTestCase;

final class NormalizeTest extends EccubeTestCase
{
    public function testGetSql()
    {
        $sql = $this->entityManager->createQueryBuilder()
            ->select('p.id')->from(Product::class, 'p')
            ->where('NORMALIZE(p.name) LIKE :name')
            ->getQuery()->getSql();
        $platform = $this->entityManager->getConnection()->getDatabasePlatform();
        if ($platform instanceof PostgreSQLPlatform) {
            $this->assertStringContainsString('LOWER(TRANSLATE(', (string) $sql);
            $this->assertStringContainsString('あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわをんがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょゎゐゑー', (string) $sql);
            $this->assertStringContainsString('アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンガギグゲゴザジズゼゾダヂヅデドバビブベボパピプペポァィゥェォッャュョヮヰヱー', (string) $sql);
        } elseif ($platform instanceof AbstractMySQLPlatform) {
            $this->assertStringContainsString('CONVERT(', (string) $sql);
            $this->assertStringContainsString('USING utf8) COLLATE utf8_unicode_ci', (string) $sql);
        } elseif ($platform instanceof SQLitePlatform) {
            $this->assertStringContainsString('LOWER(', (string) $sql);
        }
    }
}
