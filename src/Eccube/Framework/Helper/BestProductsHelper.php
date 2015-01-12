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

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\DbHelper;

/**
 * おすすめ商品を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class BestProductsHelper
{
    /**
     * おすすめ商品の情報を取得.
     *
     * @param  integer $best_id     おすすめ商品ID
     * @param  boolean $has_deleted 削除されたおすすめ商品も含む場合 true; 初期値 false
     * @return array
     */
    public function getBestProducts($best_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = 'best_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select($col, 'dtb_best_products', $where, array($best_id));

        return $arrRet[0];
    }

    /**
     * おすすめ商品の情報をランクから取得.
     *
     * @param  integer $rank        ランク
     * @param  boolean $has_deleted 削除されたおすすめ商品も含む場合 true; 初期値 false
     * @return array
     */
    public function getByRank($rank, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = 'rank = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select($col, 'dtb_best_products', $where, array($rank));

        return $arrRet[0];
    }

    /**
     * おすすめ商品一覧の取得.
     *
     * @param  integer $dispNumber  表示件数
     * @param  integer $pageNumber  ページ番号
     * @param  boolean $has_deleted 削除されたおすすめ商品も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($dispNumber = 0, $pageNumber = 0, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_best_products';
        $objQuery->setOrder('rank');
        if ($dispNumber > 0) {
            if ($pageNumber > 0) {
                $objQuery->setLimitOffset($dispNumber, (($pageNumber - 1) * $dispNumber));
            } else {
                $objQuery->setLimit($dispNumber);
            }
        }
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * おすすめ商品の登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:おすすめ商品ID, 失敗:FALSE
     */
    public function saveBestProducts($sqlval)
    {
        $objQuery = Application::alias('eccube.query');

        $best_id = $sqlval['best_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($best_id == '') {
            // INSERTの実行
            if (!$sqlval['rank']) {
                $sqlval['rank'] = $objQuery->max('rank', 'dtb_best_products') + 1;
            }
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['best_id'] = $objQuery->nextVal('dtb_best_products_best_id');
            $ret = $objQuery->insert('dtb_best_products', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'best_id = ?';
            $ret = $objQuery->update('dtb_best_products', $sqlval, $where, array($best_id));
        }

        return ($ret) ? $sqlval['best_id'] : FALSE;
    }

    /**
     * おすすめ商品の削除.
     *
     * @param  integer $best_id おすすめ商品ID
     * @return void
     */
    public function deleteBestProducts($best_id)
    {
        $objQuery = Application::alias('eccube.query');

        $table = 'dtb_best_products';
        $arrVal = array('del_flg' => 1);
        $where = 'best_id = ?';
        $arrWhereVal = array($best_id);
        $objQuery->update($table, $arrVal, $where, $arrWhereVal);
    }

    /**
     * 商品IDの配列からおすすめ商品を削除.
     *
     * @param  array $productIDs 商品ID
     * @return void
     */
    public function deleteByProductIDs($productIDs)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrList = $this->getList();
        foreach ($arrList as $recommend) {
            if (in_array($recommend['product_id'], $productIDs)) {
                $this->deleteBestProducts($recommend['best_id']);
            }
        }
    }

    /**
     * おすすめ商品の表示順をひとつ上げる.
     *
     * @param  integer $best_id おすすめ商品ID
     * @return void
     */
    public function rankUp($best_id)
    {
        $arrBestProducts = $this->getBestProducts($best_id);
        $rank = $arrBestProducts['rank'];

        if ($rank > 1) {
            // 表示順が一つ上のIDを取得する
            $arrAboveBestProducts = $this->getByRank($rank - 1);
            $above_best_id = $arrAboveBestProducts['best_id'];

            if ($above_best_id) {
                // 一つ上のものを一つ下に下げる
                $this->changeRank($above_best_id, $rank);
            } else {
                // 無ければ何もしない。(歯抜けの場合)
            }

            // 一つ上に上げる
            $this->changeRank($best_id, $rank - 1);
        }
    }

    /**
     * おすすめ商品の表示順をひとつ下げる.
     *
     * @param  integer $best_id おすすめ商品ID
     * @return void
     */
    public function rankDown($best_id)
    {
        $arrBestProducts = $this->getBestProducts($best_id);
        $rank = $arrBestProducts['rank'];

        if ($rank < RECOMMEND_NUM) {
            // 表示順が一つ下のIDを取得する
            $arrBelowBestProducts = $this->getByRank($rank + 1);
            $below_best_id = $arrBelowBestProducts['best_id'];

            if ($below_best_id) {
                // 一つ下のものを一つ上に上げる
                $this->changeRank($below_best_id, $rank);
            } else {
                // 無ければ何もしない。(歯抜けの場合)
            }

            // 一つ下に下げる
            $this->changeRank($best_id, $rank + 1);
        }
    }

    /**
     * 対象IDのrankを指定値に変更する
     *
     * @param integer $best_id 対象ID
     * @param integer $rank 変更したいrank値
     * @return void
     */
    public function changeRank($best_id, $rank)
    {
        $objQuery = Application::alias('eccube.query');

        $table = 'dtb_best_products';
        $sqlval = array('rank' => $rank);
        $where = 'best_id = ?';
        $arrWhereVal = array($best_id);
        $objQuery->update($table, $sqlval, $where, $arrWhereVal);
    }
}
