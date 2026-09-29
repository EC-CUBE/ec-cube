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

use Eccube\Command\Content\AssetApplyCommand;
use Eccube\Command\Content\AssetShowCommand;
use Eccube\Service\Content\AssetContentService;
use Eccube\Service\Content\UserDataFileService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * 一時ディレクトリを root にしてコマンドを直接組み立てるため, 実際の html/user_data には触れない.
 */
final class AssetCommandTest extends TestCase
{
    private string $root;

    private Filesystem $fs;

    private AssetContentService $assetContentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->root = sys_get_temp_dir().'/eccube-asset-cmd-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->root);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => $id);

        $this->assetContentService = new AssetContentService(new UserDataFileService(
            $this->root,
            ['css', 'js'],
            new Filesystem(),
            $translator
        ));
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->remove($this->root);
        }

        parent::tearDown();
    }

    public function testApplyCreatesFile(): void
    {
        $tester = $this->apply(['--type' => 'css', '--body' => 'body{}']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('created: css', $tester->getDisplay());
        $this->assertSame('body{}', file_get_contents($this->root.'/assets/css/customize.css'));
    }

    public function testApplyReadsBodyFromStdin(): void
    {
        $tester = $this->apply(['--type' => 'js', '--body' => '-'], ['console.log(1);']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('console.log(1);', trim((string) file_get_contents($this->root.'/assets/js/customize.js')));
    }

    public function testApplyIsIdempotent(): void
    {
        $this->apply(['--type' => 'css', '--body' => 'body{}']);
        $tester = $this->apply(['--type' => 'css', '--body' => 'body{}']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('unchanged: css', $tester->getDisplay());
    }

    public function testApplyRejectsUnknownType(): void
    {
        $tester = $this->apply(['--type' => 'scss', '--body' => 'x']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyRequiresBody(): void
    {
        $tester = $this->apply(['--type' => 'css']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyDryRunDoesNotWrite(): void
    {
        $tester = $this->apply(['--type' => 'css', '--body' => 'body{}', '--dry-run' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('dry-run', $tester->getDisplay());
        $this->assertFileDoesNotExist($this->root.'/assets/css/customize.css');
    }

    public function testShowOutputsBody(): void
    {
        $this->apply(['--type' => 'css', '--body' => 'body{}']);

        $tester = new CommandTester(new AssetShowCommand($this->assetContentService));
        $tester->execute(['--type' => 'css']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('body{}', $tester->getDisplay());
    }

    public function testShowOutputsEmptyStringWhenMissing(): void
    {
        $tester = new CommandTester(new AssetShowCommand($this->assetContentService));
        $tester->execute(['--type' => 'js']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('', $tester->getDisplay());
    }

    public function testShowOutputsJson(): void
    {
        $this->apply(['--type' => 'css', '--body' => 'body{}']);

        $tester = new CommandTester(new AssetShowCommand($this->assetContentService));
        $tester->execute(['--type' => 'css', '--format' => 'json']);

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame('css', $decoded['type']);
        $this->assertSame('body{}', $decoded['body']);
        $this->assertSame($this->root.'/assets/css/customize.css', $decoded['path']);
    }

    /**
     * @param array<string, mixed> $input
     * @param list<string>         $stdin
     */
    private function apply(array $input, array $stdin = []): CommandTester
    {
        $tester = new CommandTester(new AssetApplyCommand($this->assetContentService));
        if ([] !== $stdin) {
            $tester->setInputs($stdin);
        }
        $tester->execute($input);

        return $tester;
    }
}
