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

namespace Eccube\Tests\Repository\Master;

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
        $this->orderStatusRepository = $this->container->get(OrderStatusRepository::class);
    }

    public function testFindNotContainsBy()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([]);
        $this->actual = count($OrderStatuses);
        $this->expected = 10;
        $this->verify();
    }

    public function testFindNotContainsBy1()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(['name' => '決済処理中']);
        $this->actual = count($OrderStatuses);
        $this->expected = 9;
        $this->verify();
    }

    public function testFindNotContainsBy2()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(['name' => ['決済処理中', '新規受付']]);
        $this->actual = count($OrderStatuses);
        $this->expected = 8;
        $this->verify();
    }

    public function testFindNotContainsBy3()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(['id' => [1, 2, 3, 4, 5, 6, 7]]);
        $this->actual = count($OrderStatuses);
        $this->expected = 3;
        $this->verify();
    }

    public function testFindNotContainsBy4()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id' => 'DESC']);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '10, 9, 8, 7, 6, 5, 4, 3, 2, 1';
        $this->verify();
    }

    public function testFindNotContainsBy5()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id']);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '1, 2, 3, 4, 5, 6, 7, 8, 9, 10';
        $this->verify();
    }

    public function testFindNotContainsBy6()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id'], 1);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '1';
        $this->verify();
    }

    public function testFindNotContainsBy7()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy([], ['id'], 2, 2);
        $this->actual = implode(', ', array_map(
            function ($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '3, 4';
        $this->verify();
    }
}
