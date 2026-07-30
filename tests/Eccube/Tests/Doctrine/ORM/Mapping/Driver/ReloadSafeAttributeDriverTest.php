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

namespace Eccube\Tests\Doctrine\ORM\Mapping\Driver;

use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Eccube\Doctrine\ORM\Mapping\Driver\ReloadSafeAttributeDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * SchemaService::executeCallback が差し替えるドライバの回帰テスト.
 *
 * getAllClassNames() の戻り値は MappingDriverChain::getAllClassNames() で foreach されるため,
 * Entity が 1 つも無い名前空間 (標準構成の app/Customize/Entity は空ディレクトリ) でも
 * null ではなく配列を返さなければならない. null を返すと
 * "foreach() argument must be of type array|object, null given" となり,
 * スキーマ更新を伴うプラグインのインストール・更新が全て失敗する.
 */
final class ReloadSafeAttributeDriverTest extends TestCase
{
    private string $baseDir;

    private string $entityDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseDir = sys_get_temp_dir().'/reload_safe_driver_test_'.uniqid('', true);
        $this->entityDir = $this->baseDir.'/Entity';
        (new Filesystem())->mkdir([$this->entityDir, $this->baseDir.'/proxy', $this->baseDir.'/output']);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->baseDir);
        parent::tearDown();
    }

    public function testGetAllClassNamesReturnsEmptyArrayWhenNoEntityExists(): void
    {
        $classNames = $this->createDriver()->getAllClassNames();

        $this->assertSame(
            [],
            $classNames,
            'Entity が存在しないディレクトリでは空配列を返す必要がある.'
            .' null を返すと MappingDriverChain::getAllClassNames() の foreach が Warning になり,'
            .' プラグインのインストール・更新が失敗する'
        );
    }

    public function testChainResolvesClassNamesWhenDriverHasNoEntity(): void
    {
        $chain = new MappingDriverChain();
        $chain->addDriver($this->createDriver(), 'Customize\Entity');

        $this->assertSame([], $chain->getAllClassNames());
    }

    private function createDriver(): ReloadSafeAttributeDriver
    {
        $driver = new ReloadSafeAttributeDriver([$this->entityDir]);
        $driver->setTraitProxiesDirectory($this->baseDir.'/proxy');
        $driver->setNewProxyFiles([]);
        $driver->setOutputDir($this->baseDir.'/output');

        return $driver;
    }
}
