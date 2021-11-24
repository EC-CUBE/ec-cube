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
        $CartItem->setCart($Cart);
        $Cart->addCartItem($CartItem);

        $this->entityManager->persist($Cart);
        $this->entityManager->flush();

        $id = $Cart->getId();

        self::assertNotNull($this->entityManager->find(Cart::class, $id));

        /** @var DeleteCartsCommand $command */
        $command = self::$container->get(DeleteCartsCommand::class);

        $tester = new CommandTester($command);
        $tomorrow = new \DateTime('+2day');
        $tester->execute(['date' => $tomorrow->format('Y/m/d')]);

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
        $CartItem->setCart($Cart);
        $Cart->addCartItem($CartItem);

        $this->entityManager->persist($Cart);
        $this->entityManager->flush();

        $id = $Cart->getId();

        self::assertNotNull($this->entityManager->find(Cart::class, $id));

        /** @var DeleteCartsCommand $command */
        $command = self::$container->get(DeleteCartsCommand::class);

        $tester = new CommandTester($command);
        $tomorrow = new \DateTime('yesterday');
        $tester->execute(['date' => $tomorrow->format('Y/m/d')]);

        $this->entityManager->clear();

        self::assertNotNull($this->entityManager->find(Cart::class, $id));
    }
}
