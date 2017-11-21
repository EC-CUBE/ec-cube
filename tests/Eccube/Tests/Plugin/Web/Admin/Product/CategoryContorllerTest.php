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


namespace Eccube\Tests\Plugin\Web\Admin\Product;

use Eccube\Entity\Category;
use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;

/**
 * @group plugin
 */
class CategoryControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->remove();
        $this->createCategories();
    }

    public function createCategories()
    {
        $categories = array(
            array('name' => '親1', 'hierarchy' => 1, 'sort_no' => 1,
                  'child' => array(
                      array('name' => '子1', 'hierarchy' => 2, 'sort_no' => 4,
                            'child' => array(
                                array('name' => '孫1', 'hierarchy' => 3, 'sort_no' => 9)
                            ),
                      ),
                  ),
            ),
            array('name' => '親2', 'hierarchy' => 1, 'sort_no' => 2,
                  'child' => array(
                      array('name' => '子2-0', 'hierarchy' => 2, 'sort_no' => 5,
                            'child' => array(
                                array('name' => '孫2', 'hierarchy' => 3, 'sort_no' => 10)
                            )
                      ),
                      array('name' => '子2-1', 'hierarchy' => 2, 'sort_no' => 6),
                      array('name' => '子2-2', 'hierarchy' => 2, 'sort_no' => 7)
                  ),
            ),
            array('name' => '親3', 'hierarchy' => 1, 'sort_no' => 3,
                  'child' => array(
                      array('name' => '子3', 'hierarchy' => 2, 'sort_no' => 8,
                            'child' => array(
                                array('name' => '孫3', 'hierarchy' => 3, 'sort_no' => 11)
                            )
                      )
                  ),
            ),
        );

        foreach ($categories as $category_array) {
            $Category = new Category();
            $Category->setPropertiesFromArray($category_array);
            $Category->setCreateDate(new \DateTime());
            $Category->setUpdateDate(new \DateTime());
            $this->app['orm.em']->persist($Category);
            $this->app['orm.em']->flush();
            if (!array_key_exists('child', $category_array)) {
                continue;
            }
            foreach ($category_array['child'] as $child_array) {
                $Child = new Category();
                $Child->setPropertiesFromArray($child_array);
                $Child->setParent($Category);
                $Child->setCreateDate(new \DateTime());
                $Child->setUpdateDate(new \DateTime());
                $this->app['orm.em']->persist($Child);
                $this->app['orm.em']->flush();
                if (!array_key_exists('child', $child_array)) {
                    continue;
                }
                foreach ($child_array['child'] as $grandson_array) {
                    $Grandson = new Category();
                    $Grandson->setPropertiesFromArray($grandson_array);
                    $Grandson->setParent($Child);
                    $Grandson->setCreateDate(new \DateTime());
                    $Grandson->setUpdateDate(new \DateTime());
                    $this->app['orm.em']->persist($Grandson);
                    $this->app['orm.em']->flush();
                }
            }
        }
    }

    /**
     * 既存のデータを論理削除しておく.
     */
    public function remove() {
        $Categories = $this->app['eccube.repository.category']->findAll();
        foreach ($Categories as $Category) {
            $this->app['orm.em']->merge($Category);
        }
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminProductCategory()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_category')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_category'),
            array('admin_category' => array(
                '_token' => 'dummy',
                'name' => 'テストカテゴリ'
            ))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_category')));

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithPostParent()
    {
        $Parent = $this->app['eccube.repository.category']->findOneBy(array('name' => '子1'));
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_category_show', array('parent_id' => $Parent->getId())),
            array('admin_category' => array(
                '_token' => 'dummy',
                'name' => 'テストカテゴリ'
            ))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_category_show', array('parent_id' => $Parent->getId()))));

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE,
        );

        $this->verifyOutputString($expected);
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
        $this->client->request('DELETE',
            $this->app->url('admin_product_category_delete',
                array('id' => $test_category_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE,
        );

        $this->verifyOutputString($expected);

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
    }

    /**
     * test export category
     */
    public function testExport()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_category_export')
        );
        $expected = EccubeEvents::ADMIN_PRODUCT_CATEGORY_CSV_EXPORT;
        $this->expectOutputRegex("/".$expected."/");
    }

    private function newTestCategory($TestCreator, $TestParentCategory = null)
    {
        $TestCategory = new \Eccube\Entity\Category();
        if ($TestParentCategory == null) {
            $TestCategory->setName('テスト家具')
                ->setSortNo(100)
                ->setHierarchy(100)
                ->setParent($TestParentCategory)
                ->setCreator($TestCreator);
        } else {
            $TestCategory->setName($TestParentCategory->getName() . '_c')
                ->setSortNo($TestParentCategory->getSortNo() + 1)
                ->setHierarchy($TestParentCategory->getHierarchy() + 1)
                ->setParent($TestParentCategory)
                ->setCreator($TestCreator);
        }

        return $TestCategory;
    }
}
