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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Common\Constant;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CsvControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingCsv()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_csv', ['id' => 1]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetCsv()
    {
        $CsvType = $this->entityManager->getRepository(\Eccube\Entity\Master\CsvType::class)->find(1);
        $this->assertNotEmpty($CsvType);

        $Csv = $this->entityManager->getRepository(\Eccube\Entity\Csv::class)->findBy(['CsvType' => $CsvType, 'enabled' => true], ['sort_no' => 'ASC']);
        $this->assertNotEmpty($Csv);
    }

    public function testSetCsv()
    {
        $this->entityManager->getConnection()->beginTransaction();

        $Csv = $this->entityManager->getRepository(\Eccube\Entity\Csv::class)->find(1);
        $Csv->setSortNo(1);
        $Csv->setEnabled(false);

        $this->entityManager->flush();

        $Csv2 = $this->entityManager->getRepository(\Eccube\Entity\Csv::class)->find(1);
        $this->assertEquals(false, $Csv2->isEnabled());

        $this->entityManager->getConnection()->rollback();
    }

    public function testRoutingCsvFail()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_csv', ['id' => 9999]));

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testSubmit()
    {
        $csvType = CsvType::CSV_TYPE_PRODUCT;
        $CsvOut = $this->createCsv($csvType);
        $CsvNotOut = $this->createCsv($csvType);

        $form = [
            '_token' => 'dummy',
            'csv_type' => $csvType,
            'csv_not_output' => [
                $CsvOut->getId(),
            ],
            'csv_output' => [
                $CsvNotOut->getId(),
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_csv', ['id' => $csvType]),
            ['form' => $form]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_csv', ['id' => $csvType]);
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = [$CsvNotOut->isEnabled(), $CsvOut->isEnabled()];
        $this->expected = [Constant::ENABLED, Constant::DISABLED];
        $this->verify();
    }

    protected function createCsv($csvType = CsvType::CSV_TYPE_PRODUCT, $field = 'id', $entity = 'Eccube\Entity\Product', $ref = null)
    {
        $CsvType = $this->entityManager->getRepository(\Eccube\Entity\Master\CsvType::class)->find($csvType);
        $Creator = $this->createMember();

        $csv = $this->entityManager->getRepository(\Eccube\Entity\Csv::class)->findOneBy(['CsvType' => $CsvType], ['sort_no' => 'DESC']);
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
