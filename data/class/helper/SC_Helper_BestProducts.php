<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * おすすめ商品を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_BestProducts
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();

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
        $objDb = new SC_Helper_DB_Ex();
        // ランク付きレコードの削除
        $objDb->sfDeleteRankRecord('dtb_best_products', 'best_id', $best_id, '', TRUE);
    }

    /**
     * 商品IDの配列からおすすめ商品を削除.
     *
     * @param  array $productIDs 商品ID
     * @return void
     */
    public function deleteByProductIDs($productIDs)
    {
        $objDb = new SC_Helper_DB_Ex();
        $arrList = $this->getList();
        foreach ($arrList as $recommend) {
            if (in_array($recommend['product_id'], $productIDs)) {
                $objDb->sfDeleteRankRecord('dtb_best_products', 'best_id', $recommend['best_id'], '', TRUE);
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
        $objDb = new SC_Helper_DB_Ex();
        //おすすめはデータベースの登録が昇順なので、Modeを逆にする。
        $objDb->sfRankDown('dtb_best_products', 'best_id', $best_id);
    }

    /**
     * おすすめ商品の表示順をひとつ下げる.
     *
     * @param  integer $best_id おすすめ商品ID
     * @return void
     */
    public function rankDown($best_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        //おすすめはデータベースの登録が昇順なので、Modeを逆にする。
        $objDb->sfRankUp('dtb_best_products', 'best_id', $best_id);
    }
}
