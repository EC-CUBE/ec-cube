<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Repository;

use Eccube\Repository\LayoutRepository;
use Eccube\Tests\EccubeTestCase;

class LayoutRepositoryTest extends EccubeTestCase
{
    /** @var LayoutRepository */
    protected $layoutRepository;

    public function setUp()
    {
        parent::setUp();
        $this->layoutRepository = $this->container->get(LayoutRepository::class);
    }

    public function testGet()
    {
        $Page = $this->layoutRepository->find(1);

        $this->expected = 1;
        $this->actual = $Page->getId();
        $this->verify();
        $this->assertNotNull($Page->getBlockPositions());
        foreach ($Page->getBlockPositions() as $BlockPosition) {
            $this->assertNotNull($BlockPosition->getBlock()->getId());
        }
    }
}
