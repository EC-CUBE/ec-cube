<?php

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\Category;

class SearchProductControllerTest extends AbstractWebTestCase
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

    public function testRoutingSearchProduct()
    {
        $this->client->request('GET',
            $this->app->url('block_search_product')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testCategory()
    {
        // Give
        $Category = $this->app['eccube.repository.category']->findOneBy(array('name' => '孫1'));

        // When
        $crawler = $this->client->request('GET',
            $this->app->url('block_search_product')
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $categoryNameLastElement = $crawler->filter('.search select#category_id option')->last()->text();

        $this->expected = $Category->getNameWithLevel();
        $this->actual = $categoryNameLastElement;
        $this->verify();
    }
}
