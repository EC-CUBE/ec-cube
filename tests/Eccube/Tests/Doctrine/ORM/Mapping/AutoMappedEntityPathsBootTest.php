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

use Customize\Entity\StripAutoMappedTarget;
use Customize\Lib\Entity\StripAutoMappedExtra;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * auto_mapping による Entity ディレクトリの二重登録が、実際にアプリケーションを起動した状態で
 * redeclare fatal を起こさないことの回帰テスト.
 *
 * StripAutoMappedEntityPathsPassTest は ContainerBuilder を組み立てるコンパイル時の単体テスト、
 * EccubeEntityMetadataDriverTest は素の構成でのドライバ種別の検証であり、いずれも
 * issue #6979 の再現構成そのものは組み立てていない. 本テストは再現に必要な 3 要素を実際に配置し、
 * メタデータ解決まで到達することを検証する.
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
 *      require_once した時点で "Cannot redeclare class ..." になる.
 *
 * 検証は bin/console をサブプロセスで実行して行う. 回帰時の redeclare は PHP の fatal であり、
 * 同一プロセスで起動すると tearDown が実行されず fixture が残るため、
 * 子プロセスに閉じ込めて終了コードで判定する.
 *
 * 実プロジェクトのファイルを一時的に置き換えるため、既存ファイルは setUp で退避し tearDown で復元する.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6979
 * @see https://github.com/EC-CUBE/ec-cube/pull/6895 Entity の if(!class_exists()) ガード全廃
 */
final class AutoMappedEntityPathsBootTest extends TestCase
{
    // fixture を app/Customize へ配置して初めて実体を持つクラス. ::class は静的解決のためロードは発生しない
    private const TARGET_ENTITY = StripAutoMappedTarget::class;

    private const EXTRA_ENTITY = StripAutoMappedExtra::class;

    private const PROXY_PATH = 'app/proxy/entity/app/Customize/Entity/StripAutoMappedTarget.php';

    /**
     * 本テストが作成・削除するパス (プロジェクトルートからの相対).
     * 実プロジェクトに同名のものがある場合は退避して復元する.
     */
    private const MANAGED_PATHS = [
        'app/Customize/CustomizeRootBundle.php',
        'app/Customize/Entity/StripAutoMappedTarget.php',
        'app/Customize/Lib',
        'app/Customize/Resource/config/bundles.php',
        self::PROXY_PATH,
    ];

    private string $projectDir;

    private string $backupDir;

    private Filesystem $fs;

    /** @var array<string, string> 退避したパス (相対パス => 退避先) */
    private array $backups = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectDir = \dirname(__DIR__, 6);
        $this->backupDir = sys_get_temp_dir().'/eccube_auto_mapped_backup_'.uniqid('', true);
        $this->fs = new Filesystem();

        $this->stashExistingFiles();

        $this->fs->mirror(\dirname(__DIR__, 5).'/Fixtures/CustomizeRootBundle', $this->projectDir.'/app/Customize');

        // eccube:generate:proxies 相当. 元ソースと同一 FQCN の Proxy を配置する
        $this->fs->copy(
            $this->projectDir.'/app/Customize/Entity/StripAutoMappedTarget.php',
            $this->projectDir.'/'.self::PROXY_PATH,
            true
        );

        // 明示登録・auto_mapping ともにコンパイル時に決まるため、コンテナを作り直させる
        $this->fs->remove($this->projectDir.'/var/cache/test');
    }

    protected function tearDown(): void
    {
        $this->restoreStashedFiles();
        $this->fs->remove($this->projectDir.'/var/cache/test');
        parent::tearDown();
    }

    /**
     * 再現構成のままアプリケーションを起動し、メタデータを解決できること.
     *
     * 併せて、二重登録を取り除いても Entity のマッピング自体は失われないこと
     * (issue #6979 の検証 5 と同じ観点) を確認する.
     */
    public function testMappingIsResolvedWithRootLevelBundleAndProxy(): void
    {
        $process = new Process(['bin/console', 'doctrine:mapping:info'], $this->projectDir, ['APP_ENV' => 'test']);
        $process->run();

        $output = $process->getOutput().$process->getErrorOutput();

        $this->assertSame(
            0,
            $process->getExitCode(),
            'app/Customize 直下の Bundle と Proxy が共存する構成でメタデータを解決できない.'
            .' auto_mapping による Entity ディレクトリの二重登録が残っている可能性がある:'.\PHP_EOL.$output
        );
        $this->assertStringContainsString(
            self::TARGET_ENTITY,
            $output,
            '明示登録 (TraitProxyAttributeDriver) された app/Customize/Entity のマッピングが失われている'
        );
        $this->assertStringContainsString(
            self::EXTRA_ENTITY,
            $output,
            'auto_mapping 側のパス除去が過剰で、第三者バンドルの Entity まで失われている'
        );
    }

    /**
     * 実プロジェクトに同名のファイル・ディレクトリがあれば退避する.
     */
    private function stashExistingFiles(): void
    {
        foreach (self::MANAGED_PATHS as $relative) {
            $path = $this->projectDir.'/'.$relative;
            if (!$this->fs->exists($path)) {
                continue;
            }

            $backup = $this->backupDir.'/'.str_replace('/', '__', $relative);
            $this->fs->mkdir($this->backupDir);
            $this->fs->rename($path, $backup, true);
            $this->backups[$relative] = $backup;
        }
    }

    /**
     * 本テストが配置したファイルを取り除き、退避したものを元に戻す.
     */
    private function restoreStashedFiles(): void
    {
        foreach (self::MANAGED_PATHS as $relative) {
            $this->fs->remove($this->projectDir.'/'.$relative);
        }

        foreach ($this->backups as $relative => $backup) {
            $this->fs->rename($backup, $this->projectDir.'/'.$relative, true);
        }

        $this->removeEmptyProxyDirectories();

        $this->fs->remove($this->backupDir);
        $this->backups = [];
    }

    /**
     * Proxy の配置で作られたディレクトリを、空になった場合のみ取り除く.
     * 実運用で生成された Proxy を巻き込まないよう、中身がある場合は残す.
     */
    private function removeEmptyProxyDirectories(): void
    {
        $directories = [
            'app/proxy/entity/app/Customize/Entity',
            'app/proxy/entity/app/Customize',
            'app/proxy/entity/app',
        ];

        foreach ($directories as $relative) {
            $path = $this->projectDir.'/'.$relative;
            if (is_dir($path) && !(new \FilesystemIterator($path))->valid()) {
                $this->fs->remove($path);
            }
        }
    }
}
