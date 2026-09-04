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

use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Exception\ContentValidationException;
use Eccube\Service\Content\ContentResult;
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\PageContentService;
use Eccube\Tests\EccubeTestCase;

final class PageContentServiceTest extends EccubeTestCase
{
    private ?PageContentService $pageContentService = null;

    /**
     * @var list<string>|null
     */
    private ?array $createdFiles = null;

    private ?string $url = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageContentService = self::getContainer()->get(PageContentService::class);
        $this->createdFiles = [];
        $this->url = 'test_page_'.bin2hex(random_bytes(4));
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

    public function testApplyCreatesPageAndTemplate(): void
    {
        $result = $this->apply(['name' => 'テストページ', 'body' => 'created body']);

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertNotNull($result->id);

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('テストページ', $Page->getName());
        $this->assertSame($this->url, $Page->getFileName(), 'ファイル名を省略した場合は URL を既定値にする');
        $this->assertSame('created body', file_get_contents((string) $result->path()));
    }

    public function testApplyIsIdempotent(): void
    {
        $this->apply(['name' => 'テストページ', 'body' => 'same body']);
        $result = $this->apply(['name' => 'テストページ', 'body' => 'same body']);

        $this->assertSame(ContentStatus::Unchanged, $result->status);
        $this->assertSame([], $result->writtenPaths, '変更が無い場合はファイルを書き換えない');
    }

    public function testApplyKeepsUnspecifiedValues(): void
    {
        $this->apply(['name' => 'テストページ', 'body' => 'first body', 'author' => '作者']);
        $result = $this->apply(['body' => 'second body']);

        $this->assertSame(ContentStatus::Updated, $result->status);

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('テストページ', $Page->getName());
        $this->assertSame('作者', $Page->getAuthor());
        $this->assertSame('second body', file_get_contents((string) $result->path()));
    }

    public function testApplyDryRunDoesNotWrite(): void
    {
        $created = $this->apply(['name' => 'テストページ', 'body' => 'body']);
        $path = (string) $created->path();

        $result = $this->apply(['body' => 'changed body'], true);

        $this->assertSame(ContentStatus::Updated, $result->status);
        $this->assertSame([], $result->writtenPaths);
        $this->assertArrayHasKey($path, $result->fileChanges);
        $this->assertSame('body', file_get_contents($path), 'dry-run はファイルを書き換えない');

        $this->entityManager->clear();
        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('テストページ', $Page->getName());
    }

    public function testApplyRejectsInvalidTwig(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->apply(['name' => 'テストページ', 'body' => '{% block foo %}']);
    }

    public function testApplyRejectsDuplicatedFileName(): void
    {
        $this->apply(['name' => 'テストページ', 'body' => 'body']);

        $other = 'test_page_'.bin2hex(random_bytes(4));

        try {
            $this->pageContentService->apply([
                'url' => $other,
                'name' => '別のページ',
                'file_name' => (string) $this->url,
                'body' => 'body',
            ]);
            self::fail('重複したファイル名は登録できない');
        } catch (ContentValidationException $e) {
            $this->assertNotSame([], $e->getErrors());
        }
    }

    public function testApplyRenamesTemplateFile(): void
    {
        $created = $this->apply(['name' => 'テストページ', 'body' => 'body']);
        $oldPath = (string) $created->path();

        $newFileName = $this->url.'_renamed';
        $result = $this->apply(['file_name' => $newFileName]);
        $this->createdFiles[] = (string) $result->path();

        $this->assertSame(ContentStatus::Updated, $result->status);
        $this->assertSame([$oldPath], $result->removedPaths, '旧ファイルを削除する');
        $this->assertFileDoesNotExist($oldPath);
        $this->assertSame('body', file_get_contents((string) $result->path()));
    }

    public function testApplyLinksLayout(): void
    {
        $Layout = $this->findLayout();

        $this->apply(['name' => 'テストページ', 'body' => 'body', 'pc_layout' => (string) $Layout->getId()]);

        // PageLayout は Page のコレクションへ追加せず永続化するため, 読み直して確認する
        $this->entityManager->clear();
        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame([$Layout->getId()], array_map(static fn (Layout $L): ?int => $L->getId(), $Page->getLayouts()));
    }

    public function testRemoveDeletesPageAndTemplate(): void
    {
        $created = $this->apply(['name' => 'テストページ', 'body' => 'body']);
        $path = (string) $created->path();

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);

        $result = $this->pageContentService->remove($Page);

        $this->assertSame(ContentStatus::Removed, $result->status);
        $this->assertFileDoesNotExist($path);
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByUrl((string) $this->url));
    }

    public function testRemoveRejectsDefaultPage(): void
    {
        $Page = $this->entityManager->getRepository(Page::class)->findOneBy(['edit_type' => Page::EDIT_TYPE_DEFAULT]);
        $this->assertInstanceOf(Page::class, $Page);

        $this->expectException(\LogicException::class);

        $this->pageContentService->remove($Page);
    }

    public function testApplyKeepsFileNameOfDefaultPage(): void
    {
        $Page = $this->entityManager->getRepository(Page::class)->findOneBy(['edit_type' => Page::EDIT_TYPE_DEFAULT]);
        $this->assertInstanceOf(Page::class, $Page);
        $fileName = (string) $Page->getFileName();
        $body = $this->pageContentService->readTemplate($Page);

        $result = $this->pageContentService->apply([
            'url' => (string) $Page->getUrl(),
            'file_name' => 'must_be_ignored',
            'body' => $body,
        ], true);

        $this->assertArrayNotHasKey('file_name', $result->fieldChanges, '既定ページのファイル名は変更できない');
        $this->assertSame($fileName, $Page->getFileName());
    }

    /**
     * @param array<string, string> $payload
     */
    private function apply(array $payload, bool $dryRun = false): ContentResult
    {
        /** @var array{url: string} $payload */
        $payload = ['url' => (string) $this->url] + $payload;
        $result = $this->pageContentService->apply($payload, $dryRun);

        foreach ($result->writtenPaths as $path) {
            $this->createdFiles[] = $path;
        }

        return $result;
    }

    private function findLayout(): Layout
    {
        $DeviceType = $this->entityManager->getRepository(DeviceType::class)->find(DeviceType::DEVICE_TYPE_PC);
        $Layout = $this->entityManager->getRepository(Layout::class)->findOneBy(['DeviceType' => $DeviceType], ['id' => 'DESC']);
        $this->assertInstanceOf(Layout::class, $Layout);

        return $Layout;
    }
}
