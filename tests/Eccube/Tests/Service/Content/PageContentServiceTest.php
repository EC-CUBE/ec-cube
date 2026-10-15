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
use Eccube\Tests\EffectiveUserTrait;
use Eccube\Tests\TwigNamespacePathTrait;
use Symfony\Component\Filesystem\Filesystem;

final class PageContentServiceTest extends EccubeTestCase
{
    use EffectiveUserTrait;
    use TwigNamespacePathTrait;

    private ?PageContentService $pageContentService = null;

    /**
     * @var list<string>|null
     */
    private ?array $createdFiles = null;

    /**
     * @var list<string>|null テストで作成したディレクトリ (中身ごと消す)
     */
    private ?array $createdDirs = null;

    private ?string $route = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageContentService = self::getContainer()->get(PageContentService::class);
        $this->createdFiles = [];
        $this->createdDirs = [];
        $this->route = 'test_page_'.bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles ?? [] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        (new Filesystem())->remove($this->createdDirs ?? []);

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
        $this->skipIfRoot();

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

    /**
     * 両方のレイアウトを外す操作で, 変更後のスナップショットが現在の値を読み直さないこと.
     *
     * 読み直すと差分が出ず Unchanged で早期 return し, レイアウトが外れないまま終わる.
     */
    public function testApplyUnlinksBothLayouts(): void
    {
        $Layout = $this->findLayout();
        $this->apply(['name' => 'テストページ', 'body' => 'body', 'pc_layout' => (string) $Layout->getId()]);

        // PageLayout は Page のコレクションへ追加せず永続化するため, CLI の 2 回目の実行と
        // 同じ状態 (DB から読み直したエンティティ) にしてから解除する
        $this->entityManager->clear();

        $result = $this->apply(['pc_layout' => '', 'sp_layout' => '']);

        $this->assertSame(ContentStatus::Updated, $result->status, 'レイアウトの解除は変更として扱う');

        $this->entityManager->clear();
        $Page = $this->pageContentService->findByRoute((string) $this->route);
        $this->assertInstanceOf(Page::class, $Page);
        $this->assertSame([], $Page->getLayouts());
    }

