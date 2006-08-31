<?php
$SC_VIEW_PHP_DIR = realpath(dirname(__FILE__));
require_once($SC_VIEW_PHP_DIR . "/../module/Smarty/libs/Smarty.class.php");

class SC_View {
	
    var $_smarty;
	var $objSiteInfo; // サイト情報
	
    // コンストラクタ
    function SC_View($siteinfo = true) {
		global $SC_VIEW_PHP_DIR;

    	$this->_smarty = new Smarty;
		$this->_smarty->left_delimiter = '<!--{';
		$this->_smarty->right_delimiter = '}-->';
		$this->_smarty->register_modifier("sfDispDBDate","sfDispDBDate");
		$this->_smarty->register_modifier("sfConvSendDateToDisp","sfConvSendDateToDisp");
		$this->_smarty->register_modifier("sfConvSendWdayToDisp","sfConvSendWdayToDisp");
		$this->_smarty->register_modifier("sfGetVal", "sfGetVal");
		$this->_smarty->register_function("sfSetErrorStyle","sfSetErrorStyle");
		$this->_smarty->register_function("sfGetErrorColor","sfGetErrorColor");
		$this->_smarty->register_function("srTrim", "sfTrim");
		$this->_smarty->register_function("sfPreTax", "sfPreTax");
		$this->_smarty->register_function("sfPrePoint", "sfPrePoint");
		$this->_smarty->register_function("sfGetChecked", "sfGetChecked");
		$this->_smarty->register_function("sfTrimURL", "sfTrimURL");
		$this->_smarty->register_function("sfMultiply", "sfMultiply");
		$this->_smarty->register_function("sfPutBR", "sfPutBR");
		$this->_smarty->register_function("sfRmDupSlash", "sfRmDupSlash");
		$this->_smarty->register_function("sfCutString", "sfCutString");
		$this->_smarty->plugins_dir=array("plugins", $SC_VIEW_PHP_DIR . "/../smarty_extends");
		$this->_smarty->register_function("sf_mb_convert_encoding","sf_mb_convert_encoding");
		$this->_smarty->register_function("sf_mktime","sf_mktime");
		$this->_smarty->register_function("sf_date","sf_date");		

		if(ADMIN_MODE == '1') {		
			$this->time_start = time();
		}

		// サイト情報を取得する
		if($siteinfo) {
			if(!defined('LOAD_SITEINFO')) {
				$this->objSiteInfo = new SC_SiteInfo();
				$arrInfo['arrSiteInfo'] = $this->objSiteInfo->data;
				
				// 都道府県名を変換
				global $arrPref;
				$arrInfo['arrSiteInfo']['pref'] = $arrPref[$arrInfo['arrSiteInfo']['pref']];
				
	 			// サイト情報を割り当てる
				foreach ($arrInfo as $key => $value){
					$this->_smarty->assign($key, $value);
				}
				
				define('LOAD_SITEINFO', 1);
			}
		}
	}
    
    // テンプレートに値を割り当てる
    function assign($val1, $val2) {
        $this->_smarty->assign($val1, $val2);
    }
    
    // テンプレートの処理結果を取得
    function fetch($template) {
        return $this->_smarty->fetch($template);
    }
    
    // テンプレートの処理結果を表示
    function display($template) {
		global $GLOBAL_ERR;
		print($GLOBAL_ERR);
		$this->_smarty->display($template);
		if(ADMIN_MODE == '1') {
			$time_end = time();
			$time = $time_end - $this->time_start;
			print("処理時間:" . $time . "秒");
		}
	}
  	
  	// オブジェクト内の変数をすべて割り当てる。
  	function assignobj($obj) {
		$data = get_object_vars($obj);
		
		foreach ($data as $key => $value){
			$this->_smarty->assign($key, $value);
		}
  	}
  	
  	// 連想配列内の変数をすべて割り当てる。
  	function assignarray($array) {
  		foreach ($array as $key => $val) {
  			$this->_smarty->assign($key, $val);
  		}
  	}

	/* サイト初期設定 */
	function initpath() {
		global $SC_VIEW_PHP_DIR;
		
		$array['tpl_mainnavi'] = $SC_VIEW_PHP_DIR . '/../Smarty/templates/frontparts/mainnavi.tpl';
		$array['tpl_root_id'] = sfGetRootId();
		$this->assignarray($array);
	}
}

class SC_AdminView extends SC_View{
    function SC_AdminView() {
    	parent::SC_View(false);
		$this->_smarty->template_dir = TEMPLATE_ADMIN_DIR;
		$this->_smarty->compile_dir = COMPILE_ADMIN_DIR;
		$this->initpath();
	}

	function printr($data){
		print_r($data);
	}
}

class SC_SiteView extends SC_View{
    function SC_SiteView() {
    	parent::SC_View();
		$this->_smarty->template_dir = TEMPLATE_DIR;
		$this->_smarty->compile_dir = COMPILE_DIR;
		$this->initpath();
		
		// PHP5ではsessionをスタートする前にヘッダー情報を送信していると警告が出るため、先にセッションをスタートするように変更
		sfDomainSessionStart();
	}
}

class SC_UserView extends SC_SiteView{
    function SC_UserView($template_dir, $compile_dir = COMPILE_DIR) {
    	parent::SC_SiteView();
		$this->_smarty->template_dir = $template_dir;
		$this->_smarty->compile_dir = $compile_dir;
	}
}

class SC_InstallView extends SC_View{
    function SC_InstallView($template_dir, $compile_dir = COMPILE_DIR) {
    	parent::SC_View(false);
		$this->_smarty->template_dir = $template_dir;
		$this->_smarty->compile_dir = $compile_dir;
	}
}

?>