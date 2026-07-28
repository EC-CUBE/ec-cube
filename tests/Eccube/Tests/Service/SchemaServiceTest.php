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

namespace Eccube\Tests\Service;

use Doctrine\Bundle\DoctrineBundle\Mapping\MappingDriver as BundleMappingDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Eccube\Doctrine\ORM\Mapping\Driver\ReloadSafeAttributeDriver;
use Eccube\Entity\Product;
use Eccube\Service\PluginContext;
use Eccube\Service\SchemaService;
use Eccube\Tests\EccubeTestCase;

/**
 * SchemaService::executeCallback のドライバ差し替えの回帰テスト.
 *
 * executeCallback はスキーマ更新前に、明示登録されている Entity 名前空間のドライバを
 * ReloadSafeAttributeDriver に差し替える. これにより、同一プロセスにロード済みの
 * 古い Entity 定義ではなく、新しく生成された Proxy (トレイト適用済み) から
 * メタデータが抽出され、trait 由来のカラムがスキーマ更新に反映される.
 *
 * Customize\Entity が差し替え対象から漏れていると、プラグインの
 * #[EntityExtension('Customize\Entity\...')] トレイトによるカラム追加が
 * インストール/有効化フロー内のスキーマ更新に反映されない.
 */
final class SchemaServiceTest extends EccubeTestCase
{
    public function testExecuteCallbackReplacesExplicitNamespacesWithReloadSafeDriver(): void
    {
        $schemaService = new SchemaService(
            $this->entityManager,
            static::getContainer()->get(PluginContext::class)
        );

        $driversInCallback = [];
        $schemaService->executeCallback(function () use (&$driversInCallback): void {
            $driver = $this->entityManager->getConfiguration()->getMetadataDriverImpl();
            if ($driver instanceof BundleMappingDriver) {
                $driver = $driver->getDriver();
            }
            $this->assertInstanceOf(MappingDriverChain::class, $driver);
            $driversInCallback = $driver->getDrivers();
        }, [], static::getContainer()->getParameter('kernel.project_dir').'/app/proxy/entity');

        $this->assertArrayHasKey('Eccube\Entity', $driversInCallback);
        $this->assertInstanceOf(ReloadSafeAttributeDriver::class, $driversInCallback['Eccube\Entity']);

        $this->assertArrayHasKey('Customize\Entity', $driversInCallback);
        $this->assertInstanceOf(
            ReloadSafeAttributeDriver::class,
            $driversInCallback['Customize\Entity'],
            'Customize\Entity のドライバが ReloadSafeAttributeDriver に差し替えられていない.'
            .' プラグインの #[EntityExtension] トレイトによる Customize エンティティへの'
            .'カラム追加が、インストール/有効化時のスキーマ更新に反映されなくなる'
        );
    }

    /**
     * 差し替え後のドライバは、Entity が 1 つも無い名前空間 (標準構成の app/Customize/Entity は
     * 空ディレクトリ) でも getAllClassNames() で配列を返す必要がある.
     *
     * null を返すと MappingDriverChain::getAllClassNames() の foreach が
     * "foreach() argument must be of type array|object, null given" となり,
     * スキーマ更新を伴うプラグインのインストール・更新が全て失敗する.
     */
    public function testExecuteCallbackKeepsAllClassNamesResolvable(): void
    {
        $schemaService = new SchemaService(
            $this->entityManager,
            static::getContainer()->get(PluginContext::class)
        );

        $classNamesByNamespace = [];
        $chainClassNames = null;
        $schemaService->executeCallback(function () use (&$classNamesByNamespace, &$chainClassNames): void {
            $driver = $this->entityManager->getConfiguration()->getMetadataDriverImpl();
            if ($driver instanceof BundleMappingDriver) {
                $driver = $driver->getDriver();
            }
            $this->assertInstanceOf(MappingDriverChain::class, $driver);
            foreach ($driver->getDrivers() as $namespace => $eachDriver) {
                $classNamesByNamespace[$namespace] = $eachDriver->getAllClassNames();
            }
            $chainClassNames = $driver->getAllClassNames();
        }, [], static::getContainer()->getParameter('kernel.project_dir').'/app/proxy/entity');

        foreach ($classNamesByNamespace as $namespace => $classNames) {
            $this->assertIsArray(
                $classNames,
                sprintf('%s のドライバが getAllClassNames() で配列を返していない', $namespace)
            );
        }

        $this->assertIsArray($chainClassNames);
        $this->assertContains(Product::class, $chainClassNames);
    }
}