    /**
     * DB の削除に失敗したとき, 退避中に書き出された別の内容を上書きしない.
     *
     * 退避 -> DB 削除 の間に同じパスへ書き出しがあると, 上書き付きで復元すればその更新を失う.
     * 競合として扱い, 退避ファイルを残して手動で復旧できる状態にする.
     */
    public function testRemoveDoesNotOverwriteConcurrentWriteOnRestore(): void
    {
        $created = $this->apply(['name' => 'テストページ', 'body' => 'original']);
        $filePath = (string) $created->path();
        $this->createdFiles[] = $filePath;

        // removeTemplatesAround() は private のため, 退避 -> 失敗 -> 復元 の経路を直接呼び出す
        $method = new \ReflectionMethod($this->pageContentService, 'removeTemplatesAround');
        $commit = function () use ($filePath): void {
            // 退避が済んだこの時点で, 別の処理が同じパスへ書き出した状況を作る
            file_put_contents($filePath, 'written by another process');

            throw new \RuntimeException('DB の削除に失敗');
        };

        try {
            $method->invoke($this->pageContentService, [$filePath], $commit);
            self::fail('$commit の例外はそのまま伝播する');
        } catch (\RuntimeException $e) {
            $this->assertSame('DB の削除に失敗', $e->getMessage());
        }

        $this->assertSame(
            'written by another process',
            file_get_contents($filePath),
            '復元で他の処理の書き込みを上書きしない'
        );

        $staged = glob(dirname($filePath).'/'.basename($filePath).'.removing-*') ?: [];
        foreach ($staged as $path) {
            $this->createdFiles[] = $path;
        }
        $this->assertCount(1, $staged, '復元できなかった退避ファイルは残す');
        $this->assertSame('original', file_get_contents($staged[0]));
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
     * コアページのメタ情報だけを更新しても app/template に写しを作らない.
     *
     * twig の探索は app/template を src/Eccube/Resource/template より優先するため,
     * 内容が同じ写しを置くと upstream のテンプレート修正 (脆弱性パッチを含む) が
     * 画面へ反映されなくなる.
     */
    public function testApplyDoesNotShadowCoreTemplate(): void
    {
        $Page = $this->findDefaultPageWithoutOverride();
        $route = (string) $Page->getUrl();
        $filePath = $this->pageContentService->getFilePath($Page);
        $original = (string) $Page->getMetaRobots();
        // 万一書き出された場合にリポジトリへ残さない
        $this->createdFiles[] = $filePath;

        try {
            $result = $this->pageContentService->apply(['route' => $route, 'meta_robots' => 'noindex']);

            $this->assertSame(ContentStatus::Updated, $result->status);
            $this->assertSame([], $result->writtenPaths);
            $this->assertFileDoesNotExist($filePath, 'メタ情報だけの更新では app/template に写しを作らない');
        } finally {
            $this->pageContentService->apply(['route' => $route, 'meta_robots' => $original]);
        }
    }

    public function testApplyDoesNotRewriteTemplateWhenBodyIsUnchanged(): void
    {
        $created = $this->apply(['name' => 'テストページ', 'body' => 'body']);
        $path = (string) $created->path();

        $result = $this->apply(['author' => '作者']);

        $this->assertSame(ContentStatus::Updated, $result->status);
        $this->assertSame([], $result->writtenPaths, '本文が変わらなければテンプレートを書き換えない');
        $this->assertSame('body', file_get_contents($path));
    }

    /**
     * 本文を指定しない新規登録は, 配置先に既にあるテンプレートを使う.
     *
     * リポジトリへコミット済みのテンプレートに対応するレコードを作る操作
     * (eccube:contents:import) がこれに当たる.
     */
    public function testApplyUsesExistingTemplateWhenBodyIsNotSpecified(): void
    {
        $path = $this->eccubeConfig->get('eccube_theme_user_data_dir').'/'.$this->route.'.twig';
        file_put_contents($path, 'committed body');
        $this->createdFiles[] = $path;

        $result = $this->apply(['name' => 'テストページ']);

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertSame([], $result->writtenPaths, '既にあるテンプレートは書き換えない');
        $this->assertSame('committed body', file_get_contents($path));
        $this->assertInstanceOf(Page::class, $this->pageContentService->findByRoute((string) $this->route));
    }

    /**
     * プラグインの名前空間を指す既定ページは, プラグイン同梱のテンプレートではなく
     * 上書き用の app/template/plugin へ置く.
     *
     * @see \Eccube\DependencyInjection\EccubeExtension::configureTwigPaths()
     */
    public function testGetTemplateDirForPluginNamespacePage(): void
    {
        $Page = (new Page())->setFileName('@Boomerang/example')->setEditType(Page::EDIT_TYPE_DEFAULT);

        $this->assertTrue($this->pageContentService->isPluginNamespacePage($Page));
        $this->assertSame(
            $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin',
            $this->pageContentService->getTemplateDir($Page)
        );
    }

    /**
     * "@" は twig の記法でディレクトリ名には含まれない. 付けたまま書き出すと
     * EccubeExtension が登録するパス (app/template/plugin/Boomerang) と一致せず,
     * 保存しても画面へ反映されない.
     */
    public function testGetFilePathStripsTwigNamespacePrefix(): void
    {
        $Page = (new Page())->setFileName('@Boomerang/example')->setEditType(Page::EDIT_TYPE_DEFAULT);

        $this->assertSame(
            $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/Boomerang/example.twig',
            $this->pageContentService->getFilePath($Page)
        );
    }

    /**
     * 名前空間を持たないページの配置先は変えない.
     */
    public function testGetTemplateDirForNormalPageIsUnchanged(): void
    {
        $UserPage = (new Page())->setFileName('user_data/example')->setEditType(Page::EDIT_TYPE_USER);
        $this->assertFalse($this->pageContentService->isPluginNamespacePage($UserPage));
        $this->assertSame(
            $this->eccubeConfig->get('eccube_theme_user_data_dir'),
            $this->pageContentService->getTemplateDir($UserPage)
        );

        $DefaultPage = (new Page())->setFileName('Product/list')->setEditType(Page::EDIT_TYPE_DEFAULT);
        $this->assertFalse($this->pageContentService->isPluginNamespacePage($DefaultPage));
        $this->assertSame(
            $this->eccubeConfig->get('eccube_theme_front_dir').'/Product/list.twig',
            $this->pageContentService->getFilePath($DefaultPage)
        );
    }

    /**
     * ユーザーが作成したページは "@" で始まっても名前空間として扱わない.
     * フロント (UserDataController) は "@user_data/@Boomerang/example.twig" を描画するため,
     * 配置先は app/template/user_data/@Boomerang/example.twig になる.
     */
    public function testGetFilePathKeepsAtSignForUserPage(): void
    {
        $Page = (new Page())->setFileName('@Boomerang/example')->setEditType(Page::EDIT_TYPE_USER);

        $this->assertFalse($this->pageContentService->isPluginNamespacePage($Page));
        $this->assertSame(
            $this->eccubeConfig->get('eccube_theme_user_data_dir').'/@Boomerang/example.twig',
            $this->pageContentService->getFilePath($Page)
        );
    }

    /**
     * "@" で始まるユーザーのページのファイル名を変更したとき, 旧ファイルは user_data の配下から消す.
     *
     * 名前空間として扱うと app/template/plugin/[code] を探し, 同じ名前の既定ページの
     * 上書きファイルを巻き込んで消しかねない.
     */
    public function testApplyRemovesPreviousTemplateFromItsOwnDir(): void
    {
        $userDataDir = (string) $this->eccubeConfig->get('eccube_theme_user_data_dir');
        $code = 'Boomerang'.bin2hex(random_bytes(4));
        $this->createdDirs[] = $userDataDir.'/@'.$code;
        $this->createdDirs[] = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code;

        // プラグインが DB へ直接登録した, "@" で始まるファイル名を持つユーザーのページ
        $this->createPluginNamespacePage('@'.$code.'/'.$this->route, Page::EDIT_TYPE_USER);
        $previousPath = $userDataDir.'/@'.$code.'/'.$this->route.'.twig';
        (new Filesystem())->dumpFile($previousPath, 'before');

        // 巻き添えを検知するため, 同じ名前の既定ページの上書きファイルを置いておく
        $decoyPath = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code.'/'.$this->route.'.twig';
        (new Filesystem())->dumpFile($decoyPath, 'decoy');

        // ファイル名だけ "@" の無いものへ変更する
        $movedPath = $userDataDir.'/'.$this->route.'.twig';
        $this->apply(['name' => '移動後', 'file_name' => $this->route, 'body' => 'after']);

        $this->assertFileExists($movedPath, '新しいファイル名で書き出す');
        $this->assertFileDoesNotExist($previousPath, '旧ファイルは user_data の配下から消す');
        $this->assertSame('decoy', file_get_contents($decoyPath), '既定ページの上書きファイルを巻き込まない');
    }

    /**
     * twig の名前空間に app/template/plugin/[code] が登録されるのは, コンテナのコンパイル時に
     * そのディレクトリが有った場合だけ. 登録されるまでは, コンテナを作り直さないと
     * フロントへ反映されないことを呼び出し側へ伝え続ける (案内を見落としても次の保存で再び伝える).
     *
     * @see \Eccube\DependencyInjection\EccubeExtension::configureTwigPaths()
     */
    public function testSaveRequiresContainerRebuildUntilNamespaceDirIsRegistered(): void
    {
        $code = 'Rebuild'.bin2hex(random_bytes(4));
        $namespaceDir = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code;
        $this->assertDirectoryDoesNotExist($namespaceDir);
        $this->createdDirs[] = $namespaceDir;
        $this->createPluginNamespacePage('@'.$code.'/'.$this->route, Page::EDIT_TYPE_DEFAULT);
        $this->pageContentService = $this->createPageContentServiceWithEnabledPlugins($code);

        $first = $this->apply(['body' => 'first']);
        $this->assertSame([$namespaceDir.'/'.$this->route.'.twig'], $first->writtenPaths);
        $this->assertTrue($first->containerRebuildRequired, '上書き用ディレクトリが登録されていなければ再構築が要る');

        $second = $this->apply(['body' => 'second']);
        $this->assertTrue($second->containerRebuildRequired, '再構築するまでは, 次の保存でも再構築が要る');

        // コンテナを作り直した後の状態
        $this->registerTwigNamespacePath($code, $namespaceDir);
        $third = $this->apply(['body' => 'third']);
        $this->assertSame([$namespaceDir.'/'.$this->route.'.twig'], $third->writtenPaths);
        $this->assertFalse($third->containerRebuildRequired, '登録済みなら再構築は要らない');
    }

    /**
     * 内容が変わらず保存を省いた (Unchanged) 場合も, 再構築が要るかを返す.
     * CLI で同じ内容の apply を再実行したときに, 再構築するまで案内し続けるため.
     */
    public function testApplyUnchangedStillRequiresContainerRebuild(): void
    {
        $code = 'Rebuild'.bin2hex(random_bytes(4));
        $namespaceDir = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code;
        $this->createdDirs[] = $namespaceDir;
        $this->createPluginNamespacePage('@'.$code.'/'.$this->route, Page::EDIT_TYPE_DEFAULT);
        $this->pageContentService = $this->createPageContentServiceWithEnabledPlugins($code);

        $this->assertTrue($this->apply(['body' => 'same'])->containerRebuildRequired);

        $again = $this->apply(['body' => 'same']);
        $this->assertSame(ContentStatus::Unchanged, $again->status);
        $this->assertTrue($again->containerRebuildRequired, '内容が同じでも, 登録されるまでは再構築が要る');

        // コンテナを作り直した後の状態
        $this->registerTwigNamespacePath($code, $namespaceDir);
        $registered = $this->apply(['body' => 'same']);
        $this->assertSame(ContentStatus::Unchanged, $registered->status);
        $this->assertFalse($registered->containerRebuildRequired);
    }

    /**
     * 無効なプラグインの名前空間は, コンテナを作り直しても twig に登録されないため, 再構築を求めない.
     * 求めると, 案内に従っても解消せず, 保存のたびに再構築 (管理画面では cache:clear) が走る.
     *
     * @see \Eccube\DependencyInjection\EccubeExtension::configureTwigPaths()
     */
    public function testSaveDoesNotRequireContainerRebuildForDisabledPlugin(): void
    {
        $code = 'Disabled'.bin2hex(random_bytes(4));
        $this->assertNotContains($code, $this->eccubeConfig->get('eccube.plugins.enabled'));
        $namespaceDir = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code;
        $this->createdDirs[] = $namespaceDir;
        $this->createPluginNamespacePage('@'.$code.'/'.$this->route, Page::EDIT_TYPE_DEFAULT);

        $result = $this->apply(['body' => 'body']);

        $this->assertSame([$namespaceDir.'/'.$this->route.'.twig'], $result->writtenPaths, '上書き用ディレクトリはある');
        $this->assertFalse($result->containerRebuildRequired);
    }

    /**
     * 上書きしていない (上書き用ディレクトリが無い) 既定ページは, 同梱のテンプレートを表示するのが正しく,
     * メタ情報だけを保存しても再構築を求めない.
     */
    public function testSaveDoesNotRequireContainerRebuildWithoutOverride(): void
    {
        $code = 'Rebuild'.bin2hex(random_bytes(4));
        $this->createPluginNamespacePage('@'.$code.'/'.$this->route, Page::EDIT_TYPE_DEFAULT);
        $this->pageContentService = $this->createPageContentServiceWithEnabledPlugins($code);
        $overrideDir = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$code;
        $this->createdDirs[] = $overrideDir;

        // プラグイン同梱のテンプレート (app/Plugin/[code]/Resource/template 相当)
        $bundledDir = sys_get_temp_dir().'/'.$code;
        $this->createdDirs[] = $bundledDir;
        (new Filesystem())->dumpFile($bundledDir.'/'.$this->route.'.twig', 'bundled');
        $this->registerTwigNamespacePath($code, $bundledDir);

        $result = $this->apply(['name' => '名前だけ変更']);

        $this->assertSame(ContentStatus::Updated, $result->status);
        $this->assertSame([], $result->writtenPaths);
        $this->assertDirectoryDoesNotExist($overrideDir);
        $this->assertFalse($result->containerRebuildRequired);
    }

    /**
     * 名前空間を持たないページは, 書き出してもコンテナの再構築を求めない.
     */
    public function testSaveDoesNotRequireContainerRebuildForNormalPage(): void
    {
        $result = $this->apply(['name' => 'テストページ', 'body' => 'body']);

        $this->assertNotSame([], $result->writtenPaths);
        $this->assertFalse($result->containerRebuildRequired);
    }

    /**
     * app/template へ上書きしておらず, コアのテンプレートを解決できる既定ページ.
     */
    private function findDefaultPageWithoutOverride(): Page
    {
        /** @var list<Page> $Pages */
        $Pages = $this->entityManager->getRepository(Page::class)
            ->findBy(['edit_type' => Page::EDIT_TYPE_DEFAULT], ['id' => 'ASC']);
        foreach ($Pages as $Page) {
            if (!is_file($this->pageContentService->getFilePath($Page))
                && '' !== $this->pageContentService->readTemplate($Page)) {
                return $Page;
            }
        }

        self::fail('app/template へ上書きしていない既定ページが見つかりません.');
    }

    /**
     * プラグインが dtb_page へ直接登録する, ファイル名が twig の名前空間を指すページを作る.
     */
    private function createPluginNamespacePage(string $fileName, int $editType): Page
    {
        $Page = (new Page())
            ->setName('プラグインのページ')
            ->setUrl((string) $this->route)
            ->setFileName($fileName)
            ->setEditType($editType);
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        return $Page;
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
