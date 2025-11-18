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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Common\Constant;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Product;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CsvControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingCsv()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop_csv', ['id' => 1]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testGetCsv()
    {
        $CsvType = $this->entityManager->getRepository(CsvType::class)->find(1);
        $this->assertInstanceOf(CsvType::class, $CsvType);

        $Csv = $this->entityManager->getRepository(Csv::class)->findBy(['CsvType' => $CsvType, 'enabled' => true], ['sort_no' => 'ASC']);
        $this->assertNotEmpty($Csv);
    }

    public function testSetCsv()
    {
        $this->entityManager->getConnection()->beginTransaction();

        $Csv = $this->entityManager->getRepository(Csv::class)->find(1);
        $this->assertInstanceOf(Csv::class, $Csv);
        $Csv->setSortNo(1);
        $Csv->setEnabled(false);

        $this->entityManager->flush();

        $Csv2 = $this->entityManager->getRepository(Csv::class)->find(1);
        $this->assertInstanceOf(Csv::class, $Csv2);
        $this->assertEquals(false, $Csv2->isEnabled());

        $this->entityManager->getConnection()->rollback();
    }

    public function testRoutingCsvFail()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop_csv', ['id' => 9999]));

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
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
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_csv', ['id' => $csvType]),
            ['form' => $form]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_csv', ['id' => $csvType]);
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = [(int) $CsvNotOut->isEnabled(), (int) $CsvOut->isEnabled()];
        $this->expected = [Constant::ENABLED, Constant::DISABLED];
        $this->verify();
    }

    protected function createCsv($csvType = CsvType::CSV_TYPE_PRODUCT, $field = 'id', $entity = Product::class, $ref = null)
    {
        $CsvType = $this->entityManager->getRepository(CsvType::class)->find($csvType);
        $Creator = $this->createMember();

        $csv = $this->entityManager->getRepository(Csv::class)->findOneBy(['CsvType' => $CsvType], ['sort_no' => 'DESC']);
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
