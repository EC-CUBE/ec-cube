<?php

namespace Eccube\Tests\Service;

use Eccube\Common\Constant;
use Eccube\Entity\Master\CsvType;
use Eccube\Service\CsvExportService;
use org\bovigo\vfs\vfsStream;

class CsvExportServiceTest extends AbstractServiceTestCase
{

    protected $url;

    public function setUp()
    {
        parent::setUp();
        $root = vfsStream::setup('rootDir');
        $this->url = vfsStream::url('rootDir/test.csv');

        // CsvExportService のファイルポインタを Vfs のファイルポインタにしておく
        $objReflect = new \ReflectionClass($this->app['eccube.service.csv.export']);
        $Property = $objReflect->getProperty('fp');
        $Property->setAccessible(true);
        $Property->setValue($this->app['eccube.service.csv.export'], fopen($this->url, 'w'));

        $Csv = $this->app['eccube.repository.csv']->find(1);
        $Csv->setRank(1);
        $Csv->setEnableFlg(Constant::DISABLED);
        $this->app['orm.em']->flush();
    }

    public function testExportHeader()
    {
        $this->app['eccube.service.csv.export']->initCsvType(CsvType::CSV_TYPE_PRODUCT);
        $this->app['eccube.service.csv.export']->exportHeader();

        $Csv = $this->app['eccube.repository.csv']->findBy(
            array('CsvType' => CsvType::CSV_TYPE_PRODUCT,
                  'enable_flg' => Constant::ENABLED
            )
        );
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
        $Order->setMessage("aaaa".PHP_EOL."bbbb");
        $Order->setNote("bbb".PHP_EOL."bbb");
        $this->createOrder($Customer);
        $this->createOrder($Customer);
        $this->app['orm.em']->flush();

        $qb = $this->app['eccube.repository.order']->createQueryBuilder('o')
            // FIXME https://github.com/EC-CUBE/ec-cube/issues/1236
            // jeftJoin した QueryBuilder で iterate() を実行すると QueryException が発生してしまう
            // ->select(array('o','d'))
            // ->leftJoin('o.OrderDetails', 'd')
            // ->addOrderBy('o.update_date', 'DESC')
;

        $this->app['eccube.service.csv.export']->initCsvType(CsvType::CSV_TYPE_ORDER);
        $this->app['eccube.service.csv.export']->setExportQueryBuilder($qb);

        $this->app['eccube.service.csv.export']->exportData(function ($entity, $csvService) {

            $Csvs = $csvService->getCsvs();

            /** @var $Order \Eccube\Entity\Order */
            $Order = $entity;
            $row = array();
            // CSV出力項目と合致するデータを取得.
            foreach ($Csvs as $Csv) {
                $row[] = $csvService->getData($Csv, $Order);
            }
            // 出力.
            $csvService->fputcsv($row);
        });

        $Result = $qb->getQuery()->getResult();
        $fp = fopen($this->url, 'r');
        $File = array();
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
