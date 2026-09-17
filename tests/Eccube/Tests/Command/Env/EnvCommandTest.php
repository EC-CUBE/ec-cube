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

namespace Eccube\Tests\Command\Env;

use Eccube\Command\Env\EnvGetCommand;
use Eccube\Command\Env\EnvSetCommand;
use Eccube\Service\EnvFileService;
use Eccube\Tests\EffectiveUserTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 一時ディレクトリを projectDir にしてコマンドを直接組み立てるため, 実際の .env には触れない.
 */
final class EnvCommandTest extends TestCase
{
    use EffectiveUserTrait;

    private string $projectDir;

    private Filesystem $fs;

    private EnvFileService $envFileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->projectDir = sys_get_temp_dir().'/eccube-env-cmd-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->projectDir);
        $this->fs->dumpFile($this->projectDir.'/.env', "APP_ENV=prod\nECCUBE_TEMPLATE_CODE=default\n");
        $this->envFileService = new EnvFileService($this->projectDir);
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->chmod($this->projectDir, 0755, 0000, true);
            $this->fs->remove($this->projectDir);
        }

        parent::tearDown();
    }

    public function testGetOutputsValue(): void
    {
        $tester = $this->get(['key' => 'ECCUBE_TEMPLATE_CODE']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame("default\n", $tester->getDisplay());
    }

    public function testGetOutputsJson(): void
    {
        $tester = $this->get(['key' => 'ECCUBE_TEMPLATE_CODE', '--format' => 'json']);

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame('ECCUBE_TEMPLATE_CODE', $decoded['key']);
        $this->assertSame('default', $decoded['file_value']);
        $this->assertFalse($decoded['overridden']);
    }

    public function testGetReturnsErrorWhenKeyIsMissing(): void
    {
        $tester = $this->get(['key' => 'ECCUBE_NOT_DEFINED_IN_TEST']);

        $this->assertSame(1, $tester->getStatusCode());
    }

    public function testSetUpdatesExistingKey(): void
    {
        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom'], '--no-cache-clear' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('custom', $this->envFileService->get('ECCUBE_TEMPLATE_CODE'));
        $this->assertSame('prod', $this->envFileService->get('APP_ENV'), '他のキーは書き換えない');
    }

    public function testSetAppendsUnknownKey(): void
    {
        $this->set(['assignments' => ['ECCUBE_FORCE_SSL=1'], '--no-cache-clear' => true]);

        $this->assertSame('1', $this->envFileService->get('ECCUBE_FORCE_SSL'));
    }

    public function testSetAcceptsMultipleAssignments(): void
    {
        $tester = $this->set([
            'assignments' => ['ECCUBE_TEMPLATE_CODE=custom', "TRUSTED_HOSTS='^example\\.com$'"],
            '--no-cache-clear' => true,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('custom', $this->envFileService->get('ECCUBE_TEMPLATE_CODE'));
        $this->assertSame("'^example\\.com$'", $this->envFileService->get('TRUSTED_HOSTS'), '値はそのまま書き出す');
    }

    /**
     * 値に = を含む場合は最初の = だけで分割する.
     */
    public function testSetKeepsEqualSignInValue(): void
    {
        $this->set(['assignments' => ['MAILER_DSN=smtp://localhost:25?verify_peer=0'], '--no-cache-clear' => true]);

        $this->assertSame('smtp://localhost:25?verify_peer=0', $this->envFileService->get('MAILER_DSN'));
    }

    public function testSetDryRunDoesNotWrite(): void
    {
        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom'], '--dry-run' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('dry-run', $tester->getDisplay());
        $this->assertSame('default', $this->envFileService->get('ECCUBE_TEMPLATE_CODE'));
    }

    public function testSetIsIdempotent(): void
    {
        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=default'], '--no-cache-clear' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('変更はありません', $tester->getDisplay());
    }

    public function testSetRejectsInvalidAssignment(): void
    {
        $tester = $this->set(['assignments' => ['NOT_AN_ASSIGNMENT'], '--no-cache-clear' => true]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testSetRejectsInvalidKey(): void
    {
        $tester = $this->set(['assignments' => ['9INVALID=1'], '--no-cache-clear' => true]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testSetReturnsErrorWhenEnvIsMissing(): void
    {
        $this->fs->remove($this->projectDir.'/.env');

        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom'], '--no-cache-clear' => true]);

        $this->assertSame(1, $tester->getStatusCode());
    }

    public function testSetReturnsErrorWhenEnvIsNotWritable(): void
    {
        $this->skipIfRoot();

        $this->fs->chmod($this->projectDir.'/.env', 0444);

        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom'], '--no-cache-clear' => true]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('eccube:doctor:permissions', $tester->getDisplay());
    }

    /**
     * .env.local.php があると .env の変更は反映されないため, 書き込んだうえで手動対応を促す.
     */
    public function testSetReturnsManualActionRequiredWhenLocalPhpExists(): void
    {
        $this->fs->dumpFile($this->projectDir.'/.env.local.php', "<?php\n\nreturn [];\n");

        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom'], '--no-cache-clear' => true]);

        $this->assertSame(EnvSetCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->getStatusCode());
        $this->assertStringContainsString('composer dump-env', $tester->getDisplay());
        $this->assertSame('custom', $this->envFileService->get('ECCUBE_TEMPLATE_CODE'), '書き込み自体は完了する');
    }

    /**
     * 再生成は別プロセスで行う. 現在のプロセスは起動時に読み込んだ古い .env を保持しているため.
     */
    public function testSetRunsCacheBuildInSubprocess(): void
    {
        $this->givenConsoleStub(0);

        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom']]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('eccube:cache:build', $tester->getDisplay());
        $this->assertFileExists($this->projectDir.'/rebuilt', 'サブプロセスが実行されていること');
    }

    public function testSetReturnsManualActionRequiredWhenCacheBuildFails(): void
    {
        $this->givenConsoleStub(1);

        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=custom']]);

        $this->assertSame(EnvSetCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->getStatusCode());
        $this->assertSame('custom', $this->envFileService->get('ECCUBE_TEMPLATE_CODE'), '書き込み自体は完了する');
    }

    /**
     * bin/console の代わりに終了コードだけを返すスクリプトを置く.
     */
    private function givenConsoleStub(int $exitCode): void
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            self::markTestSkipped('Windows ではシェルスクリプトのスタブを実行できない.');
        }

        $this->fs->dumpFile(
            $this->projectDir.'/bin/console',
            sprintf("#!/bin/sh\ntouch \"\$(dirname \"\$0\")/../rebuilt\"\nexit %d\n", $exitCode)
        );
        $this->fs->chmod($this->projectDir.'/bin/console', 0755);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function get(array $input): CommandTester
    {
        $tester = new CommandTester(new EnvGetCommand($this->envFileService));
        $tester->execute($input);

        return $tester;
    }

    /**
     * @param array<string, mixed> $input
     */
    private function set(array $input): CommandTester
    {
        $tester = new CommandTester(new EnvSetCommand($this->envFileService, $this->projectDir));
        $tester->execute($input);

        return $tester;
    }
}
