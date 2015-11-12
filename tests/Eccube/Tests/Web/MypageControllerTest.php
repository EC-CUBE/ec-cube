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
