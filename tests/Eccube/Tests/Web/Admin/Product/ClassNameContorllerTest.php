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

use Eccube\Common\Constant;
use Eccube\Entity\ClassName;
use Eccube\Entity\Member;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\MemberRepository;
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
        $this->productClassRepo = $this->container->get(ProductClassRepository::class);
        $this->classCategoryRepo = $this->container->get(ClassCategoryRepository::class);
        $this->classNameRepo = $this->container->get(ClassNameRepository::class);
        $this->Member = $this->container->get(MemberRepository::class)->find(1);
        $this->removeClass();

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setDisplayName('class-'.$i)
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
            array(
                'admin_class_name' => array(
                'display_name' => '規格1',
                Constant::TOKEN_NAME => 'dummy',
            ))
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_product_class_name')));
    }

    public function testIndexWithPostBackendName()
    {
        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('admin_product_class_name'),
            array(
                'admin_class_name' => array(
                    'backend_name' => '規格1',
                    'display_name' => '表示規格1',
                    Constant::TOKEN_NAME => 'dummy',
                ))
        );
        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('admin_product_class_name')));
    }


    public function testRoutingAdminProductClassNameEdit()
    {
        // before
        $TestCreator = $this->Member;
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepo
            ->findOneBy(array(
                'backend_name' => $TestClassName->getBackendName()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_name_edit', array('id' => $test_class_name_id))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassDisplayNameEdit()
    {
        // before
        $TestCreator = $this->Member;
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepo
            ->findOneBy(array(
                'display_name' => $TestClassName->getDisplayName()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_name_edit', array('id' => $test_class_name_id))
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
            ->findOneBy(array(
                'backend_name' => $TestClassName->getBackendName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_class_name');
        $this->client->request('DELETE',
            $this->generateUrl('admin_product_class_name_delete', array('id' => $test_class_name_id)),
            array(
                Constant::TOKEN_NAME => 'dummy',
            )
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testMoveSortNo()
    {
        $ClassName = $this->classNameRepo->findOneBy(array('backend_name' => 'class-1'));

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_class_name_sort_no_move'),
            array($ClassName->getId() => 10),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
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
            ->setDisplayName('表示形状')
            ->setSortNo(100)
            ->setCreator($TestCreator);

        return $TestClassName;
    }
}
