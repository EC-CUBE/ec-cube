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

namespace Eccube\Tests\Service\Content;

use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Exception\ContentValidationException;
use Eccube\Service\Content\BlockContentService;
use Eccube\Service\Content\ContentResult;
use Eccube\Service\Content\ContentStatus;
use Eccube\Tests\EccubeTestCase;

final class BlockContentServiceTest extends EccubeTestCase
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

    public function testApplyCreatesBlockAndTemplate(): void
    {
        $result = $this->apply(['name' => 'テストブロック', 'body' => '<p>created</p>']);

        $this->assertSame(ContentStatus::Created, $result->status);

        $Block = $this->findBlock();
        $this->assertInstanceOf(Block::class, $Block);
        $this->assertSame('テストブロック', $Block->getName());
        $this->assertTrue($Block->isDeletable());
        $this->assertSame('<p>created</p>', file_get_contents((string) $result->path()));
    }

    public function testApplyIsIdempotent(): void
    {
        $this->apply(['name' => 'テストブロック', 'body' => '<p>same</p>']);
        $result = $this->apply(['name' => 'テストブロック', 'body' => '<p>same</p>']);

        $this->assertSame(ContentStatus::Unchanged, $result->status);
        $this->assertSame([], $result->writtenPaths);
    }

    public function testApplyDryRunDoesNotWrite(): void
    {
        $created = $this->apply(['name' => 'テストブロック', 'body' => '<p>body</p>']);
        $path = (string) $created->path();

        $result = $this->apply(['body' => '<p>changed</p>'], true);

        $this->assertSame(ContentStatus::Updated, $result->status);
        $this->assertSame('<p>body</p>', file_get_contents($path));
    }

    public function testApplyRejectsInvalidTwig(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->apply(['name' => 'テストブロック', 'body' => '{% block foo %}']);
    }

    public function testRemoveDeletesBlockAndTemplate(): void
    {
        $created = $this->apply(['name' => 'テストブロック', 'body' => '<p>body</p>']);
        $path = (string) $created->path();

        $Block = $this->findBlock();
        $this->assertInstanceOf(Block::class, $Block);

        $result = $this->blockContentService->remove($Block);

        $this->assertSame(ContentStatus::Removed, $result->status);
        $this->assertFileDoesNotExist($path);
        $this->assertNotInstanceOf(Block::class, $this->findBlock());
    }

    public function testRemoveRejectsUndeletableBlock(): void
    {
        $created = $this->apply(['name' => 'テストブロック', 'body' => '<p>body</p>']);
        $this->createdFiles[] = (string) $created->path();

        $Block = $this->findBlock();
        $this->assertInstanceOf(Block::class, $Block);
        $Block->setDeletable(false);

        $this->expectException(\LogicException::class);

        $this->blockContentService->remove($Block);
    }

    /**
     * @param array<string, string> $payload
     */
    /**
     * コアブロックの名前だけを更新しても app/template に写しを作らない
     * (PageContentServiceTest::testApplyDoesNotShadowCoreTemplate と同じ理由).
     */
    public function testApplyDoesNotShadowCoreTemplate(): void
    {
        $Block = $this->findCoreBlockWithoutOverride();
        $fileName = $Block->getFileName();
        $filePath = $this->blockContentService->getFilePath($Block);
        $original = $Block->getName();
        // 万一書き出された場合にリポジトリへ残さない
        $this->createdFiles[] = $filePath;

        try {
            $result = $this->blockContentService->apply(['file_name' => $fileName, 'name' => $original.' (改)']);

            $this->assertSame(ContentStatus::Updated, $result->status);
            $this->assertSame([], $result->writtenPaths);
            $this->assertFileDoesNotExist($filePath, '名前だけの更新では app/template に写しを作らない');
        } finally {
            $this->blockContentService->apply(['file_name' => $fileName, 'name' => $original]);
        }
    }

    /**
     * 本文を指定しない新規登録は, 配置先に既にあるテンプレートを使う
     * (PageContentServiceTest::testApplyUsesExistingTemplateWhenBodyIsNotSpecified と同じ).
     */
    public function testApplyUsesExistingTemplateWhenBodyIsNotSpecified(): void
    {
        $path = $this->blockContentService->getTemplateDir().'/'.$this->fileName.'.twig';
        file_put_contents($path, '<p>committed</p>');
        $this->createdFiles[] = $path;

        $result = $this->apply(['name' => 'テストブロック']);

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertSame([], $result->writtenPaths, '既にあるテンプレートは書き換えない');
        $this->assertSame('<p>committed</p>', file_get_contents($path));
        $this->assertInstanceOf(Block::class, $this->findBlock());
    }

    /**
     * app/template へ上書きしておらず, コアのテンプレートを解決できるブロック.
     */
    private function findCoreBlockWithoutOverride(): Block
    {
        $DeviceType = $this->blockContentService->getDeviceType();
        /** @var list<Block> $Blocks */
        $Blocks = $this->entityManager->getRepository(Block::class)
            ->findBy(['DeviceType' => $DeviceType, 'deletable' => false], ['id' => 'ASC']);
        foreach ($Blocks as $Block) {
            if (!is_file($this->blockContentService->getFilePath($Block))
                && '' !== $this->blockContentService->readTemplate($Block)) {
                return $Block;
            }
        }

        self::fail('app/template へ上書きしていないコアブロックが見つかりません.');
    }

    private function apply(array $payload, bool $dryRun = false): ContentResult
    {
        /** @var array{file_name: string} $payload */
        $payload = ['file_name' => (string) $this->fileName] + $payload;
        $result = $this->blockContentService->apply($payload, $dryRun);

        foreach ($result->writtenPaths as $path) {
            $this->createdFiles[] = $path;
        }

        return $result;
    }

    private function findBlock(): ?Block
    {
        return $this->blockContentService->findByFileName(
            (string) $this->fileName,
            $this->blockContentService->getDeviceType(DeviceType::DEVICE_TYPE_PC)
        );
    }
}
