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
use Eccube\Framework\Helper\CustomerHelper;

/**
 * 会員の登録配送先を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class AddressHelper
{
    /**
     * お届け先を登録
     *
     * @param  array   $sqlval
     * @return array()
     */
    public function registAddress($sqlval)
    {
        if ($this->delivErrorCheck($sqlval)) {
            return false;
        }

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $customer_id = $sqlval['customer_id'];
        $other_deliv_id = $sqlval['other_deliv_id'];

        // 追加
        if (strlen($other_deliv_id == 0)) {
            // 別のお届け先最大登録数に達している場合、エラー
            $from   = 'dtb_other_deliv';
            $where  = 'customer_id = ?';
            $arrVal = array($customer_id);
            $deliv_count = $objQuery->count($from, $where, $arrVal);
            if ($deliv_count >= DELIV_ADDR_MAX) {
                return false;
            }

            // 別のお届け先を追加
            $sqlval['other_deliv_id'] = $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $ret = $objQuery->insert($from, $sqlval);

        // 変更
        } else {
            $from   = 'dtb_other_deliv';
            $where  = 'customer_id = ? AND other_deliv_id = ?';
            $arrVal = array($customer_id, $other_deliv_id);
            $deliv_count = $objQuery->count($from, $where, $arrVal);
            if ($deliv_count != 1) {
                return false;
            }

            // 別のお届け先を変更
            $ret = $objQuery->update($from, $sqlval, $where, $arrVal);
        }
        
        return $ret;
    }

    /**
     * お届け先を取得
     *
     * @param integer $other_deliv_id
     * @return array()
     */
    public function getAddress($other_deliv_id, $customer_id = '')
    {
        if ($this->delivErrorCheck(array('customer_id' => $customer_id, 'other_deliv_id' => $other_deliv_id))) {
            return false;
        }

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');

        $col    = '*';
        $from   = 'dtb_other_deliv';
        $where  = 'customer_id = ? AND other_deliv_id = ?';
        $arrVal = array($customer_id, $other_deliv_id);
        $address = $objQuery->getRow($col, $from, $where, $arrVal);

        return $address;
    }

    /**
     * お届け先の一覧を取得
     *
     * @param  integer $customer_id
     * @param  integer $startno
     * @return array
     */
    public function getList($customer_id, $startno = '')
    {
        if ($this->delivErrorCheck(array('customer_id' => $customer_id))) {
            return false;
        }

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $objQuery->setOrder('other_deliv_id DESC');
        //スマートフォン用の処理
        if ($startno != '') {
            $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        }

        $col    = '*';
        $from   = 'dtb_other_deliv';
        $where  = 'customer_id = ?';
        $arrVal = array($customer_id);
        return $objQuery->select($col, $from, $where, $arrVal);
    }

    /**
     * お届け先の削除
     *
     * @return void
     */
    public function deleteAddress($other_deliv_id, $customer_id = '')
    {
        if ($this->delivErrorCheck(array('customer_id' => $customer_id, 'other_deliv_id' => $other_deliv_id))) {
            return false;
        }
        
        $objQuery   = Application::alias('eccube.query');

        $from   = 'dtb_other_deliv';
        $where  = 'customer_id = ? AND other_deliv_id = ?';
        $arrVal = array($customer_id, $other_deliv_id);
        return $objQuery->delete($from, $where, $arrVal);
    }

    /**
     * お届け先フォーム初期化
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function setFormParam(&$objFormParam)
    {
        Application::alias('eccube.helper.customer')->sfCustomerCommonParam($objFormParam);
        $objFormParam->addParam('', 'other_deliv_id');
    }

    /**
     * お届け先フォームエラーチェック
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function errorCheck(&$objFormParam)
    {
        $objErr = Application::alias('eccube.helper.customer')->sfCustomerCommonErrorCheck($objFormParam);

        return $objErr->arrErr;
    }
    
    /**
     * お届け先エラーチェック
     * 
     * @param array $arrParam
     * @return boolean / false
     */
    public function delivErrorCheck($arrParam)
    {
        $error_flg = false;
        
        if (is_null($arrParam['customer_id']) || !is_numeric($arrParam['customer_id']) || !preg_match("/^\d+$/", $arrParam['customer_id'])) {
            $error_flg = true;
        }

        if (strlen($arrParam['other_deliv_id']) > 0 && (!is_numeric($arrParam['other_deliv_id']) || !preg_match("/^\d+$/", $arrParam['other_deliv_id']))) {
            $error_flg = true;
        }
        
        return $error_flg;
    }
}
