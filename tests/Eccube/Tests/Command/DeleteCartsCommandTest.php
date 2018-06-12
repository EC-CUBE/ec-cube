<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Tests\Command;

use Eccube\Command\DeleteCartsCommand;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DeleteCartsCommandTest extends EccubeTestCase
{
    public function testShouldDeletePastCarts()
    {
        $Product = $this->createProduct();
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $Cart = new Cart();
        $Cart->setTotalPrice(1000);
        $Cart->setDeliveryFeeTotal(0);

        $CartItem = new CartItem();
        $CartItem->setProductClass($ProductClass);
        $CartItem->setQuantity(1);
        $CartItem->setPrice(1000);
        $Cart->addCartItem($CartItem);

        $this->entityManager->persist($Cart);
        $this->entityManager->flush();

        $id = $Cart->getId();

        self::assertNotNull($this->entityManager->find(Cart::class, $id));

        /** @var DeleteCartsCommand $command */
        $command = $this->container->get(DeleteCartsCommand::class);

        $tester = new CommandTester($command);
        $tomorrow = new \DateTime('tomorrow');
        $tester->execute(['date'=>$tomorrow->format('Y/m/d')]);

        $this->entityManager->clear();

        self::assertNull($this->entityManager->find(Cart::class, $id));
    }

    public function testShouldNotDeleteFutureCarts()
    {
        $Product = $this->createProduct();
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $Cart = new Cart();
        $Cart->setTotalPrice(1000);
        $Cart->setDeliveryFeeTotal(0);

        $CartItem = new CartItem();
        $CartItem->setProductClass($ProductClass);
        $CartItem->setQuantity(1);
        $CartItem->setPrice(1000);
        $Cart->addCartItem($CartItem);

        $this->entityManager->persist($Cart);
        $this->entityManager->flush();

        $id = $Cart->getId();

        self::assertNotNull($this->entityManager->find(Cart::class, $id));

        /** @var DeleteCartsCommand $command */
        $command = $this->container->get(DeleteCartsCommand::class);

        $tester = new CommandTester($command);
        $tomorrow = new \DateTime('yesterday');
        $tester->execute(['date'=>$tomorrow->format('Y/m/d')]);

        $this->entityManager->clear();

        self::assertNotNull($this->entityManager->find(Cart::class, $id));
    }
}
