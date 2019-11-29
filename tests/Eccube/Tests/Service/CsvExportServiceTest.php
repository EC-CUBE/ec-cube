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

namespace Eccube\Tests\Service;

use Eccube\Entity\Master\CsvType;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CsvExportService;
use org\bovigo\vfs\vfsStream;

class CsvExportServiceTest extends AbstractServiceTestCase
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @var CsvRepository
     */
    protected $csvRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->csvExportService = self::$container->get(CsvExportService::class);
        $this->csvRepository = $this->entityManager->getRepository(\Eccube\Entity\Csv::class);
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);

        $root = vfsStream::setup('rootDir');
        $this->url = vfsStream::url('rootDir/test.csv');

        // CsvExportService のファイルポインタを Vfs のファイルポインタにしておく
        $objReflect = new \ReflectionClass($this->csvExportService);
        $Property = $objReflect->getProperty('fp');
        $Property->setAccessible(true);
        $Property->setValue($this->csvExportService, fopen($this->url, 'w'));

        $Csv = $this->csvRepository->find(1);
        $Csv->setSortNo(1);
        $Csv->setEnabled(false);
        $this->entityManager->flush();
    }

    public function testExportHeader()
    {
        $this->csvExportService->initCsvType(CsvType::CSV_TYPE_PRODUCT);
        $this->csvExportService->exportHeader();

        $Csv = $this->csvRepository->findBy([
            'CsvType' => CsvType::CSV_TYPE_PRODUCT,
            'enabled' => true,
        ]);
        $arrHeader = explode(',', file_get_contents($this->url));
        // Vfs に出力すると日本語が化けてしまうようなので, カウントのみ比較
        $this->expected = count($Csv);
        $this->actual = count($arrHeader);
        $this->verify();
    }

    public function testExportData()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setMessage('aaaa'.PHP_EOL.'bbbb');
        $Order->setNote('bbb'.PHP_EOL.'bbb');
        $this->createOrder($Customer);
        $this->createOrder($Customer);
        $this->entityManager->flush();

        $qb = $this->orderRepository->createQueryBuilder('o')
            // FIXME https://github.com/EC-CUBE/ec-cube/issues/1236
            // jeftJoin した QueryBuilder で iterate() を実行すると QueryException が発生してしまう
            // ->select(array('o','d'))
            // ->addOrderBy('o.update_date', 'DESC')
;

        $this->csvExportService->initCsvType(CsvType::CSV_TYPE_ORDER);
        $this->csvExportService->setExportQueryBuilder($qb);

        $this->csvExportService->exportData(function ($entity, $csvService) {
            $Csvs = $csvService->getCsvs();

            /** @var $Order \Eccube\Entity\Order */
            $Order = $entity;
            $row = [];
            // CSV出力項目と合致するデータを取得.
            foreach ($Csvs as $Csv) {
                $row[] = $csvService->getData($Csv, $Order);
            }
            // 出力.
            $csvService->fputcsv($row);
        });

        $Result = $qb->getQuery()->getResult();
        $fp = fopen($this->url, 'r');
        $File = [];
        if ($fp !== false) {
            while (($data = fgetcsv($fp)) !== false) {
                $File[] = $data;
            }
            fclose($fp);
        }
        // Vfs に出力すると日本語が化けてしまうようなので, カウントのみ比較
        $this->expected = count($Result);
        $this->actual = count($File);
        $this->verify();
    }
}
