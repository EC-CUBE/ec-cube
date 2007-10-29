<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(realpath(dirname( __FILE__)) . "/include.php");

/**
 * メルマガ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_MDL_SPEEDMAIL extends LC_Page {
     var $objFormParam;
     var $arrErr;
     var $objQuery;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = MODULE2_PATH . THIS_MODULE_NAME . "/config.tpl";
        $this->objFormParam = new SC_FormParam();
        $this->intiParam();
           $this->arrErr = array();
           $this->objQuery = new SC_Query();
           $this->loadData();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
           $objView = new SC_AdminView();
        $objSess = new SC_Session();

        // 認証可否の判定
        //SC_Utils_Ex::sfIsSuccess($objSess);
        $this->objFormParam->setParam($_POST);

        switch($_POST['mode']) {
            case 'regist':
                // エラーチェック
                $this->arrErr = $this->checkError();
                if(count($objPage->arrErr) <= 0) {
                    $this->registData();
                }
            break;
        }
        $this->arrForm = $this->objFormParam->getFormParamList();
        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 値の初期化
     *
     * @return void なし
     */
    function intiParam() {
        $this->objFormParam->addParam("IPアドレス1", "ip01", 3, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("IPアドレス2", "ip02", 3, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("IPアドレス3", "ip03", 3, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
           $this->objFormParam->addParam("IPアドレス4", "ip04", 3, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     * エラーチェック
     *
     * @return array $arr->arrErr
     */
    function checkError() {
        $arrErr = $this->objFormParam->checkError();
        $arrParam = $this->objFormParam->getHashArray();

        foreach($arrParam as $key => $val) {
            if(!(($val >= 0) && ($val <= 255))) {
                $arrErr[$key] = "※ 不正なIPアドレスです。<br>";
                break;
            }
        }
        return $arrErr;
    }

    // 登録データを読み込む
    function loadData(){
        // 設定されているSMTP_HOSTを取得する
        $arrRet = $this->objQuery->select("id, name", "mtb_constants", "id = ?", array('SMTP_HOST'));
        $name = ereg_replace("\"", "", $arrRet[0]['name']);
        list($arrParam['ip01'], $arrParam['ip02'], $arrParam['ip03'], $arrParam['ip04']) = split("\.", $name);
        $this->objFormParam->setParam($arrParam);
    }

    // データの更新処理
    function registData(){
        $arrParam = $this->objFormParam->getHashArray();
        $strIP = "\"" . $arrParam['ip01'] . "." .  $arrParam['ip02'] . "." . $arrParam['ip03'] . "." . $arrParam['ip04'] . "\"";
        $sqlval['name'] = $strIP;
        $this->objQuery->update("mtb_constants", $sqlval, "id = ?", array('SMTP_HOST'));
    }
}
?>
