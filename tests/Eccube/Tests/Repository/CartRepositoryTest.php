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

use Eccube\Repository\CartRepository;
use Eccube\Tests\EccubeTestCase;

class CartRepositoryTest extends EccubeTestCase
{
    public function testConstruct()
    {
        /** @var CartRepository $CartRepository */
        $CartRepository = $this->entityManager->getRepository(\Eccube\Entity\Cart::class);
        $this->assertInstanceOf(CartRepository::class, $CartRepository);

        $Cart = $CartRepository->find(1);
        $this->assertEmpty($Cart);
    }
}
