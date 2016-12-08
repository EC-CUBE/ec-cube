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
                // add child category
                $Category->addChild($Child);

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
                    // add child category
                    $Child->addChild($Grandson);
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
    }

    public function testRoutingAdminProductCategoryShow()
    {
        // before
        $TestCreator = $this->createMember();
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
        $TestCreator = $this->createMember();
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

    public function testRoutingAdminProductCategoryDelete()
    {
        // before
        $TestCreator = $this->createMember();
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

        // after
        $this->app['orm.em']->remove($TestCategory);
        $this->app['orm.em']->flush();
    }

    public function testMoveRank()
    {
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '子1'));

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_category_rank_move'),
            array($Category->getId() => 10),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $MovedCategory = $this->app['eccube.repository.category']->find($Category->getId());
        $this->expected = 10;
        $this->actual = $MovedCategory->getRank();
        $this->verify();
    }

    public function testExport()
    {
        $this->expectOutputRegex('/2-0/', '2-0 という文字列が含まれる CSV が出力されるか');

        $this->client->request('GET',
            $this->app->url('admin_product_category_export')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
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

    public function testMoveRankAndShow()
    {
        // Give
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '親1'));
        $Category2 = $this->app['eccube.repository.category']->findOneBy(array('name' => '親2'));
        $newRanks = array(
            $Category->getId() => $Category2->getRank(),
            $Category2->getId() => $Category->getRank()
        );

        // When
        $this->client->request(
            'POST',
            $this->app->url('admin_product_category_rank_move'),
            $newRanks,
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = $newRanks[$Category->getId()];
        $this->actual = $Category->getRank();
        $this->verify();

        $crawler = $this->client->request('GET',
            $this->app->url('admin_product_product_new')
        );

        $CategoryLast = $this->app['eccube.repository.category']->findOneBy(array('name' => '子2-2'));
        $categoryNameLastElement = $crawler->filter('#detail_wrap select#admin_product_Category option')->last()->text();

        $this->expected = $CategoryLast->getNameWithLevel();
        $this->actual = $categoryNameLastElement;
        $this->verify();
    }
}
