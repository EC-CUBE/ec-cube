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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Page;
use Eccube\Repository\PageRepository;
use Eccube\Tests\EccubeTestCase;

final class PageRepositoryTest extends EccubeTestCase
{
    protected ?PageRepository $pageRepo = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->pageRepo = $this->entityManager->getRepository(Page::class);
        static::getContainer()->getParameter('eccube_theme_user_data_dir');
        static::getContainer()->getParameter('eccube_theme_app_dir');
        static::getContainer()->getParameter('eccube_theme_src_dir');
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
