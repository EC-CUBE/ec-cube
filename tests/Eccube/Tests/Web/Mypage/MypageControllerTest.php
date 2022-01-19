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

namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Tests\Fixture\Generator;
use Eccube\Tests\Web\AbstractWebTestCase;

class MypageControllerTest extends AbstractWebTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

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
            $this->generateUrl('mypage_favorite_delete', ['id' => $TestFavorite->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->entityManager->remove($TestFavorite);
        $this->entityManager->flush();
    }

    public function testRoutingOrder()
    {
        $this->loginTo($this->Customer);
        $client = $this->client;

        $Order = $this->createOrder($this->Customer);

        $client->request('PUT',
            $this->generateUrl('mypage_order', ['order_no' => $Order->getOrderNo()])
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
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = self::$container->get(Generator::class)->createOrder($this->Customer, [$ProductClasses[0]], null,
            0, 0, OrderStatus::NEW);
        $this->loginTo($this->Customer);
        $client = $this->client;

        $crawler = $client->request(
            'GET',
            $this->generateUrl('mypage_history', ['order_no' => $Order->getOrderNo()])
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testHistory404()
    {
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = self::$container->get(Generator::class)->createOrder($this->Customer, [$ProductClasses[0]], null,
            0, 0, OrderStatus::PROCESSING);
        $this->loginTo($this->Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_history', ['order_no' => $Order->getOrderNo()])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testHistoryWithNotFound()
    {
        $this->loginTo($this->Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_history', ['order_no' => 999999999])
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
        $expectedIds = [];
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
            $CustomerFavoriteProduct->setCreateDate(new \DateTime('-'.$i.' days'));
            $this->entityManager->flush();
        }

        $this->loginTo($this->Customer);
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_favorite')
        );
        // 最初の画面で表示されているお気に入りの ID を取得する
        $actualIds = [];
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
        $Product = $this->entityManager->getRepository(\Eccube\Entity\Product::class)->find(1);
        $CustomerFavoriteProduct->setCreateDate(new \DateTime());
        $CustomerFavoriteProduct->setUpdateDate(new \DateTime());
        $CustomerFavoriteProduct->setProduct($Product);

        return $CustomerFavoriteProduct;
    }
}
