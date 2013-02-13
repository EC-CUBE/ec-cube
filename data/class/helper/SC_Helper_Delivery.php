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
 * 配送方法を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Delivery
{

    /**
     * 配送方法一覧の取得.
     *
     * @param boolean $has_deleted 削除された支払方法も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_deliv';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);
        return $arrRet;
    }

    /**
     * 配送方法の削除.
     * 
     * @param integer $deliv_id 配送方法ID
     * @return void
     */
    public function delete($deliv_id) {
        $objDb = new SC_Helper_DB_Ex();
        // ランク付きレコードの削除
        $objDb->sfDeleteRankRecord('dtb_deliv', 'deliv_id', $deliv_id);
    }

    /**
     * 配送方法の表示順をひとつ上げる.
     * 
     * @param integer $deliv_id 配送方法ID
     * @return void
     */
    public function rankUp($deliv_id) {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankUp('dtb_deliv', 'deliv_id', $deliv_id);
    }

    /**
     * 配送方法の表示順をひとつ下げる.
     * 
     * @param integer $deliv_id 配送方法ID
     * @return void
     */
    public function rankDown($deliv_id) {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankDown('dtb_deliv', 'deliv_id', $deliv_id);
    }

    /**
     * 配送方法IDをキー, 名前を値とする配列を取得.
     * 
     * @param string $type 値のタイプ
     * @return array
     */
    public static function getIDValueList($type = 'name') {
        return SC_Helper_DB_Ex::sfGetIDValueList('dtb_deliv', 'deliv_id', $type);
    }
}
