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

use Eccube\Entity\Master\Pref;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * DeliveryFeeRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class DeliveryFeeRepositoryTest extends EccubeTestCase
{
    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepo;

    /**
     * @var PrefRepository
     */
    protected $masterPrefRepo;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepo;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->deliveryRepo = $this->container->get(DeliveryRepository::class);
        $this->masterPrefRepo = $this->container->get(PrefRepository::class);
        $this->deliveryFeeRepo = $this->container->get(DeliveryFeeRepository::class);
    }

    public function testFindOrCreateWithFind()
    {
        $Delivery = $this->deliveryRepo->find(1);
        $Pref = $this->masterPrefRepo->find(1);

        $this->assertNotNull($Pref);
        $this->assertNotNull($Delivery);

        $DeliveryFee = $this->deliveryFeeRepo->findOrCreate(
            ['Delivery' => $Delivery, 'Pref' => $Pref]
        );

        $this->expected = 1000; // 配送料の初期設定
        $this->actual = $DeliveryFee->getFee();
        $this->verify('配送料は'.$this->expected.'ではありません');
    }

    public function testFindOrCreateWithCreate()
    {
        $Delivery = $this->deliveryRepo->find(1);
        $Pref = new Pref();

        $Pref
            ->setId(500)
            ->setName('その他')
            ->setSortNo(99);
        $this->entityManager->persist($Pref);
        $this->entityManager->flush();

        $DeliveryFee = $this->deliveryFeeRepo->findOrCreate(
            ['Delivery' => $Delivery, 'Pref' => $Pref]
        );

        $this->expected = 0;
        $this->actual = $DeliveryFee->getFee();

        $this->verify('配送料は'.$this->expected.'ではありません');
    }
}
