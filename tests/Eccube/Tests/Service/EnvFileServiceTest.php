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

        $this->assertSame([], $service->getIneffectiveReasons(['FOO']));
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

    public function testOverriddenWhenKeyIsInProcessEnv(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env', "FOO=bar\n");

        $service = new EnvFileService($this->projectDir);

        putenv('ECCUBE_TEST_OVERRIDE_KEY=1');
        try {
            $reasons = $service->getIneffectiveReasons(['ECCUBE_TEST_OVERRIDE_KEY']);
            $this->assertContains(EnvFileService::REASON_OVERRIDDEN, $reasons);
            $this->assertFalse($service->isEffective(['ECCUBE_TEST_OVERRIDE_KEY']));
            // 対象キーに含めなければ反映可能と判定される
            $this->assertTrue($service->isEffective(['UNRELATED_KEY']));
        } finally {
            putenv('ECCUBE_TEST_OVERRIDE_KEY');
        }
    }
}
