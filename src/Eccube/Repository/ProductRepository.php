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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{
    /**
     * @var array
     */
    private $config;

    private $limit;

    private $offset;

    /**
     * setConfig
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        $this->offset = 0;
        $this->limit = null;
    }

    /**
     * set limit.
     *
     * @param  integer $limit
     * @return boolean
     *
     * @throws none
     */
    public function setLimit($limit = null)
    {
        if(!is_null($limit) && is_numeric($limit)){
            $this->limit = $limit;
            return true;
        }
        return false;
    }

    /**
     * set offset.
     *
     * @param  integer $offset
     * @return boolean
     *
     * @throws none
     */
    public function setoffset($offset = null)
    {
        if(!is_null($offset) && is_numeric($offset)){
            $this->offset = $offset;
            return true;
        }
        return false;
    }

    /**
     * get Limit.
     *
     * @param  none
     * @return integer $limit
     *
     * @throws NotFoundHttpException
     */
    public function getLimit()
    {
        if(!is_null($this->limit) && is_numeric($this->limit)){
            return $this->limit;
        }
        throw new NotFoundHttpException();
    }

    /**
     * get Offset.
     *
     * @param  none
     * @return integer $offset
     *
     * @throws NotFoundHttpException
     */
    public function getOffset()
    {
        if(!is_null($this->offset) && is_numeric($this->offset)){
            return $this->offset;
        }
        throw new NotFoundHttpException();
    }

    /**
     * get Product.
     *
     * @param  integer $productId
     * @return \Eccube\Entity\Product
     *
     * @throws NotFoundHttpException
     */
    public function get($productId)
    {
        // Product
        try {
            $qb = $this->createQueryBuilder('p')
                ->andWhere('p.id = :id');

            $product = $qb
                ->getQuery()
                ->setParameters(array(
                    'id' => $productId,
                ))
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new NotFoundHttpException();
        }

        return $product;
    }


    /**
     * get search object.
     *
     * @param  array $searchData
     * @return array ProductObject
     */
    public function getObjectCollectionBySearchData($searchData)
    {
        $qb = $this->_createObjectCollectionQueryBuilderBySearchData($searchData);

        $qb->setFirstResult($this->offset)
            ->setMaxResults($this->limit);

        $res = $qb->getQuery()->getResult();
        $pids = array();
        foreach($res as $val)
        {
            if(isset($val['id']) && !empty($val['id'])){
                $pids[] = $val['id'];
            }
        }

        $pobj = $this->createQueryBuilder('p')
            ->andWhere('p.Status = 1')
            ->andWhere($qb->expr()->in('p.id', ':pids'))
            ->setParameter('pids', $pids)
            ->getQuery()->getResult();

        $ref_obj = array();

        foreach($pobj as $key => $obj){
            for($i = 0; $i < count($pids); $i++){
                if($pids[$i] == $obj->getId()){
                    $ref_obj[$i] = $obj;
                    continue;
                }
            }
        }

        ksort($ref_obj);

        return $ref_obj;
    }

    /**
     * get object count num.
     *
     * @param  array $searchData
     * @return int $count
     */
    public function countObjectCollectionBySearchData($searchData)
    {
        $qb = $this->_createObjectCollectionQueryBuilderBySearchData($searchData);
        $count = 0;
        //$count = $qb->getQuery()->getSingleScalarResult();
        $count = 0;
        $count = count($qb->getQuery()->getResult());
        return $count;
    }

    /**
     * create ObjectCollectionBySearchDataQueryBuilder.
     *
     * @param  array $searchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function _createObjectCollectionQueryBuilderBySearchData($searchData)
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
        if (!empty($searchData['name']) && $searchData['name']) {
            $keywords = preg_split('/[\s　]+/u', $searchData['name'], -1, PREG_SPLIT_NO_EMPTY);

            foreach ($keywords as $index => $keyword) {
                $key = sprintf('keyword%s', $index);
                $qb
                    ->andWhere(sprintf('p.name LIKE :%s OR p.search_word LIKE :%s', $key, $key))
                    ->setParameter($key, '%' . $keyword . '%');
            }
        }

        // 価格の降順じゃない際のエラーハンドリング
        if (empty($searchData['orderby']) || $searchData['orderby']->getId() != '1') {
            return false;
        }

        $qb->innerJoin('p.ProductClasses', 'pc')
            ->select('p.id')
            //->distinct('p.id')
            ->addSelect('p')
            //->addSelect($qb->expr()->MAX('pc.price02'))
            ->addSelect('MAX(pc.price02) as max_price')
            ->groupBy('p.id')
            ->orderBy('max_price', 'DESC');
            //->orderBy('pc.id', 'ASC');

        return $qb;
    }

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
        if (!empty($searchData['name']) && $searchData['name']) {
            $keywords = preg_split('/[\s　]+/u', $searchData['name'], -1, PREG_SPLIT_NO_EMPTY);

            foreach ($keywords as $index => $keyword) {
                $key = sprintf('keyword%s', $index);
                $qb
                    ->andWhere(sprintf('p.name LIKE :%s OR p.search_word LIKE :%s', $key, $key))
                    ->setParameter($key, '%' . $keyword . '%');
            }
        }

        if (!empty($searchData['orderby']) && $searchData['orderby']->getId() == '2') {
            $qb->innerJoin('p.ProductClasses', 'pc');
            $qb->orderBy('p.create_date', 'DESC');
        } else {
            if ($categoryJoin == false) {
                $qb
                    ->leftJoin('p.ProductCategories', 'pct')
                    ->leftJoin('pct.Category', 'c');
            }
            $qb
                ->addSelect('pct')
                ->addSelect('c')
                ->orderBy('c.rank', 'DESC')
                ->addOrderBy('pct.rank', 'DESC')
                ->addOrderBy('p.id', 'DESC');
        }

        return $qb;
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
            ->select(array('p', 'pi'))
            ->leftJoin('p.ProductImage', 'pi')
            ->innerJoin('p.ProductClasses', 'pc');

        // id
        if (!empty($searchData['id']) && $searchData['id']) {
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
        if (!empty($searchData['status']) && $searchData['status']->toArray()) {
            $qb
                ->andWhere($qb->expr()->in('p.Status', ':Status'))
                ->setParameter('Status', $searchData['status']->toArray());
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
            $date = $searchData['create_date_start']
                ->format('Y-m-d H:i:s');
            $qb
                ->andWhere('p.create_date >= :create_date_start')
                ->setParameter('create_date_start', $date);
        }

        if (!empty($searchData['create_date_end']) && $searchData['create_date_end']) {
            $date = $searchData['create_date_end']
                ->modify('+1 days')
                ->format('Y-m-d H:i:s');
            $qb
                ->andWhere('p.create_date < :create_date_end')
                ->setParameter('create_date_end', $date);
        }

        // update_date
        if (!empty($searchData['update_date_start']) && $searchData['update_date_start']) {
            $date = $searchData['update_date_start']
                ->format('Y-m-d H:i:s');
            $qb
                ->andWhere('p.update_date >= :update_date_start')
                ->setParameter('update_date_start', $date);
        }
        if (!empty($searchData['update_date_end']) && $searchData['update_date_end']) {
            $date = $searchData['update_date_end']
                ->modify('+1 days')
                ->format('Y-m-d H:i:s');
            $qb
                ->andWhere('p.update_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        }


        // Order By
        $qb
            ->orderBy('p.update_date', 'DESC')
            ->addOrderBy('pi.rank', 'DESC');

        return $qb;
    }

    /**
     * get query builder.
     *
     * @param $Customer
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getFavoriteProductQueryBuilderByCustomer($Customer)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.CustomerFavoriteProducts', 'cfp')
            ->where('cfp.Customer = :Customer AND p.Status = 1')
            ->setParameter('Customer', $Customer);

        // Order By
        $qb->addOrderBy('cfp.create_date', 'DESC');

        return $qb;
    }
}
