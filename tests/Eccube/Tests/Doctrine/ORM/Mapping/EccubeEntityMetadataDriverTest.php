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
 * EC-CUBE が明示登録している Entity ディレクトリが、TraitProxyAttributeDriver 以外から
 * 二重登録されないことの回帰テスト.
 *
 * Kernel::addEntityExtensionPass は src/Eccube/Entity・app/Customize/Entity・
 * app/Plugin/<Code>/Entity を TraitProxyAttributeDriver で登録する.
 * これは Proxy (app/proxy/entity) で宣言済みの Entity を再 require しない実装になっている.
 * ところが doctrine.orm.auto_mapping がこれらのディレクトリを持つバンドルを検出すると、
 * 同じディレクトリが素の AttributeDriver でも登録される. 素のドライバは
 * ColocatedMappingDriver::getAllClassNames() で Entity ソースを無条件に require_once するため、
 * Kernel::loadEntityProxies が Proxy を先にロードした状態では "Cannot redeclare class" で
 * fatal になる (Entity の if (!class_exists()) ガード全廃前は、そのガードが吸収していた).
 *
 * 二重登録は StripAutoMappedEntityPathsPass がコンパイル時に取り除く.
 *
 * @see https://github.com/EC-CUBE/ec-cube/pull/6895 Entity の if(!class_exists()) ガード全廃
 * @see https://github.com/EC-CUBE/ec-cube/issues/6979 プラグイン/Customize 直下にバンドルを置いた場合の再発
 */
final class EccubeEntityMetadataDriverTest extends EccubeTestCase
{
    /**
     * 明示登録した Entity ディレクトリを担当するドライバが TraitProxyAttributeDriver だけであることを検証する.
     */
    public function testExplicitlyMappedPathsAreMappedOnlyByTraitProxyAttributeDriver(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $driver = $entityManager->getConfiguration()->getMetadataDriverImpl();

        $explicitlyMappedPaths = $this->explicitlyMappedPaths();
        $this->assertNotEmpty($explicitlyMappedPaths);

        $mappedPaths = [];
        foreach ($this->flattenDrivers($driver) as $each) {
            if (!method_exists($each, 'getPaths')) {
                continue;
            }
            foreach ($each->getPaths() as $path) {
                $path = realpath($path);
                if (!\in_array($path, $explicitlyMappedPaths, true)) {
                    continue;
                }
                $mappedPaths[] = $path;
                $this->assertInstanceOf(TraitProxyAttributeDriver::class, $each, $path.' は TraitProxyAttributeDriver 以外から登録してはならない'
                    .' (素の AttributeDriver は Entity ソースを無条件に require_once するため、'
                    .'Proxy ロード済みの環境で "Cannot redeclare class" になる)');
            }
        }

        // 対象ドライバが 0 件でもループが素通りするため、明示登録そのものが失われた構成も検知する
        $coreEntityDir = realpath(static::getContainer()->getParameter('kernel.project_dir').'/src/Eccube/Entity');
        $this->assertContains(
            $coreEntityDir,
            $mappedPaths,
            'src/Eccube/Entity のマッピングドライバ (Kernel::addEntityExtensionPass の TraitProxyAttributeDriver) が登録されていない'
        );
    }

    /**
     * Kernel::addEntityExtensionPass が TraitProxyAttributeDriver で明示登録するディレクトリ.
     *
     * @return list<string>
     */
    private function explicitlyMappedPaths(): array
    {
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');

        $paths = [
            $projectDir.'/src/Eccube/Entity',
            $projectDir.'/app/Customize/Entity',
            ...glob($projectDir.'/app/Plugin/*/Entity', GLOB_ONLYDIR),
        ];

        return array_values(array_filter(array_map(realpath(...), $paths)));
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
