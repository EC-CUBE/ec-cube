<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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