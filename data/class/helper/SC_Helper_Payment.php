<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * 支払方法を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Payment
{
    /**
     * 支払方法の情報を取得.
     * 
     * @param integer $payment_id 支払方法ID
     * @return array
     */
    public function get($payment_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'payment_id = ?';
        $arrRet = $objQuery->select('*', 'dtb_payment', $where, array($payment_id));
        return $arrRet[0];
    }

    /**
     * 支払方法一覧の取得.
     *
     * @return array
     */
    public function getList() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'payment_id, payment_method, charge, rule_max, upper_rule, note, fix, charge_flg';
        $where = 'del_flg = 0';
        $table = 'dtb_payment';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where);
        return $arrRet;
    }

    /**
     * 支払方法の登録.
     * 
     * @param array $sqlval
     * @return void
     */
    public function save($sqlval) {
        $payment_id = $sqlval['payment_id'];

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 新規登録
        if ($payment_id == '') {
            // INSERTの実行
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_payment') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['payment_id'] = $objQuery->nextVal('dtb_payment_payment_id');
            $objQuery->insert('dtb_payment', $sqlval);
        // 既存編集
        } else {
            unset($sqlval['creator_id']);
            $where = 'payment_id = ?';
            $objQuery->update('dtb_payment', $sqlval, $where, array($payment_id));
        }
    }

    /**
     * 支払方法の削除.
     * 
     * @param integer $payment_id 支払方法ID
     * @return void
     */
    public function delete($payment_id) {
        $objDb = new SC_Helper_DB_Ex();
        // ランク付きレコードの削除
        $objDb->sfDeleteRankRecord('dtb_payment', 'payment_id', $payment_id);
    }

    /**
     * 支払方法の表示順をひとつ上げる.
     * 
     * @param integer $payment_id 支払方法ID
     * @return void
     */
    public function rankUp($payment_id) {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankUp('dtb_payment', 'payment_id', $payment_id);
    }

    /**
     * 支払方法の表示順をひとつ下げる.
     * 
     * @param integer $payment_id 支払方法ID
     * @return void
     */
    public function rankDown($payment_id) {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRankDown('dtb_payment', 'payment_id', $payment_id);
    }

    /**
     * 決済モジュールを使用するかどうか.
     *
     * dtb_payment.memo03 に値が入っている場合は決済モジュールと見なす.
     *
     * @param integer $payment_id 支払い方法ID
     * @return boolean 決済モジュールを使用する支払い方法の場合 true
     */
    public static function useModule($payment_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $memo03 = $objQuery->get('memo03', 'dtb_payment', 'payment_id = ?', array($payment_id));
        return !SC_Utils_Ex::isBlank($memo03);
    }
}
