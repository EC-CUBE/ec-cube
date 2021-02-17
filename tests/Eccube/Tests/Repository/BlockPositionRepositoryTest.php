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
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockPositionRepository;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\LayoutRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * BlockPositionRepository test cases.
 */
class BlockPositionRepositoryTest extends EccubeTestCase
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
     * @var  string
     */
    private $layout_id;

    /**
     * @var  Block
     */
    private $UsedBlocks;

    /**
     * @var  Block
     */
    private $UnusedBlocks;

    /**
     * @var  BlockRepository
     */
    protected $blockRepository;

    /**
     * @var  BlockPositionRepository
     */
    protected $blockPositionRepository;

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
        $this->blockRepository = $this->entityManager->getRepository(\Eccube\Entity\Block::class);
        $this->blockPositionRepository = $this->entityManager->getRepository(\Eccube\Entity\BlockPosition::class);
        $this->layoutRepository = $this->entityManager->getRepository(\Eccube\Entity\Layout::class);
        $this->remove();
        $this->DeviceType = $this->entityManager->getRepository(DeviceType::class)
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Layout = new Layout();
        $Layout
            ->setName('テスト用レイアウト')
            ->setDeviceType($this->DeviceType);
        $this->entityManager->persist($Layout);
        $this->entityManager->flush($Layout); // ここで flush しないと, MySQL で ID が取得できない
        $this->layout_id = $Layout->getId();

        for ($i = 0; $i < 3; $i++) {
            $UsedBlocks = new Block();
            $UsedBlocks
                ->setName('block-'.$i)
                ->setFileName('block/block-'.$i)
                ->setUseController(true)
                ->setDeletable(false)
                ->setDeviceType($this->DeviceType);
            $this->entityManager->persist($UsedBlocks);
            $this->entityManager->flush($UsedBlocks); // ここで flush しないと, MySQL で ID が取得できない
            $this->block_id = $UsedBlocks->getId();
            $this->UsedBlocks[] = $UsedBlocks;
        }

        for ($i = 3; $i < 10; $i++) {
            $UnusedBlocks = new Block();
            $UnusedBlocks
                ->setName('block-'.$i)
                ->setFileName('block/block-'.$i)
                ->setUseController(true)
                ->setDeletable(false)
                ->setDeviceType($this->DeviceType);
            $this->entityManager->persist($UnusedBlocks);
            $this->entityManager->flush($UnusedBlocks); // ここで flush しないと, MySQL で ID が取得できない
            $this->block_id = $UnusedBlocks->getId();
            $this->UnusedBlocks[] = $UnusedBlocks;
        }
    }

    protected function remove()
    {
        $Blocks = $this->blockRepository->findAll();
        foreach ($Blocks as $Block) {
            $this->entityManager->remove($Block);
        }
        $this->entityManager->flush();

        $BlockPositions = $this->blockPositionRepository->findAll();
        foreach ($BlockPositions as $BlockPosition) {
            $this->entityManager->remove($BlockPosition);
        }
        $this->entityManager->flush();
    }

    public function testRegister()
    {
        $Layout = $this->layoutRepository->get($this->layout_id);

        $count = 1;
        foreach ($this->UsedBlocks as $Block) {
            $data['block_id_'.$count] = $Block->getId();
            $data['section_'.$count] = $Block->getId();
            $data['block_row_'.$count] = $Block->getId();

            $count++;
        }

        $this->blockPositionRepository->register($data, $this->UsedBlocks, $this->UnusedBlocks, $Layout);

        $BlockPositions = $this->blockPositionRepository->findAll();
        $this->expected = 3;
        $this->actual = count($BlockPositions);
        $this->verify();
    }
}
