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

namespace Eccube\Tests\Web;

use Eccube\Entity\Category;

class SearchProductControllerTest extends AbstractWebTestCase
{
    /**
     * @var \Eccube\Repository\CategoryRepository
     */
    protected $categoryRepository;

    public function setUp()
    {
        parent::setUp();
        $this->remove();
        $this->createCategories();
        $this->categoryRepository = $this->entityManager->getRepository(\Eccube\Entity\Category::class);
    }

    public function createCategories()
    {
        $categories = [
            ['name' => '親1', 'hierarchy' => 1, 'sort_no' => 1,
                'child' => [
                    ['name' => '子1', 'hierarchy' => 2, 'sort_no' => 4,
                        'child' => [
                            ['name' => '孫1', 'hierarchy' => 3, 'sort_no' => 9],
                        ],
                    ],
                ],
            ],
            ['name' => '親2', 'hierarchy' => 1, 'sort_no' => 2,
                'child' => [
                    ['name' => '子2-0', 'hierarchy' => 2, 'sort_no' => 5,
                        'child' => [
                            ['name' => '孫2', 'hierarchy' => 3, 'sort_no' => 10],
                        ],
                    ],
                    ['name' => '子2-1', 'hierarchy' => 2, 'sort_no' => 6],
                    ['name' => '子2-2', 'hierarchy' => 2, 'sort_no' => 7],
                ],
            ],
            ['name' => '親3', 'hierarchy' => 1, 'sort_no' => 3,
                'child' => [
                    ['name' => '子3', 'hierarchy' => 2, 'sort_no' => 8,
                        'child' => [
                            ['name' => '孫3', 'hierarchy' => 3, 'sort_no' => 11],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $category_array) {
            $Category = new Category();
            $Category->setPropertiesFromArray($category_array);
            $Category->setCreateDate(new \DateTime());
            $Category->setUpdateDate(new \DateTime());
            $this->entityManager->persist($Category);
            $this->entityManager->flush();

            if (!array_key_exists('child', $category_array)) {
                continue;
            }
            foreach ($category_array['child'] as $child_array) {
                $Child = new Category();
                $Child->setPropertiesFromArray($child_array);
                $Child->setParent($Category);
                $Child->setCreateDate(new \DateTime());
                $Child->setUpdateDate(new \DateTime());
                $this->entityManager->persist($Child);
                $this->entityManager->flush();

                $Category->addChild($Child);

                if (!array_key_exists('child', $child_array)) {
                    continue;
                }
                foreach ($child_array['child'] as $grandson_array) {
                    $Grandson = new Category();
                    $Grandson->setPropertiesFromArray($grandson_array);
                    $Grandson->setParent($Child);
                    $Grandson->setCreateDate(new \DateTime());
                    $Grandson->setUpdateDate(new \DateTime());
                    $this->entityManager->persist($Grandson);
                    $this->entityManager->flush();
                    $Child->addChild($Grandson);
                }
            }
        }
    }

    /**
     * 既存のデータを論理削除しておく.
     */
    public function remove()
    {
        $this->deleteAllRows([
            'dtb_product_category',
            'dtb_category',
        ]);
    }

    public function testRoutingSearchProduct()
    {
        $this->client->request('GET', $this->generateUrl('block_search_product'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testCategory()
    {
        // Give
        $Category = $this->categoryRepository->findOneBy(['name' => '孫1']);

        // When
        $crawler = $this->client->request('GET', $this->generateUrl('block_search_product'));

        // Then
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $categoryNameLastElement = $crawler->filter('.category_id option')->last()->text();

        $this->expected = $Category->getNameWithLevel();
        $this->actual = $categoryNameLastElement;
        $this->verify();
    }
}
