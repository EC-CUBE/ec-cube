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
 * 休日を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Holiday
{
    /**
     * 休日の情報を取得.
     *
     * @param  integer $holiday_id  休日ID
     * @param  boolean $has_deleted 削除された休日も含む場合 true; 初期値 false
     * @return array
     */
    public function get($holiday_id, $has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'holiday_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_holiday', $where, array($holiday_id));

        return $arrRet[0];
    }

    /**
     * 休日一覧の取得.
     *
     * @param  boolean $has_deleted 削除された休日も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'holiday_id, title, month, day';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_holiday';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * 休日の登録.
     *
     * @param  array    $sqlval
     * @return multiple 登録成功:休日ID, 失敗:FALSE
     */
    public function save($sqlval)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $holiday_id = $sqlval['holiday_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // 新規登録
        if ($holiday_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_holiday') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['holiday_id'] = $objQuery->nextVal('dtb_holiday_holiday_id');
            $ret = $objQuery->insert('dtb_holiday', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $where = 'holiday_id = ?';
            $ret = $objQuery->update('dtb_holiday', $sqlval, $where, array($holiday_id));
        }

        return ($ret) ? $sqlval['holiday_id'] : FALSE;
    }

    /**
     * 休日の削除.
     *
     * @param  integer $holiday_id 休日ID
     * @return void
     */
    public function delete($holiday_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        // ランク付きレコードの削除
        $objDb->sfDeleteRankRecord('dtb_holiday', 'holiday_id', $holiday_id, '', true);
    }

    /**
     * 休日の表示順をひとつ上げる.
     *
     * @param  integer $holiday_id 休日ID
     * @return void
     */
    public function rankUp($holiday_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankUp('dtb_holiday', 'holiday_id', $holiday_id);
    }

    /**
     * 休日の表示順をひとつ下げる.
     *
     * @param  integer $holiday_id 休日ID
     * @return void
     */
    public function rankDown($holiday_id)
    {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankDown('dtb_holiday', 'holiday_id', $holiday_id);
    }

    /**
     * 同じ日付の休日が存在するか確認.
     *
     * @param  integer $month
     * @param  integer $day
     * @param  integer $holiday_id
     * @return boolean 同日付の休日が存在:true
     */
    public function isDateExist($month, $day, $holiday_id = NULL)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'del_flg = 0 AND month = ? AND day = ?';
        $arrVal = array($month, $day);
        if (!SC_Utils_Ex::isBlank($holiday_id)) {
            $where .= ' AND holiday_id <> ?';
            $arrVal[] = $holiday_id;
        }
        $arrRet = $objQuery->select('holiday_id, title', 'dtb_holiday', $where, $arrVal);

        return !SC_Utils_Ex::isBlank($arrRet);
    }
}
