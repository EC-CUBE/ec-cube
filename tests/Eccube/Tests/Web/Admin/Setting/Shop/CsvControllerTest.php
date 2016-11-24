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


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Common\Constant;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CsvControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $Csv = $this->app['eccube.repository.csv']->find(1);
        $Csv->setRank(1);
        $Csv->setEnableFlg(Constant::DISABLED);
        $this->app['orm.em']->flush();
    }

    public function testRoutingCsv()
    {
        if ($this->app['config']['database']['driver'] == 'pdo_sqlite') {
            // 何故か CsvType が EntityNotFoundException: Entity was not found. になる
            $this->markTestSkipped('Can not support for sqlite3');
        }
        $this->client->request('GET', $this->app['url_generator']->generate('admin_setting_shop_csv', array('id' => 1)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetCsv()
    {
        $CsvType = $this->app['eccube.repository.master.csv_type']->find(1);
        $this->assertNotEmpty($CsvType);

        $Csv = $this->app['eccube.repository.csv']->findBy(array('CsvType' => $CsvType, 'enable_flg' => Constant::ENABLED), array('rank' => 'ASC'));
        $this->assertNotEmpty($Csv);
    }


    public function testSetCsv()
    {
        $this->app['orm.em']->getConnection()->beginTransaction();

        $Csv = $this->app['eccube.repository.csv']->find(1);
        $Csv->setRank(1);
        $Csv->setEnableFlg(Constant::DISABLED);

        $this->app['orm.em']->flush();

        $Csv2 = $this->app['eccube.repository.csv']->find(1);
        $this->assertEquals(Constant::DISABLED, $Csv2->getEnableFlg());

        $this->app['orm.em']->getConnection()->rollback();
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRoutingCsvFail()
    {
        if ($this->app['config']['database']['driver'] == 'pdo_sqlite') {
            // 何故か CsvType が EntityNotFoundException: Entity was not found. になる
            $this->markTestSkipped('Can not support for sqlite3');
        }
        $this->client->request('GET', $this->app->url('admin_setting_shop_csv', array('id' => 9999)));
        $this->fail();
    }

    public function testSubmit()
    {
        if ($this->app['config']['database']['driver'] == 'pdo_sqlite') {
            // 何故か CsvType が EntityNotFoundException: Entity was not found. になる
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $csvType = CsvType::CSV_TYPE_PRODUCT;
        $CsvOut = $this->createCsv($csvType);
        $CsvNotOut = $this->createCsv($csvType);

        $form = array(
            '_token' => 'dummy',
            'csv_type' => $csvType,
            'csv_not_output' => array(
                $CsvOut->getId(),
            ),
            'csv_output' => array(
                $CsvNotOut->getId(),
            ),
        );

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_csv', array('id' => $csvType)),
            array('form' => $form)
        );

        $redirectUrl = $this->app->url('admin_setting_shop_csv', array('id' => $csvType));
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = array($CsvNotOut->getEnableFlg(), $CsvOut->getEnableFlg());
        $this->expected = array(Constant::ENABLED, Constant::DISABLED);
        $this->verify();
    }

    protected function createCsv($csvType = CsvType::CSV_TYPE_PRODUCT, $field = 'id', $entity = 'Eccube\Entity\Product', $ref = null)
    {
        $CsvType = $this->app['eccube.repository.master.csv_type']->find($csvType);
        $Creator = $this->app['eccube.repository.member']->find(2);

        $csv = $this->app['eccube.repository.csv']->findOneBy(array('CsvType' => $CsvType), array('rank' => 'DESC'));
        $rank = 1;
        if ($csv) {
            $rank = $csv->getRank() + 1;
        }

        $Csv = new Csv();
        $Csv->setCsvType($CsvType);
        $Csv->setCreator($Creator);
        $Csv->setEntityName($entity);
        $Csv->setFieldName($field);
        $Csv->setReferenceFieldName($ref);
        $Csv->setDispName('Test');
        $Csv->setEnableFlg(Constant::DISABLED);
        $Csv->setRank($rank);

        $this->app['orm.em']->persist($Csv);
        $this->app['orm.em']->flush();

        return $Csv;
    }
}
