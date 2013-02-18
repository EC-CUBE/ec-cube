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
class SC_Helper_Delivery {

    /**
     * 配送方法の情報を取得.
     * 
     * @param integer $deliv_id 配送方法ID
     * @param boolean $has_deleted 削除された支払方法も含む場合 true; 初期値 false
     * @return array
     */
    public function get($deliv_id, $has_deleted = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 配送業者一覧の取得
        $col = '*';
        $where = 'deliv_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $table = 'dtb_deliv';
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $arrDeliv = $arrRet[0];
        if (!$arrDeliv) {
            return $arrDeliv;
        }

        // お届け時間の取得
        $arrDeliv['deliv_time'] = $this->getDelivTime($deliv_id);

        // 配送料金の取得
        $arrDeliv['deliv_fee'] = $this->getDelivFeeList($deliv_id);

        // 支払方法
        $arrDeliv['payment_ids'] = $this->getPayments($deliv_id);

        return $arrDeliv;
    }

    /**
     * 配送方法一覧の取得.
     *
     * @param integer $product_type_id 商品種別ID
     * @param boolean $has_deleted 削除された支払方法も含む場合 true; 初期値 false
     * @return array
     */
    public function getList($product_type_id = null, $has_deleted = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $where = '';
        $arrVal = array();
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        if (!is_null($product_type_id)) {
            if (!$has_deleted) {
                $where .= ' AND ';
            }
            $where .= 'product_type_id = ?';
            $arrVal[] = $product_type_id;
        }
        $table = 'dtb_deliv';
        $objQuery->setOrder('rank DESC');
        $arrRet = $objQuery->select($col, $table, $where, $arrVal);
        return $arrRet;
    }

    /**
     * 配送方法の登録.
     * 
     * @param array $sqlval
     * @return integer $deliv_id
     */
    public function save($sqlval) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        // お届け時間
        $sqlval_deliv_time = $sqlval['deliv_time'];
        unset($sqlval['deliv_time']);
        // 配送料
        if (INPUT_DELIV_FEE) {
            $sqlval_deliv_fee = $sqlval['deliv_fee'];
        }
        unset($sqlval['deliv_fee']);
        // 支払い方法
        $sqlval_payment_ids = $sqlval['payment_ids'];
        unset($sqlval['payment_ids']);

        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';

        // deliv_id が決まっていた場合
        if ($sqlval['deliv_id'] != '') {
            unset($sqlval['creator_id']);
            unset($sqlval['create_date']);
            $deliv_id = $sqlval['deliv_id'];
            $where = 'deliv_id = ?';
            $objQuery->update('dtb_deliv', $sqlval, $where, array($deliv_id));

            // お届け時間の登録
            $table = 'dtb_delivtime';
            $where = 'deliv_id = ? AND time_id = ?';
            for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
                $arrWhereVal = array($deliv_id, $cnt);
                // 既存データの有無を確認
                $curData = $objQuery->select('*', $table, $where, $arrWhereVal);

                if (isset($sqlval_deliv_time[$cnt])) {
                    $deliv_time = array();
                    $deliv_time['deliv_time'] = $sqlval_deliv_time[$cnt];

                    // 入力が空ではなく、DBに情報があれば更新
                    if (count($curData)) {
                        $objQuery->update($table, $deliv_time, $where, $arrWhereVal);
                    }
                    // DBに情報がなければ登録
                    else {
                        $deliv_time['deliv_id'] = $deliv_id;
                        $deliv_time['time_id'] = $cnt;
                        $objQuery->insert($table, $deliv_time);
                    }
                }
                // 入力が空で、DBに情報がある場合は削除
                elseif (count($curData)) {
                    $objQuery->delete($table, $where, $arrWhereVal);
                }
            }

