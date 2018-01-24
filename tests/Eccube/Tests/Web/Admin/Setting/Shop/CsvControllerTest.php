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
use Eccube\Repository\CsvRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CsvControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingCsv()
    {

        $this->client->request('GET', $this->generateUrl('admin_setting_shop_csv', array('id' => 1)));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetCsv()
    {
        $CsvType = $this->container->get(CsvTypeRepository::class)->find(1);
        $this->assertNotEmpty($CsvType);

        $Csv = $this->container->get(CsvRepository::class)->findBy(array('CsvType' => $CsvType, 'enabled' => true), array('sort_no' => 'ASC'));
        $this->assertNotEmpty($Csv);
    }


    public function testSetCsv()
    {
        $this->entityManager->getConnection()->beginTransaction();

        $Csv = $this->container->get(CsvRepository::class)->find(1);
        $Csv->setSortNo(1);
        $Csv->setEnabled(false);

        $this->entityManager->flush();

        $Csv2 = $this->container->get(CsvRepository::class)->find(1);
        $this->assertEquals(false, $Csv2->isEnabled());

        $this->entityManager->getConnection()->rollback();
    }

    public function testRoutingCsvFail()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_csv', array('id' => 9999)));

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testSubmit()
    {
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
            $this->generateUrl('admin_setting_shop_csv', array('id' => $csvType)),
            array('form' => $form)
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_csv', array('id' => $csvType));
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = array($CsvNotOut->isEnabled(), $CsvOut->isEnabled());
        $this->expected = array(Constant::ENABLED, Constant::DISABLED);
        $this->verify();
    }

    protected function createCsv($csvType = CsvType::CSV_TYPE_PRODUCT, $field = 'id', $entity = 'Eccube\Entity\Product', $ref = null)
    {
        $CsvType = $this->container->get(CsvTypeRepository::class)->find($csvType);
        $Creator = $this->createMember();

        $csv = $this->container->get(CsvRepository::class)->findOneBy(array('CsvType' => $CsvType), array('sort_no' => 'DESC'));
        $sortNo = 1;
        if ($csv) {
            $sortNo = $csv->getSortNo() + 1;
        }

        $Csv = new Csv();
        $Csv->setCsvType($CsvType);
        $Csv->setCreator($Creator);
        $Csv->setEntityName($entity);
        $Csv->setFieldName($field);
        $Csv->setReferenceFieldName($ref);
        $Csv->setDispName('Test');
        $Csv->setEnabled(false);
        $Csv->setSortNo($sortNo);

        $this->entityManager->persist($Csv);
        $this->entityManager->flush();

        return $Csv;
    }
}
