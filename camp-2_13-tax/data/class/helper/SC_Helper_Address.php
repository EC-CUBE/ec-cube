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
 * 会員の登録配送先を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Address
{
    /**
     * お届け先を登録
     *
     * @param array $sqlval
     * @return array()
     */
    function registAddress($sqlval)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $customer_id = $sqlval['customer_id'];
        $other_deliv_id = $sqlval['other_deliv_id'];

        // 顧客IDのチェック
        if (is_null($customer_id) || !is_numeric($customer_id) || !preg_match("/^\d+$/", $customer_id)) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, '顧客IDを正しく指定して下さい。');
        }
        // 追加
        if (strlen($other_deliv_id == 0)) {
            // 別のお届け先登録数の取得
            $deliv_count = $objQuery->count('dtb_other_deliv', 'customer_id = ?', array($customer_id));
            // 別のお届け先最大登録数に達している場合、エラー
            if ($deliv_count >= DELIV_ADDR_MAX) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, '別のお届け先最大登録数に達しています。');
            }

            // 実行
            $sqlval['other_deliv_id'] = $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $objQuery->insert('dtb_other_deliv', $sqlval);

        // 変更
        } else {
            $deliv_count = $objQuery->count('dtb_other_deliv','other_deliv_id = ?' ,array($other_deliv_id));
            if ($deliv_count != 1) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, '一致する別のお届け先がありません。');
            }

            // 実行
            $objQuery->update('dtb_other_deliv', $sqlval, 'other_deliv_id = ?', array($other_deliv_id));
        }
    }

    /**
     * お届け先を取得
     *
     * @param integer $other_deliv_id
     * @return array()
     */
    function getAddress($other_deliv_id)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $address = $objQuery->select('*', 'dtb_other_deliv', 'other_deliv_id = ?', array($other_deliv_id));
        return $address ? $address[0] : FALSE;
    }

    /**
     * お届け先の一覧を取得
     *
     * @param integer $customerId
     * @param integer $startno
     * @return array
     */
    function getList($customer_id, $startno = '')
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('other_deliv_id DESC');
        //スマートフォン用の処理
        if ($startno != '') {
            $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        }
        return $objQuery->select('*', 'dtb_other_deliv', 'customer_id = ?', array($customer_id));
    }

    /**
     * お届け先の削除
     *
     * @param integer $delivId
     * @return void
     */
    function deleteAddress($other_deliv_id)
    {
        $where      = 'other_deliv_id = ?';
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objQuery->delete('dtb_other_deliv', $where, array($other_deliv_id));
    }

    /**
     * お届け先フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function setFormParam(&$objFormParam)
    {
        SC_Helper_Customer_Ex::sfCustomerCommonParam($objFormParam);
        $objFormParam->addParam('', 'other_deliv_id');
    }

    /**
     * お届け先フォームエラーチェック
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function errorCheck(&$objFormParam)
    {
        $objErr = SC_Helper_Customer_Ex::sfCustomerCommonErrorCheck($objFormParam);
        return $objErr->arrErr;
    }
}
