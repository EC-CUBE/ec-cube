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

use Eccube\Command\Content\ContentsExportCommand;
use Eccube\Command\Content\ContentsImportCommand;
use Eccube\Entity\Block;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Service\Content\BlockContentService;
use Eccube\Service\Content\ContentsArchive;
use Eccube\Service\Content\PageContentService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * eccube:contents:export / import.
 *
 * アーカイブはテンプレートの本文を持たないため, 取り込みでは
 * app/template 配下のファイルが増減しないことを併せて確認する.
 */
final class ContentsCommandTest extends EccubeTestCase
{
    private ?PageContentService $pageContentService = null;

    private ?BlockContentService $blockContentService = null;

    private ?Filesystem $filesystem = null;

    private ?string $dir = null;

    private ?string $route = null;

    /**
     * @var list<string>|null
     */
    private ?array $createdFiles = null;

    /**
     * tearDown で削除するページ (テストが途中で失敗しても DB に残さない).
     *
     * @var list<string>|null
     */
    private ?array $createdRoutes = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageContentService = self::getContainer()->get(PageContentService::class);
        $this->blockContentService = self::getContainer()->get(BlockContentService::class);
        $this->filesystem = new Filesystem();
        $this->dir = sys_get_temp_dir().'/eccube_contents_'.bin2hex(random_bytes(6));
        $this->route = 'test_contents_'.bin2hex(random_bytes(4));
        $this->createdFiles = [];
        $this->createdRoutes = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->createdRoutes ?? [] as $route) {
            $Page = $this->pageContentService->findByRoute($route);
            if ($Page instanceof Page && Page::EDIT_TYPE_USER === $Page->getEditType()) {
                $this->pageContentService->remove($Page);
            }
        }
        foreach ($this->createdFiles ?? [] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        $this->filesystem->remove((string) $this->dir);

        parent::tearDown();
    }

    public function testExportWritesSectionFiles(): void
    {
        $tester = $this->export();

        $this->assertSame(0, $tester->getStatusCode());
        foreach ([ContentsArchive::MANIFEST_FILE, 'pages.yaml', 'blocks.yaml', 'mail_templates.yaml', 'layouts.yaml'] as $file) {
            $this->assertFileExists($this->dir.'/'.$file);
        }

        $manifest = Yaml::parseFile($this->dir.'/'.ContentsArchive::MANIFEST_FILE);
        $this->assertSame(ContentsArchive::SCHEMA_VERSION, $manifest['schema']);
    }

    /**
     * 本文はリポジトリで管理する前提のため, アーカイブへ複製しない.
     */
    public function testExportDoesNotCopyTemplateBodies(): void
    {
        $this->export();

        foreach (['pages', 'blocks', 'mail_templates'] as $section) {
            foreach ((array) Yaml::parseFile($this->dir.'/'.$section.'.yaml') as $row) {
                $this->assertIsArray($row);
                $this->assertArrayNotHasKey('body', $row, $section.' に本文を持たない');
            }
        }

        // twig もアーカイブへ書き出さない
        $this->assertCount(0, iterator_to_array(Finder::create()->in((string) $this->dir)->files()->name('*.twig')));
    }

    /**
     * export -> import で全件 unchanged になり, 再 export がバイト単位で一致する.
     */
    public function testRoundTripIsIdempotent(): void
    {
        $this->export();
        $before = $this->snapshotArchive();
        $templates = $this->countAppTemplates();

        $tester = $this->import();

        $this->assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        $this->assertSame(0, $this->countChanged($tester), $tester->getDisplay());
        $this->assertSame($templates, $this->countAppTemplates(), '取り込みで app/template のファイルは増減しない');

        $this->filesystem->remove((string) $this->dir);
        $this->export();
        $this->assertSame($before, $this->snapshotArchive(), '再エクスポートの結果が一致する');
    }

    /**
     * リポジトリへコミット済みのテンプレートに対応するページを作れる.
     */
    public function testImportCreatesPageFromCommittedTemplate(): void
    {
        $this->export();
        $path = $this->eccubeConfig->get('eccube_theme_user_data_dir').'/'.$this->route.'.twig';
        file_put_contents($path, 'committed body');
        $this->createdFiles[] = $path;
        $this->createdRoutes[] = (string) $this->route;
        $this->appendPage(['route' => $this->route, 'name' => '取り込みテスト', 'file_name' => $this->route]);

        $tester = $this->import();

        $this->assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        $this->assertInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
        $this->assertSame('committed body', file_get_contents($path), 'コミット済みのテンプレートを書き換えない');
    }

    /**
     * 本文はアーカイブに含まれないため, テンプレートを解決できないレコードは取り込めない.
     * FormType の NotBlank だけでは原因が分からないので, 理由を添えて弾く.
     */
    public function testImportFailsWhenTemplateIsMissing(): void
    {
        $path = $this->eccubeConfig->get('eccube_theme_user_data_dir').'/'.$this->route.'.twig';
        $this->createdFiles[] = $path;
        $this->createdRoutes[] = (string) $this->route;
        $this->pageContentService->apply(['route' => (string) $this->route, 'name' => '取り込みテスト', 'body' => 'body']);
        $this->export();
        unlink($path);

        $tester = $this->import();

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('テンプレートが見つかりません', $tester->getDisplay());
    }

    /**
     * 取り込みの鍵はアーカイブのファイル名ではなく yaml の中身から取る.
     */
    public function testImportRejectsInvalidKey(): void
    {
        $this->export();
        $this->appendPage(['route' => '../../evil', 'name' => '不正', 'file_name' => '../../evil']);

        $tester = $this->import();

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('route が不正です', $tester->getDisplay());
        $this->assertFileDoesNotExist($this->eccubeConfig->get('kernel.project_dir').'/evil.twig');
    }

    public function testImportResolvesLayoutByName(): void
    {
        $Layout = $this->findLayout();
        $this->export();

        $rows = (array) Yaml::parseFile($this->dir.'/pages.yaml');
        $names = array_column($rows, 'pc_layout');
        $this->assertContains($Layout->getName(), $names, 'レイアウトは ID ではなく名前で参照する');
    }

    public function testImportFailsWhenLayoutIsMissing(): void
    {
        $this->export();
        $this->createdRoutes[] = (string) $this->route;
        $this->appendPage([
            'route' => $this->route,
            'name' => '取り込みテスト',
            'file_name' => $this->route,
            'pc_layout' => '存在しないレイアウト',
        ]);

        $tester = $this->import();

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('レイアウトが見つかりません', $tester->getDisplay());
    }

    /**
     * ブロックの配置場所は Layout::TARGET_ID_* と名前の間で往復する.
     */
    public function testLayoutSectionNamesRoundTrip(): void
    {
        $this->export();

        $sections = [];
        foreach ((array) Yaml::parseFile($this->dir.'/layouts.yaml') as $row) {
            foreach ($row['blocks'] as $block) {
                $sections[] = $block['section'];
            }
        }

        $this->assertNotEmpty($sections);
        foreach (array_unique($sections) as $name) {
            $id = ContentsArchive::blockSectionId((string) $name);
            $this->assertNotNull($id, $name);
            $this->assertSame($name, ContentsArchive::blockSectionName($id));
        }
    }

    public function testExportSkipsUserDataUnlessIncluded(): void
    {
        $this->export();
        $this->assertDirectoryDoesNotExist($this->dir.'/'.ContentsArchive::SECTION_USER_DATA);

        $this->filesystem->remove((string) $this->dir);
        $this->export(['--include' => ContentsArchive::SECTION_USER_DATA]);
        $this->assertDirectoryExists($this->dir.'/'.ContentsArchive::SECTION_USER_DATA);
    }

    /**
     * html/ はドキュメントルートのため, 配置できない拡張子は取り込まない.
     */
    public function testImportSkipsDisallowedUserDataExtension(): void
    {
        $this->export(['--include' => ContentsArchive::SECTION_USER_DATA]);
        file_put_contents($this->dir.'/'.ContentsArchive::SECTION_USER_DATA.'/evil.php', '<?php echo 1;');

        $tester = $this->import(['--include' => ContentsArchive::SECTION_USER_DATA]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('読み飛ばしました', $tester->getDisplay());
        $this->assertFileDoesNotExist($this->eccubeConfig->get('eccube_html_dir').'/user_data/evil.php');
    }

    public function testImportWarnsWhenTemplateCodeDiffers(): void
    {
        $this->export();
        $manifest = (array) Yaml::parseFile($this->dir.'/'.ContentsArchive::MANIFEST_FILE);
        $manifest['template_code'] = 'other_theme';
        file_put_contents($this->dir.'/'.ContentsArchive::MANIFEST_FILE, ContentsArchive::dump($manifest));

        $tester = $this->import();

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('テンプレートコードが異なります', $tester->getDisplay());
    }

    public function testImportRejectsUnknownSchema(): void
    {
        $this->export();
        file_put_contents(
            $this->dir.'/'.ContentsArchive::MANIFEST_FILE,
            ContentsArchive::dump(['schema' => 999, 'template_code' => 'default'])
        );

        $tester = $this->import();

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('書式版', $tester->getDisplay());
    }

    /**
     * 取り込めない行があると既定では中断し, --continue-on-error では最後まで進む.
     */
    public function testImportStopsOnFirstErrorUnlessContinueOnError(): void
    {
        $this->export();
        $this->appendPage(['route' => '../../evil', 'name' => '不正', 'file_name' => '../../evil']);
        $this->appendPage(['route' => '../../evil2', 'name' => '不正', 'file_name' => '../../evil2']);

        $stopped = $this->import();
        $this->assertSame(1, $stopped->getStatusCode());
        $this->assertStringNotContainsString('evil2', $stopped->getDisplay());

        $continued = $this->import(['--continue-on-error' => true]);
        $this->assertSame(1, $continued->getStatusCode());
        $this->assertStringContainsString('evil2', $continued->getDisplay());
    }

    /**
     * --prune は削除できるものだけを対象にし, --dry-run では削除しない.
     *
     * 削除できないもの (コアページ / deletable = false のブロック) も
     * アーカイブから外して, 対象外として素通りすることを確かめる.
     */
    public function testPruneRemovesOnlyRemovableEntries(): void
    {
        $path = $this->eccubeConfig->get('eccube_theme_user_data_dir').'/'.$this->route.'.twig';
        file_put_contents($path, 'body');
        $this->createdFiles[] = $path;
        $this->createdRoutes[] = (string) $this->route;
        $this->pageContentService->apply(['route' => (string) $this->route, 'name' => '削除対象', 'body' => 'body']);

        $CorePage = $this->findCorePage();
        $CoreBlock = $this->findCoreBlock();

        // アーカイブから外す = Git から消した状態
        $this->export();
        $this->removeRow('pages', 'route', (string) $this->route);
        $this->removeRow('pages', 'route', (string) $CorePage->getUrl());
        $this->removeRow('blocks', 'file_name', (string) $CoreBlock->getFileName());

        $dryRun = $this->import(['--prune' => true, '--dry-run' => true]);
        $this->assertSame(0, $dryRun->getStatusCode(), $dryRun->getDisplay());
        $this->assertStringContainsString((string) $this->route, $dryRun->getDisplay());
        $this->assertInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route), 'dry-run では削除しない');

        $applied = $this->import(['--prune' => true]);

        $this->assertSame(0, $applied->getStatusCode(), $applied->getDisplay());
        $this->assertNotInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
        $this->assertInstanceOf(
            Page::class,
            $this->pageContentService->findByRoute((string) $CorePage->getUrl()),
            'コアページはアーカイブから外れていても削除しない'
        );
        $this->assertInstanceOf(
            Block::class,
            $this->blockContentService->findByFileName(
                (string) $CoreBlock->getFileName(),
                $this->blockContentService->getDeviceType(DeviceType::DEVICE_TYPE_PC)
            ),
            '削除できないブロックはアーカイブから外れていても削除しない'
        );
    }

    public function testUnknownSectionIsRejected(): void
    {
        $tester = $this->export(['--only' => 'nope']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertStringContainsString('未知のセクションです', $tester->getDisplay());
    }

    /**
     * @param array<string, mixed> $input
     */
    private function export(array $input = []): CommandTester
    {
        $tester = new CommandTester(self::getContainer()->get(ContentsExportCommand::class));
        $tester->execute($input + ['--to' => $this->dir]);

        return $tester;
    }

    /**
     * @param array<string, mixed> $input
     */
    private function import(array $input = []): CommandTester
    {
        $tester = new CommandTester(self::getContainer()->get(ContentsImportCommand::class));
        $tester->execute($input + ['--from' => $this->dir, '--no-cache-clear' => true]);

        return $tester;
    }

    /**
     * pages.yaml へ 1 件足す.
     *
     * @param array<string, mixed> $row
     */
    private function appendPage(array $row): void
    {
        $path = $this->dir.'/pages.yaml';
        $rows = (array) Yaml::parseFile($path);
        $rows[] = $row;
        file_put_contents($path, ContentsArchive::dump(array_values($rows)));
    }

    /**
     * アーカイブから 1 件外す.
     */
    private function removeRow(string $section, string $key, string $value): void
    {
        $path = $this->dir.'/'.$section.'.yaml';
        $rows = array_values(array_filter(
            (array) Yaml::parseFile($path),
            static fn (array $row): bool => $value !== ($row[$key] ?? null)
        ));
        file_put_contents($path, ContentsArchive::dump($rows));
    }

    /**
     * @return array<string, string> パス => 内容
     */
    private function snapshotArchive(): array
    {
        $files = [];
        foreach (Finder::create()->in((string) $this->dir)->files()->sortByName() as $file) {
            $files[$file->getRelativePathname()] = (string) file_get_contents($file->getPathname());
        }

        return $files;
    }

    private function countChanged(CommandTester $tester): int
    {
        preg_match('/変更 (\d+) 件/', $tester->getDisplay(), $matches);

        return (int) ($matches[1] ?? -1);
    }

    private function countAppTemplates(): int
    {
        $dir = $this->eccubeConfig->get('eccube_theme_app_dir');

        return count(iterator_to_array(Finder::create()->in($dir)->files()->name('*.twig')));
    }

    private function findCorePage(): Page
    {
        $Page = $this->entityManager->getRepository(Page::class)
            ->findOneBy(['edit_type' => Page::EDIT_TYPE_DEFAULT], ['id' => 'ASC']);
        $this->assertInstanceOf(Page::class, $Page);

        return $Page;
    }

    private function findCoreBlock(): Block
    {
        $Block = $this->entityManager->getRepository(Block::class)
            ->findOneBy(['deletable' => false], ['id' => 'ASC']);
        $this->assertInstanceOf(Block::class, $Block);

        return $Block;
    }

    private function findLayout(): Layout
    {
        $DeviceType = $this->entityManager->getRepository(DeviceType::class)->find(DeviceType::DEVICE_TYPE_PC);
        $Layout = $this->entityManager->getRepository(Layout::class)
            ->findOneBy(['DeviceType' => $DeviceType, 'id' => Layout::DEFAULT_LAYOUT_UNDERLAYER_PAGE]);
        $this->assertInstanceOf(Layout::class, $Layout);

        return $Layout;
    }
}
