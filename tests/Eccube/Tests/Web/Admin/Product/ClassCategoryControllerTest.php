<?php

declare(strict_types=1);

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
use Eccube\Entity\ClassCategory;
use Eccube\Entity\ClassName;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ClassCategoryControllerTest extends AbstractAdminWebTestCase
{
    protected ?ClassNameRepository $classNameRepository = null;

    protected ?ClassCategoryRepository $classCategoryRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classNameRepository = $this->entityManager->getRepository(ClassName::class);
        $this->classCategoryRepository = $this->entityManager->getRepository(ClassCategory::class);
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
        $this->client->request(Request::METHOD_GET,
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
        $this->client->request(Request::METHOD_GET,
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
        $this->client->request(Request::METHOD_GET,
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
        $this->client->request(Request::METHOD_POST,
            $this->generateUrl('admin_product_class_category_edit', ['class_name_id' => $classNameId, 'id' => $classCategoryId]),
            $editInlineForm
        );

        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString($editName, $crawler->filter('ul.sortable-container li:nth-child(3)')->text());
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
        $this->client->request(Request::METHOD_DELETE,
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
        $this->client->request(Request::METHOD_PUT,
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
        /** @var ClassCategory $ClassCategory */
        // set チョコ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'チョコ']);
        $testData[$ClassCategory->getId()] = 1;
        $ClassCategory->setSortNo(3);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        // set 抹茶 rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => '抹茶']);
        $this->assertInstanceOf(ClassCategory::class, $ClassCategory);
        $testData[$ClassCategory->getId()] = 3;
        $this->assertInstanceOf(ClassCategory::class, $ClassCategory);
        $ClassCategory->setSortNo(2);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);
        // set バニラ rank
        $ClassCategory = $this->classCategoryRepository->findOneBy(['name' => 'バニラ']);
        $this->assertInstanceOf(ClassCategory::class, $ClassCategory);
        $testData[$ClassCategory->getId()] = 2;
        $ClassCategory->setSortNo(1);
        $this->entityManager->persist($ClassCategory);
        $this->entityManager->flush($ClassCategory);

        $client = $this->client;
        $client->request(Request::METHOD_POST, $this->generateUrl('admin_product_class_category_sort_no_move'),
            $testData,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
        $crawler = $client->request(Request::METHOD_GET, $this->generateUrl('admin_product_class_category', ['class_name_id' => 1]));

        // チョコ, 抹茶, バニラ sort by rank setup above.
        $this->expected = '抹茶';
        $this->actual = $crawler->filter('ul.sortable-container > li:nth-child(3)')->text();
        $this->assertStringContainsString($this->expected, $this->actual);
        $this->expected = 'バニラ';
        $this->actual = $crawler->filter('ul.sortable-container > li:nth-child(4)')->text();
        $this->assertStringContainsString($this->expected, $this->actual);
        $this->expected = 'チョコ';
        $this->actual = $crawler->filter('ul.sortable-container > li:nth-child(5)')->text();
        $this->assertStringContainsString($this->expected, $this->actual);
    }

    private function newTestClassName($TestCreator)
    {
        $TestClassName = new ClassName();
        $TestClassName->setName('形状')
            ->setSortNo(100)
            ->setCreator($TestCreator);

        return $TestClassName;
    }

    private function newTestClassCategory($TestCreator, $TestClassName)
    {
        $TestClassCategory = new ClassCategory();
        $TestClassCategory->setName('立方体')
            ->setSortNo(100)
            ->setClassName($TestClassName)
            ->setBackendName($TestClassName->getName())
            ->setVisible(true)
            ->setCreator($TestCreator);

        return $TestClassCategory;
    }

    /**
     * ID・表示順が整数でない場合は 400 にする.
     *
     * @param array<mixed> $sortNos
     */
    #[DataProvider(methodName: 'provideInvalidSortNos')]
    public function testMoveSortNoWithInvalidValue(array $sortNos): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_class_category_sort_no_move'),
            $sortNos,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * @return \Iterator<string, array{array<mixed>}>
     */
    public static function provideInvalidSortNos(): \Iterator
    {
        yield 'alpha id' => [['abc' => '1']];
        yield 'out of range id' => [['2147483648' => '1']];
        yield 'alpha sort_no' => [['1' => 'abc']];
        yield 'array sort_no' => [['1' => ['1']]];
    }

    /**
     * 存在しない ID は無視する.
     */
    public function testMoveSortNoWithNotFoundId(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_class_category_sort_no_move'),
            ['2147483647' => '1'],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
