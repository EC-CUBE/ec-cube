<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Shipping;

use Eccube\Controller\Admin\Shipping\CsvImportController;
use Eccube\Service\CsvImportService;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvImportControllerTest extends AbstractAdminWebTestCase
{
    public function testLoadCsv()
    {
        $Shipping = $this->createOrder($this->createCustomer())->getShippings()[0];
        self::assertNull($Shipping->getTrackingNumber());
        self::assertNull($Shipping->getShippingDate());

        $this->loadCsv([
            '出荷ID,出荷伝票番号,出荷日',
            $Shipping->getId().',1234,2018-01-23',
        ]);

        $this->entityManager->refresh($Shipping);

        self::assertEquals('1234', $Shipping->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-01-23'), $Shipping->getShippingDate());
    }

    public function testLoadCsv_FlippedColumns()
    {
        $Shipping = $this->createOrder($this->createCustomer())->getShippings()[0];
        self::assertNull($Shipping->getTrackingNumber());
        self::assertNull($Shipping->getShippingDate());

        $this->loadCsv([
            '出荷ID,出荷日,出荷伝票番号',
            $Shipping->getId().',2018-01-23,1234',
        ]);

        $this->entityManager->refresh($Shipping);

        self::assertEquals('1234', $Shipping->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-01-23'), $Shipping->getShippingDate());
    }

    /**
     * @dataProvider loadCsvInvalidFormatProvider
     */
    public function testLoadCsv_InvalidFormat($csv, $errorMessage)
    {
        $Shipping = $this->createOrder($this->createCustomer())->getShippings()[0];
        self::assertNull($Shipping->getTrackingNumber());
        self::assertNull($Shipping->getShippingDate());

        $errors = $this->loadCsv(array_map(function ($row) use ($Shipping) {
            return preg_replace('/\{id}/', $Shipping->getId(), $row);
        }, $csv));

        $this->entityManager->refresh($Shipping);

        self::assertEquals($errors[0], $errorMessage);
    }

    public function loadCsvInvalidFormatProvider()
    {
        return [
            [
                [
                    '出荷日,出荷伝票番号',
                    '2018-01-23,1234',
                ], 'CSVのフォーマットが一致しません。',
            ],
            [
                [
                    '出荷日,出荷伝票番号',
                ], 'CSVのフォーマットが一致しません。',
            ],
            [
                [
                    '出荷ID,出荷日,出荷伝票番号',
                    '99999999,2018-01-23,1234',
                ], '1行目の出荷IDが存在しません。',
            ],
            [
                [
                    '出荷ID,出荷日,出荷伝票番号',
                    'x,2018-01-23,1234',
                ], '1行目の出荷IDが存在しません。',
            ],
            [
                [
                    '出荷ID,出荷日,出荷伝票番号',
                    '{id},2018/01/23,1234',
                ], '1行目出荷IDの日付フォーマットが異なります。',
            ],
        ];
    }

    private function loadCsv($csvRows)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_import_controller_test');
        file_put_contents($tempFile, implode(PHP_EOL, $csvRows));

        $csv = new CsvImportService(new \SplFileObject($tempFile));
        $csv->setHeaderRowNumber(0);

        $controller = $this->container->get(CsvImportController::class);
        $rc = new \ReflectionClass(CsvImportController::class);
        $method = $rc->getMethod('loadCsv');
        $method->setAccessible(true);
        $errors = [];
        $method->invokeArgs($controller, [$csv, &$errors]);

        $this->entityManager->flush();

        return $errors;
    }

    public function testCsvShipping()
    {
        $Shipping1 = $this->createOrder($this->createCustomer())->getShippings()[0];
        $Shipping2 = $this->createOrder($this->createCustomer())->getShippings()[0];

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_import_controller_test');
        file_put_contents($tempFile, implode(PHP_EOL, [
            '出荷ID,出荷伝票番号,出荷日',
            $Shipping1->getId().',1234,2018-01-11',
            $Shipping2->getId().',5678,2018-02-22',
        ]));

        $file = new UploadedFile($tempFile, 'shipping.csv', 'text/csv', null, null, true);

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_csv_import'),
            [
                'admin_csv_import' => [
                    '_token' => 'dummy',
                    'import_file' => $file,
                ],
            ],
            ['import_file' => $file]
        );

        $this->assertRegexp(
            '/出荷登録CSVファイルをアップロードしました。/u',
            $crawler->filter('div.alert-primary')->text()
        );

        $this->entityManager->refresh($Shipping1);
        self::assertEquals('1234', $Shipping1->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-01-11'), $Shipping1->getShippingDate());

        $this->entityManager->refresh($Shipping2);
        self::assertEquals('5678', $Shipping2->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-02-22'), $Shipping2->getShippingDate());
    }

    private function parseDate($value)
    {
        $result = \DateTime::createFromFormat('Y-m-d', $value);
        $result->setTime(0, 0, 0);

        return $result;
    }
}
