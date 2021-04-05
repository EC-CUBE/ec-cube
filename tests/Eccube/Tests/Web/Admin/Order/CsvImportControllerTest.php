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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Controller\Admin\Order\CsvImportController;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Service\CsvImportService;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvImportControllerTest extends AbstractAdminWebTestCase
{
    public function testLoadCsv()
    {
        $OrderStatus = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);
        $Order = $this->createOrder($this->createCustomer());
        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->flush();
        $Shipping = $Order->getShippings()[0];
        self::assertNull($Shipping->getTrackingNumber());
        self::assertNull($Shipping->getShippingDate());

        $this->loadCsv([
            '出荷ID,お問い合わせ番号,出荷日',
            $Shipping->getId().',1234,2018-01-23',
        ]);

        $this->entityManager->refresh($Shipping);

        self::assertEquals('1234', $Shipping->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-01-23'), $Shipping->getShippingDate());
    }

    public function testLoadCsvFlippedColumns()
    {
        $Shipping = $this->createOrder($this->createCustomer())->getShippings()[0];
        self::assertNull($Shipping->getTrackingNumber());
        self::assertNull($Shipping->getShippingDate());

        $this->loadCsv([
            '出荷ID,出荷日,お問い合わせ番号',
            $Shipping->getId().',2018-01-23,1234',
        ]);

        $this->entityManager->refresh($Shipping);

        self::assertEquals('1234', $Shipping->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-01-23'), $Shipping->getShippingDate());
    }

    /**
     * @dataProvider loadCsvInvalidFormatProvider
     */
    public function testLoadCsvInvalidFormat($csv, $errorMessage)
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
                    '出荷日,お問い合わせ番号',
                    '2018-01-23,1234',
                ], 'CSVのフォーマットが一致しません',
            ],
            [
                [
                    '出荷日,お問い合わせ番号',
                ], 'CSVのフォーマットが一致しません',
            ],
            [
                [
                    '出荷ID,出荷日,お問い合わせ番号',
                    '99999999,2018-01-23,1234',
                ], '2行目の出荷IDが存在しません',
            ],
            [
                [
                    '出荷ID,出荷日,お問い合わせ番号',
                    'x,2018-01-23,1234',
                ], '2行目の出荷IDが存在しません',
            ],
            [
                [
                    '出荷ID,出荷日,お問い合わせ番号',
                    '{id},2018/01/23,1234',
                ], '2行目の出荷日の日付フォーマットが異なります',
            ],
        ];
    }

    private function loadCsv($csvRows)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_import_controller_test');
        $csvContent = implode(PHP_EOL, $csvRows);

        // see https://github.com/EC-CUBE/ec-cube/pull/1781
        if ('\\' === DIRECTORY_SEPARATOR) {
            // Windows 環境では、 ロケールとファイルエンコーディングを一致させる必要がある
            setlocale(LC_ALL, '');
            if (mb_detect_encoding($csvContent) === 'UTF-8') {
                $csvContent = mb_convert_encoding($csvContent, 'SJIS-win', 'UTF-8');
            }
        }
        file_put_contents($tempFile, $csvContent);

        $csv = new CsvImportService(new \SplFileObject($tempFile));
        $csv->setHeaderRowNumber(0);

        $controller = self::$container->get(CsvImportController::class);
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
        $OrderStatus = $this->entityManager->find(OrderStatus::class, OrderStatus::NEW);
        $Order1 = $this->createOrder($this->createCustomer());
        $Order1->setOrderStatus($OrderStatus);
        $Order2 = $this->createOrder($this->createCustomer());
        $Order2->setOrderStatus($OrderStatus);
        $Order3 = $this->createOrder($this->createCustomer());
        $Order3->setOrderStatus($OrderStatus);
        $this->entityManager->flush();

        $Shipping1 = $Order1->getShippings()[0];
        $Shipping2 = $Order2->getShippings()[0];
        $Shipping3 = $Order3->getShippings()[0];

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_import_controller_test');
        file_put_contents($tempFile, implode(PHP_EOL, [
            '出荷ID,お問い合わせ番号,出荷日',
            $Shipping1->getId().',1234,2018-01-11',
            $Shipping2->getId().',5678,2018-02-22',
            $Shipping3->getId().',9012,2018-03-22',
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
            '/CSVファイルをアップロードしました/u',
            $crawler->filter('div.alert-primary')->text()
        );

        $this->entityManager->refresh($Shipping1);
        self::assertEquals('1234', $Shipping1->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-01-11'), $Shipping1->getShippingDate());

        $this->entityManager->refresh($Shipping2);
        self::assertEquals('5678', $Shipping2->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-02-22'), $Shipping2->getShippingDate());

        $this->entityManager->refresh($Shipping3);
        self::assertEquals('9012', $Shipping3->getTrackingNumber());
        self::assertEquals($this->parseDate('2018-03-22'), $Shipping3->getShippingDate());
    }

    private function parseDate($value)
    {
        $result = \DateTime::createFromFormat('Y-m-d', $value);
        $result->setTime(0, 0, 0);

        return $result;
    }
}
