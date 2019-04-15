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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\ProductClass;

class CartControllerTest extends AbstractWebTestCase
{
    public function testRoutingCart()
    {
        $this->client->request('GET', '/cart');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingCartUp()
    {
        $this->client->request('PUT', '/cart/up/1',
            [Constant::TOKEN_NAME => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testRoutingCartDown()
    {
        $this->client->request('PUT', '/cart/down/1',
            [Constant::TOKEN_NAME => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testRoutingCartRemove()
    {
        $this->client->request('PUT', '/cart/remove/1',
            [Constant::TOKEN_NAME => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    /**
     * https://github.com/EC-CUBE/ec-cube/pull/3499
     */
    public function testCartErrors()
    {
        $this->cartIn(1);
        $this->cartIn(2);

        $ProductClass1 = $this->entityManager->find(ProductClass::class, 1);
        $ProductClass2 = $this->entityManager->find(ProductClass::class, 2);

        // 販売制限数を0に設定
        $ProductClass1->setSaleLimit(0);
        $ProductClass2->setSaleLimit(0);
        $this->entityManager->flush();

        // エラーが2件表示される
        $crawler = $this->client->request('GET', '/cart');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(2, $crawler->filter('div.ec-cartRole__error'));
    }

    private function cartIn($product_class_id)
    {
        $this->client->request(
            'PUT',
            $this->generateUrl(
                'cart_handle_item',
                [
                    'operation' => 'up',
                    'productClassId' => $product_class_id,
                ]
            ),
            [Constant::TOKEN_NAME => '_dummy']
        );
    }
}
