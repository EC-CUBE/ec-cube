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

use Eccube\Command\Content\PageApplyCommand;
use Eccube\Command\Content\PageListCommand;
use Eccube\Command\Content\PageRemoveCommand;
use Eccube\Command\Content\PageShowCommand;
use Eccube\Entity\Page;
use Eccube\Service\Content\PageContentService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class PageCommandTest extends EccubeTestCase
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

    public function testApplyCreatesPage(): void
    {
        $tester = $this->apply([
            '--url' => $this->url,
            '--name' => 'テストページ',
            '--body' => 'created body',
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('created', $tester->getDisplay());

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('created body', file_get_contents($this->pageContentService->getFilePath($Page)));
    }

    public function testApplyReadsBodyFromStdin(): void
    {
        $tester = $this->apply([
            '--url' => $this->url,
            '--name' => 'テストページ',
            '--body' => '-',
        ], ['body from stdin']);

        $this->assertSame(0, $tester->getStatusCode());

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('body from stdin', trim((string) file_get_contents($this->pageContentService->getFilePath($Page))));
    }

    public function testApplyDryRunDoesNotCreatePage(): void
    {
        $tester = $this->apply([
            '--url' => $this->url,
            '--name' => 'テストページ',
            '--body' => 'body',
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('dry-run', $tester->getDisplay());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByUrl((string) $this->url));
    }

    public function testApplyOutputsJson(): void
    {
        $tester = $this->apply([
            '--url' => $this->url,
            '--name' => 'テストページ',
            '--body' => 'body',
            '--format' => 'json',
        ]);

        $this->assertSame(0, $tester->getStatusCode());

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame('created', $decoded['status']);
        $this->assertSame($this->url, $decoded['identifier']);
    }

    public function testApplyReturnsInvalidWithoutUrl(): void
    {
        $tester = $this->apply(['--name' => 'テストページ']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyReturnsInvalidWithUnknownFormat(): void
    {
        $tester = $this->apply(['--url' => $this->url, '--format' => 'yaml']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyFailsWithInvalidTwig(): void
    {
        $tester = $this->apply([
            '--url' => $this->url,
            '--name' => 'テストページ',
            '--body' => '{% block foo %}',
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByUrl((string) $this->url));
    }

    /**
     * キャッシュ削除まで含めて実行する (トレイトの #[Required] 注入が効いていることの確認を兼ねる).
     */
    public function testApplyClearsCache(): void
    {
        $tester = new CommandTester(self::getContainer()->get(PageApplyCommand::class));
        $tester->execute([
            '--url' => $this->url,
            '--name' => 'テストページ',
            '--body' => 'body',
        ]);

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        if ($Page instanceof Page) {
            $this->createdFiles[] = $this->pageContentService->getFilePath($Page);
        }

        $this->assertSame(0, $tester->getStatusCode());
    }

    public function testShowOutputsTemplate(): void
    {
        $this->apply(['--url' => $this->url, '--name' => 'テストページ', '--body' => 'shown body']);

        $tester = new CommandTester(self::getContainer()->get(PageShowCommand::class));
        $tester->execute(['--url' => $this->url]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('shown body', $tester->getDisplay());
    }

    public function testShowReturnsErrorWhenNotFound(): void
    {
        $tester = new CommandTester(self::getContainer()->get(PageShowCommand::class));
        $tester->execute(['--url' => 'not_found_page']);

        $this->assertSame(1, $tester->getStatusCode());
    }

    public function testListContainsCreatedPage(): void
    {
        $this->apply(['--url' => $this->url, '--name' => 'テストページ', '--body' => 'body']);

        $tester = new CommandTester(self::getContainer()->get(PageListCommand::class));
        $tester->execute(['--format' => 'json']);

        $this->assertSame(0, $tester->getStatusCode());

        $urls = array_column((array) json_decode($tester->getDisplay(), true), 'url');
        $this->assertContains($this->url, $urls);
    }

    public function testRemoveDeletesPage(): void
    {
        $this->apply(['--url' => $this->url, '--name' => 'テストページ', '--body' => 'body']);

        $tester = new CommandTester(self::getContainer()->get(PageRemoveCommand::class));
        $tester->execute(['--url' => $this->url, '--force' => true, '--no-cache-clear' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByUrl((string) $this->url));
    }

    public function testRemoveIsAbortedWithoutConfirmation(): void
    {
        $this->apply(['--url' => $this->url, '--name' => 'テストページ', '--body' => 'body']);

        $tester = new CommandTester(self::getContainer()->get(PageRemoveCommand::class));
        $tester->setInputs(['no']);
        $tester->execute(['--url' => $this->url, '--no-cache-clear' => true]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertInstanceOf(Page::class, $this->pageContentService->findByUrl((string) $this->url));
    }

    public function testRemoveRejectsDefaultPage(): void
    {
        $Page = $this->entityManager->getRepository(Page::class)->findOneBy(['edit_type' => Page::EDIT_TYPE_DEFAULT]);
        $this->assertInstanceOf(Page::class, $Page);

        $tester = new CommandTester(self::getContainer()->get(PageRemoveCommand::class));
        $tester->execute(['--url' => $Page->getUrl(), '--force' => true, '--no-cache-clear' => true]);

        $this->assertSame(1, $tester->getStatusCode());
    }

    /**
     * @param array<string, mixed> $input
     * @param list<string>         $stdin
     */
    private function apply(array $input, array $stdin = []): CommandTester
    {
        $tester = new CommandTester(self::getContainer()->get(PageApplyCommand::class));
        if ([] !== $stdin) {
            $tester->setInputs($stdin);
        }
        $tester->execute($input + ['--no-cache-clear' => true]);

        $Page = $this->pageContentService->findByUrl((string) $this->url);
        if ($Page instanceof Page) {
            $this->createdFiles[] = $this->pageContentService->getFilePath($Page);
        }

        return $tester;
    }
}
