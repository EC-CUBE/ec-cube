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

namespace Eccube\Tests\Doctrine\ORM\Mapping;

use Doctrine\Bundle\DoctrineBundle\Mapping\MappingDriver as BundleMappingDriver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Eccube\Doctrine\ORM\Mapping\Driver\TraitProxyAttributeDriver;
use Eccube\Tests\EccubeTestCase;

/**
 * src/Eccube/Entity のマッピングが TraitProxyAttributeDriver 以外から二重登録されないことの回帰テスト.
 *
 * Kernel::addEntityExtensionPass は src/Eccube/Entity を TraitProxyAttributeDriver で登録する.
 * これは Proxy (app/proxy/entity) で宣言済みの Entity を再 require しない実装になっている.
 * ところが doctrine.orm.auto_mapping が EccubeBundle を検出すると、同じ src/Eccube/Entity が
 * 素の AttributeDriver でも登録される. 素のドライバは ColocatedMappingDriver::getAllClassNames() で
 * Entity ソースを無条件に require_once するため、Kernel::loadEntityProxies が Proxy を
 * 先にロードした状態では "Cannot redeclare class" で fatal になる
 * (Entity の if (!class_exists()) ガード全廃前は、そのガードが吸収していた).
 *
 * @see https://github.com/EC-CUBE/ec-cube/pull/6895 Entity の if(!class_exists()) ガード全廃
 */
final class EccubeEntityMetadataDriverTest extends EccubeTestCase
{
    /**
     * src/Eccube/Entity を担当するドライバが TraitProxyAttributeDriver だけであることを検証する.
     */
    public function testCoreEntityPathIsMappedOnlyByTraitProxyAttributeDriver(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $driver = $entityManager->getConfiguration()->getMetadataDriverImpl();

        $coreEntityDir = realpath(__DIR__.'/../../../../../../src/Eccube/Entity');
        $this->assertIsString($coreEntityDir);

        foreach ($this->flattenDrivers($driver) as $each) {
            if (!method_exists($each, 'getPaths')) {
                continue;
            }
            foreach ($each->getPaths() as $path) {
                if (realpath($path) !== $coreEntityDir) {
                    continue;
                }
                $this->assertInstanceOf(TraitProxyAttributeDriver::class, $each, 'src/Eccube/Entity は TraitProxyAttributeDriver 以外から登録してはならない'
                .' (素の AttributeDriver は Entity ソースを無条件に require_once するため、'
                .'Proxy ロード済みの環境で "Cannot redeclare class" になる)');
            }
        }
    }

    /**
     * MappingDriverChain を再帰的に展開して、実際にマッピングを解決するドライバを列挙する.
     *
     * @return list<MappingDriver>
     */
    private function flattenDrivers(?MappingDriver $driver): array
    {
        if ($driver === null) {
            return [];
        }

        // doctrine-bundle の MappingDriver は実ドライバをラップしているため中身を取り出す
        if ($driver instanceof BundleMappingDriver) {
            return $this->flattenDrivers($driver->getDriver());
        }

        if (!$driver instanceof MappingDriverChain) {
            return [$driver];
        }

        $drivers = [];
        foreach ($driver->getDrivers() as $each) {
            $drivers = [...$drivers, ...$this->flattenDrivers($each)];
        }

        return [...$drivers, ...$this->flattenDrivers($driver->getDefaultDriver())];
    }
}
