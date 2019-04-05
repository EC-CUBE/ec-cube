<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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


namespace Eccube\Tests\Plugin\Web\Admin\Product;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Entity\ClassName;

class ClassNameControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->removeClass();
        $this->Member = $this->app['eccube.repository.member']->find(2);

        for ($i = 0; $i < 3; $i++) {
            $ClassName = new ClassName();
            $ClassName
                ->setName('class-'.$i)
                ->setCreator($this->Member)
                ->setDelFlg(0)
                ->setRank($i)
                ;
            $this->app['orm.em']->persist($ClassName);
        }
        $this->app['orm.em']->flush();
    }

    public function removeClass()
    {
        $ProductClasses = $this->app['eccube.repository.product_class']->findAll();
        foreach ($ProductClasses as $ProductClass) {
            $this->app['orm.em']->remove($ProductClass);
        }
        $ClassCategories = $this->app['eccube.repository.class_category']->findAll();
        foreach ($ClassCategories as $ClassCategory) {
            $this->app['orm.em']->remove($ClassCategory);
        }
        $this->app['orm.em']->flush();
        $All = $this->app['eccube.repository.class_name']->findAll();
        foreach ($All as $ClassName) {
            $this->app['orm.em']->remove($ClassName);
        }
        $this->app['orm.em']->flush();
    }

    public function testRoutingAdminProductClassName()
    {
        $crawler = $this->client->request('GET',
            $this->app->url('admin_product_class_name')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_class_name'),
            array('admin_class_name' => array(
                '_token' => 'dummy',
                'name' => '規格1'
            ))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_class_name')));

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE,
        );

        $this->verifyOutputString($expected);
    }

    public function testRoutingAdminProductClassNameDelete()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->app['orm.em']->persist($TestClassName);
        $this->app['orm.em']->flush();
        $test_class_name_id = $this->app['eccube.repository.class_name']
            ->findOneBy(array(
                'name' => $TestClassName->getName()
            ))
            ->getId();

        // main
        $redirectUrl = $this->app->url('admin_product_class_name');
        $this->client->request('DELETE',
            $this->app->url('admin_product_class_name_delete', array('id' => $test_class_name_id))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $expected = array(
            EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE,
        );

        $this->verifyOutputString($expected);

        // after
        $this->app['orm.em']->remove($TestClassName);
        $this->app['orm.em']->flush();
    }

    private function newTestClassName($TestCreator)
    {
        $TestClassName = new \Eccube\Entity\ClassName();
        $TestClassName->setName('形状')
            ->setRank(100)
            ->setDelFlg(false)
            ->setCreator($TestCreator);

        return $TestClassName;
    }
}
