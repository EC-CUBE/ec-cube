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


namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\Fixture\Generator;
use Eccube\Tests\Web\AbstractWebTestCase;

class MypageControllerTest extends AbstractWebTestCase
{
    /**
     * @var Customer
     */
    private $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    public function testRoutingFavorite()
    {
        $this->logInTo($this->Customer);

        $this->client->request('GET', $this->generateUrl('mypage_favorite'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingFavoriteDelete()
    {
        $this->logInTo($this->Customer);

        // before
        $TestFavorite = $this->newTestFavorite();
        $this->entityManager->persist($TestFavorite);
        $this->entityManager->flush();

        // main
        $redirectUrl = $this->generateUrl('mypage_favorite');
        $this->client->request('DELETE',
            $this->generateUrl('mypage_favorite_delete', array('id' => $TestFavorite->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->entityManager->remove($TestFavorite);
        $this->entityManager->flush();
    }


    public function testRoutingOrder()
    {
        self::markTestIncomplete('purchaseFlowに対応後、テストを作成');

        $this->loginTo($this->Customer);
        $client = $this->client;

        $Order = $this->createOrder($this->Customer);

        $client->request('PUT',
            $this->generateUrl('mypage_order', array('id' => $Order->getId()))
        );

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testLogin()
    {
        $this->logInTo($this->Customer);
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_login')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage')));
    }

    public function testLoginWithFailure()
    {
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_login')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndex()
    {
        $Order = $this->createOrder($this->Customer);
        $this->logInTo($this->Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testHistory()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = $this->container->get(Generator::class)->createOrder($this->Customer, array($ProductClasses[0]), null,
            0, 0, 'order_new');
        $this->loginTo($this->Customer);
        $client = $this->client;

        $crawler = $client->request(
            'GET',
            $this->generateUrl('mypage_history', array('id' => $Order->getId()))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testHistory404()
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = $this->container->get(Generator::class)->createOrder($this->Customer, array($ProductClasses[0]), null,
            0, 0, 'order_processing');
        $this->loginTo($this->Customer);
        $client = $this->client;

        $crawler = $client->request(
            'GET',
            $this->generateUrl('mypage_history', array('id' => $Order->getId()))
        );

        $this->expected = 404;
        $this->actual = $client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testHistoryWithNotFound()
    {
        $this->loginTo($this->Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_history', array('id' => 999999999))
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    /**
     * Paginator を経由したお気に入りの取得
     *
     * 主に正しくソートされているかチェックする.
     */
    public function testFavoriteWithPaginator()
    {
        $expectedIds = array();
        for ($i = 0; $i < 30; $i++) {
            $Product = $this->createProduct();
            $expectedIds[] = $Product->getId();
            $CustomerFavoriteProduct = new CustomerFavoriteProduct();
            $CustomerFavoriteProduct->setCustomer($this->Customer);
            $CustomerFavoriteProduct->setCreateDate(new \DateTime());
            $CustomerFavoriteProduct->setUpdateDate(new \DateTime());
            $CustomerFavoriteProduct->setProduct($Product);
            $this->entityManager->persist($CustomerFavoriteProduct);
            $this->entityManager->flush();

            // id とは 逆順に create_date を設定する.
            // 画面表示は create_date 降順なので, id 昇順にソートされるはず
            $CustomerFavoriteProduct->setCreateDate(new \DateTime('-' . $i . ' days'));
            $this->entityManager->flush();
        }

        $this->loginTo($this->Customer);
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_favorite')
        );
        // 最初の画面で表示されているお気に入りの ID を取得する
        $actualIds = array();
        $nodes = $crawler->filterXPath('//div[@class="product_item"]/a[1]');
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (preg_match('/detail\/([0-9]+)/', $href, $matched)) {
                $actualIds[] = $matched[1];
            }
        }
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = array_slice($expectedIds, 0, count($actualIds));
        $this->actual = $actualIds;
        $this->verify('画面表示は create_date 降順なので, id 昇順にソートされるはず');
    }

    private function newTestFavorite()
    {
        $CustomerFavoriteProduct = new CustomerFavoriteProduct();
        $CustomerFavoriteProduct->setCustomer($this->Customer);
        $Product = $this->container->get(ProductRepository::class)->find(1);
        $CustomerFavoriteProduct->setCreateDate(new \DateTime());
        $CustomerFavoriteProduct->setUpdateDate(new \DateTime());
        $CustomerFavoriteProduct->setProduct($Product);

        return $CustomerFavoriteProduct;
    }

}
