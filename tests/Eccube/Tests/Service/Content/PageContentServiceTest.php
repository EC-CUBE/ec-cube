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
use Eccube\Exception\ContentWriteException;
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

    private ?string $route = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageContentService = self::getContainer()->get(PageContentService::class);
        $this->createdFiles = [];
        $this->route = 'test_page_'.bin2hex(random_bytes(4));
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

        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('テストページ', $Page->getName());
        $this->assertSame($this->route, $Page->getFileName(), 'ファイル名を省略した場合はルーティング名を既定値にする');
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

        $Page = $this->pageContentService->findByRoute((string) $this->route);
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
        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('テストページ', $Page->getName());
    }

    public function testApplyRejectsInvalidTwig(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->apply(['name' => 'テストページ', 'body' => '{% block foo %}']);
    }

    /**
     * テンプレートを書き出せない場合は DB もロールバックする.
     *
     * 先にコミットするとレコードだけが残り, テンプレートの無いページとして
     * フロントの表示が 500 になる. 権限を分離した構成では書き出しだけが失敗し得る.
     */
    public function testApplyRollsBackWhenTemplateIsNotWritable(): void
    {
        if (0 === getmyuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        $Page = new Page();
        $Page->setEditType(Page::EDIT_TYPE_USER);
        $templateDir = $this->pageContentService->getTemplateDir($Page);
        $originalPerms = fileperms($templateDir) & 0777;

        chmod($templateDir, 0555);

        try {
            $this->apply(['name' => 'テストページ', 'body' => 'body']);
            self::fail('書き込めない場合は ContentWriteException を投げる');
        } catch (ContentWriteException $e) {
            $this->assertStringContainsString((string) $this->route, $e->getPath());
        } finally {
            chmod($templateDir, $originalPerms);
        }

        $this->entityManager->clear();

        $this->assertNotInstanceOf(
            Page::class,
            $this->pageContentService->findByRoute((string) $this->route),
            'テンプレートを書き出せなかったページのレコードが残ってはいけない'
        );
    }

    public function testApplyRejectsDuplicatedFileName(): void
    {
        $this->apply(['name' => 'テストページ', 'body' => 'body']);

        $other = 'test_page_'.bin2hex(random_bytes(4));

        try {
            $this->pageContentService->apply([
                'route' => $other,
                'name' => '別のページ',
                'file_name' => (string) $this->route,
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

        $newFileName = $this->route.'_renamed';
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
        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame([$Layout->getId()], array_map(static fn (Layout $L): ?int => $L->getId(), $Page->getLayouts()));
    }

    public function testRemoveDeletesPageAndTemplate(): void
    {
        $created = $this->apply(['name' => 'テストページ', 'body' => 'body']);
        $path = (string) $created->path();

        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);

        $result = $this->pageContentService->remove($Page);

        $this->assertSame(ContentStatus::Removed, $result->status);
        $this->assertFileDoesNotExist($path);
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
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
            'route' => (string) $Page->getUrl(),
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
        /** @var array{route: string} $payload */
        $payload = ['route' => (string) $this->route] + $payload;
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
