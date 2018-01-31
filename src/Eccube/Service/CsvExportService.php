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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Util\EntityUtil;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Eccube\Entity\Master\CsvType;
use Eccube\Entity\Csv;

class CsvExportService
{
    /**
     * @var resource
     */
    protected $fp;

    /**
     * @var boolean
     */
    protected $closed = false;

    /**
     * @var \Closure
     */
    protected $convertEncodingCallBack;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var QueryBuilder;
     */
    protected $qb;

    /**
     * @var EccubeConfig
     */
    protected $config;

    /**
     * @var CsvType
     */
    protected $CsvType;

    /**
     * @var Csv[]
     */
    protected $Csvs;

    /**
     * @var CsvRepository
     */
    protected $csvRepository;

    /**
     * @var CsvTypeRepository
     */
    protected $csvTypeRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * CsvExportService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CsvRepository $csvRepository
     * @param CsvTypeRepository $csvTypeRepository
     * @param OrderRepository $orderRepository
     * @param CustomerRepository $customerRepository
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CsvRepository $csvRepository,
        CsvTypeRepository $csvTypeRepository,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository,
        EccubeConfig $eccubeConfig
    ) {
        $this->entityManager = $entityManager;
        $this->csvRepository = $csvRepository;
        $this->csvTypeRepository = $csvTypeRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->config = $eccubeConfig;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param CsvRepository $csvRepository
     */
    public function setCsvRepository(CsvRepository $csvRepository)
    {
        $this->csvRepository = $csvRepository;
    }

    /**
     * @param CsvTypeRepository $csvTypeRepository
     */
    public function setCsvTypeRepository(CsvTypeRepository $csvTypeRepository)
    {
        $this->csvTypeRepository = $csvTypeRepository;
    }

    /**
     * @param OrderRepository $orderRepository
     */
    public function setOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param CustomerRepository $customerRepository
     */
    public function setCustomerRepository(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param ProductRepository $productRepository
     */
    public function setProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param QueryBuilder $qb
     */
    public function setExportQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * Csv種別からServiceの初期化を行う.
     *
     * @param $CsvType|integer
     */
    public function initCsvType($CsvType)
    {
        if ($CsvType instanceof CsvType) {
            $this->CsvType = $CsvType;
        } else {
            $this->CsvType = $this->csvTypeRepository->find($CsvType);
        }

        $criteria = array(
            'CsvType' => $CsvType,
            'enabled' => true,
        );
        $orderBy = array(
            'sort_no' => 'ASC'
        );
        $this->Csvs = $this->csvRepository->findBy($criteria, $orderBy);
    }

    /**
     * @return Csv[]
     */
    public function getCsvs()
    {
        return $this->Csvs;
    }

    /**
     * ヘッダ行を出力する.
     * このメソッドを使う場合は, 事前にinitCsvType($CsvType)で初期化しておく必要がある.
     */
    public function exportHeader()
    {
        if (is_null($this->CsvType) || is_null($this->Csvs)) {
            throw new \LogicException('init csv type incomplete.');
        }

        $row = array();
        foreach ($this->Csvs as $Csv) {
            $row[] = $Csv->getDispName();
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
        if (is_null($this->qb) || is_null($this->entityManager)) {
            throw new \LogicException('query builder not set.');
        }

        $this->fopen();

        $query = $this->qb->getQuery();
        foreach ($query->getResult() as $iteratableResult) {
            $closure($iteratableResult, $this);
            $this->entityManager->detach($iteratableResult);
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
    public function getData(Csv $Csv, $entity)
    {
        // エンティティ名が一致するかどうかチェック.
        $csvEntityName = str_replace('\\\\', '\\', $Csv->getEntityName());
        $entityName = str_replace('\\\\', '\\', get_class($entity));
        if ($csvEntityName !== $entityName) {
            $entityName = str_replace('DoctrineProxy\\__CG__\\', '', $entityName);
            if ($csvEntityName !== $entityName) {
                return null;
            }
        }

        // カラム名がエンティティに存在するかどうかをチェック.
        if (!$entity->offsetExists($Csv->getFieldName())) {
            return null;
        }

        // データを取得.
        $data = $entity->offsetGet($Csv->getFieldName());

        // one to one の場合は, dtb_csv.referece_field_nameと比較し, 合致する結果を取得する.
        if ($data instanceof \Eccube\Entity\AbstractEntity) {
            if (EntityUtil::isNotEmpty($data)) {
                return $data->offsetGet($Csv->getReferenceFieldName());
            }
        } elseif ($data instanceof \Doctrine\Common\Collections\Collection) {
            // one to manyの場合は, カンマ区切りに変換する.
            $array = array();
            foreach ($data as $elem) {
                if (EntityUtil::isNotEmpty($elem)) {
                    $array[] = $elem->offsetGet($Csv->getReferenceFieldName());
                }
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
    public function getConvertEncodhingCallback()
    {
        $config = $this->config;

        return function ($value) use ($config) {
            return mb_convert_encoding(
                (string) $value, $config['csv_export_encoding'], 'UTF-8'
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
            $this->convertEncodingCallBack = $this->getConvertEncodhingCallback();
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
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderQueryBuilder(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('eccube.admin.order.search')) {
            $searchData = $session->get('eccube.admin.order.search');
            $this->findDeserializeObjects($searchData);
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
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCustomerQueryBuilder(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('eccube.admin.customer.search')) {
            $searchData = $session->get('eccube.admin.customer.search');
            $this->findDeserializeObjects($searchData);
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
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductQueryBuilder(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('eccube.admin.product.search')) {
            $searchData = $session->get('eccube.admin.product.search');
            $this->findDeserializeObjects($searchData);
        } else {
            $searchData = array();
        }

        // 商品データのクエリビルダを構築.
        $qb = $this->productRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }

    /**
     * セッションでシリアライズされた Doctrine のオブジェクトを取得し直す.
     *
     * XXX self::setExportQueryBuilder() をコールする前に EntityManager を取得したいので、引数で渡している
     *
     * @param array $searchData セッションから取得した検索条件の配列
     */
    protected function findDeserializeObjects(array &$searchData)
    {
        $em = $this->getEntityManager();
        foreach ($searchData as &$Conditions) {
            if ($Conditions instanceof ArrayCollection) {
                $Conditions = new ArrayCollection(
                    array_map(
                        function ($Entity) use ($em) {
                            return $em->getRepository(get_class($Entity))->find($Entity->getId());
                        }, $Conditions->toArray()
                    )
                );
            } elseif ($Conditions instanceof \Eccube\Entity\AbstractEntity) {
                $Conditions = $em->getRepository(get_class($Conditions))->find($Conditions->getId());
            }
        }
    }
}
