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
 * メーカーを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Maker
{
    /**
     * メーカーの情報を取得.
     *
     * @param integer $maker_id メーカーID
     * @param boolean $has_deleted 削除されたメーカーも含む場合 true; 初期値 false
     * @return array
     */
    public function getMaker($maker_id, $has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'maker_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_maker', $where, array($maker_id));

        return $arrRet[0];
    }

    /**
     * 名前からメーカーの情報を取得.
     *
     * @param integer $name メーカー名
     * @param boolean $has_deleted 削除されたメーカーも含む場合 true; 初期値 false
     * @return array
     */
    public function getByName($name, $has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'name = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_maker', $where, array($name));

        return $arrRet[0];
    }

    /**
     * メーカー一覧の取得.
     *
     * @param boolean $has_deleted 削除されたメーカーも含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'maker_id, name';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_maker';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * メーカーの登録.
     *
     * @param array $sqlval
     * @return multiple 登録成功:メーカーID, 失敗:FALSE
     */
    public function saveMaker($sqlval)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $maker_id = $sqlval['maker_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($maker_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_maker') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['maker_id'] = $objQuery->nextVal('dtb_maker_maker_id');
            $ret = $objQuery->insert('dtb_maker', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'maker_id = ?';
            $ret = $objQuery->update('dtb_maker', $sqlval, $where, array($maker_id));
        }

        return ($ret) ? $sqlval['maker_id'] : FALSE;
    }

    /**
     * メーカーの削除.
     *
     * @param integer $maker_id メーカーID
     * @return void
     */
    public function delete($maker_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        // ランク付きレコードの削除
        $objDb->sfDeleteRankRecord('dtb_maker', 'maker_id', $maker_id, '', true);
    }

    /**
     * メーカーの表示順をひとつ上げる.
     *
     * @param integer $maker_id メーカーID
     * @return void
     */
    public function rankUp($maker_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankUp('dtb_maker', 'maker_id', $maker_id);
    }

    /**
     * メーカーの表示順をひとつ下げる.
     *
     * @param integer $maker_id メーカーID
     * @return void
     */
    public function rankDown($maker_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankDown('dtb_maker', 'maker_id', $maker_id);
    }

    /**
     * メーカーIDをキー, 名前を値とする配列を取得.
     *
     * @return array
     */
    public static function getIDValueList()
    {
        return SC_Helper_DB_Ex::sfGetIDValueList('dtb_maker', 'maker_id', 'name');
    }
}
