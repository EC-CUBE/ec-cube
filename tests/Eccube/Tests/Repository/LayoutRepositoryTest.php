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

use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\LayoutRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * LayoutRepository test cases.
 */
class LayoutRepositoryTest extends EccubeTestCase
{
    /**
     * @var  DeviceType
     */
    protected $DeviceType;

    /**
     * @var  string
     */
    private $layout_id;

    /**
     * @var  LayoutRepository
     */
    protected $layoutRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->layoutRepository = $this->entityManager->getRepository(\Eccube\Entity\Layout::class);
        $this->DeviceType = $this->entityManager->getRepository(\Eccube\Entity\Master\DeviceType::class)
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Layout = new Layout();
        $Layout
            ->setName('テスト用レイアウト')
            ->setDeviceType($this->DeviceType);
        $this->entityManager->persist($Layout);
        $this->entityManager->flush(); // ここで flush しないと, MySQL で ID が取得できない
        $this->layout_id = $Layout->getId();
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
