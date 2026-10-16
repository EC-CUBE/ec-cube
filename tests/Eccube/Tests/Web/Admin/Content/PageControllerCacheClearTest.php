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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\Page;
use Eccube\Service\Content\PageContentService;
use Eccube\Tests\TwigNamespacePathTrait;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

/**
 * ページ管理で, プラグインの名前空間を指す既定ページを保存したときのコンテナの再構築.
 *
 * 再構築は本物の cache:clear を伴うため, cache-clear グループに分ける.
 * 判定には kernel.cache_dir に置いた目印を使う (CacheControllerTest と同じ理由で kernel.build_dir は見ない).
 *
 * @see \Eccube\DependencyInjection\EccubeExtension::configureTwigPaths()
 */
#[Group('cache-clear')]
final class PageControllerCacheClearTest extends AbstractAdminWebTestCase
{
    use TwigNamespacePathTrait;

    private ?string $code = null;

    private ?string $marker = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->code = 'Rebuild'.bin2hex(random_bytes(4));
        $this->marker = rtrim((string) static::getContainer()->getParameter('kernel.cache_dir'), '/').'/sample';
        // 目印を置いてから実行する. 存在しないまま assertFileDoesNotExist すると素通りする
        (new Filesystem())->dumpFile($this->marker, 'test');
        // 無効なプラグインの名前空間は再構築の対象外のため, 有効なプラグインとして扱わせる
        static::getContainer()->set(
            PageContentService::class,
            $this->createPageContentServiceWithEnabledPlugins($this->code)
        );
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove([
            (string) $this->marker,
            $this->pluginOverrideDir().'/'.$this->code,
        ]);

        parent::tearDown();
    }

    /**
     * 上書き用ディレクトリが twig の名前空間に登録されていなければ, 保存後にコンテナを作り直す.
     */
    public function testSaveClearsCacheWhenNamespaceDirIsNotRegistered(): void
    {
        $this->post($this->createPluginNamespacePage());

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertFileDoesNotExist((string) $this->marker, '登録されていなければコンテナを作り直す');
    }

    /**
     * 登録済み (コンテナを作り直した後) なら, 保存のたびに作り直さない.
     */
    public function testSaveDoesNotClearCacheWhenNamespaceDirIsRegistered(): void
    {
        $Page = $this->createPluginNamespacePage();
        // 登録した状態を次のリクエストへ引き継ぐため, カーネルを作り直さない
        $this->client->disableReboot();
        $this->registerTwigNamespacePath(
            (string) $this->code,
            $this->pluginOverrideDir().'/'.$this->code
        );

        $this->post($Page);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertFileExists((string) $this->marker, '登録済みならコンテナを作り直さない');
    }

    private function createPluginNamespacePage(): Page
    {
        $Page = (new Page())
            ->setName('プラグインのページ')
            ->setUrl('logout')
            ->setFileName('@'.$this->code.'/'.$this->getFaker()->word())
            ->setEditType(Page::EDIT_TYPE_DEFAULT);
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        return $Page;
    }

    private function post(Page $Page): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()]),
            [
                'main_edit' => [
                    'name' => 'プラグインのページ',
                    'url' => $Page->getUrl(),
                    'file_name' => $Page->getFileName(),
                    'tpl_data' => 'body',
                    '_token' => 'dummy',
                ],
            ]
        );
    }
}
