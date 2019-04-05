<?php

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
use Eccube\Repository\PageRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class PageControllerTest extends AbstractAdminWebTestCase
{
    public function test_routing_AdminContentPage_index()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_page'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentPage_edit()
    {
        $this->client->request('GET',
            $this->generateUrl(
                'admin_content_page_edit',
                ['id' => 1]
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentPage_delete()
    {
        $redirectUrl = $this->generateUrl('admin_content_page');

        $this->client->request('DELETE',
            $this->generateUrl(
                'admin_content_page_delete',
                ['id' => 1]
            )
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertTrue($actual);
    }

    public function test_routing_AdminContentPage_delete_flg_user()
    {
        $redirectUrl = $this->generateUrl('admin_content_page');

        $Page = new Page();
        $Page->setEditType(Page::EDIT_TYPE_USER);
        $Page->setUrl('hogehoge');
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        $this->client->request('DELETE',
            $this->generateUrl(
                'admin_content_page_delete',
                ['id' => $Page->getId()]
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function test_routing_AdminContentPage_edit_name()
    {
        $client = $this->client;

        $editable = false;

        $templatePath = $this->container->getParameter('eccube_theme_front_dir');
        $Page = $this->container->get(PageRepository::class)->find(1);

        $source = $this->container->get('twig')
            ->getLoader()
            ->getSourceContext($Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            'POST',
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

    public function test_routing_AdminContentPageWithCreate()
    {
        $client = $this->client;
        $faker = $this->getFaker();

        $templatePath = $this->container->getParameter('eccube_theme_user_data_dir');

        $name = $faker->word;
        $source = $faker->realText();
        $client->request(
            'POST',
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
        preg_match('|content/page/([0-9]+)/edit|', $client->getResponse()->headers->get('Location'), $matches);
        $Page = $this->container->get(PageRepository::class)->find($matches[1]);

        $this->expected = $name;
        $this->actual = $Page->getName();
        $this->verify();

        if (file_exists($templatePath.'/'.$Page->getFileName().'.twig')) {
            unlink($templatePath.'/'.$Page->getFileName().'.twig');
        }
    }

    public function testAdminContentPageDuplicateWithEditTypeDefault()
    {
        $client = $this->client;

        $templatePath = $this->container->getParameter('eccube_theme_front_dir');
        $Page = $this->container->get(PageRepository::class)->find(42); // Shoppin/index

        $source = $this->container->get('twig')
            ->getLoader()
            ->getSourceContext($Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            'POST',
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

        $templatePath = $this->container->getParameter('eccube_theme_user_data_dir');

        $name = $faker->word;
        $source = $faker->realText();
        $client->request(
            'POST',
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
        preg_match('|content/page/([0-9]+)/edit|', $client->getResponse()->headers->get('Location'), $matches);
        $Page = $this->container->get(PageRepository::class)->find($matches[1]);

        $this->expected = $name;
        $this->actual = $Page->getName();
        $this->verify('ページ新規作成');

        $source = $this->container->get('twig')
            ->getLoader()
            ->getSourceContext('@user_data/'.$Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            'POST',
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

        $name = $faker->word;
        $source = $faker->realText();
        $client->request(
            'POST',
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
}
