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

use Eccube\Common\Constant;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ClassCategoryControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    public function setUp()
    {
        parent::setUp();

        $this->classNameRepository = $this->entityManager->getRepository(\Eccube\Entity\ClassName::class);
        $this->classCategoryRepository = $this->entityManager->getRepository(\Eccube\Entity\ClassCategory::class);
    }

    public function testRoutingAdminProductClassCategory()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepository
            ->findOneBy([
                'name' => $TestClassName->getName(),
            ])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_category', ['class_name_id' => $test_class_name_id])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassCategoryEdit()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();

        $test_class_name_id = $this->classNameRepository
            ->findOneBy([
                'name' => $TestClassName->getName(),
            ])
            ->getId();

        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $test_class_category_id = $this->classCategoryRepository
            ->findOneBy([
                'name' => $TestClassCategory->getName(),
            ])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_category_edit',
                ['class_name_id' => $test_class_name_id, 'id' => $test_class_category_id]),
            ['_token' => 'dummy']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassCategoryEditInline()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $classNameId = $TestClassName->getId();

        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $classCategoryId = $TestClassCategory->getId();

        $editName = 'new name';

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_category',
                ['class_name_id' => $classNameId])
        );
        $editInlineForm = [
            'class_category_'.$classCategoryId => [
                'name' => $editName,
                'backend_name' => $editName,
                'visible' => true,
                Constant::TOKEN_NAME => 'dummy',
            ],
        ];
        $this->client->request('POST',
            $this->generateUrl('admin_product_class_category_edit', ['class_name_id' => $classNameId, 'id' => $classCategoryId]),
            $editInlineForm
        );

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains($editName, $crawler->filter('ul.sortable-container li:nth-child(3)')->text());
    }

    public function testRoutingAdminProductClassCategoryDelete()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepository
            ->findOneBy([
                'name' => $TestClassName->getName(),
            ])
            ->getId();
        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $test_class_category_id = $this->classCategoryRepository
            ->findOneBy([
                'name' => $TestClassCategory->getName(),
            ])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_class_category', ['class_name_id' => $test_class_name_id]);
        $this->client->request('DELETE',
            $this->generateUrl('admin_product_class_category_delete',
                ['class_name_id' => $test_class_name_id, 'id' => $test_class_category_id]
                ),
            ['_token' => 'dummy']
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminProductClassCategoryToggle()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepository
            ->findOneBy([
                'name' => $TestClassName->getName(),
            ])
            ->getId();
        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $test_class_category_id = $this->classCategoryRepository
            ->findOneBy([
                'name' => $TestClassCategory->getName(),
            ])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_class_category', ['class_name_id' => $test_class_name_id]);
        $this->client->request('PUT',
            $this->generateUrl('admin_product_class_category_visibility',
                ['class_name_id' => $test_class_name_id, 'id' => $test_class_category_id]),
            ['_token' => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    /**
     * testProductClassSortByRank
     */
    public function testClassCategorySortByRank()
    {
        /* @var $ClassCategory \Eccube\Entity\ClassCategory */
        //set チョコ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'チョコ']);
        $testData[$ClassCategory->getId()] = 1;
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set 抹茶 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '抹茶']);
        $testData[$ClassCategory->getId()] = 3;
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        //set バニラ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'バニラ']);
        $testData[$ClassCategory->getId()] = 2;
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);

        $client = $this->client;
        $client->request('POST', $this->generateUrl('admin_product_class_category_sort_no_move'),
            $testData,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
        /** @var Crawler $crawler */
        $crawler = $client->request('GET', $this->generateUrl('admin_product_class_category', ['class_name_id' => 1]));

        //チョコ, 抹茶, バニラ sort by rank setup above.
        $this->expected = '抹茶';
        $this->actual = $crawler->filter('ul.sortable-container > li:nth-child(3)')->text();
        $this->assertContains($this->expected, $this->actual);
        $this->expected = 'バニラ';
        $this->actual = $crawler->filter('ul.sortable-container > li:nth-child(4)')->text();
        $this->assertContains($this->expected, $this->actual);
        $this->expected = 'チョコ';
        $this->actual = $crawler->filter('ul.sortable-container > li:nth-child(5)')->text();
        $this->assertContains($this->expected, $this->actual);
    }

    private function newTestClassName($TestCreator)
    {
        $TestClassName = new \Eccube\Entity\ClassName();
        $TestClassName->setName('形状')
            ->setSortNo(100)
            ->setCreator($TestCreator);

        return $TestClassName;
    }

    private function newTestClassCategory($TestCreator, $TestClassName)
    {
        $TestClassCategory = new \Eccube\Entity\ClassCategory();
        $TestClassCategory->setName('立方体')
            ->setSortNo(100)
            ->setClassName($TestClassName)
            ->setBackendName($TestClassName)
            ->setVisible(true)
            ->setCreator($TestCreator);

        return $TestClassCategory;
    }
}
