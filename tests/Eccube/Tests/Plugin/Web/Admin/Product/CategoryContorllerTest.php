<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Common\Constant;
use Eccube\Entity\Category;

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
            array('name' => '親1', 'level' => 1, 'rank' => 1,
                  'child' => array(
                      array('name' => '子1', 'level' => 2, 'rank' => 4,
                            'child' => array(
                                array('name' => '孫1', 'level' => 3, 'rank' => 9)
                            ),
                      ),
                  ),
            ),
            array('name' => '親2', 'level' => 1, 'rank' => 2,
                  'child' => array(
                      array('name' => '子2-0', 'level' => 2, 'rank' => 5,
                            'child' => array(
                                array('name' => '孫2', 'level' => 3, 'rank' => 10)
                            )
                      ),
                      array('name' => '子2-1', 'level' => 2, 'rank' => 6),
                      array('name' => '子2-2', 'level' => 2, 'rank' => 7)
                  ),
            ),
            array('name' => '親3', 'level' => 1, 'rank' => 3,
                  'child' => array(
                      array('name' => '子3', 'level' => 2, 'rank' => 8,
                            'child' => array(
                                array('name' => '孫3', 'level' => 3, 'rank' => 11)
                            )
                      )
                  ),
            ),
        );

        foreach ($categories as $category_array) {
            $Category = new Category();
            $Category->setPropertiesFromArray($category_array);
            $Category->setDelFlg(Constant::DISABLED);
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
                $Child->setDelFlg(Constant::DISABLED);
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
                    $Grandson->setDelFlg(Constant::DISABLED);
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
            $Category->setDelFlg(Constant::ENABLED);
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
