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


namespace Eccube\Tests\Web;

use Eccube\Entity\Product;
use Symfony\Component\HttpKernel\Client;

class CartValidationTest extends AbstractWebTestCase
{

    // 商品詳細画面からカート画面のvalidation

    /**
     * 在庫制限チェック
     */
    public function testValidationStock()
    {
        /** @var Product $Product */
        $Product = $this->createProduct('test1');

        $ProductClass = $Product->getProductClasses()->get(1);

        // 在庫数を設定
        $ProductClass->setStock(1);
        $this->app['orm.em']->persist($ProductClass);

        $arr = array(
            'product_id' => $Product->getId(),
            'mode' => 'add_cart',
            'product_class_id' => $ProductClass->getId(),
            'quantity' => 9999,
            '_token' => 'dummy',
        );

        /** @var Client $client */
        $client = $this->client;

        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => $Product->getId())),
            $arr
        );

        $crawler = $client->followRedirect();

        // エラーメッセージは改行されているため2回に分けてチェック

        $message = $crawler->filter('.errormsg')->text();

        $this->assertContains('選択された商品(test1)の在庫が不足しております。', $message);

        $this->assertContains( '一度に在庫数を超える購入はできません。', $message);

    }

}
