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

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Entity\Category;
use Eccube\Repository\CategoryRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CategoryControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function setUp()
    {
        parent::setUp();

        $this->remove();
        $this->createCategories();
        $this->client->disableReboot();
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
                // add child category
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
                    // add child category
                    $Child->addChild($Grandson);
                }
            }
        }
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

    public function testRoutingAdminProductCategory()
    {
        $this->client->request('GET',
            $this->generateUrl('admin_product_category')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithPost()
    {
        $params = [
            '_token' => 'dummy',
            'name' => 'テストカテゴリ',
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_category'),
            ['admin_category' => $params]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_product_category')));
    }

    public function testInlineEdit()
    {
        /** @var Category $Category */
        $Category = $this->categoryRepository->findOneBy(['name' => '親1']);
        $params = [
            'category_'.$Category->getId() => [
                '_token' => 'dummy',
                'name' => '親0',
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_category'),
            $params
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_product_category')));
        $this->assertEquals('親0', $Category->getName());
    }

    public function testInlineEditWithParent()
    {
        /** @var Category $Parent */
        $Parent = $this->categoryRepository->findOneBy(['name' => '親1']);

        /** @var Category $Category */
        $Category = $Parent->getChildren()->current();

        $params = [
            'category_'.$Category->getId() => [
                '_token' => 'dummy',
                'name' => '子0',
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_category_show', ['parent_id' => $Parent->getId()]),
            $params
        );

        $rUrl = $this->generateUrl('admin_product_category_show', ['parent_id' => $Parent->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($rUrl));
        $this->assertEquals('子0', $Category->getName());
    }

    public function testIndexWithPostParent()
    {
        $params = [
            '_token' => 'dummy',
            'name' => 'テストカテゴリ',
        ];
        $Parent = $this->categoryRepository->findOneBy(['name' => '子1']);
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_category_show', ['parent_id' => $Parent->getId()]),
            ['admin_category' => $params]
        );
        $url = $this->generateUrl('admin_product_category_show', ['parent_id' => $Parent->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($url));
    }

    public function testRoutingAdminProductCategoryShow()
    {
        // before
        $TestCreator = $this->createMember();
        $TestParentCategory = $this->newTestCategory($TestCreator);
        $this->entityManager->persist($TestParentCategory);
        $this->entityManager->flush();
        $TestCategory = $this->newTestCategory($TestCreator, $TestParentCategory);
        $this->entityManager->persist($TestCategory);
        $this->entityManager->flush();

        $test_parent_category_id = $this->categoryRepository
            ->findOneBy([
                'name' => $TestParentCategory->getName(),
            ])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_category_show',
                ['parent_id' => $test_parent_category_id])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductCategoryEdit()
    {
        // before
        $TestCreator = $this->createMember();
        $TestCategory = $this->newTestCategory($TestCreator);
        $this->entityManager->persist($TestCategory);
        $this->entityManager->flush();
        $test_category_id = $this->categoryRepository
            ->findOneBy([
                'name' => $TestCategory->getName(),
            ])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_category_edit',
                ['id' => $test_category_id])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductCategoryDelete()
    {
        // before
        $TestCreator = $this->createMember();
        $TestCategory = $this->newTestCategory($TestCreator);
        $this->entityManager->persist($TestCategory);
        $this->entityManager->flush();
        $test_category_id = $this->categoryRepository
            ->findOneBy([
                'name' => $TestCategory->getName(),
            ])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_category');
        $this->client->request('DELETE',
            $this->generateUrl('admin_product_category_delete',
                ['id' => $test_category_id]),
            ['_token' => 'dummy']
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testMoveSortNo()
    {
        $Category = $this->categoryRepository->findOneBy(['name' => '子1']);

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_category_sort_no_move'),
            [$Category->getId() => 10],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $MovedCategory = $this->categoryRepository->find($Category->getId());
        $this->expected = 10;
        $this->actual = $MovedCategory->getSortNo();
        $this->verify();
    }

    public function testExport()
    {
        // 2-0 という文字列が含まれる CSV が出力されるか
        $this->expectOutputRegex('/2-0/');

        $this->client->request('GET',
            $this->generateUrl('admin_product_category_export')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
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
            $TestCategory->setName($TestParentCategory->getName().'_c')
                ->setSortNo($TestParentCategory->getSortNo() + 1)
                ->setHierarchy($TestParentCategory->getHierarchy() + 1)
                ->setParent($TestParentCategory)
                ->setCreator($TestCreator);
        }

        return $TestCategory;
    }

    public function testMoveSortNoAndShow()
    {
        // FIXME doctrine/doctrine-bundleに起因してテストが通らないため一時的にスキップ
        // https://github.com/EC-CUBE/ec-cube/issues/4592
        $this->markTestIncomplete();

        // Give
        $Category = $this->categoryRepository->findOneBy(['name' => '親1']);
        $Category2 = $this->categoryRepository->findOneBy(['name' => '親2']);
        $newSortNos = [
            $Category->getId() => $Category2->getSortNo(),
            $Category2->getId() => $Category->getSortNo(),
        ];

        // When
        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_category_sort_no_move'),
            $newSortNos,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = $newSortNos[$Category->getId()];
        $this->actual = $Category->getSortNo();
        $this->verify();

        $crawler = $this->client->request('GET',
            $this->generateUrl('admin_product_product_new')
        );

        $CategoryLast = $this->categoryRepository->findOneBy(['name' => '子2-2']);
        $categoryNameLastElement = $crawler->filter('.c-directoryTree--register label')->last()->text();
        $this->expected = $CategoryLast->getName();
        $this->actual = $categoryNameLastElement;
        $this->verify();
    }
}
