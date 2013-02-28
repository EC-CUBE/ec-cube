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
    /**
     * カテゴリー一覧の取得.
     * 
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     * @param boolean $cid_to_key 配列のキーをカテゴリーIDにする場合はtrue
     * @return array カテゴリー一覧の配列
     */
    public function getList($count_check = FALSE, $cid_to_key = FALSE)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
        // 登録商品数のチェック
        if ($count_check) {
            $where = 'del_flg = 0 AND product_count > 0';
        } else {
            $where = 'del_flg = 0';
        }
        $objQuery->setOption('ORDER BY rank DESC');
        $arrCategory = $objQuery->select($col, $from, $where);

        if ($cid_to_key) {
            // 配列のキーをカテゴリーIDに
            $arrTmp = array();
            foreach ($arrCategory as $category) {
                $arrTmp[$category['category_id']] = $category;
            }
            $arrCategory =& $arrTmp;
            unset($arrTmp);
        }
        
        return $arrCategory;
    }

    /**
     * カテゴリーツリーの取得.
     * 
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     * @return type
     */
    public function getTree($count_check = FALSE)
    {
        $arrList = $this->getList($count_check);
        $arrTree = SC_Utils_Ex::buildTree('category_id', 'parent_category_id', LEVEL_MAX, $arrList);
        return $arrTree;
    }
}
