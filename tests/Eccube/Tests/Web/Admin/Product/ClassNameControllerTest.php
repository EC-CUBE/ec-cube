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
use Eccube\Entity\ClassName;
use Eccube\Entity\Member;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ClassNameControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var Member
     */
    private $Member;

    /**
     * @var ProductClassRepository
     */
    private $productClassRepo;

    /**
     * @var ClassCategoryRepository
     */
    private $classCategoryRepo;

    /**
     * @var ClassNameRepository
     */
    private $classNameRepo;

    public function setUp()
    {
        parent::setUp();
        $this->productClassRepo = $this->entityManager->getRepository(\Eccube\Entity\ProductClass::class);
        $this->classCategoryRepo = $this->entityManager->getRepository(\Eccube\Entity\ClassCategory::class);
        $this->classNameRepo = $this->entityManager->getRepository(\Eccube\Entity\ClassName::class);
        $this->Member = $this->entityManager->getRepository(\Eccube\Entity\Member::class)->find(1);
        $this->removeClass();

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setBackendName('class-'.$i)
                ->setCreator($this->Member)
                ->setSortNo($i)
                ;
            $this->entityManager->persist($ClassName);
        }
        $this->entityManager->flush();
    }

    public function removeClass()
    {
        $ProductClasses = $this->productClassRepo->findAll();
        foreach ($ProductClasses as $ProductClass) {
            $this->entityManager->remove($ProductClass);
        }
        $ClassCategories = $this->classCategoryRepo->findAll();
        foreach ($ClassCategories as $ClassCategory) {
            $this->entityManager->remove($ClassCategory);
        }
        $this->entityManager->flush();
        $All = $this->classNameRepo->findAll();
        foreach ($All as $ClassName) {
            $this->entityManager->remove($ClassName);
        }
        $this->entityManager->flush();
    }

    public function testRoutingAdminProductClassName()
    {
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_name')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithPost()
    {
        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('admin_product_class_name'),
            [
                'admin_class_name' => [
                'name' => '規格1',
                Constant::TOKEN_NAME => 'dummy',
            ], ]
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_product_class_name')));
    }

    public function testIndexWithPostBackendName()
    {
        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('admin_product_class_name'),
            [
                'admin_class_name' => [
                    'backend_name' => '規格1',
                    'name' => '表示規格1',
                    Constant::TOKEN_NAME => 'dummy',
                ], ]
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_product_class_name')));
    }

    public function testRoutingAdminProductClassBackendNameEdit()
    {
        // before
        $TestCreator = $this->Member;
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepo
            ->findOneBy([
                'backend_name' => $TestClassName->getBackendName(),
            ])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_name_edit', ['id' => $test_class_name_id])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassNameEdit()
    {
        // before
        $TestCreator = $this->Member;
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepo
            ->findOneBy([
                'name' => $TestClassName->getName(),
            ])
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_name_edit', ['id' => $test_class_name_id])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassNameDelete()
    {
        // before
        $TestCreator = $this->Member;
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepo
            ->findOneBy([
                'backend_name' => $TestClassName->getBackendName(),
            ])
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_class_name');
        $this->client->request('DELETE',
            $this->generateUrl('admin_product_class_name_delete', ['id' => $test_class_name_id]),
            [
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testMoveSortNo()
    {
        $ClassName = $this->classNameRepo->findOneBy(['backend_name' => 'class-1']);

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_class_name_sort_no_move'),
            [$ClassName->getId() => 10],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $MovedClassName = $this->classNameRepo->find($ClassName->getId());
        $this->expected = 10;
        $this->actual = $MovedClassName->getSortNo();
        $this->verify();
    }

    private function newTestClassName($TestCreator)
    {
        $TestClassName = new \Eccube\Entity\ClassName();
        $TestClassName->setBackendName('形状')
            ->setName('表示形状')
            ->setSortNo(100)
            ->setCreator($TestCreator);

        return $TestClassName;
    }
}
