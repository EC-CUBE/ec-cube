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
use Eccube\Tests\EffectiveUserTrait;
use Eccube\Tests\TwigNamespacePathTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

final class PageCommandTest extends EccubeTestCase
{
    use EffectiveUserTrait;
    use TwigNamespacePathTrait;

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

    public function testApplyCreatesPage(): void
    {
        $tester = $this->apply([
            '--route' => $this->route,
            '--name' => 'テストページ',
            '--body' => 'created body',
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('created', $tester->getDisplay());

        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('created body', file_get_contents($this->pageContentService->getFilePath($Page)));
    }

    public function testApplyReadsBodyFromStdin(): void
    {
        $tester = $this->apply([
            '--route' => $this->route,
            '--name' => 'テストページ',
            '--body' => '-',
        ], ['body from stdin']);

        $this->assertSame(0, $tester->getStatusCode());

        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame('body from stdin', trim((string) file_get_contents($this->pageContentService->getFilePath($Page))));
    }

    public function testApplyDryRunDoesNotCreatePage(): void
    {
        $tester = $this->apply([
            '--route' => $this->route,
            '--name' => 'テストページ',
            '--body' => 'body',
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('dry-run', $tester->getDisplay());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
    }

    public function testApplyOutputsJson(): void
    {
        $tester = $this->apply([
            '--route' => $this->route,
            '--name' => 'テストページ',
            '--body' => 'body',
            '--format' => 'json',
        ]);

        $this->assertSame(0, $tester->getStatusCode());

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame('created', $decoded['status']);
        $this->assertSame($this->route, $decoded['identifier']);
    }

    public function testApplyReturnsInvalidWithoutRoute(): void
    {
        $tester = $this->apply(['--name' => 'テストページ']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyReturnsInvalidWithUnknownFormat(): void
    {
        $tester = $this->apply(['--route' => $this->route, '--format' => 'yaml']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyFailsWithInvalidTwig(): void
    {
        $tester = $this->apply([
            '--route' => $this->route,
            '--name' => 'テストページ',
            '--body' => '{% block foo %}',
        ]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
    }

    /**
     * テンプレートを書き出せない場合は, 生の例外ではなく対処方法を表示して終了する.
     *
     * 権限を分離した構成では実行ユーザーを誤ると書き込みだけが失敗するため,
     * 確認手段 (eccube:doctor:permissions) を案内する.
     */
    public function testApplyReportsWriteFailureWithGuidance(): void
    {
        $this->skipIfRoot();

        $Page = new Page();
        $Page->setEditType(Page::EDIT_TYPE_USER);
        $templateDir = $this->pageContentService->getTemplateDir($Page);
        $originalPerms = fileperms($templateDir) & 0777;

        chmod($templateDir, 0555);

        try {
            $tester = $this->apply([
                '--route' => $this->route,
                '--name' => 'テストページ',
                '--body' => 'body',
            ]);
        } finally {
            chmod($templateDir, $originalPerms);
        }

        $this->assertSame(1, $tester->getStatusCode());

        $display = $tester->getDisplay();
        $this->assertStringContainsString('ページを保存できません', $display);
        $this->assertStringContainsString('eccube:doctor:permissions', $display);

        $this->entityManager->clear();
        $this->assertNotInstanceOf(
            Page::class,
            $this->pageContentService->findByRoute((string) $this->route),
            '書き出せなかったページのレコードが残ってはいけない'
        );
    }

    /**
     * キャッシュ削除まで含めて実行する (トレイトの #[Required] 注入が効いていることの確認を兼ねる).
     */
    public function testApplyClearsCache(): void
    {
        $tester = new CommandTester(self::getContainer()->get(PageApplyCommand::class));
        $tester->execute([
            '--route' => $this->route,
            '--name' => 'テストページ',
            '--body' => 'body',
        ]);

        $Page = $this->pageContentService->findByRoute((string) $this->route);
        if ($Page instanceof Page) {
            $this->createdFiles[] = $this->pageContentService->getFilePath($Page);
        }

        $this->assertSame(0, $tester->getStatusCode());
    }

    /**
     * 実行時の cache pool を削除できない場合 (権限を分離した構成) は,
     * 本処理を完了させたうえで終了コード 3 と手動実行の案内を返す.
     */
    public function testApplyReturnsManualActionRequiredWhenCacheIsNotClearable(): void
    {
        $this->skipIfRoot();

        $poolDir = rtrim((string) self::getContainer()->getParameter('eccube_runtime_dir'), '/').'/pools';
        if (!is_dir($poolDir)) {
            mkdir($poolDir, 0775, true);
        }
        // 検証後に元へ戻す. 固定値で戻すと元の権限を書き換えたまま終わる
        $originalMode = fileperms($poolDir) & 0777;
        chmod($poolDir, 0555);

        if (is_writable($poolDir)) {
            chmod($poolDir, $originalMode);
            $this->markTestSkipped('ディレクトリを書き込み不可にできない環境');
        }

        try {
            $tester = new CommandTester(self::getContainer()->get(PageApplyCommand::class));
            $tester->execute([
                '--route' => $this->route,
                '--name' => 'テストページ',
                '--body' => 'body',
            ]);

            $Page = $this->pageContentService->findByRoute((string) $this->route);
            if ($Page instanceof Page) {
                $this->createdFiles[] = $this->pageContentService->getFilePath($Page);
            }

            $this->assertSame(3, $tester->getStatusCode());
            $this->assertInstanceOf(Page::class, $Page, '本処理は完了している');
            $this->assertStringContainsString('cache:pool:clear', $tester->getDisplay());
        } finally {
            chmod($poolDir, $originalMode);
        }
    }

    /**
     * 名前空間付きのページで上書き用ディレクトリが twig に登録されていない場合は, 本処理を完了させたうえで
     * 終了コード 3 と eccube:cache:build の案内を返す. 登録済み (コンテナの再構築後) なら 0 を返す.
     */
    public function testApplyReturnsManualActionRequiredUntilNamespaceDirIsRegistered(): void
    {
        $code = 'Rebuild'.bin2hex(random_bytes(4));
        $namespaceDir = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code;
        $this->assertDirectoryDoesNotExist($namespaceDir);
        $this->createPluginNamespacePage('@'.$code.'/'.$this->route);

        try {
            $tester = new CommandTester(self::getContainer()->get(PageApplyCommand::class));
            $tester->execute(['--route' => $this->route, '--body' => 'first']);

            $this->assertSame(3, $tester->getStatusCode());
            $this->assertFileExists($namespaceDir.'/'.$this->route.'.twig', '本処理は完了している');
            $this->assertStringContainsString('eccube:cache:build', $tester->getDisplay());

            $tester->execute(['--route' => $this->route, '--body' => 'second']);
            $this->assertSame(3, $tester->getStatusCode(), '再構築するまでは繰り返し案内する');

            // コンテナを作り直した後の状態
            $this->registerTwigNamespacePath($code, $namespaceDir);
            $tester->execute(['--route' => $this->route, '--body' => 'third']);
            $this->assertSame(0, $tester->getStatusCode(), '登録済みなら案内しない');
        } finally {
            (new Filesystem())->remove($namespaceDir);
        }
    }

    public function testShowOutputsTemplate(): void
    {
        $this->apply(['--route' => $this->route, '--name' => 'テストページ', '--body' => 'shown body']);

        $tester = new CommandTester(self::getContainer()->get(PageShowCommand::class));
        $tester->execute(['--route' => $this->route]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('shown body', $tester->getDisplay());
    }

    public function testShowReturnsErrorWhenNotFound(): void
    {
        $tester = new CommandTester(self::getContainer()->get(PageShowCommand::class));
        $tester->execute(['--route' => 'not_found_page']);

        $this->assertSame(1, $tester->getStatusCode());
    }

    public function testListContainsCreatedPage(): void
    {
        $this->apply(['--route' => $this->route, '--name' => 'テストページ', '--body' => 'body']);

        $tester = new CommandTester(self::getContainer()->get(PageListCommand::class));
        $tester->execute(['--format' => 'json']);

        $this->assertSame(0, $tester->getStatusCode());

        $routes = array_column((array) json_decode($tester->getDisplay(), true), 'route');
        $this->assertContains($this->route, $routes);
    }

    public function testRemoveDeletesPage(): void
    {
        $this->apply(['--route' => $this->route, '--name' => 'テストページ', '--body' => 'body']);

        $tester = new CommandTester(self::getContainer()->get(PageRemoveCommand::class));
        $tester->execute(['--route' => $this->route, '--force' => true, '--no-cache-clear' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
    }

    public function testRemoveIsAbortedWithoutConfirmation(): void
    {
        $this->apply(['--route' => $this->route, '--name' => 'テストページ', '--body' => 'body']);

        $tester = new CommandTester(self::getContainer()->get(PageRemoveCommand::class));
        $tester->setInputs(['no']);
        $tester->execute(['--route' => $this->route, '--no-cache-clear' => true]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
    }

    public function testRemoveRejectsDefaultPage(): void
    {
        $Page = $this->entityManager->getRepository(Page::class)->findOneBy(['edit_type' => Page::EDIT_TYPE_DEFAULT]);
        $this->assertInstanceOf(Page::class, $Page);

        $tester = new CommandTester(self::getContainer()->get(PageRemoveCommand::class));
        $tester->execute(['--route' => $Page->getUrl(), '--force' => true, '--no-cache-clear' => true]);

        $this->assertSame(1, $tester->getStatusCode());
    }

    /**
     * プラグインが dtb_page へ登録する既定ページ (ファイル名が twig の名前空間を指す) を作る.
     */
    private function createPluginNamespacePage(string $fileName): Page
    {
        $Page = (new Page())
            ->setName('プラグインのページ')
            ->setUrl((string) $this->route)
            ->setFileName($fileName)
            ->setEditType(Page::EDIT_TYPE_DEFAULT);
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        return $Page;
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

        $Page = $this->pageContentService->findByRoute((string) $this->route);
        if ($Page instanceof Page) {
            $this->createdFiles[] = $this->pageContentService->getFilePath($Page);
        }

        return $tester;
    }
}
