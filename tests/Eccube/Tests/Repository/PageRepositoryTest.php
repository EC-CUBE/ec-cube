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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageRepository;
use Eccube\Tests\EccubeTestCase;
use org\bovigo\vfs\vfsStream;

class PageRepositoryTest extends EccubeTestCase
{
    /** @var  DeviceType */
    protected $DeviceType;

    /** @var  PageRepository */
    protected $pageRepo;

    protected $userDataRealDir;
    protected $templateRealDir;
    protected $templateDefaultRealDir;

    public function setUp()
    {
        parent::setUp();
        $this->pageRepo = $this->container->get(PageRepository::class);
        $this->DeviceType = $this->container->get(DeviceTypeRepository::class)->find(DeviceType::DEVICE_TYPE_PC);
        $this->userDataRealDir = $this->container->getParameter('eccube.theme.user_data_dir');
        $this->templateRealDir = $this->container->getParameter('eccube.theme.app_dir');
        $this->templateDefaultRealDir = $this->container->getParameter('eccube.theme.src_dir');
    }

    public function test_findOrCreate_pageIdNullisCreate()
    {
        $this->expected = null;
        $Page = $this->pageRepo->findOrCreate(null, $this->DeviceType);
        $this->actual = $Page->getUrl();

        $this->verify();
    }

    public function test_findOrCreate_findTopPage()
    {
        $this->expected = array(
            'url' => 'homepage',
            'DeviceType' => DeviceType::DEVICE_TYPE_PC,
        );

        $Page = $this->pageRepo->findOrCreate(1, $this->DeviceType);
        $this->actual = array(
            'url' => $Page->getUrl(),
            'DeviceType' => $Page->getDeviceType()->getId(),
        );

        $this->verify();
    }

    public function testFindUnusedBlocks()
    {
        $Blocks = $this->pageRepo->findUnusedBlocks($this->DeviceType, 1);

        // Current: total 13, used: 7 (1,6,7,8,10,11,12,13)
        $this->expected = 5;
        $this->actual = count($Blocks);
        $this->verify();
    }

    public function testGet()
    {
        $Page = $this->pageRepo
            ->getByDeviceTypeAndId($this->DeviceType, 1);

        $this->expected = 1;
        $this->actual = $Page->getId();
        $this->verify();
        $this->assertNotNull($Page->getBlockPositions());
        foreach ($Page->getBlockPositions() as $BlockPosition) {
            $this->assertNotNull($BlockPosition->getBlock()->getId());
        }
    }

    public function testGetByUrl()
    {
        $Page = $this->pageRepo->getByUrl($this->DeviceType, 'homepage');

        $this->expected = 1;
        $this->actual = $Page->getId();
        $this->verify();
        $this->assertNotNull($Page->getBlockPositions());
        foreach ($Page->getBlockPositions() as $BlockPosition) {
            $this->assertNotNull($BlockPosition->getBlock()->getId());
        }
    }

    public function testGetPageList()
    {
        $Pages = $this->pageRepo->getPageList($this->DeviceType);
        $All = $this->pageRepo->findAll();

        $this->expected = count($All) - 1;
        $this->actual = count($Pages);
        $this->verify();
    }

    public function testGetWriteTemplatePath()
    {
        $this->expected = $this->templateRealDir;
        $this->actual = $this->pageRepo->getWriteTemplatePath();
        $this->verify();
    }
    public function testGetWriteTemplatePathWithUser()
    {
        $this->expected = $this->userDataRealDir;
        $this->actual = $this->pageRepo->getWriteTemplatePath(true);
        $this->verify();
    }

    public function testGetReadTemplateFile()
    {
        $fileName = 'example_page';
        $tesTemplate = $this->templateRealDir . '/' . $fileName . '.twig';
        file_put_contents($tesTemplate, 'test');

        $data = $this->pageRepo->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
        unlink($tesTemplate);
    }

    public function testGetReadTemplateFileWithDefault()
    {
        $fileName = 'example_page';
        $testTemplate = $this->templateDefaultRealDir . '/' . $fileName . '.twig';
        file_put_contents($testTemplate, 'test');


        $data = $this->pageRepo->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
        unlink($testTemplate);
    }

    public function testGetReadTemplateFileWithUser()
    {
        $fileName = 'example_page';

        $testTemplate = $this->userDataRealDir . '/' . $fileName . '.twig';
        file_put_contents($testTemplate, 'test');

        $data = $this->pageRepo->getReadTemplateFile($fileName, true);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
        unlink($testTemplate);
    }
}
