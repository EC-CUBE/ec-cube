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

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MypageControllerTest extends AbstractWebTestCase
{

    public function testRoutingFavorite()
    {
        $this->logIn();
        $client = $this->client;

        $client->request('GET', $this->app->url('mypage_favorite'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingFavoriteDelete()
    {
        $this->logIn();
        $client = $this->client;

        // before
        $TestFavorite = $this->newTestFavorite();
        $this->app['orm.em']->persist($TestFavorite);
        $this->app['orm.em']->flush();

        // main
        $redirectUrl = $this->app->url('mypage_favorite');
        $client->request('DELETE',
            $this->app->url('mypage_favorite_delete', array('id' => $TestFavorite->getId()))
        );
        $this->assertTrue($client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestFavorite);
        $this->app['orm.em']->flush();
    }


    public function testRoutingOrder()
    {
        $this->logIn();
        $client = $this->client;

        $Order = $this->createOrder($this->app->user());

        $client->request('PUT',
            $this->app->url('mypage_order', array('id' => $Order->getId()))
        );

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testLogin()
    {
        $this->logIn();
        $client = $this->client;
        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_login')
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('mypage')));
    }

    public function testLoginWithFailure()
    {
        $client = $this->client;
        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_login')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testIndex()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $this->logIn($Customer);
        $client = $this->client;

        $crawler = $client->request(
            'GET',
            $this->app->path('mypage')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testHistory()
    {
        $Customer = $this->createCustomer();
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
        // 後方互換のため最初の1つのみ渡す
        $Order = $this->app['eccube.fixture.generator']->createOrder($Customer, array($ProductClasses[0]),null,0,0, 'order_new');
        $this->logIn($Customer);
        $client = $this->client;

        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_history', array('id' => $Order->getId()))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
        
    }
    public function testHistory404()
    {
        $Customer = $this->createCustomer();
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();
         // 後方互換のため最初の1つのみ渡す
        $Order = $this->app['eccube.fixture.generator']->createOrder($Customer, array($ProductClasses[0]),null,0,0,'order_processing');
        $this->logIn($Customer);
        $client = $this->client;
        // debugはONの時に404ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        }
        
        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_history', array('id' => $Order->getId()))
        );
        // debugはOFFの時に404ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 404;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }

    public function testHistoryWithNotfound()
    {
        $Customer = $this->createCustomer();

        $this->logIn($Customer);
        $client = $this->client;
        // debugはONの時に404ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        }

        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_history', array('id' => 999999999))
        );
        // debugはOFFの時に404ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 404;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }

    /**
     * Paginator を経由したお気に入りの取得
     *
     * 主に正しくソートされているかチェックする.
     */
    public function testFavoriteWithPaginator()
    {
        $Customer = $this->createCustomer();
        $expectedIds = array();
        for ($i = 0; $i < 30; $i++) {
            $Product = $this->createProduct();
            $expectedIds[] = $Product->getId();
            $CustomerFavoriteProduct = new \Eccube\Entity\CustomerFavoriteProduct();
            $CustomerFavoriteProduct->setCustomer($Customer);
            $CustomerFavoriteProduct->setProduct($Product);
            $CustomerFavoriteProduct->setDelFlg(0);
            $this->app['orm.em']->persist($CustomerFavoriteProduct);
            $this->app['orm.em']->flush($CustomerFavoriteProduct);

            // id とは 逆順に create_date を設定する.
            // 画面表示は create_date 降順なので, id 昇順にソートされるはず
            $CustomerFavoriteProduct->setCreateDate(new \DateTime('-'.$i.' days'));
            $this->app['orm.em']->flush($CustomerFavoriteProduct);
        }

        $client = $this->loginTo($Customer);
        $crawler = $client->request(
            'GET',
            $this->app->path('mypage_favorite')
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
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = array_slice($expectedIds, 0, count($actualIds));
        $this->actual = $actualIds;
        $this->verify('画面表示は create_date 降順なので, id 昇順にソートされるはず');
    }

    private function newTestFavorite()
    {
        $CustomerFavoriteProduct = new \Eccube\Entity\CustomerFavoriteProduct();
        $CustomerFavoriteProduct->setCustomer($this->app->user());
        $Product = $this->app['eccube.repository.product']->get(1);
        $CustomerFavoriteProduct->setProduct($Product);
        $CustomerFavoriteProduct->setDelFlg(0);

        return $CustomerFavoriteProduct;
    }

}
