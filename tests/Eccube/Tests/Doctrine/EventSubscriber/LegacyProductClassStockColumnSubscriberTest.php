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

namespace Eccube\Tests\Doctrine\EventSubscriber;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Doctrine\EventSubscriber\LegacyProductClassStockColumnSubscriber;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Tests\EccubeTestCase;

/**
 * 在庫数の旧列 (dtb_product_class.stock) が DB に残っている間は, スキーマ生成の結果にも残すことを検証する.
 */
final class LegacyProductClassStockColumnSubscriberTest extends EccubeTestCase
{
    public function testKeepsLegacyColumnWhileItExists(): void
    {
        [$args, $schema] = $this->createEventArgs(ProductClass::class, 'dtb_product_class');
        $this->assertFalse($args->getClassTable()->hasColumn('stock'));

        (new LegacyProductClassStockColumnSubscriber($this->createConnection(withLegacy: true)))->postGenerateSchemaTable($args);

        $this->assertTrue($schema->getTable('dtb_product_class')->hasColumn('stock'));
        $column = $schema->getTable('dtb_product_class')->getColumn('stock');
        $this->assertFalse($column->getNotnull());
        $this->assertSame(10, $column->getPrecision());
        $this->assertSame(0, $column->getScale());
    }

    public function testDoesNothingAfterLegacyColumnIsDropped(): void
    {
        [$args, $schema] = $this->createEventArgs(ProductClass::class, 'dtb_product_class');

        (new LegacyProductClassStockColumnSubscriber($this->createConnection(withLegacy: false)))->postGenerateSchemaTable($args);

        $this->assertFalse($schema->getTable('dtb_product_class')->hasColumn('stock'));
    }

    public function testIgnoresOtherEntities(): void
    {
        [$args, $schema] = $this->createEventArgs(Product::class, 'dtb_product');

        (new LegacyProductClassStockColumnSubscriber($this->createConnection(withLegacy: true)))->postGenerateSchemaTable($args);

        $this->assertFalse($schema->getTable('dtb_product')->hasColumn('stock'));
    }

    /**
     * 旧列の無い DB に対するスキーマ更新では, 旧列を追加しない.
     */
    public function testSchemaUpdateDoesNotAddLegacyColumn(): void
    {
        $sqls = (new SchemaTool($this->entityManager))->getUpdateSchemaSql([$this->entityManager->getClassMetadata(ProductClass::class)]);

        foreach ($sqls as $sql) {
            $this->assertDoesNotMatchRegularExpression('/dtb_product_class.*\bstock\b\s+NUMERIC/i', $sql);
        }
    }

    /**
     * @param class-string $className
     *
     * @return array{GenerateSchemaTableEventArgs, Schema}
     */
    private function createEventArgs(string $className, string $tableName): array
    {
        $metadata = $this->entityManager->getClassMetadata($className);
        $schema = (new SchemaTool($this->entityManager))->getSchemaFromMetadata([$metadata]);

        return [new GenerateSchemaTableEventArgs($metadata, $schema, $schema->getTable($tableName)), $schema];
    }

    private function createConnection(bool $withLegacy): Connection
    {
        $conn = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $conn->executeStatement('CREATE TABLE dtb_product_class (id INTEGER PRIMARY KEY NOT NULL, stock_unlimited BOOLEAN DEFAULT 0 NOT NULL'
            .($withLegacy ? ', stock NUMERIC(10, 0) DEFAULT NULL' : '').')');

        return $conn;
    }
}
