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

use Eccube\Service\EnvFileService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @see https://github.com/EC-CUBE/ec-cube/issues/6130
 */
final class EnvFileServiceTest extends TestCase
{
    private string $projectDir;

    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->projectDir = sys_get_temp_dir().'/eccube_env_test_'.uniqid();
        $this->fs->mkdir($this->projectDir);
    }

    protected function tearDown(): void
    {
        $this->fs->remove($this->projectDir);
        parent::tearDown();
    }

    public function testEffectiveWhenEnvExistsAndWritable(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "FOO=bar\n");

        $service = new EnvFileService($this->projectDir);

        $this->assertSame([], $service->getIneffectiveReasons());
        $this->assertTrue($service->isEffective(['FOO']));
    }

    public function testNotFoundWhenEnvIsAbsent(): void
    {
        $service = new EnvFileService($this->projectDir);

        $this->assertContains(EnvFileService::REASON_NOT_FOUND, $service->getIneffectiveReasons());
        $this->assertFalse($service->isEffective());
    }

    public function testNotWritableWhenEnvIsReadOnly(): void
    {
        $envFile = $this->projectDir.'/.env';
        $this->fs->dumpFile($envFile, "FOO=bar\n");
        $this->fs->chmod($envFile, 0o444);

        $service = new EnvFileService($this->projectDir);

        // root で実行される環境では書き込み権限判定が効かないためスキップ
        if (is_writable($envFile)) {
            $this->markTestSkipped('.env is writable even with 0444 (running as root?)');
        }

        $this->assertContains(EnvFileService::REASON_NOT_WRITABLE, $service->getIneffectiveReasons());
        $this->assertFalse($service->isEffective());
    }

    public function testLocalPhpSnapshotMakesEnvIneffective(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "FOO=bar\n");
        $this->fs->dumpFile($this->projectDir.'/.env.local.php', "<?php\nreturn [];\n");

        $service = new EnvFileService($this->projectDir);

        $this->assertContains(EnvFileService::REASON_LOCAL_PHP, $service->getIneffectiveReasons());
        $this->assertFalse($service->isEffective());
    }

    /**
     * .env 系ファイルから Dotenv が populate したキー（SYMFONY_DOTENV_VARS に載る）は,
     * $_SERVER / getenv に同名の値があっても「.env が実効」として上書き扱いしないこと.
     *
     * index.php:34 の boot_env(.., true) 経路（APP_ENV 未設定＝非 Docker）では .env が
     * OS 環境変数に勝つ. この経路を getenv だけで判定すると誤検知し, 反映される保存を
     * ブロックしてしまう回帰を防ぐ（#6130 の核心）.
     */
    public function testNotOverriddenWhenKeyIsPopulatedFromDotenv(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "ECCUBE_TEST_KEY_A=fromdotenv\n");
        $service = new EnvFileService($this->projectDir);
        $key = 'ECCUBE_TEST_KEY_A';

        $this->withGlobals([
            'server' => ['SYMFONY_DOTENV_VARS' => $key, $key => 'fromdotenv'],
        ], function () use ($service, $key): void {
            $this->assertSame([], $service->getOverriddenKeys([$key]));
            $this->assertTrue($service->isEffective([$key]));
        });
    }

    /**
     * Dotenv が触れていない（SYMFONY_DOTENV_VARS 非掲載）のに $_SERVER 側に値がある
     * キー（nginx fastcgi_param / Apache SetEnv 相当）は上書きと判定すること.
     */
    public function testOverriddenWhenKeyOnlyInServer(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "FOO=bar\n");
        $service = new EnvFileService($this->projectDir);
        $key = 'ECCUBE_TEST_KEY_B';

        $this->withGlobals([
            'server' => ['SYMFONY_DOTENV_VARS' => '', $key => 'osvalue'],
        ], function () use ($service, $key): void {
            $this->assertSame([$key], $service->getOverriddenKeys([$key]));
            $this->assertFalse($service->isEffective([$key]));
        });
    }

    /**
     * .env.local / .env.$APP_ENV 等のカスケードファイルで対象キーが再定義されている場合,
     * .env の値は上書きされるため上書きと判定すること. 対象キーを含まないファイルは無視すること.
     */
    public function testOverriddenWhenKeyRedefinedInCascadeFile(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "ECCUBE_TEST_KEY_A=fromdotenv\nECCUBE_TEST_KEY_B=fromdotenv\n");
        // .env.local は KEY_A のみ再定義する
        $this->fs->dumpFile($this->projectDir.'/.env.local', "ECCUBE_TEST_KEY_A=fromlocal\n");
        $service = new EnvFileService($this->projectDir);

        $this->withGlobals([
            'server' => ['SYMFONY_DOTENV_VARS' => 'ECCUBE_TEST_KEY_A,ECCUBE_TEST_KEY_B'],
        ], function () use ($service): void {
            // KEY_A はカスケードで上書きされるため反映されない
            $this->assertSame(['ECCUBE_TEST_KEY_A'], $service->getOverriddenKeys(['ECCUBE_TEST_KEY_A']));
            // KEY_B はカスケードに無く .env 由来のため反映される
            $this->assertSame([], $service->getOverriddenKeys(['ECCUBE_TEST_KEY_B']));
        });
    }

    /**
     * 複数キーのうち上書きされているキーだけを部分集合として返すこと（画面全体を止めないための土台）.
     */
    public function testGetOverriddenKeysReturnsOnlyOverriddenSubset(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "ECCUBE_TEST_KEY_A=fromdotenv\nECCUBE_TEST_KEY_B=fromdotenv\n");
        $service = new EnvFileService($this->projectDir);

        $this->withGlobals([
            // KEY_A/KEY_B は .env 由来。うち KEY_A だけ OS 環境変数が上書き（SYMFONY_DOTENV_VARS には未掲載）
            'server' => ['SYMFONY_DOTENV_VARS' => 'ECCUBE_TEST_KEY_B', 'ECCUBE_TEST_KEY_A' => 'osvalue'],
        ], function () use ($service): void {
            $this->assertSame(
                ['ECCUBE_TEST_KEY_A'],
                $service->getOverriddenKeys(['ECCUBE_TEST_KEY_A', 'ECCUBE_TEST_KEY_B'])
            );
        });
    }

    public function testGetOverriddenKeysReturnsEmptyForNoKeys(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "FOO=bar\n");
        $service = new EnvFileService($this->projectDir);

        $this->assertSame([], $service->getOverriddenKeys([]));
    }

    /**
     * $_SERVER / $_ENV の指定キーを一時的に差し替え, コールバック実行後に元へ復元する.
     *
     * @param array{server?: array<string, string>, env?: array<string, string>} $overrides
     */
    private function withGlobals(array $overrides, callable $fn): void
    {
        $sentinel = '__ECCUBE_TEST_UNSET__';
        $backup = ['server' => [], 'env' => []];

        foreach (($overrides['server'] ?? []) as $key => $value) {
            $backup['server'][$key] = \array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $sentinel;
            $_SERVER[$key] = $value;
        }
        foreach (($overrides['env'] ?? []) as $key => $value) {
            $backup['env'][$key] = \array_key_exists($key, $_ENV) ? $_ENV[$key] : $sentinel;
            $_ENV[$key] = $value;
        }

        try {
            $fn();
        } finally {
            foreach ($backup['server'] as $key => $value) {
                if ($sentinel === $value) {
                    unset($_SERVER[$key]);
                } else {
                    $_SERVER[$key] = $value;
                }
            }
            foreach ($backup['env'] as $key => $value) {
                if ($sentinel === $value) {
                    unset($_ENV[$key]);
                } else {
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}
