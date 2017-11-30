<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        $this->expected = array($this->app['eccube.repository.master.sale_type']->find(1));
        $this->actual = $this->app['eccube.service.order']->getSaleTypes($this->Order);
        $this->verify();
    }
}
