<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Plugin\ProductReview\Helper;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;

/**
 * 商品レビューを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class ReviewHelper
{
    /**
     * レビュー情報のDB取得
     *
     * @param  int $review_id レビューID
     * @return array レビュー情報
     */
    public function get($review_id)
    {
        $query = array(
            'review_id' => $review_id
        );
        $arrReview = $this->find(array('query' => $query));

        return $arrReview[0];
    }

    /**
     * 商品に紐付いたレビューの一覧を取得
     *
     * @param integer $product_id
     * @return array|null
     */
    public function getListByProductId($product_id)
    {
        $query = array(
            'product_id' => $product_id
        );
        $arrReview = $this->find(array('query' => $query));

        return $arrReview;
    }

    /**
     * レビュー情報を登録する.
     *
     * @param $data
     * @return bool
     */
    public function save($data)
    {
        $objQuery = Application::alias('eccube.query');

        $review_id = $data['review_id'];
        $data['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($review_id == '') {
            // INSERTの実行
            $data['creator_id'] = '0';
            $data['create_date'] = 'CURRENT_TIMESTAMP';
            $data['review_id'] = $objQuery->nextVal('dtb_review_review_id');
            $ret = $objQuery->insert('dtb_review', $data);
        // 既存編集
        } else {
            unset($data['creator_id']);
            unset($data['create_date']);
            $where = 'review_id = ?';
            $ret = $objQuery->update('dtb_review', $data, $where, array($review_id));
        }

        return ($ret) ? $data['review_id'] : FALSE;
    }

    /**
     * レビューを削除する（論理削除）.
     *
     * @param $review_id
     */
    public function delete($review_id)
    {
        $objQuery = Application::alias('eccube.query');
        $data['del_flg'] = 1;
        $objQuery->update('dtb_review', $data, 'review_id = ?', array($review_id));
    }

    /**
     * 条件に合うレビュー情報の一覧を取得.
     *
     * @param array $params
     * @return array|null
     */
    public function find($params = array())
    {
        $objQuery = Application::alias('eccube.query');

        // 検索条件を作成
        $query = (isset($params['query'])) ? $params['query'] : array();
        list($where, $values) = $this->makeWhere($query);

        // 表示件数
        if (isset($params['limit'])) {
            if (isset($params['offset'])) {
                $objQuery->setLimitOffset($params['limit'], $params['offset']);
            } else {
                $objQuery->setLimit($params['limit']);
            }
        } elseif (isset($params['offset'])) {
            $objQuery->setOffset($params['offset']);
        }

        // 表示順序
        $order = (isset($params['order'])) ? $params['order'] : 'A.create_date DESC';
        $objQuery->setOrder($order);

        //検索結果の取得
        //レビュー情報のカラムの取得
        $col = 'review_id, A.product_id, reviewer_name, sex, recommend_level, ';
        $col .= 'reviewer_url, title, comment, A.status, A.create_date, A.update_date, name';
        $from = 'dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ';
        $arrReview = $objQuery->select($col, $from, $where, $values);

        return $arrReview;
    }

    /**
     * 条件に合うレビュー情報の件数を取得.
     *
     * @param array $query
     * @return int
     */
    public function count($query = array())
    {
        $objQuery = Application::alias('eccube.query');
        // 検索条件を作成
        list($where, $values) = $this->makeWhere($query);
        $from = 'dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ';
        return $objQuery->count($from, $where, $values);
    }

    /**
     * WHERE文の作成
     *
     * @param  array $query フォームデータ
     * @return array WHERE文、判定値
     */
    private function makeWhere($query = array())
    {
        //削除されていない商品を検索
        $where = 'A.del_flg = 0 AND B.del_flg = 0';
        $values = array();

        foreach ($query AS $key => $val) {
            if (empty($val)) continue;

            switch ($key) {
                case 'product_id':
                    if (is_array($val)) {
                        $where.= ' AND A.product_id IN (' . Utils::sfGetCommaList($val) . ')';
                    } else {
                        $where.= ' AND A.product_id = ?';
                        $values[] = $val;
                    }
                    break;

                case 'review_id':
                    if (is_array($val)) {
                        $where.= ' AND review_id IN (' . Utils::sfGetCommaList($val) . ')';
                    } else {
                        $where.= ' AND review_id = ?';
                        $values[] = $val;
                    }
                    break;

                case 'reviewer_name':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND reviewer_name LIKE ? ';
                    $values[] = "%$val%";
                    break;

                case 'reviewer_url':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND reviewer_url LIKE ? ';
                    $values[] = "%$val%";
                    break;

                case 'product_name':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND name LIKE ? ';
                    $values[] = "%$val%";
                    break;

                case 'product_code':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND A.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ?)';
                    $values[] = "%$val%";
                    break;

                case 'reviewer_sex':
                    $tmp_where = '';
                    //$val=配列の中身,$element=各キーの値(1,2)
                    if (is_array($val)) {
                        foreach ($val as $element) {
                            if ($element != '') {
                                if ($tmp_where == '') {
                                    $tmp_where .= ' AND (sex = ?';
                                } else {
                                    $tmp_where .= ' OR sex = ?';
                                }
                                $values[] = $element;
                            }
                        }
                        if ($tmp_where != '') {
                            $tmp_where .= ')';
                            $where .= " $tmp_where ";
                        }
                    }

                    break;

                case 'recommend_level':
                    $where.= ' AND recommend_level = ? ';
                    $values[] = $val;
                    break;

                case 'date_from':
                    $where.= ' AND A.create_date >= ? ';
                    $values[] = $val;
                    break;

                case 'date_to':
                    $where.= " AND A.create_date <= cast( ? as date) ";
                    $values[] = $val;
                    break;

                default:
                    break;
            }

        }

        return array($where, $values);
    }
}
