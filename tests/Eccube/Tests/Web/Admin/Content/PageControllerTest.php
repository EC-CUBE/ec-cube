<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Repository\Master\DeviceTypeRepository;
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
                array('id' => 1)
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
                array('id' => 1)
            )
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertTrue($actual);
    }

    public function test_routing_AdminContentPage_delete_flg_user()
    {

        $redirectUrl = $this->generateUrl('admin_content_page');

        $DeviceType = $this->container->get(DeviceTypeRepository::class)
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Page = new Page();
        $Page->setDeviceType($DeviceType);
        $Page->setEditType(Page::EDIT_TYPE_USER);
        $Page->setUrl('hogehoge');
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        $this->client->request('DELETE',
            $this->generateUrl(
                'admin_content_page_delete',
                array('id' => $Page->getId())
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

    }

    public function test_routing_AdminContentPage_edit_name()
    {
        $client = $this->client;

        $editable = false;

        $templatePath = $this->container->getParameter('eccube.theme.front_dir');
        $Page = $this->container->get(PageRepository::class)->find(1);

        $source = $this->container->get('twig')
            ->getLoader()
            ->getSourceContext($Page->getFileName().'.twig')
            ->getCode();

        $client->request(
            'POST',
            $this->generateUrl(
                'admin_content_page_edit',
                array('id' => $Page->getId())
            ),
            array(
                'main_edit' => array(
                    'id' => $Page->getId(),
                    'name' => 'testtest',
                    'url' => $Page->getUrl(),
                    'file_name' => $Page->getFileName(),
                    'tpl_data' => $source,
                    '_token' => 'dummy',
                ),
                'page_id' => $Page->getId(),
                'editable' => $editable,
                'template_path' => $templatePath,
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_content_page_edit',
            array('id' => $Page->getId()))));

        $this->expected = 'testtest';
        $this->actual = $Page->getName();
        $this->verify();

        if (file_exists($templatePath.'/'.$Page->getFileName().'.twig')) {
            unlink($templatePath.'/'.$Page->getFileName().'.twig');
        }
    }
}
