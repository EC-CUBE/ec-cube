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

use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * BlockRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class BlockRepositoryTest extends EccubeTestCase
{
    /**
     * @var  DeviceType
     */
    protected $DeviceType;

    /**
     * @var  string
     */
    private $block_id;

    /**
     * @var  BlockRepository
     */
    protected $blockRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->blockRepository = $this->entityManager->getRepository(\Eccube\Entity\Block::class);
        $this->removeBlock();
        $this->DeviceType = $this->entityManager->getRepository(\Eccube\Entity\Master\DeviceType::class)
            ->find(DeviceType::DEVICE_TYPE_PC);

        for ($i = 0; $i < 10; $i++) {
            $Block = new Block();
            $Block
                ->setName('block-'.$i)
                ->setFileName('block/block-'.$i)
                ->setUseController(true)
                ->setDeletable(false)
                ->setDeviceType($this->DeviceType);
            $this->entityManager->persist($Block);
            $this->entityManager->flush(); // ここで flush しないと, MySQL で ID が取得できない
            $this->block_id = $Block->getId();
        }
    }

    protected function removeBlock()
    {
        $Blocks = $this->blockRepository->findAll();
        foreach ($Blocks as $Block) {
            $this->entityManager->remove($Block);
        }
        $this->entityManager->flush();
    }

    public function testGetList()
    {
        $Blocks = $this->blockRepository->getList($this->DeviceType);

        $this->assertNotNull($Blocks);
        $this->expected = 10;
        $this->actual = count($Blocks);
        $this->verify();
    }
}
