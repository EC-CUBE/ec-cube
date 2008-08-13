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
/*
 * モジュールバージョン表記
 * @version ### ### 1.0
 */
require_once(MODULE_PATH . "mdl_zaikorobot/mdl_zaikorobot.inc");

//ページ管理クラス
class LC_Page {
    //コンストラクタ
    function LC_Page() {
        //メインテンプレートの指定
        $this->tpl_mainpage = MODULE_PATH . 'mdl_zaikorobot/mdl_zaikorobot.tpl';
        $this->tpl_subtitle = 'zaiko robot連携モジュール';
    }
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
$objFormParam = lfInitParam($objFormParam);
// POST値の取得
$objFormParam->setParam($_POST);

// 汎用項目を追加(必須！！)
sfAlterMemo();

switch($_POST['mode']) {
case 'edit':
    // 入力エラー判定
    $objPage->arrErr = lfCheckError();
    // エラーなしの場合
    if(count($objPage->arrErr) == 0) {
        // データ更新
        sfZaikoSetModuleDB(MDL_ZAIKOROBOT_ID, $objFormParam);
        // 決済結果受付ファイルのコピー
        copy(MODULE_PATH. "mdl_zaikorobot/hunglead_stock.php", HTML_PATH. "user_data/hunglead_stock.php");
        // 完了通知
        $objPage->tpl_onload = 'alert("登録完了しました。");window.close();';
    }
    break;
default:
    // データのロード
    lfLoadData();	
    break;
}

$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

//-----------------------------------------------------------------------

/**
 * パラメータ情報の初期化
 */ 
function lfInitParam($objFormParam) {
    $objFormParam->addParam("ID", "id", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("パスワード", "pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));	
    return $objFormParam;
}

/**
 * エラーチェック
 */
function lfCheckError() {
    global $objFormParam;
    $arrErr = $objFormParam->checkError();
    return $arrErr;
}

/**
 * 登録データの取得
 */
function lfLoadData() {
    global $objFormParam;
    //データを取得
    $arrRet = sfZaikoGetModuleDB(MDL_ZAIKOROBOT_ID);
    // 値をセット
    $objFormParam->setParam($arrRet);
}

?>