<?php

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
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array());
        $this->actual = count($OrderStatuses);
        $this->expected = 8;
        $this->verify();
    }

    public function testFindNotContainsBy1()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array('name' => '決済処理中'));
        $this->actual = count($OrderStatuses);
        $this->expected = 7;
        $this->verify();
    }

    public function testFindNotContainsBy2()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array('name' => array('決済処理中', '新規受付')));
        $this->actual = count($OrderStatuses);
        $this->expected = 6;
        $this->verify();
    }

    public function testFindNotContainsBy3()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array('id' => array(1, 2, 3, 4, 5, 6, 7)));
        $this->actual = count($OrderStatuses);
        $this->expected = 1;
        $this->verify();
    }

    public function testFindNotContainsBy4()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array(), array('id' => 'DESC'));
        $this->actual = implode(', ', array_map(
            function($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '8, 7, 6, 5, 4, 3, 2, 1';
        $this->verify();
    }

    public function testFindNotContainsBy5()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array(), array('id'));
        $this->actual = implode(', ', array_map(
            function($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '1, 2, 3, 4, 5, 6, 7, 8';
        $this->verify();
    }

    public function testFindNotContainsBy6()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array(), array('id'), 1);
        $this->actual = implode(', ', array_map(
            function($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '1';
        $this->verify();
    }

    public function testFindNotContainsBy7()
    {
        $OrderStatuses = $this->orderStatusRepository->findNotContainsBy(array(), array('id'), 2, 2);
        $this->actual = implode(', ', array_map(
            function($OrderStatus) {
                return $OrderStatus->getId();
            }, $OrderStatuses));
        $this->expected = '3, 4';
        $this->verify();
    }
}
