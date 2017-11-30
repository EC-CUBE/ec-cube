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


namespace Eccube\Repository;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Repository;
use Eccube\Doctrine\Query\Queries;
use Eccube\Util\StringUtil;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @Repository
 */
class ProductRepository extends AbstractRepository
{
    /**
     * @Inject("eccube.queries")
     * @var Queries
     */
    protected $queries;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

   /**
     * get query builder.
     *
     * @param  array $searchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderBySearchData($searchData)
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.Status = 1');

        // category
        $categoryJoin = false;
        if (!empty($searchData['category_id']) && $searchData['category_id']) {
            $Categories = $searchData['category_id']->getSelfAndDescendants();
            if ($Categories) {
                $qb
                    ->innerJoin('p.ProductCategories', 'pct')
                    ->innerJoin('pct.Category', 'c')
                    ->andWhere($qb->expr()->in('pct.Category', ':Categories'))
                    ->setParameter('Categories', $Categories);
                $categoryJoin = true;
            }
        }

        // name
        if (isset($searchData['name']) && StringUtil::isNotBlank($searchData['name'])) {
            $keywords = preg_split('/[\s　]+/u', $searchData['name'], -1, PREG_SPLIT_NO_EMPTY);

            foreach ($keywords as $index => $keyword) {
                $key = sprintf('keyword%s', $index);
                $qb
                    ->andWhere(sprintf('NORMALIZE(p.name) LIKE NORMALIZE(:%s) OR 
                        NORMALIZE(p.search_word) LIKE NORMALIZE(:%s) OR 
                        EXISTS (SELECT wpc%d FROM \Eccube\Entity\ProductClass wpc%d WHERE p = wpc%d.Product AND NORMALIZE(wpc%d.code) LIKE NORMALIZE(:%s))', 
                        $key, $key, $index, $index, $index, $index, $key))
                    ->setParameter($key, '%' . $keyword . '%');
            }
        }

        // Order By
        // 価格低い順
        $config = $this->appConfig;
        if (!empty($searchData['orderby']) && $searchData['orderby']->getId() == $config['product_order_price_lower']) {
            //@see http://doctrine-orm.readthedocs.org/en/latest/reference/dql-doctrine-query-language.html
            $qb->addSelect('MIN(pc.price02) as HIDDEN price02_min');
            $qb->innerJoin('p.ProductClasses', 'pc');
            $qb->groupBy('p');
            // postgres9.0以下は, groupBy('p.id')が利用できない
            // mysqlおよびpostgresql9.1以上であればgroupBy('p.id')にすることで性能向上が期待できる.
            // @see https://github.com/EC-CUBE/ec-cube/issues/1904
            // $qb->groupBy('p.id');
            $qb->orderBy('price02_min', 'ASC');
            $qb->addOrderBy('p.id', 'DESC');
            // 価格高い順
        } else if (!empty($searchData['orderby']) && $searchData['orderby']->getId() == $config['product_order_price_higher']) {
            $qb->addSelect('MAX(pc.price02) as HIDDEN price02_max');
            $qb->innerJoin('p.ProductClasses', 'pc');
            $qb->groupBy('p');
            $qb->orderBy('price02_max', 'DESC');
            $qb->addOrderBy('p.id', 'DESC');
            // 新着順
        } else if (!empty($searchData['orderby']) && $searchData['orderby']->getId() == $config['product_order_newer']) {
            // 在庫切れ商品非表示の設定が有効時対応
            // @see https://github.com/EC-CUBE/ec-cube/issues/1998
            if ($this->entityManager->getFilters()->isEnabled('option_nostock_hidden') == true) {
                $qb->innerJoin('p.ProductClasses', 'pc');
            }
            $qb->orderBy('p.create_date', 'DESC');
        } else {
            if ($categoryJoin === false) {
                $qb
                    ->leftJoin('p.ProductCategories', 'pct')
                    ->leftJoin('pct.Category', 'c');
            }
            $qb
                ->addOrderBy('p.id', 'DESC');
        }

        return $this->queries->customize(QueryKey::PRODUCT_SEARCH, $qb, $searchData);
    }

    /**
     * get query builder.
     *
     * @param  array $searchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderBySearchDataForAdmin($searchData)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.ProductClasses', 'pc');

        // id
        if (isset($searchData['id']) && StringUtil::isNotBlank($searchData['id'])) {
            $id = preg_match('/^\d+$/', $searchData['id']) ? $searchData['id'] : null;
            $qb
                ->andWhere('p.id = :id OR p.name LIKE :likeid OR pc.code LIKE :likeid')
                ->setParameter('id', $id)
                ->setParameter('likeid', '%' . $searchData['id'] . '%');
        }

        // code
        /*
        if (!empty($searchData['code']) && $searchData['code']) {
            $qb
                ->innerJoin('p.ProductClasses', 'pc')
                ->andWhere('pc.code LIKE :code')
                ->setParameter('code', '%' . $searchData['code'] . '%');
        }

        // name
        if (!empty($searchData['name']) && $searchData['name']) {
            $keywords = preg_split('/[\s　]+/u', $searchData['name'], -1, PREG_SPLIT_NO_EMPTY);
            foreach ($keywords as $keyword) {
                $qb
                    ->andWhere('p.name LIKE :name')
                    ->setParameter('name', '%' . $keyword . '%');
            }
        }
       */

