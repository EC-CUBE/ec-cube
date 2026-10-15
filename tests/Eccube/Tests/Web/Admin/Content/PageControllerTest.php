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
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class PageControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAdminContentPageIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_page'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentPageEdit()
    {
        $this->client->request(Request::METHOD_GET,
            $this->generateUrl(
                'admin_content_page_edit',
                ['id' => 1]
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentPageDelete()
    {
        $redirectUrl = $this->generateUrl('admin_content_page');

        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl(
                'admin_content_page_delete',
                ['id' => 1]
            )
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertTrue($actual);
    }

    public function testRoutingAdminContentPageDeleteFlgUser()
    {
        $redirectUrl = $this->generateUrl('admin_content_page');

        $Page = new Page();
        $Page->setEditType(Page::EDIT_TYPE_USER);
        $Page->setUrl('hogehoge');
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl(
                'admin_content_page_delete',
                ['id' => $Page->getId()]
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminContentPageEditName()
    {
        $client = $this->client;

        $templatePath = static::getContainer()->getParameter('eccube_theme_front_dir');
        $Page = $this->entityManager->getRepository(Page::class)->find(1);

        $source = static::getContainer()->get(Environment::class)
            ->getLoader()
            ->getSourceContext($Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            Request::METHOD_POST,
            $this->generateUrl(
                'admin_content_page_edit',
                ['id' => $Page->getId()]
            ),
            [
                'main_edit' => [
                    'name' => 'testtest',
                    'url' => $Page->getUrl(),
                    'file_name' => $Page->getFileName(),
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_content_page_edit',
            ['id' => $Page->getId()])));

        $this->expected = 'testtest';
        $this->actual = $Page->getName();
        $this->verify();

        if (file_exists($templatePath.'/'.$Page->getFileName().'.twig')) {
            unlink($templatePath.'/'.$Page->getFileName().'.twig');
        }
    }

    public function testRoutingAdminContentPageWithCreate()
    {
        $client = $this->client;
        $faker = $this->getFaker();

        $templatePath = static::getContainer()->getParameter('eccube_theme_user_data_dir');

        $name = $faker->word();
        $source = $faker->realText();
        $client->request(
            Request::METHOD_POST,
            $this->generateUrl(
                'admin_content_page_new'
            ),
            [
                'main_edit' => [
                    'name' => $name,
                    'url' => $name,
                    'file_name' => $name,
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertTrue($client->getResponse()->isRedirection());
        preg_match('|content/page/([0-9]+)/edit|', (string) $client->getResponse()->headers->get('Location'), $matches);
        $Page = $this->entityManager->getRepository(Page::class)->find($matches[1]);

        $this->expected = $name;
        $this->actual = $Page->getName();
        $this->verify();

        if (file_exists($templatePath.'/'.$Page->getFileName().'.twig')) {
            unlink($templatePath.'/'.$Page->getFileName().'.twig');
        }
    }

    /**
     * ユーザーが新しく作るページには名前空間付きのファイル名を付けられない.
     *
     * フロントは @user_data/ を前置してテンプレートを探すため, @Boomerang/xxx で保存すると
     * "@user_data/@Boomerang/xxx.twig" を解決できず 500 になる.
     */
    public function testRoutingAdminContentPageNewRejectsPluginNamespace(): void
    {
        $client = $this->client;
        $faker = $this->getFaker();

        $name = $faker->word();
        $crawler = $client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_content_page_new'),
            [
                'main_edit' => [
                    'name' => $name,
                    'url' => $name,
                    'file_name' => '@Boomerang/'.$name,
                    'tpl_data' => 'body',
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertFalse($client->getResponse()->isRedirection(), '登録されない');
        $this->assertStringContainsString(
            'ファイル名の先頭に「@」は使用できません。',
            $crawler->filter('.invalid-feedback')->text()
        );
        $this->assertNotInstanceOf(Page::class, $this->entityManager->getRepository(Page::class)->findOneBy(['url' => $name]));
        $this->assertFileDoesNotExist(
            static::getContainer()->getParameter('eccube_theme_app_dir').'/plugin/Boomerang/'.$name.'.twig'
        );
    }

    /**
     * プラグインが DB へ直接登録した, 名前空間付きのファイル名を持つユーザーのページは,
     * 同じファイル名のままなら再保存できる. 別の名前空間付きのファイル名へは変更できない.
     */
    public function testRoutingAdminContentPageResavesExistingUserPageWithPluginNamespace(): void
    {
        $client = $this->client;
        $name = $this->getFaker()->word();
        $Page = $this->createPluginNamespacePage('@Boomerang/'.$name, $name, Page::EDIT_TYPE_USER);
        $expectedPath = static::getContainer()->getParameter('eccube_theme_app_dir').'/plugin/Boomerang/'.$name.'.twig';

        try {
            $client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()]),
                [
                    'main_edit' => [
                        'name' => $name,
                        'url' => $name,
                        'file_name' => '@Boomerang/'.$name,
                        'tpl_data' => 'resaved',
                        '_token' => 'dummy',
                    ],
                ]
            );

            $this->assertTrue($client->getResponse()->isRedirect(
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()])
            ), '保存済みの値と同じなら再保存できる');
            $this->assertSame('resaved', file_get_contents($expectedPath));

            $crawler = $client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()]),
                [
                    'main_edit' => [
                        'name' => $name,
                        'url' => $name,
                        'file_name' => '@Boomerang/'.$name.'_changed',
                        'tpl_data' => 'changed',
                        '_token' => 'dummy',
                    ],
                ]
            );

            $this->assertFalse($client->getResponse()->isRedirection(), '別の名前空間付きのファイル名へは変更できない');
            $this->assertStringContainsString(
                'ファイル名の先頭に「@」は使用できません。',
                $crawler->filter('.invalid-feedback')->text()
            );
        } finally {
            if (file_exists($expectedPath)) {
                unlink($expectedPath);
            }
        }
    }

    /**
     * プラグインが登録した既定ページ (ファイル名 @PluginCode/xxx) を管理画面から保存すると,
     * 上書き用の app/template/plugin/PluginCode/xxx.twig へ書き出される (Issue #4904 の再現手順).
     *
     * 既定ページのファイル名は hidden で送られ, 保存時は保存済みの値へ戻される.
     * "@" を付けたまま書くと EccubeExtension が登録するパスと一致せず, 画面へ反映されない.
     *
     * @see \Eccube\DependencyInjection\EccubeExtension::configureTwigPaths()
     */
    public function testRoutingAdminContentPageEditSavesDefaultPageWithPluginNamespace(): void
    {
        $client = $this->client;
        $faker = $this->getFaker();

        $code = 'Boomerang'.bin2hex(random_bytes(4));
        $name = $faker->word();
        $fileName = '@'.$code.'/'.$name;
        $Page = $this->createPluginNamespacePage($fileName, 'logout', Page::EDIT_TYPE_DEFAULT);

        $pluginDir = static::getContainer()->getParameter('eccube_theme_app_dir').'/plugin';
        $expectedPath = $pluginDir.'/'.$code.'/'.$name.'.twig';
        $unexpectedPath = $pluginDir.'/@'.$code.'/'.$name.'.twig';
        $source = $faker->realText();

        try {
            $client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()]),
                [
                    'main_edit' => [
                        'name' => 'プラグインのページ',
                        'url' => $Page->getUrl(),
                        'file_name' => $fileName,
                        'tpl_data' => $source,
                        '_token' => 'dummy',
                    ],
                ]
            );

            $this->assertTrue($client->getResponse()->isRedirect(
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()])
            ));
            $this->assertSame($fileName, $this->entityManager->getRepository(Page::class)->find($Page->getId())?->getFileName());
            $this->assertFileExists($expectedPath, '@ を除いたパスへ書き出す');
            $this->assertFileDoesNotExist($unexpectedPath, '@ を含むパスへは書き出さない');
            $this->assertSame($source, file_get_contents($expectedPath));
        } finally {
            (new Filesystem())->remove([$pluginDir.'/'.$code, $pluginDir.'/@'.$code]);
        }
    }

    /**
     * 名前空間付きのファイル名を持つページの編集画面が開ける.
     *
     * 読み取り時に @user_data/ を前置すると "@user_data/@Boomerang/xxx.twig" という
     * 解決できない名前になり 500 になる. 保存経路だけを見ていると気付けない.
     */
    public function testRoutingAdminContentPageEditWithPluginNamespace(): void
    {
        $client = $this->client;
        $name = $this->getFaker()->word();
        // プラグインが dtb_page へ登録する既定ページ. url はプラグインのルーティング名
        $Page = $this->createPluginNamespacePage('@Boomerang/'.$name, 'logout', Page::EDIT_TYPE_DEFAULT);

        $source = '{% extends \'default_frame.twig\' %}';
        $writtenPath = static::getContainer()->getParameter('eccube_theme_app_dir').'/plugin/Boomerang/'.$name.'.twig';
        (new Filesystem())->dumpFile($writtenPath, $source);

        try {
            // 編集画面を開く. ここで名前空間を二重に付けると 500 になる
            $crawler = $client->request(
                Request::METHOD_GET,
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()])
            );
            $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                '名前空間付きのページでも編集画面を開ける'
            );
            $this->assertSame(
                $source,
                $crawler->filter('#main_edit_tpl_data')->text(),
                '書き出したテンプレートを読み戻す'
            );
            $this->assertStringContainsString(
                'app/template/plugin',
                $crawler->filter('.card-body')->text(),
                '配置先を app/template/plugin として表示する'
            );
        } finally {
            if (file_exists($writtenPath)) {
                unlink($writtenPath);
            }
        }
    }

    /**
     * ファイル名の検証は "." を許さないため, 名前空間を許可しても
     * 上位ディレクトリへ抜けるファイル名は受け付けない.
     */
    public function testRoutingAdminContentPageRejectsPathTraversal()
    {
        $client = $this->client;
        $faker = $this->getFaker();

        foreach (['@../../../../etc/passwd', '../../etc/passwd', '@Boomerang/../../../etc/passwd'] as $fileName) {
            $name = $faker->word();
            $crawler = $client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_content_page_new'),
                [
                    'main_edit' => [
                        'name' => $name,
                        'url' => $name,
                        'file_name' => $fileName,
                        'tpl_data' => 'body',
                        '_token' => 'dummy',
                    ],
                ]
            );

            $this->assertFalse(
                $client->getResponse()->isRedirection(),
                sprintf('%s は登録されない', $fileName)
            );
            $this->assertGreaterThan(
                0,
                $crawler->filter('.invalid-feedback')->count(),
                sprintf('%s はバリデーションエラーになる', $fileName)
            );
        }
    }

    public function testAdminContentPageDuplicateWithEditTypeDefault()
    {
        $client = $this->client;

        $templatePath = static::getContainer()->getParameter('eccube_theme_front_dir');
        $Page = $this->entityManager->getRepository(Page::class)->find(42); // Shoppin/index

        $source = static::getContainer()->get(Environment::class)
            ->getLoader()
            ->getSourceContext($Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            Request::METHOD_POST,
            $this->generateUrl(
                'admin_content_page_edit',
                ['id' => $Page->getId()]
            ),
            [
                'main_edit' => [
                    'name' => 'testtest',
                    'url' => $Page->getUrl(),
                    'file_name' => $Page->getFileName(),
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_content_page_edit',
            ['id' => $Page->getId()])));

        $this->expected = 'testtest';
        $this->actual = $Page->getName();
        $this->verify('EDIT_TYPE_DEFAULT 編集時はファイル名重複していても編集可能');

        if (file_exists($templatePath.'/'.$Page->getFileName().'.twig')) {
            unlink($templatePath.'/'.$Page->getFileName().'.twig');
        }
    }

    public function testAdminContentPageDuplicateWithEditTypeUser()
    {
        $client = $this->client;
        $faker = $this->getFaker();

        $templatePath = static::getContainer()->getParameter('eccube_theme_user_data_dir');

        $name = $faker->word();
        $source = $faker->realText();
        $client->request(
            Request::METHOD_POST,
            $this->generateUrl(
                'admin_content_page_new'
            ),
            [
                'main_edit' => [
                    'name' => $name,
                    'url' => $name,
                    'file_name' => $name,
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertTrue($client->getResponse()->isRedirection());
        preg_match('|content/page/([0-9]+)/edit|', (string) $client->getResponse()->headers->get('Location'), $matches);
        $Page = $this->entityManager->getRepository(Page::class)->find($matches[1]);

        $this->expected = $name;
        $this->actual = $Page->getName();
        $this->verify('ページ新規作成');

        $source = static::getContainer()->get(Environment::class)
            ->getLoader()
            ->getSourceContext('@user_data/'.$Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            Request::METHOD_POST,
            $this->generateUrl(
                'admin_content_page_edit',
                ['id' => $Page->getId()]
            ),
            [
                'main_edit' => [
                    'name' => 'testtest',
                    'url' => $Page->getUrl(),
                    'file_name' => 'Shopping/index',
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertFalse(
            $client->getResponse()->isRedirect(
                $this->generateUrl('admin_content_page_edit', ['id' => $Page->getId()])),
            'ファイル名 Shopping/index は使用不可');

        $name = $faker->word();
        $source = $faker->realText();
        $client->request(
            Request::METHOD_POST,
            $this->generateUrl(
                'admin_content_page_new'
            ),
            [
                'main_edit' => [
                    'name' => $name,
                    'url' => $name,
                    'file_name' => $Page->getFileName(),
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ],
            ]
        );

        $this->assertFalse($client->getResponse()->isRedirection(), 'EDIT_TYPE_USER でファイル名の重複不可');

        if (file_exists($templatePath.'/'.$Page->getFileName().'.twig')) {
            unlink($templatePath.'/'.$Page->getFileName().'.twig');
        }
    }

    /**
     * プラグインが dtb_page へ直接登録する, 名前空間付きのファイル名を持つページを作る.
     */
    private function createPluginNamespacePage(string $fileName, string $url, int $editType): Page
    {
        $Page = (new Page())
            ->setName('プラグインのページ')
            ->setUrl($url)
            ->setFileName($fileName)
            ->setEditType($editType);
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        return $Page;
    }
}
