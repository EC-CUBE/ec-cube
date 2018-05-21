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

use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
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
        $this->blockRepository = $this->container->get(BlockRepository::class);
        $this->removeBlock();
        $this->DeviceType = $this->container->get(DeviceTypeRepository::class)
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

    public function testGetBlock()
    {
        $Block = $this->blockRepository->getBlock($this->block_id, $this->DeviceType);
        $this->assertNotNull($Block);
        $this->expected = $this->block_id;
        $this->actual = $Block->getId();
        $this->verify('ブロックIDは'.$this->expected.'ではありません');
    }

    public function testFindOrCreate()
    {
        // TODO findOrCreate(array $condition) にするべき
        // https://github.com/EC-CUBE/ec-cube/issues/922
        $Block = $this->blockRepository->findOrCreate($this->block_id, $this->DeviceType);

        $this->assertNotNull($Block);
        $this->expected = $this->block_id;
        $this->actual = $Block->getId();
        $this->verify('ブロックIDは'.$this->expected.'ではありません');

        $Block = $this->blockRepository->findOrCreate(null, $this->DeviceType);
        $this->assertNotNull($Block);
        $this->assertTrue($Block instanceof Block);
        $this->assertNull($Block->getId());

        $Block = $this->blockRepository->findOrCreate(999999, $this->DeviceType);
        $this->assertNull($Block); // XXX block_id = 999999 の新たなインスタンスを返してほしいが不可能.
    }
}
