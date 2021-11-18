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

namespace Eccube\Tests\Repository;

use Eccube\Repository\CartItemRepository;
use Eccube\Tests\EccubeTestCase;

class CartItemRepositoryTest extends EccubeTestCase
{
    public function testConstruct()
    {
        /** @var CartItemRepository $CartItemRepository */
        $CartItemRepository = $this->entityManager->getRepository(\Eccube\Entity\CartItem::class);
        $this->assertInstanceOf(CartItemRepository::class, $CartItemRepository);

        $CartItem = $CartItemRepository->find(1);
        $this->assertEmpty($CartItem);
    }
}
