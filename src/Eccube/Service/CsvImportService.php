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

namespace Eccube\Service;

use Eccube\Application;

class CsvImportService
{

    /** @var \Eccube\Application */
    protected $app;

    /**
     * @var
     */
    protected $fp;

    /**
     * @var
     */
    protected $closed = false;

    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var \Closure
     */
    protected $convertEncodingCallBack;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\QueryBuilder;
     */
    protected $qb;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Eccube\Entity\Master\CsvType
     */
    protected $CsvType;

    /**
     * @var \Eccube\Entity\Csv[]
     */
    protected $Csvs;

    /**
     * @var \Eccube\Repository\CsvRepository
     */
    protected $csvRepository;

    /**
     * @var \Eccube\Repository\Master\CsvTypeRepository
     */
    protected $csvTypeRepository;

    /**
     * @var \Eccube\Repository\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Eccube\Repository\CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var \Eccube\Repository\ProductRepository
     */
    protected $productRepository;


    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param \SplFileObject $file
     */
    public function setCsvImport(\SplFileObject $file)
    {
        ini_set('auto_detect_line_endings', true);

        $this->file = $file;
        $this->file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $this->file->setCsvControl(
            $this->app['config']['csv_delimiter'],
            $this->app['config']['csv_enclosure'],
            $this->app['config']['csv_escape']
        );

    }


    public function getReader() {


    }






    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param \Eccube\Repository\CsvRepository $csvRepository
     */
    public function setCsvRepository(\Eccube\Repository\CsvRepository $csvRepository)
    {
        $this->csvRepository = $csvRepository;
    }

    /**
     * @param \Eccube\Repository\Master\CsvTypeRepository $csvTypeRepository
     */
    public function setCsvTypeRepository(\Eccube\Repository\Master\CsvTypeRepository $csvTypeRepository)
    {
        $this->csvTypeRepository = $csvTypeRepository;
    }

