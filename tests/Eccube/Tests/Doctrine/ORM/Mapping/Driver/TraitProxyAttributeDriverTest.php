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

use Eccube\Doctrine\ORM\Mapping\Driver\TraitProxyAttributeDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Entity の if (!class_exists()) ガード全廃に伴う二重宣言(redeclare)防止の回帰テスト.
 *
 * Kernel::loadEntityProxies が app/proxy/entity の Proxy を先にロードした後、
 * TraitProxyAttributeDriver::getAllClassNames() が同一 FQCN の元ソースを require すると
 * "Cannot redeclare class" で fatal になる。旧実装は各 Entity の if (!class_exists()) で
 * これを吸収していたが、ガード全廃後はドライバ側で「宣言済みのクラスは再 require しない」
 * ことで防止する。
 *
 * DB に依存しないため、UpdateSchemaDoctrineCommandTest (PostgreSQL 限定) と異なり
 * 全 DB マトリクスの unit-test で実行され、MySQL / SQLite 環境でも回帰を検知できる.
 */
final class TraitProxyAttributeDriverTest extends TestCase
{
    private string $proxyLikeDir;

    private string $sourceLikeDir;

    private const FQCN = 'Eccube\\Tests\\Fixture\\Doctrine\\RedeclareGuardTarget';

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $base = sys_get_temp_dir().'/trait_proxy_driver_test_'.uniqid('', true);
        $this->proxyLikeDir = $base.'/proxy';
        $this->sourceLikeDir = $base.'/source';
        $fs = new Filesystem();
        $fs->mkdir([$this->proxyLikeDir, $this->sourceLikeDir]);

        // 同一 FQCN を持つ Entity を「Proxy 相当」「元ソース相当」の 2 パスに配置する.
        $code = <<<'PHP'
            <?php

            namespace Eccube\Tests\Fixture\Doctrine;

            use Doctrine\ORM\Mapping as ORM;

            #[ORM\Entity]
            #[ORM\Table(name: 'dtb_redeclare_guard_target')]
            class RedeclareGuardTarget
            {
                #[ORM\Id]
                #[ORM\Column(type: 'integer')]
                #[ORM\GeneratedValue]
                private ?int $id = null;
            }
            PHP;
        file_put_contents($this->proxyLikeDir.'/RedeclareGuardTarget.php', $code);
        file_put_contents($this->sourceLikeDir.'/RedeclareGuardTarget.php', $code);
    }

    #[\Override]
    protected function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove(\dirname($this->proxyLikeDir));
        parent::tearDown();
    }

    /**
     * Proxy 相当パスからロード済みのクラスに対し、getAllClassNames() が
     * 元ソースを再 require せず(= redeclare fatal を起こさず)、FQCN を収集することを検証する.
     *
     * ガード除去前の実装(無条件 require_once)であれば、この呼び出しは
     * "Cannot redeclare class" の fatal でプロセスごと停止する.
     */
    public function testGetAllClassNamesDoesNotRedeclareAlreadyLoadedEntity(): void
    {
        // Kernel::loadEntityProxies 相当: 元ソースとは別パス(Proxy 相当)からクラスをロードする
        require_once $this->proxyLikeDir.'/RedeclareGuardTarget.php';
        self::assertTrue(class_exists(self::FQCN, false));

        $driver = new TraitProxyAttributeDriver([$this->sourceLikeDir]);
        // Proxy ディレクトリを実在しないパスに向け、元ソース require の分岐を通す.
        // (元ソースは既にロード済み FQCN を宣言している)
        $driver->setTraitProxiesDirectory(sys_get_temp_dir().'/nonexistent_'.uniqid('', true));

        $classNames = $driver->getAllClassNames();

        self::assertContains(self::FQCN, $classNames);
    }
}
