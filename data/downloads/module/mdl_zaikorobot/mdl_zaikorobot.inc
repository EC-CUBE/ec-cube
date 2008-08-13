<?php
/**
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
define("MDL_ZAIKOROBOT_ID", 15);
define("MODULE_NAME", "zaiko robot連動モジュール");

//================================================================================

/**
 * DBにデータ登録
 */
function sfZaikoSetModuleDB($module_id, $objFormParam) {
    $objQuery = new SC_Query();
    $arrRet = $objFormParam->getHashArray();
    foreach($arrRet as $key => $val) {
        $arrVal[$key] = $val;
    }    
    $sqlval['sub_data'] = serialize($arrVal);
    $objQuery->update("dtb_module", $sqlval, "module_id = ?", array($module_id));
}

/**
 * DBからデータ取得
 */
function sfZaikoGetModuleDB($module_id) {
    $objQuery = new SC_Query();
    $arrVal = array($module_id);
    $arrRet = array();
    $sql = "SELECT sub_data
            FROM dtb_module WHERE module_id = ?";
    $arrRet = $objQuery->getall($sql, $arrVal);
    return unserialize($arrRet[0]['sub_data']);
}

/**
 * IDチェック
 */
function sfZaikoIdCheck() {
    $ret = sfZaikoGetModuleDb(MDL_ZAIKOROBOT_ID);
    // 認証成功
    if (isset($_POST['sys_id']) && isset($_POST['sys_pass']) && 
        $ret['id'] == $_POST['sys_id'] && $ret['pass'] == $_POST['sys_pass']) {
        return true;
    // 認証エラー
    } else {
        echo "NG\n";
        echo "auth_error";
        exit;
    }
}

/**
 * 在庫調整
 */
function sfZaikoUpdate() {
    if (isset($_POST['product'])) {
        $objQuery = new SC_Query();
        $cnt = "0";
        foreach($_POST['product'] as $key => $val) {
            $table = "dtb_products_class";
            $where = "product_id = ? AND product_code = ?";
            $arrval = array();
            $arrval[] = empty($val['product_id']) ? "0" : $val['product_id'];
            $arrval[] = empty($val['product_code']) ? "0" : $val['product_code'];
            // 在庫更新
            if (sfDataExists($table, $where, $arrval) === true) {
                $sqlval['stock'] = empty($val['stock']) ? "0" : $val['stock'];
                $objQuery->update($table, $sqlval, $where, $arrval);
            // 商品エラー
            } else {
                $cnt++;
                if ($cnt == "1") $mes = "NG\n";
                $mes .= "no_product(product_id=". $arrval[0]. ",product_code=". $arrval[1]. ")\n";
            }
        }
        // 成功
        if ($cnt == "0") {
           $mes = "OK";
        }
    // クエリーエラー
    } else {
        $mes = "NG\n". "no_query";
    }
    echo $mes;
    exit;
}

?>