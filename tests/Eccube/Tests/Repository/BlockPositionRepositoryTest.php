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

use Eccube\Entity\Block;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockPositionRepository;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\LayoutRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * BlockPositionRepository test cases.
 */
final class BlockPositionRepositoryTest extends EccubeTestCase
{
    protected ?DeviceType $DeviceType = null;

    private ?int $layout_id = null;

    /**
     * @var  Block[]|null
     */
    private ?array $UsedBlocks = [];

    /**
     * @var  Block[]|null
     */
    private ?array $UnusedBlocks = [];

    protected ?BlockRepository $blockRepository = null;

    protected ?BlockPositionRepository $blockPositionRepository = null;

    protected ?LayoutRepository $layoutRepository = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->blockRepository = $this->entityManager->getRepository(Block::class);
        $this->blockPositionRepository = $this->entityManager->getRepository(BlockPosition::class);
        $this->layoutRepository = $this->entityManager->getRepository(Layout::class);
        $this->remove();
        $this->DeviceType = $this->entityManager->getRepository(DeviceType::class)
            ->find(DeviceType::DEVICE_TYPE_PC);
        $Layout = new Layout();
        $Layout
            ->setName('テスト用レイアウト')
            ->setDeviceType($this->DeviceType);
        $this->entityManager->persist($Layout);
        $this->entityManager->flush($Layout);
        // ここで flush しないと, MySQL で ID が取得できない
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
            $this->entityManager->flush($UsedBlocks);
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
            $this->entityManager->flush($UnusedBlocks);
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

    /**
     * 整数でない値・存在しないブロック・同じ配置への重複は登録しない.
     */
    public function testRegisterWithInvalidData(): void
    {
        $Layout = $this->layoutRepository->get($this->layout_id);
        $Block = $this->UsedBlocks[0];

        $data = [
            // 正しい値
            'block_id_0' => (string) $Block->getId(), 'section_0' => '1', 'block_row_0' => '0',
            // 整数でない値
            'block_id_1' => 'abc', 'section_1' => '1', 'block_row_1' => '1',
            'block_id_2' => (string) $Block->getId(), 'section_2' => 'abc', 'block_row_2' => '1',
            'block_id_3' => (string) $Block->getId(), 'section_3' => '2', 'block_row_3' => ['1'],
            // 範囲外・存在しないブロック
            'block_id_4' => '2147483648', 'section_4' => '1', 'block_row_4' => '1',
            'block_id_5' => '2147483647', 'section_5' => '1', 'block_row_5' => '1',
            // 同じ配置への重複
            'block_id_6' => (string) $Block->getId(), 'section_6' => '1', 'block_row_6' => '2',
        ];

        $this->blockPositionRepository->register($data, $this->blockRepository->findAll(), [], $Layout);

        $BlockPositions = $this->blockPositionRepository->findBy(['Layout' => $Layout]);
        $this->assertCount(1, $BlockPositions);
        $this->assertSame($Block->getId(), $BlockPositions[0]->getBlockId());
        $this->assertSame(1, $BlockPositions[0]->getSection());
        $this->assertSame(0, $BlockPositions[0]->getBlockRow());
    }
}
