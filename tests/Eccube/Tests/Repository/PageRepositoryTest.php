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

namespace Eccube\Tests\Repository;

use Eccube\Repository\PageRepository;
use Eccube\Tests\EccubeTestCase;

class PageRepositoryTest extends EccubeTestCase
{
    /** @var PageRepository */
    protected $pageRepo;

    protected $userDataRealDir;
    protected $templateRealDir;
    protected $templateDefaultRealDir;

    public function setUp()
    {
        parent::setUp();
        $this->pageRepo = $this->entityManager->getRepository(\Eccube\Entity\Page::class);
        $this->userDataRealDir = self::$container->getParameter('eccube_theme_user_data_dir');
        $this->templateRealDir = self::$container->getParameter('eccube_theme_app_dir');
        $this->templateDefaultRealDir = self::$container->getParameter('eccube_theme_src_dir');
    }

    public function testGetByUrl()
    {
        $Page = $this->pageRepo->getByUrl('homepage');

        $this->expected = 1;
        $this->actual = $Page->getId();
        $this->verify();
    }

    public function testGetPageList()
    {
        $Pages = $this->pageRepo->getPageList();
        $All = $this->pageRepo->findAll();

        $this->expected = count($All) - 2;
        $this->actual = count($Pages);
        $this->verify();
    }
}
