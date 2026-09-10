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

use Eccube\Command\Content\BlockApplyCommand;
use Eccube\Command\Content\BlockListCommand;
use Eccube\Command\Content\BlockRemoveCommand;
use Eccube\Command\Content\BlockShowCommand;
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Service\Content\BlockContentService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class BlockCommandTest extends EccubeTestCase
{
    private ?BlockContentService $blockContentService = null;

    /**
     * @var list<string>|null
     */
    private ?array $createdFiles = null;

    private ?string $fileName = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->blockContentService = self::getContainer()->get(BlockContentService::class);
        $this->createdFiles = [];
        $this->fileName = 'test_block_'.bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles ?? [] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function testApplyCreatesBlock(): void
    {
        $tester = $this->apply([
            '--file-name' => $this->fileName,
            '--name' => 'テストブロック',
            '--body' => '<p>created</p>',
        ]);

        $this->assertSame(0, $tester->getStatusCode());

        $Block = $this->findBlock();
        $this->assertInstanceOf(Block::class, $Block);
        $this->assertSame('<p>created</p>', file_get_contents($this->blockContentService->getFilePath($Block)));
    }

    public function testApplyReturnsInvalidWithoutFileName(): void
    {
        $tester = $this->apply(['--name' => 'テストブロック']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyReturnsInvalidWithUnknownDeviceType(): void
    {
        $tester = $this->apply([
            '--file-name' => $this->fileName,
            '--name' => 'テストブロック',
            '--body' => '<p>x</p>',
            '--device-type' => '9999',
        ]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testShowOutputsTemplate(): void
    {
        $this->apply(['--file-name' => $this->fileName, '--name' => 'テストブロック', '--body' => '<p>shown</p>']);

        $tester = new CommandTester(self::getContainer()->get(BlockShowCommand::class));
        $tester->execute(['--file-name' => $this->fileName]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('<p>shown</p>', $tester->getDisplay());
    }

    public function testListContainsCreatedBlock(): void
    {
        $this->apply(['--file-name' => $this->fileName, '--name' => 'テストブロック', '--body' => '<p>x</p>']);

        $tester = new CommandTester(self::getContainer()->get(BlockListCommand::class));
        $tester->execute(['--format' => 'json']);

        $this->assertSame(0, $tester->getStatusCode());

        $fileNames = array_column((array) json_decode($tester->getDisplay(), true), 'file_name');
        $this->assertContains($this->fileName, $fileNames);
    }

    public function testRemoveDeletesBlock(): void
    {
        $this->apply(['--file-name' => $this->fileName, '--name' => 'テストブロック', '--body' => '<p>x</p>']);

        $tester = new CommandTester(self::getContainer()->get(BlockRemoveCommand::class));
        $tester->execute(['--file-name' => $this->fileName, '--force' => true, '--no-cache-clear' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertNotInstanceOf(Block::class, $this->findBlock());
    }

    /**
     * @param array<string, mixed> $input
     */
    private function apply(array $input): CommandTester
    {
        $tester = new CommandTester(self::getContainer()->get(BlockApplyCommand::class));
        $tester->execute($input + ['--no-cache-clear' => true]);

        $Block = $this->findBlock();
        if ($Block instanceof Block) {
            $this->createdFiles[] = $this->blockContentService->getFilePath($Block);
        }

        return $tester;
    }

    private function findBlock(): ?Block
    {
        return $this->blockContentService->findByFileName(
            (string) $this->fileName,
            $this->blockContentService->getDeviceType(DeviceType::DEVICE_TYPE_PC)
        );
    }
}
