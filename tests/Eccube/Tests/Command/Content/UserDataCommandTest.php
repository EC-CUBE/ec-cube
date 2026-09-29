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

namespace Eccube\Tests\Command\Content;

use Eccube\Command\Content\UserDataListCommand;
use Eccube\Command\Content\UserDataPutCommand;
use Eccube\Command\Content\UserDataRemoveCommand;
use Eccube\Command\Content\UserDataShowCommand;
use Eccube\Service\Content\UserDataFileService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * 一時ディレクトリを root にしてコマンドを直接組み立てるため, 実際の html/user_data には触れない.
 */
final class UserDataCommandTest extends TestCase
{
    private string $root;

    private Filesystem $fs;

    private UserDataFileService $userDataFileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->root = sys_get_temp_dir().'/eccube-user-data-cmd-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->root);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => $id);

        $this->userDataFileService = new UserDataFileService(
            $this->root,
            ['css', 'js', 'txt', 'png'],
            new Filesystem(),
            $translator
        );
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->remove($this->root);
        }

        parent::tearDown();
    }

    public function testPutCreatesFile(): void
    {
        $tester = $this->put(['--path' => 'assets/css/customize.css', '--body' => 'body{}']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('created', $tester->getDisplay());
        $this->assertSame('body{}', file_get_contents($this->root.'/assets/css/customize.css'));
    }

    public function testPutReadsBodyFromStdin(): void
    {
        $tester = $this->put(['--path' => 'sample.txt', '--body' => '-'], ['from stdin']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('from stdin', trim((string) file_get_contents($this->root.'/sample.txt')));
    }

    public function testPutRequiresBody(): void
    {
        $tester = $this->put(['--path' => 'sample.txt']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertFileDoesNotExist($this->root.'/sample.txt');
    }

    public function testPutRequiresPath(): void
    {
        $tester = $this->put(['--body' => 'x']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testPutRejectsTraversal(): void
    {
        $tester = $this->put(['--path' => '../outside.txt', '--body' => 'x']);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileDoesNotExist(\dirname($this->root).'/outside.txt');
    }

    /**
     * html/ はドキュメントルートのため, CLI からも実行可能なファイルは配置できない.
     */
    public function testPutRejectsDisallowedExtension(): void
    {
        $tester = $this->put(['--path' => 'evil.php', '--body' => '<?php echo 1;']);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileDoesNotExist($this->root.'/evil.php');
    }

    public function testPutOutputsJson(): void
    {
        $tester = $this->put(['--path' => 'sample.txt', '--body' => 'x', '--format' => 'json']);

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame('created', $decoded['status']);
        $this->assertSame('/sample.txt', $decoded['identifier']);
        $this->assertFalse($decoded['dry_run']);
    }

    public function testPutDryRunDoesNotWrite(): void
    {
        $tester = $this->put(['--path' => 'sample.txt', '--body' => 'x', '--dry-run' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('dry-run', $tester->getDisplay());
        $this->assertFileDoesNotExist($this->root.'/sample.txt');
    }

    public function testShowOutputsBody(): void
    {
        $this->put(['--path' => 'sample.txt', '--body' => 'shown']);

        $tester = new CommandTester(new UserDataShowCommand($this->userDataFileService));
        $tester->execute(['--path' => 'sample.txt']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('shown', $tester->getDisplay());
    }

    /**
     * json は不正な UTF-8 を扱えないため, 画像等は base64 で返す.
     */
    public function testShowOutputsBase64ForBinary(): void
    {
        $binary = "\x89PNG\r\n\x1a\n\xff\xfe";
        $this->fs->dumpFile($this->root.'/logo.png', $binary);

        $tester = new CommandTester(new UserDataShowCommand($this->userDataFileService));
        $tester->execute(['--path' => 'logo.png', '--format' => 'json']);

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertArrayNotHasKey('body', $decoded);
        $this->assertSame($binary, base64_decode($decoded['body_base64'], true));
    }

    public function testShowReturnsErrorWhenMissing(): void
    {
        $tester = new CommandTester(new UserDataShowCommand($this->userDataFileService));
        $tester->execute(['--path' => 'not-exists.txt']);

        $this->assertSame(1, $tester->getStatusCode());
    }

    public function testList(): void
    {
        $this->put(['--path' => 'assets/css/customize.css', '--body' => 'body{}']);

        $tester = new CommandTester(new UserDataListCommand($this->userDataFileService));
        $tester->execute(['--recursive' => true, '--format' => 'json']);

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame(['/assets', '/assets/css', '/assets/css/customize.css'], array_column($decoded, 'path'));
    }

    public function testRemoveRequiresConfirmation(): void
    {
        $this->put(['--path' => 'sample.txt', '--body' => 'x']);

        $tester = new CommandTester(new UserDataRemoveCommand($this->userDataFileService));
        $tester->setInputs(['no']);
        $tester->execute(['--path' => 'sample.txt']);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertFileExists($this->root.'/sample.txt');
    }

    public function testRemoveWithForce(): void
    {
        $this->put(['--path' => 'sample.txt', '--body' => 'x']);

        $tester = new CommandTester(new UserDataRemoveCommand($this->userDataFileService));
        $tester->execute(['--path' => 'sample.txt', '--force' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertFileDoesNotExist($this->root.'/sample.txt');
    }

    public function testRemoveRequiresRecursiveForNonEmptyDirectory(): void
    {
        $this->put(['--path' => 'assets/css/customize.css', '--body' => 'body{}']);

        $tester = new CommandTester(new UserDataRemoveCommand($this->userDataFileService));
        $tester->execute(['--path' => 'assets', '--force' => true]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertDirectoryExists($this->root.'/assets');
        $this->assertStringContainsString('--recursive', $tester->getDisplay());
    }

    /**
     * @param array<string, mixed> $input
     * @param list<string>         $stdin
     */
    private function put(array $input, array $stdin = []): CommandTester
    {
        $tester = new CommandTester(new UserDataPutCommand($this->userDataFileService));
        if ([] !== $stdin) {
            $tester->setInputs($stdin);
        }
        $tester->execute($input);

        return $tester;
    }
}
