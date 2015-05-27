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


namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CategoryControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRoutingAdminProductCategory()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_category')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductCategoryShow()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestParentCategory = $this->newTestCategory($TestCreator);
        $this->app['orm.em']->persist($TestParentCategory);
        $this->app['orm.em']->flush();
        $TestCategory = $this->newTestCategory($TestCreator, $TestParentCategory);
        $this->app['orm.em']->persist($TestCategory);
        $this->app['orm.em']->flush();

        $test_parent_category_id = $this->app['eccube.repository.category']
            ->findOneBy(array(
                'name' => $TestParentCategory->getName()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->app->url('admin_product_category_show',
                array('parent_id' => $test_parent_category_id))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
        $this->app['orm.em']->remove($TestParentCategory);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminProductCategoryEdit()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestCategory = $this->newTestCategory($TestCreator);
        $this->app['orm.em']->persist($TestCategory);
        $this->app['orm.em']->flush();
        $test_category_id = $this->app['eccube.repository.category']
            ->findOneBy(array(
                'name' => $TestCategory->getName()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->app->url('admin_product_category_edit',
                array('id' => $test_category_id))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminProductCategoryUp()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestCategory = $this->newTestCategory($TestCreator);
        $this->app['orm.em']->persist($TestCategory);
        $this->app['orm.em']->flush();
        $test_category_id = $this->app['eccube.repository.category']
            ->findOneBy(array(
                'name' => $TestCategory->getName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_product_category');
        $this->client->request('POST',
            $this->app->url('admin_product_category_up',
                array('id' => $test_category_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminProductCategoryDown()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestCategory = $this->newTestCategory($TestCreator);
        $this->app['orm.em']->persist($TestCategory);
        $this->app['orm.em']->flush();
        $test_category_id = $this->app['eccube.repository.category']
            ->findOneBy(array(
                'name' => $TestCategory->getName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_product_category');
        $this->client->request('POST',
            $this->app->url('admin_product_category_down',
                array('id' => $test_category_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminProductCategoryDelete()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestCategory = $this->newTestCategory($TestCreator);
        $this->app['orm.em']->persist($TestCategory);
        $this->app['orm.em']->flush();
        $test_category_id = $this->app['eccube.repository.category']
            ->findOneBy(array(
                'name' => $TestCategory->getName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_product_category');
        $this->client->request('POST',
            $this->app->url('admin_product_category_delete',
                array('id' => $test_category_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
    }

    private function newTestCategory($TestCreator, $TestParentCategory = null)
    {
        $TestCategory = new \Eccube\Entity\Category();
        if ($TestParentCategory == null) {
            $TestCategory->setName('テスト家具')
                ->setRank(100)
                ->setLevel(100)
                ->setDelFlg(false)
                ->setParent($TestParentCategory)
                ->setCreator($TestCreator);
        } else {
            $TestCategory->setName($TestParentCategory->getName() . '_c')
                ->setRank($TestParentCategory->getRank() + 1)
                ->setLevel($TestParentCategory->getLevel() + 1)
                ->setDelFlg(false)
                ->setParent($TestParentCategory)
                ->setCreator($TestCreator);
        }

        return $TestCategory;
    }
}