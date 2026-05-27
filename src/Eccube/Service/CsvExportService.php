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

namespace Eccube\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CsvExportService
{
    /**
     * @var resource|null
     */
    protected $fp;

    protected bool $closed = false;

    protected ?\Closure $convertEncodingCallBack = null;

    protected ?QueryBuilder $qb = null;

    protected ?CsvType $CsvType = null;

    /**
     * @var Csv[]|null
     */
    protected ?array $Csvs = null;

    /**
     * CsvExportService constructor.
     */
    public function __construct(protected ?EntityManagerInterface $entityManager, protected CsvRepository $csvRepository, protected CsvTypeRepository $csvTypeRepository, protected OrderRepository $orderRepository, protected ShippingRepository $shippingRepository, protected CustomerRepository $customerRepository, protected ProductRepository $productRepository, protected EccubeConfig $eccubeConfig, protected FormFactoryInterface $formFactory, protected PaginatorInterface $paginator)
    {
    }

    public function setConfig(EccubeConfig $config): void
    {
        $this->eccubeConfig = $config;
    }

    public function setCsvRepository(CsvRepository $csvRepository): void
    {
        $this->csvRepository = $csvRepository;
    }

    public function setCsvTypeRepository(CsvTypeRepository $csvTypeRepository): void
    {
        $this->csvTypeRepository = $csvTypeRepository;
    }

    public function setOrderRepository(OrderRepository $orderRepository): void
    {
        $this->orderRepository = $orderRepository;
    }

    public function setCustomerRepository(CustomerRepository $customerRepository): void
    {
        $this->customerRepository = $customerRepository;
    }

    public function setProductRepository(ProductRepository $productRepository): void
    {
        $this->productRepository = $productRepository;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function setExportQueryBuilder(QueryBuilder $qb): void
    {
        $this->qb = $qb;
    }

    /**
     * Csv種別からServiceの初期化を行う.
     */
    public function initCsvType(CsvType|int $CsvType): void
    {
        if ($CsvType instanceof CsvType) {
            $this->CsvType = $CsvType;
        } else {
            $this->CsvType = $this->csvTypeRepository->find($CsvType);
        }

        $criteria = [
            'CsvType' => $CsvType,
            'enabled' => true,
        ];
        $orderBy = [
            'sort_no' => 'ASC',
        ];
        $this->Csvs = $this->csvRepository->findBy($criteria, $orderBy);
    }

    /**
     * @return Csv[]
     */
    public function getCsvs(): array
    {
        return $this->Csvs;
    }

    /**
     * ヘッダ行を出力する.
     * このメソッドを使う場合は, 事前にinitCsvType($CsvType)で初期化しておく必要がある.
     */
    public function exportHeader(): void
    {
        if (is_null($this->CsvType) || is_null($this->Csvs)) {
            throw new \LogicException('init csv type incomplete.');
        }

        $row = [];
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
     */
    public function exportData(\Closure $closure): void
    {
        if (is_null($this->qb) || is_null($this->entityManager)) {
            throw new \LogicException('query builder not set.');
        }

        $this->fopen();

        $page = 1;
        $limit = 100;
        while ($results = $this->paginator->paginate($this->qb, $page, $limit)) {
            /** @var AbstractPagination<int, mixed> $results */
            if (!$results->valid()) {
                break;
            }

            foreach ($results as $result) {
                $closure($result, $this);
                flush();
            }

            $this->entityManager->clear();
            $page++;
        }

        $this->fclose();
    }

    /**
     * CSV出力項目と比較し, 合致するデータを返す.
     */
    public function getData(Csv $Csv, AbstractEntity $entity): ?string
    {
        // エンティティ名が一致するかどうかチェック.
        $csvEntityName = str_replace('\\\\', '\\', $Csv->getEntityName());
        $entityName = ClassUtils::getClass($entity);
        if ($csvEntityName !== $entityName) {
            return null;
        }

        // カラム名がエンティティに存在するかどうかをチェック.
        if (!$entity->offsetExists($Csv->getFieldName())) {
            return null;
        }

        // データを取得.
        $data = $entity->offsetGet($Csv->getFieldName());

        // one to one の場合は, dtb_csv.reference_field_name, 合致する結果を取得する.
        if ($data instanceof AbstractEntity) {
            return $data->offsetGet($Csv->getReferenceFieldName());
        } elseif ($data instanceof Collection) {
            // one to manyの場合は, カンマ区切りに変換する.
            $array = [];
            foreach ($data as $elem) {
                $array[] = $elem->offsetGet($Csv->getReferenceFieldName());
            }

            return implode($this->eccubeConfig['eccube_csv_export_multidata_separator'], $array);
        } elseif ($data instanceof \DateTime) {
            // datetimeの場合は文字列に変換する.
            return $data->format($this->eccubeConfig['eccube_csv_export_date_format']);
        } elseif (is_bool($data)) {
            // booleanの場合は文字列に変換する.
            return $data ? '1' : '0';
        }

        // スカラ値の場合はそのまま.
        return $data;
    }

    /**
     * 文字エンコーディングの変換を行うコールバック関数を返す.
     */
    public function getConvertEncodingCallback(): \Closure
    {
        $config = $this->eccubeConfig;

        return fn ($value) => mb_convert_encoding(
            (string) $value, $config['eccube_csv_export_encoding'], 'UTF-8'
        );
    }

    public function fopen(): void
    {
        if (is_null($this->fp) || $this->closed) {
            $this->fp = fopen('php://output', 'w');
        }
    }

    /**
     * @param array<int, string|int> $row
     */
    public function fputcsv(array $row): void
    {
        if (is_null($this->convertEncodingCallBack)) {
            $this->convertEncodingCallBack = $this->getConvertEncodingCallback();
        }

        fputcsv($this->fp, array_map($this->convertEncodingCallBack, $row), $this->eccubeConfig['eccube_csv_export_separator'], '"', '\\');
    }

    public function fclose(): void
    {
        if (!$this->closed) {
            fclose($this->fp);
            $this->closed = true;
        }
    }

    /**
     * 受注検索用のクエリビルダを返す.
     */
    public function getOrderQueryBuilder(Request $request): QueryBuilder
    {
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchOrderType::class);
        $searchForm = $builder->getForm();

        $viewData = $session->get('eccube.admin.order.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

        // 受注データのクエリビルダを構築.
        $qb = $this->orderRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }

    /**
     * 会員検索用のクエリビルダを返す.
     */
    public function getCustomerQueryBuilder(Request $request): QueryBuilder
    {
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);
        $searchForm = $builder->getForm();

        $viewData = $session->get('eccube.admin.customer.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

        // 会員データのクエリビルダを構築.
        $qb = $this->customerRepository
            ->getQueryBuilderBySearchData($searchData);

        return $qb;
    }

    /**
     * 商品検索用のクエリビルダを返す.
     */
    public function getProductQueryBuilder(Request $request): QueryBuilder
    {
        $session = $request->getSession();
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);
        $searchForm = $builder->getForm();

        $viewData = $session->get('eccube.admin.product.search', []);
        $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

        // 商品データのクエリビルダを構築.
        $qb = $this->productRepository
            ->getQueryBuilderBySearchDataForAdmin($searchData);

        return $qb;
    }
}
