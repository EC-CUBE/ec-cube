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
 * カテゴリーを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Category
{
    private $count_check;

    /**
     * コンストラクター
     * 
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     */
    function __construct($count_check = FALSE)
    {
        $this->count_check = $count_check;
    }

    /**
     * カテゴリーの情報を取得.
     * 
     * @param integer $category_id カテゴリーID
     * @return array
     */
    public function get($category_id)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'dtb_category.*, dtb_category_total_count.product_count';
        $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
        $where = 'dtb_category.category_id = ? AND del_flg = 0';
        // 登録商品数のチェック
        if ($this->count_check) {
            $where .= ' AND product_count > 0';
        }
        $arrRet = $objQuery->getRow($col, $from, $where, array($category_id));
        return $arrRet;
    }

    /**
     * カテゴリー一覧の取得.
     * 
     * @param boolean $cid_to_key 配列のキーをカテゴリーIDにする場合はtrue
     * @return array カテゴリー一覧の配列
     */
    public function getList($cid_to_key = FALSE)
    {
        static $arrCategory = array(), $cidIsKey = array();

        if (!isset($arrCategory[$this->count_check])) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $col = 'dtb_category.*, dtb_category_total_count.product_count';
            $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
            // 登録商品数のチェック
            if ($this->count_check) {
                $where = 'del_flg = 0 AND product_count > 0';
            } else {
                $where = 'del_flg = 0';
            }
            $objQuery->setOption('ORDER BY rank DESC');
            $arrTmp = $objQuery->select($col, $from, $where);

            $arrCategory[$this->count_check] = $arrTmp;
        }

        if ($cid_to_key) {
            if (!isset($cidIsKey[$this->count_check])) {
                // 配列のキーをカテゴリーIDに
                $cidIsKey[$this->count_check] = SC_Utils_Ex::makeArrayIDToKey('category_id', $arrCategory[$this->count_check]);
            }
            return $cidIsKey[$this->count_check];
        }

        return $arrCategory[$this->count_check];
    }

    /**
     * カテゴリーツリーの取得.
     * 
     * @return type
     */
    public function getTree()
    {
        static $arrTree = array();
        if (!isset($arrTree[$this->count_check])) {
            $arrList = $this->getList();
            $arrTree[$this->count_check] = SC_Utils_Ex::buildTree('category_id', 'parent_category_id', LEVEL_MAX, $arrList);
        }
        return $arrTree[$this->count_check];
    }

    /**
     * 親カテゴリーIDの配列を取得.
     * 
     * @param integer $category_id 起点のカテゴリーID
     * @param boolean $id_only IDだけの配列を返す場合はtrue
     * @return array
     */
    public function getTreeTrail($category_id, $id_only = TRUE)
    {
        $arrCategory = $this->getList(TRUE);
        $arrTrailID = SC_Utils_Ex::getTreeTrail($category_id, 'category_id', 'parent_category_id', $arrCategory, TRUE, 0, $id_only);
        return $arrTrailID;
    }
}
