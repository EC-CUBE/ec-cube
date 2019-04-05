<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Entity\Master\DeviceType;
use org\bovigo\vfs\vfsStream;

class PageLayoutRepositoryTest extends EccubeTestCase
{
    protected $DeviceType;

    public function setUp()
    {
        parent::setUp();
        $this->DeviceType = $this->app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);
    }

    public function test_findOrCreate_pageIdNullisCreate()
    {
        $this->expected = null;
        $PageLayout = $this->app['eccube.repository.page_layout']
            ->findOrCreate(null, $this->DeviceType);
        $this->actual = $PageLayout->getUrl();

        $this->verify();
    }

    public function test_findOrCreate_findTopPage()
    {
        $this->expected = array(
            'url' => 'homepage',
            'DeviceType' => DeviceType::DEVICE_TYPE_PC,
        );

        $PageLayout = $this->app['eccube.repository.page_layout']
            ->findOrCreate(1, $this->DeviceType);
        $this->actual = array(
            'url' => $PageLayout->getUrl(),
            'DeviceType' => $PageLayout->getDeviceType()->getId(),
        );

        $this->verify();
    }

    public function testFindUnusedBlocks()
    {
        $Blocks = $this->app['eccube.repository.page_layout']
            ->findUnusedBlocks($this->DeviceType, 1);

        $this->expected = 0;
        $this->actual = count($Blocks);
        $this->verify();
    }

    public function testGet()
    {
        $PageLayout = $this->app['eccube.repository.page_layout']
            ->get($this->DeviceType, 1);

        $this->expected = 1;
        $this->actual = $PageLayout->getId();
        $this->verify();
        $this->assertNotNull($PageLayout->getBlockPositions());
        foreach ($PageLayout->getBlockPositions() as $BlockPosition) {
            $this->assertNotNull($BlockPosition->getBlock()->getId());
        }
    }

    public function testGetByUrl()
    {
        $PageLayout = $this->app['eccube.repository.page_layout']
            ->getByUrl($this->DeviceType, 'homepage');

        $this->expected = 1;
        $this->actual = $PageLayout->getId();
        $this->verify();
        $this->assertNotNull($PageLayout->getBlockPositions());
        foreach ($PageLayout->getBlockPositions() as $BlockPosition) {
            $this->assertNotNull($BlockPosition->getBlock()->getId());
        }
    }

    public function testGetPageList()
    {
        $PageLayouts = $this->app['eccube.repository.page_layout']
            ->getPageList($this->DeviceType);
        $All = $this->app['eccube.repository.page_layout']->findAll();

        $this->expected = count($All) - 1;
        $this->actual = count($PageLayouts);
        $this->verify();
    }

    public function testGetWriteTemplatePath()
    {
        $this->expected = $this->app['config']['template_realdir'];
        $this->actual = $this->app['eccube.repository.page_layout']->getWriteTemplatePath();
        $this->verify();
    }
    public function testGetWriteTemplatePathWithUser()
    {
        $this->expected = $this->app['config']['user_data_realdir'];
        $this->actual = $this->app['eccube.repository.page_layout']->getWriteTemplatePath(true);
        $this->verify();
    }

    public function testGetReadTemplateFile()
    {
        $fileName = 'example_page';
        $root = vfsStream::setup('rootDir');
        vfsStream::newDirectory('default');

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['template_realdir'] = vfsStream::url('rootDir');
        $config['template_default_realdir'] = vfsStream::url('rootDir/default');
        $this->app['config'] = $config;

        file_put_contents($this->app['config']['template_realdir'].'/'.$fileName.'.twig', 'test');

        $data = $this->app['eccube.repository.page_layout']->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }

    public function testGetReadTemplateFileWithDefault()
    {
        $fileName = 'example_page';
        $root = vfsStream::setup('rootDir');
        mkdir(vfsStream::url('rootDir').'/default', 0777, true);

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['template_realdir'] = vfsStream::url('rootDir');
        $config['template_default_realdir'] = vfsStream::url('rootDir/default');
        $this->app['config'] = $config;

        file_put_contents($this->app['config']['template_default_realdir'].'/'.$fileName.'.twig', 'test');

        $data = $this->app['eccube.repository.page_layout']->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }

    public function testGetReadTemplateFileWithUser()
    {
        $fileName = 'example_page';
        $root = vfsStream::setup('rootDir');

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['user_data_realdir'] = vfsStream::url('rootDir');
        $this->app['config'] = $config;

        file_put_contents($this->app['config']['user_data_realdir'].'/'.$fileName.'.twig', 'test');

        $data = $this->app['eccube.repository.page_layout']->getReadTemplateFile($fileName, true);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }
}
