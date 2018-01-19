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

use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

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

        $this->classNameRepository = $this->container->get(ClassNameRepository::class);
        $this->classCategoryRepository = $this->container->get(ClassCategoryRepository::class);
    }

    public function testRoutingAdminProductClassCategory()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepository
            ->findOneBy(array(
                'name' => $TestClassName->getName()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_category', array('class_name_id' => $test_class_name_id))
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
            ->findOneBy(array(
                'name' => $TestClassName->getName()
            ))
            ->getId();

        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $test_class_category_id = $this->classCategoryRepository
            ->findOneBy(array(
                'name' => $TestClassCategory->getName()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->generateUrl('admin_product_class_category_edit',
                array('class_name_id' => $test_class_name_id, 'id' => $test_class_category_id)),
            array('_token' => 'dummy')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassCategoryDelete()
    {
        // before
        $TestCreator = $this->createMember();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->entityManager->persist($TestClassName);
        $this->entityManager->flush();
        $test_class_name_id = $this->classNameRepository
            ->findOneBy(array(
                'name' => $TestClassName->getName()
            ))
            ->getId();
        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $test_class_category_id = $this->classCategoryRepository
            ->findOneBy(array(
                'name' => $TestClassCategory->getName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_class_category', array('class_name_id' => $test_class_name_id));
        $this->client->request('DELETE',
            $this->generateUrl('admin_product_class_category_delete',
                array('class_name_id' => $test_class_name_id, 'id' => $test_class_category_id, )
                ),
            array('_token' => 'dummy')
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
            ->findOneBy(array(
                'name' => $TestClassName->getName()
            ))
            ->getId();
        $TestClassCategory = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->entityManager->persist($TestClassCategory);
        $this->entityManager->flush();
        $test_class_category_id = $this->classCategoryRepository
            ->findOneBy(array(
                'name' => $TestClassCategory->getName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->generateUrl('admin_product_class_category', array('class_name_id' => $test_class_name_id));
        $this->client->request('PUT',
            $this->generateUrl('admin_product_class_category_visibility',
                array('class_name_id' => $test_class_name_id, 'id' => $test_class_category_id)),
            array('_token' => 'dummy')
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
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
            ->setVisible(true)
            ->setCreator($TestCreator);

        return $TestClassCategory;
    }
}