            // 配送料の登録
            if (INPUT_DELIV_FEE) {
                foreach ($sqlval_deliv_fee as $deliv_fee) {
                    $objQuery->update('dtb_delivfee', array('fee' => $deliv_fee['fee']), 'deliv_id = ? AND fee_id = ?', array($deliv_id, $deliv_fee['fee_id']));
                }
            }
        } else {
            // 登録する配送業者IDの取得
            $deliv_id = $objQuery->nextVal('dtb_deliv_deliv_id');
            $sqlval['deliv_id'] = $deliv_id;
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_deliv') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            // INSERTの実行
            $objQuery->insert('dtb_deliv', $sqlval);

            // お届け時間の設定
            foreach ($sqlval_deliv_time as $cnt => $deliv_time) {
                $deliv_time['deliv_id'] = $deliv_id;
                $deliv_time['time_id'] = $cnt;
                // INSERTの実行
                $objQuery->insert('dtb_delivtime', $deliv_time);
            }

            if (INPUT_DELIV_FEE) {
                // 配送料金の設定
                foreach ($sqlval_deliv_fee as $deliv_fee) {
                    $deliv_fee['deliv_id'] = $deliv_id;
                    // INSERTの実行
                    $objQuery->insert('dtb_delivfee', $deliv_fee);
                }
            }
        }

        // 支払い方法
        $objQuery->delete('dtb_payment_options', 'deliv_id = ?', array($deliv_id));
        $i = 1;
        foreach ($sqlval_payment_ids as $payment_id) {
            $sqlval_payment_id = array();
            $sqlval_payment_id['deliv_id'] = $deliv_id;
            $sqlval_payment_id['payment_id'] = $payment_id;
            $sqlval_payment_id['rank'] = $i;
            $objQuery->insert('dtb_payment_options', $sqlval_payment_id);
            $i++;
        }

        $objQuery->commit();

        return $deliv_id;
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
     * 同じ内容の配送方法が存在するか確認.
     * 
     * @param array $arrDeliv
     * @return boolean
     */
    public function checkExist($arrDeliv) {
        $objDb = new SC_Helper_DB_Ex();
        if ($arrDeliv['deliv_id'] == '') {
            $ret = $objDb->sfIsRecord('dtb_deliv', 'service_name', array($arrDeliv['service_name']));
        } else {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $ret = (($objQuery->count('dtb_deliv', 'deliv_id != ? AND service_name = ? ', array($arrDeliv['deliv_id'], $arrDeliv['service_name'])) > 0) ? true : false);
        }
        return $ret;
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

    /**
     * 配送業者IDからお届け時間の配列を取得する.
     *
     * @param integer $deliv_id 配送業者ID
     * @return array お届け時間の配列
     */
    public static function getDelivTime($deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('time_id');
        $results = $objQuery->select('time_id, deliv_time', 'dtb_delivtime', 'deliv_id = ?', array($deliv_id));
        $arrDelivTime = array();
        foreach ($results as $val) {
            $arrDelivTime[$val['time_id']] = $val['deliv_time'];
        }
        return $arrDelivTime;
    }

    /**
     * 配送業者ID から, 有効な支払方法IDを取得する.
     *
     * @param integer $deliv_id 配送業者ID
     * @return array 有効な支払方法IDの配列
     */
    public static function getPayments($deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('rank');
        return $objQuery->getCol('payment_id', 'dtb_payment_options', 'deliv_id = ?', array($deliv_id), MDB2_FETCHMODE_ORDERED);
    }

    /**
     * 都道府県から配送料金を取得する.
     *
     * @param integer|array $pref_id 都道府県ID 又は都道府県IDの配列
     * @param integer $deliv_id 配送業者ID
     * @return string 指定の都道府県, 配送業者の配送料金
     */
    public static function getDelivFee($pref_id, $deliv_id = 0) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        if (!is_array($pref_id)) {
            $pref_id = array($pref_id);
        }
        $sql = <<< __EOS__
            SELECT T1.fee AS fee
            FROM dtb_delivfee T1
                JOIN dtb_deliv T2
                    ON T1.deliv_id = T2.deliv_id
            WHERE T1.pref = ?
                AND T1.deliv_id = ?
                AND T2.del_flg = 0
__EOS__;
        $result = 0;
        foreach ($pref_id as $pref) {
            $result += $objQuery->getOne($sql, array($pref, $deliv_id));
        }
        return $result;
    }

    /**
     * 配送業者ID から, 配送料金の一覧を取得する.
     *
     * @param integer $deliv_id 配送業者ID
     * @return array 配送料金の配列
     */
    public static function getDelivFeeList($deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('pref');
        $col = 'fee_id, fee, pref';
        $where = 'deliv_id = ?';
        $table = 'dtb_delivfee';
        return $objQuery->select($col, $table, $where, array($deliv_id));
    }
}