        // category
        if (!empty($searchData['category_id']) && $searchData['category_id']) {
            $Categories = $searchData['category_id']->getSelfAndDescendants();
            if ($Categories) {
                $qb
                    ->innerJoin('p.ProductCategories', 'pct')
                    ->innerJoin('pct.Category', 'c')
                    ->andWhere($qb->expr()->in('pct.Category', ':Categories'))
                    ->setParameter('Categories', $Categories);
            }
        }

        // status
        if (!empty($searchData['status']) && $searchData['status']) {
            $qb
                ->andWhere($qb->expr()->in('p.Status', ':Status'))
                ->setParameter('Status', $searchData['status']);
        }

        // link_status
        if (isset($searchData['link_status'])) {
            $qb
                ->andWhere($qb->expr()->in('p.Status', ':Status'))
                ->setParameter('Status', $searchData['link_status']);
        }

        // stock status
        if (isset($searchData['stock_status'])) {
            $qb
                ->andWhere('pc.stock_unlimited = :StockUnlimited AND pc.stock = 0')
                ->setParameter('StockUnlimited', $searchData['stock_status']);
        }

        // crate_date
        if (!empty($searchData['create_date_start']) && $searchData['create_date_start']) {
            $date = $searchData['create_date_start'];
            $qb
                ->andWhere('p.create_date >= :create_date_start')
                ->setParameter('create_date_start', $date);
        }

        if (!empty($searchData['create_date_end']) && $searchData['create_date_end']) {
            $date = clone $searchData['create_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('p.create_date < :create_date_end')
                ->setParameter('create_date_end', $date);
        }

        // update_date
        if (!empty($searchData['update_date_start']) && $searchData['update_date_start']) {
            $date = $searchData['update_date_start'];
            $qb
                ->andWhere('p.update_date >= :update_date_start')
                ->setParameter('update_date_start', $date);
        }
        if (!empty($searchData['update_date_end']) && $searchData['update_date_end']) {
            $date = clone $searchData['update_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('p.update_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        }


        // Order By
        $qb
            ->orderBy('p.update_date', 'DESC');

        return $this->queries->customize(QueryKey::PRODUCT_SEARCH_ADMIN, $qb, $searchData);
    }

    /**
     * get query builder.
     *
     * @param $Customer
     * @return \Doctrine\ORM\QueryBuilder
     * @see CustomerFavoriteProductRepository::getQueryBuilderByCustomer()
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getFavoriteProductQueryBuilderByCustomer($Customer)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.CustomerFavoriteProducts', 'cfp')
            ->where('cfp.Customer = :Customer AND p.Status = 1')
            ->setParameter('Customer', $Customer);

        // Order By
        // XXX Paginater を使用した場合に PostgreSQL で正しくソートできない
        $qb->addOrderBy('cfp.create_date', 'DESC');

        return $this->queries->customize(QueryKey::PRODUCT_GET_FAVORITE, $qb, ['customer' => $Customer]);
    }
}