    /**
     * @param \Eccube\Repository\OrderRepository $orderRepository
     */
    public function setOrderRepository(\Eccube\Repository\OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Eccube\Repository\CustomerRepository $customerRepository
     */
    public function setCustomerRepository(\Eccube\Repository\CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Eccube\Repository\ProductRepository $productRepository
     */
    public function setProductRepository(\Eccube\Repository\ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function setExportQueryBuilder(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->qb = $qb;
        $this->setEntityManager($qb->getEntityManager());
    }

    /**
     * Csv種別からServiceの初期化を行う.
     *
     * @param $CsvType |integer
     */
    public function initCsvType($CsvType)
    {
        if ($CsvType instanceof \Eccube\Entity\Master\CsvType) {
            $this->CsvType = $CsvType;
        } else {
            $this->CsvType = $this->csvTypeRepository->find($CsvType);
        }

        $criteria = array(
            'CsvType' => $CsvType,
            'enable_flg' => Constant::ENABLED
        );
        $orderBy = array(
            'rank' => 'ASC'
        );
        $this->Csvs = $this->csvRepository->findBy($criteria, $orderBy);
    }

    /**
     * @return \Eccube\Entity\Csv[]
     */
    public function getCsvs()
    {
        return $this->Csvs;
    }

    /**
     * ヘッダ行を出力する.
     */
    public function exportHeader()
    {
        $row = array();
        foreach ($this->getProductCsvHeader() as $key => $value) {
            $row[] = $key;
        }

        $this->fopen();
        $this->fputcsv($row);
        $this->fclose();
    }

    /**
     * クエリビルダにもとづいてデータ行を出力する.
     * このメソッドを使う場合は, 事前にsetExportQueryBuilder($qb)で出力対象のクエリビルダをわたしておく必要がある.
     *
     * @param \Closure $closure
     */
    public function exportData(\Closure $closure)
    {
        if (is_null($this->qb) || is_null($this->em)) {
            throw new \LogicException('query builder not set.');
        }

        $this->fopen();

        $query = $this->qb->getQuery();
        foreach ($query->iterate() as $iteratableResult) {
            $closure($iteratableResult[0], $this);

            $this->em->detach($iteratableResult[0]);
            $this->em->clear();
            $query->free();
            flush();
        }

        $this->fclose();
    }

    /**
     * CSV出力項目と比較し, 合致するデータを返す.
     *
     * @param \Eccube\Entity\Csv $Csv
     * @param $entity
     * @return mixed|null|string|void
     */
    public function getData(\Eccube\Entity\Csv $Csv, $entity)
    {
        // エンティティ名が一致するかどうかチェック.
        if ($Csv->getEntityName() !== get_class($entity)) {
            return;
        }

        // カラム名がエンティティに存在するかどうかをチェック.
        if (!$entity->offsetExists($Csv->getFieldName())) {
            return;
        }

        // データを取得.
        $data = $entity->offsetGet($Csv->getFieldName());

        // one to one の場合は, dtb_csv.referece_field_nameと比較し, 合致する結果を取得する.
        if ($data instanceof \Eccube\Entity\AbstractEntity) {
            return $data->offsetGet($Csv->getReferenceFieldName());

        } elseif ($data instanceof \Doctrine\Common\Collections\Collection) {
            // one to manyの場合は, カンマ区切りに変換する.
            $array = array();
            foreach ($data as $elem) {
                $array[] = $elem->offsetGet($Csv->getReferenceFieldName());
            }
            return implode($this->config['csv_export_multidata_separator'], $array);

        } elseif ($data instanceof \DateTime) {
            // datetimeの場合は文字列に変換する.
            return $data->format($this->config['csv_export_date_format']);

        } else {
            // スカラ値の場合はそのまま.
            return $data;
        }

        return null;
    }

    /**
     * 文字エンコーディングの変換を行うコールバック関数を返す.
     *
     * @return \Closure
     */
    public function getConvertEncodingCallback()
    {
        $config = $this->config;

        return function ($value) use ($config) {
            return mb_convert_encoding(
                (string)$value, $config['csv_export_encoding'], 'UTF-8'
            );
        };
    }

    /**
     *
     */
    public function fopen()
    {
        if (is_null($this->fp) || $this->closed) {
            $this->fp = fopen('php://output', 'w');
        }
    }

    /**
     * @param $row
     * @param null $callback
     */
    public function fputcsv($row)
    {
        if (is_null($this->convertEncodingCallBack)) {
            $this->convertEncodingCallBack = $this->getConvertEncodingCallback();
        }

        fputcsv($this->fp, array_map($this->convertEncodingCallBack, $row), $this->config['csv_export_separator']);
    }

    /**
     *
     */
    public function fclose()
    {
        if (!$this->closed) {
            fclose($this->fp);
            $this->closed = true;
        }
    }

    /**
     * 受注検索用のクエリビルダを返す.
     *
     * @param FormFactory $formFactory
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderQueryBuilder(FormFactory $formFactory, Request $request)
    {
        $session = $request->getSession();
        if ($session->has('eccube.admin.order.search')) {
            $searchData = $session->get('eccube.admin.order.search');
        } else {
            $searchData = array();
        }

        // 受注データのクエリビルダを構築.
        $qb = $this->orderRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }

    /**
     * 会員検索用のクエリビルダを返す.
     *
     * @param FormFactory $formFactory
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCustomerQueryBuilder(FormFactory $formFactory, Request $request)
    {
        $session = $request->getSession();
        if ($session->has('eccube.admin.customer.search')) {
            $searchData = $session->get('eccube.admin.customer.search');
        } else {
            $searchData = array();
        }

        // 会員データのクエリビルダを構築.
        $qb = $this->customerRepository
            ->getQueryBuilderBySearchData($searchData);

        return $qb;
    }

    /**
     * 商品検索用のクエリビルダを返す.
     *
     * @param FormFactory $formFactory
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductQueryBuilder(FormFactory $formFactory, Request $request)
    {
        $session = $request->getSession();
        if ($session->has('eccube.admin.product.search')) {
            $searchData = $session->get('eccube.admin.product.search');
        } else {
            $searchData = array();
        }

        // 商品データのクエリビルダを構築.
        $qb = $this->productRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }

    /**
     * CSV定義
     */
    public function getProductCsvHeader()
    {

        return array(
            '商品ID' => 'id',
            '公開ステータス(ID)' => 'status',
            '商品名' => 'name',
            'ショップ用メモ欄' => 'note',
            '商品説明(一覧)' => 'description_list',
            '商品説明(一覧)' => 'description_detail',
            '検索ワード' => 'search_word',
            'フリーエリア' => 'free_area',
            '商品削除フラグ' => 'product_del_flg',
            '商品画像' => 'product_image',
            '商品カテゴリ(ID)' => 'product_category',
            '商品種別(ID)' => 'product_type',
            '規格分類1(ID)' => 'class_category1',
            '規格分類2(ID)' => 'class_category2',
            '発送日目安(ID)' => 'deliveryFee',
            '商品コード' => 'product_code',
            '在庫数' => 'stock',
            '在庫数無制限フラグ' => 'stock_unlimited',
            '販売数制限数' => 'sale_limit',
            '通常価格' => 'price01',
            '販売価格' => 'price02',
            '送料' => 'delivery_fee',
            '商品規格削除フラグ' => 'product_class_del_flg',
        );
    }

}
