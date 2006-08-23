<?php
$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/Smarty/libs/Smarty.class.php");

class SC_View {
	
    var $_smarty;
	
    // コンストラクタ
    function SC_View() {
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
		$this->_smarty->plugins_dir=array("plugins", ROOT_DIR . "data/smarty_extends");
		$this->_smarty->register_function("sf_mb_convert_encoding","sf_mb_convert_encoding");
		$this->_smarty->register_function("sf_mktime","sf_mktime");
		$this->_smarty->register_function("sf_date","sf_date");		
		
		if(ADMIN_MODE == '1') {		
			$this->time_start = time();
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
		$array['tpl_mainnavi'] = ROOT_DIR . 'data/Smarty/templates/frontparts/mainnavi.tpl';
//		$array['tpl_search_products_php'] = ROOT_DIR . 'html/frontparts/search_products.php';			// 商品検索
//		$array['tpl_leftnavi'] = 'frontparts/leftnavi.tpl';							// 左ナビ
		$array['tpl_search_products_php'] = ROOT_DIR . 'html/frontparts/bloc/search_products.php';		// 商品検索
		$array['tpl_leftnavi'] = ROOT_DIR . 'html/frontparts/bloc/leftnavi.tpl';						// 左ナビ
		$array['tpl_category_php'] = ROOT_DIR . 'html/frontparts/category.php';		// カテゴリ
		$array['tpl_pankuzu_php'] = ROOT_DIR . 'html/frontparts/pankuzu.php';		// パンクズ
		$array['tpl_tv_products'] = 'frontparts/tv_products.tpl';					// TV紹介商品
		$array['tpl_maintitle'] = 'frontparts/maintitle.tpl';						// 小見出し
		$array['tpl_login_php'] = ROOT_DIR . 'html/frontparts/login.php';			
		$array['tpl_banner'] = 'frontparts/banner.tpl';								// バナー
		$array['tpl_root_id'] = sfGetRootId();
		$array['tpl_mypage_list'] = 'mypage/list.tpl';								//マイページの編集リスト
		$this->assignarray($array);
	}
}

class SC_AdminView extends SC_View{
    function SC_AdminView() {
    	parent::SC_View();
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
?>