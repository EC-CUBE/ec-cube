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
use Doctrine\ORM\Query\QueryException;
use Eccube\Doctrine\ORM\Query\DateFormat;
use Eccube\Entity\Order;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class DateFormatTest extends EccubeTestCase
{
    public function testGetSql()
    {
        $sql = $this->entityManager->createQueryBuilder()
            ->select("DATEFORMAT(o.order_date, 'Y/m/d')")->from(Order::class, 'o')
            ->getQuery()->getSql();
        $platform = $this->entityManager->getConnection()->getDatabasePlatform();
        if ($platform instanceof PostgreSQLPlatform) {
            $this->assertStringContainsString('TO_CHAR(', (string) $sql);
            $this->assertStringContainsString("'YYYY/MM/DD'", (string) $sql);
        } elseif ($platform instanceof AbstractMySQLPlatform) {
            $this->assertStringContainsString('DATE_FORMAT(', (string) $sql);
            $this->assertStringContainsString("'%Y/%m/%d'", (string) $sql);
        } elseif ($platform instanceof SQLitePlatform) {
            $this->assertStringContainsString('STRFTIME(', (string) $sql);
            $this->assertStringContainsString("'%Y/%m/%d'", (string) $sql);
        } else {
            $this->fail(sprintf('Unexpected platform: %s', $platform::class));
        }
    }

    public function testUnsupportedFormat()
    {
        $this->expectException(QueryException::class);

        $this->entityManager->createQueryBuilder()
            ->select("DATEFORMAT(o.order_date, 'd/m/Y')")->from(Order::class, 'o')
            ->getQuery()->getSql();
    }

    /**
     * 月境界の直後 (00:00:00) と直前 (23:59:59) が、UTC ではなく
     * アプリケーションのタイムゾーンの日付で整形されることを確認する.
     * 月・日が 1 桁の日付でゼロ埋めも確認する.
     */
    #[DataProvider(methodName: 'formatProvider')]
    public function testFormatInApplicationTimezone(string $format)
    {
        $Customer = $this->createCustomer();
        [$AfterBoundary, $BeforeBoundary] = $this->createOrders([$Customer, $Customer]);

        $startOfMonth = new \DateTime('2024-03-01 00:00:00');
        $endOfPrevMonth = new \DateTime('2024-02-29 23:59:59');
        // flush 時に UTCDateTimeTzType が DateTime を UTC へ変更するため、期待値とは別のインスタンスを渡す
        $AfterBoundary->setOrderDate(clone $startOfMonth);
        $BeforeBoundary->setOrderDate(clone $endOfPrevMonth);
        $this->entityManager->flush();

        foreach ([[$AfterBoundary, $startOfMonth], [$BeforeBoundary, $endOfPrevMonth]] as [$Order, $expected]) {
            $actual = $this->entityManager->createQueryBuilder()
                ->select("DATEFORMAT(o.order_date, '{$format}')")->from(Order::class, 'o')
                ->where('o.id = :id')->setParameter('id', $Order->getId())
                ->getQuery()->getSingleScalarResult();

            $this->assertSame($expected->format($format), $actual);
        }
    }

    public static function formatProvider(): \Iterator
    {
        foreach (array_keys(DateFormat::FORMATS) as $format) {
            yield $format => [$format];
        }
    }
}
