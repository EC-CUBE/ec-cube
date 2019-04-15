<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Application;

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
        $quantity = 3;
        $price = 100;
        $rows = count($this->Order->getOrderDetails());

        $subTotal = 0;
        foreach ($this->Order->getOrderDetails() as $Detail) {
            $Detail->setPrice($price);
            $Detail->setQuantity($quantity);
            $subTotal = $Detail->getPriceIncTax() * $Detail->getQuantity();
        }
        $this->Order->setSubTotal($subTotal);
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = ($price + ($price * ($this->rate / 100))) * $quantity * $rows;
        $this->actual = $this->app['eccube.service.order']->getSubTotal($Result);
        $this->verify();
    }

    public function testGetTotalQuantity()
    {
        $quantity = 3;
        $rows = count($this->Order->getOrderDetails());

        $total = 0;
        foreach ($this->Order->getOrderDetails() as $Detail) {
            $Detail->setQuantity($quantity);
            $total += $Detail->getQuantity();
        }
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = $total;
        $this->actual = $this->app['eccube.service.order']->getTotalQuantity($Result);
        $this->verify();
    }

    public function testGetTotalTax()
    {
        $quantity = 3;
        $price = 100;
        $rows = count($this->Order->getOrderDetails());

        $totalTax = 0;
        foreach ($this->Order->getOrderDetails() as $Detail) {
            $Detail->setPrice($price);
            $Detail->setQuantity($quantity);
            $totalTax += ($Detail->getPriceIncTax() - $Detail->getPrice()) * $Detail->getQuantity();
        }
        $this->app['orm.em']->flush();

        $Result = $this->app['eccube.repository.order']->find($this->Order->getId());

        $this->expected = ($price * ($this->rate / 100)) * $quantity * $rows;
        $this->actual = $this->app['eccube.service.order']->getTotalTax($Result);
        $this->verify();
    }

    public function testGetProductTypes()
    {
        $this->expected = array($this->app['eccube.repository.master.product_type']->find(1));
        $this->actual = $this->app['eccube.service.order']->getProductTypes($this->Order);
        $this->verify();
    }
}
