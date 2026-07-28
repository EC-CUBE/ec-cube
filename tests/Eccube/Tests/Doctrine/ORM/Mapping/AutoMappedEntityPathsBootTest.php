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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * auto_mapping による Entity ディレクトリの二重登録が、実際に Kernel を boot した状態で
 * redeclare fatal を起こさないことの回帰テスト.
 *
 * StripAutoMappedEntityPathsPassTest は ContainerBuilder を組み立てるコンパイル時の単体テスト、
 * EccubeEntityMetadataDriverTest は素の構成でのドライバ種別の検証であり、いずれも
 * issue #6979 の再現構成そのものは組み立てていない. 本テストは再現に必要な 3 要素を実際に配置して
 * boot し、メタデータ解決まで到達することを検証する.
 *
 * 再現に必要な 3 要素 (issue #6979 の対照実験 A / B で確定):
 *
 *   1. Entity を持つ第三者バンドル (Customize\Lib\CustomizeLibBundle)
 *      prefix Customize\Lib\Entity は明示登録の対象外なので、auto_mapping が生成する
 *      素の AttributeDriver がこの prefix で MappingDriverChain に残る.
 *      これが無いと共有ドライバはチェーンから消えるため fatal にならない (対照実験 A).
 *   2. app/Customize 直下に置かれた Bundle (Customize\CustomizeRootBundle)
 *      doctrine-bundle は「Bundle クラスのあるディレクトリ + /Entity」を auto_mapping 対象とするため、
 *      EC-CUBE が明示登録している app/Customize/Entity が素のドライバの paths にも入る.
 *   3. 同一 FQCN の Proxy (app/proxy/entity/app/Customize/Entity/...)
 *      Kernel::loadEntityProxies() が先にロードするため、素のドライバが元ソースを
 *      require_once した時点で "Cannot declare class ..." になる.
 *
 * 修正 (StripAutoMappedEntityPathsPass) が失われると、このテストは
 * アサーション失敗ではなく PHP の fatal error でプロセスごと停止する.
 * TraitProxyAttributeDriverTest と同じ性質のテストである.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6979
 * @see https://github.com/EC-CUBE/ec-cube/pull/6895 Entity の if(!class_exists()) ガード全廃
 */
final class AutoMappedEntityPathsBootTest extends KernelTestCase
{
    private const TARGET_ENTITY = 'Customize\\Entity\\StripAutoMappedTarget';

    private const EXTRA_ENTITY = 'Customize\\Lib\\Entity\\StripAutoMappedExtra';

    private string $projectDir;

    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectDir = \dirname(__DIR__, 6);
        $this->fs = new Filesystem();

        $this->fs->mirror(\dirname(__DIR__, 5).'/Fixtures/CustomizeRootBundle', $this->projectDir.'/app/Customize');

        // eccube:generate:proxies 相当. 元ソースと同一 FQCN の Proxy を配置する
        $this->fs->copy(
            $this->projectDir.'/app/Customize/Entity/StripAutoMappedTarget.php',
            $this->proxyFile(),
            true
        );

        // 明示登録・auto_mapping ともにコンパイル時に決まるため、コンテナを作り直させる
        $this->fs->remove($this->projectDir.'/var/cache/test');
    }

    protected function tearDown(): void
    {
        $this->fs->remove([
            $this->projectDir.'/app/Customize/CustomizeRootBundle.php',
            $this->projectDir.'/app/Customize/Entity/StripAutoMappedTarget.php',
            $this->projectDir.'/app/Customize/Lib',
            $this->projectDir.'/app/Customize/Resource/config/bundles.php',
            $this->proxyFile(),
            $this->projectDir.'/var/cache/test',
        ]);
        // Restore exception handler to prevent risky test warnings
        restore_exception_handler();
        parent::tearDown();
    }

    /**
     * 再現構成のまま boot してメタデータを解決できること.
     *
     * 併せて、二重登録を取り除いても Entity のマッピング自体は失われないこと
     * (issue #6979 の検証 5 と同じ観点) を確認する.
     */
    public function testBootResolvesMetadataWithRootLevelBundleAndProxy(): void
    {
        static::bootKernel();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $classNames = array_map(
            static fn ($metadata) => $metadata->getName(),
            $entityManager->getMetadataFactory()->getAllMetadata()
        );

        $this->assertContains(
            self::TARGET_ENTITY,
            $classNames,
            '明示登録 (TraitProxyAttributeDriver) された app/Customize/Entity のマッピングが失われている'
        );
        $this->assertContains(
            self::EXTRA_ENTITY,
            $classNames,
            'auto_mapping 側のパス除去が過剰で、第三者バンドルの Entity まで失われている'
        );
    }

    private function proxyFile(): string
    {
        return $this->projectDir.'/app/proxy/entity/app/Customize/Entity/StripAutoMappedTarget.php';
    }
}
