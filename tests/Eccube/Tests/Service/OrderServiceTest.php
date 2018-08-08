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

namespace Eccube\Tests\Service;

class OrderServiceTest extends AbstractServiceTestCase
{
    protected $app;
    protected $Customer;
    protected $Order;
    protected $rate;

    public function setUp()
    {
        $this->markTestIncomplete('To be removed');
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();
        $this->rate = $TaxRule->getTaxRate();
    }

    public function testGetSubTotal()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $quantity = 3;
        $price = 100;
        $rows = count($this->Order->getOrderItems());

        $subTotal = 0;
        foreach ($this->Order->getOrderItems() as $Item) {
            $Item->setPrice($price);
            $Item->setQuantity($quantity);
            $subTotal = $Item->getPriceIncTax() * $Item->getQuantity();
        }
        $this->Order->setSubTotal($subTotal);
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = ($price + ($price * ($this->rate / 100))) * $quantity * $rows;
        $this->actual = $this->app['eccube.service.order']->getSubTotal($Result);
        $this->verify();
    }

    public function testGetSaleTypes()
    {
        $this->expected = [$this->app['eccube.repository.master.sale_type']->find(1)];
        $this->actual = $this->app['eccube.service.order']->getSaleTypes($this->Order);
        $this->verify();
    }
}
