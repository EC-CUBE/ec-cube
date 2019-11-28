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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Category;
use Eccube\Repository\CategoryRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * CategoryRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CategoryRepositoryTest extends EccubeTestCase
{
    /**
     * @var  CategoryRepository
     */
    protected $categoryRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->categoryRepository = $this->entityManager->getRepository(\Eccube\Entity\Category::class);
        $this->remove();
        $this->createCategories();
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
                $Category->addChild($Child);
                $this->entityManager->persist($Child);
                $this->entityManager->flush();
                if (!array_key_exists('child', $child_array)) {
                    continue;
                }
                foreach ($child_array['child'] as $grandson_array) {
                    $Grandson = new Category();
                    $Grandson->setPropertiesFromArray($grandson_array);
                    $Grandson->setParent($Child);
                    $Grandson->setCreateDate(new \DateTime());
                    $Grandson->setUpdateDate(new \DateTime());
                    $Child->addChild($Grandson);
                    $this->entityManager->persist($Grandson);
                    $this->entityManager->flush();
                }
            }
        }
        // 登録したEntityをEntityManagerからクリアする
        // ソート順が上記の登録順でキャッシュされているため、クリアして、DBから再取得させる
        $this->entityManager->clear();
    }

    /**
     * 既存のデータを削除しておく.
     */
    public function remove()
    {
        $this->deleteAllRows([
            'dtb_product_category',
            'dtb_category',
        ]);
    }

    public function testGetTotalCount()
    {
        $this->expected = 11;
        $this->actual = $this->categoryRepository->getTotalCount();

        $this->verify('カテゴリの合計数は'.$this->expected.'ではありません');
    }

    public function testGetList()
    {
        $Categories = $this->categoryRepository->getList();

        $this->expected = 3;
        $this->actual = count($Categories);
        $this->verify('ルートカテゴリの合計数は'.$this->expected.'ではありません');

        $this->actual = [];
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = ['親3', '親2', '親1'];
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testGetListWithParent()
    {
        $Parent1 = $this->categoryRepository->findOneBy(['name' => '子1']);
        $Categories = $this->categoryRepository->getList($Parent1);

        $this->expected = 1;
        $this->actual = count($Categories);
        $this->verify('ルートカテゴリの合計数は'.$this->expected.'ではありません');

        $this->actual = [];
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = ['孫1'];
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testGetListWithFlat()
    {
        $Categories = $this->categoryRepository->getList(null, true);

        $this->expected = 11;
        $this->actual = count($Categories);
        $this->verify('ルートカテゴリの合計数は'.$this->expected.'ではありません');

        $this->actual = [];
        foreach ($Categories as $Category) {
            $this->actual[] = $Category->getName();
        }

        $this->expected = ['親3', '子3', '孫3', '親2', '子2-2', '子2-1', '子2-0', '孫2', '親1', '子1', '孫1'];
        $this->verify('取得したカテゴリ名が正しくありません');
    }

    public function testSave()
    {
        $faker = $this->getFaker();
        $name = $faker->name;
        $Category = new Category();
        $Category->setName($name)
            ->setHierarchy(1);
        $this->categoryRepository->save($Category);

        $this->expected = 12;
        $this->actual = $Category->getSortNo();
        $this->verify('カテゴリの sort_no は'.$this->expected.'ではありません');
    }

    public function testSaveWithParent()
    {
        $faker = $this->getFaker();
        $name = $faker->name;
        $Category = $this->categoryRepository->findOneBy(['name' => '子2-1']);
        $Category->setName($name);
        $updateDate = $Category->getUpdateDate();
        sleep(1);
        $this->categoryRepository->save($Category);

        $this->expected = $updateDate;
        $this->actual = $Category->getUpdateDate();

        $this->assertNotEquals($this->expected, $this->actual);

        // 名前を変更したので null になっているはず
        $Category = $this->categoryRepository->findOneBy(['name' => '子2-1']);
        $this->assertNull($Category);
    }

    public function testDelete()
    {
        $Category = $this->categoryRepository->findOneBy(['name' => '孫2']);

        $this->categoryRepository->delete($Category);

        $Category = $this->categoryRepository->findOneBy(['name' => '孫2']);
        $this->assertNull($Category);
    }

    public function testDeleteFail()
    {
        // 商品をカテゴリに紐付けて作成.
        $this->createProduct();
        $Category = $this->categoryRepository->findOneBy(['name' => '孫2']);

        try {
            // 紐付いた商品が存在している場合は削除できない.
            $this->categoryRepository->delete($Category);
            $this->fail();
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }
}
