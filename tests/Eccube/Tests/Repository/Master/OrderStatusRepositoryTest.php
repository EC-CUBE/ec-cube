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

namespace Eccube\Tests\Repository\Master;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * OrderStatusRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderStatusRepositoryTest extends EccubeTestCase
{
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->orderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
    }

    public function testFindNotContainsBy()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([]);
        $this->actual = count($OrderStatuses);
        $this->expected = 8;
        $this->verify();
    }

    public function testFindNotContainsBy1()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(['name' => '決済処理中']);
        $this->actual = count($OrderStatuses);
        $this->expected = 7;
        $this->verify();
    }

    public function testFindNotContainsBy2()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(['name' => ['決済処理中', '新規受付']]);
        $this->actual = count($OrderStatuses);
        $this->expected = 6;
        $this->verify();
    }

    public function testFindNotContainsBy3()
    {
        $orderStatuses = [
            OrderStatus::NEW,
            OrderStatus::CANCEL,
            OrderStatus::IN_PROGRESS,
            OrderStatus::DELIVERED,
            OrderStatus::PAID,
            OrderStatus::PENDING,
            OrderStatus::RETURNED,
        ];

        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(['id' => $orderStatuses]);
        $this->actual = count($OrderStatuses);
        $this->expected = 1;
        $this->verify();
    }

    public function testFindNotContainsBy4()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id' => 'DESC']);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));

        $orderStatuses = [
            OrderStatus::RETURNED,
            OrderStatus::PROCESSING,
            OrderStatus::PENDING,
            OrderStatus::PAID,
            OrderStatus::DELIVERED,
            OrderStatus::IN_PROGRESS,
            OrderStatus::CANCEL,
            OrderStatus::NEW,
        ];

        $this->expected = implode(', ', $orderStatuses);
        $this->verify();
    }

    public function testFindNotContainsBy5()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id']);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));

        $orderStatuses = [
            OrderStatus::NEW,
            OrderStatus::CANCEL,
            OrderStatus::IN_PROGRESS,
            OrderStatus::DELIVERED,
            OrderStatus::PAID,
            OrderStatus::PENDING,
            OrderStatus::PROCESSING,
            OrderStatus::RETURNED,
        ];

        $this->expected = implode(', ', $orderStatuses);
        $this->verify();
    }

    public function testFindNotContainsBy6()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id'], 1);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = OrderStatus::NEW;
        $this->verify();
    }

    public function testFindNotContainsBy7()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id'], 2, 2);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = OrderStatus::IN_PROGRESS.', '.OrderStatus::DELIVERED;
        $this->verify();
    }
}
