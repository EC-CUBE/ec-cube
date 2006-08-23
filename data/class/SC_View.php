<?php
$current_dir = realpath(dirname(__FILE__));
require_once($current_dir . "/../module/Smarty/libs/Smarty.class.php");

class SC_View {
	
    var $_smarty;
	
    // ���󥹥ȥ饯��
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
    
    // �ƥ�ץ졼�Ȥ��ͤ������Ƥ�
    function assign($val1, $val2) {
        $this->_smarty->assign($val1, $val2);
    }
    
    // �ƥ�ץ졼�Ȥν�����̤����
    function fetch($template) {
        return $this->_smarty->fetch($template);
    }
    
    // �ƥ�ץ졼�Ȥν�����̤�ɽ��
    function display($template) {
		$this->_smarty->display($template);
		if(ADMIN_MODE == '1') {
			$time_end = time();
			$time = $time_end - $this->time_start;
			print("��������:" . $time . "��");
		}
	}
  	
  	// ���֥�����������ѿ��򤹤٤Ƴ�����Ƥ롣
  	function assignobj($obj) {
		$data = get_object_vars($obj);
		foreach ($data as $key => $value){
			$this->_smarty->assign($key, $value);
		}
  	}
  	
  	// Ϣ����������ѿ��򤹤٤Ƴ�����Ƥ롣
  	function assignarray($array) {
  		foreach ($array as $key => $val) {
  			$this->_smarty->assign($key, $val);
  		}
  	}

	/* �����Ƚ������ */
	function initpath() {
		$array['tpl_mainnavi'] = ROOT_DIR . 'data/Smarty/templates/frontparts/mainnavi.tpl';
//		$array['tpl_search_products_php'] = ROOT_DIR . 'html/frontparts/search_products.php';			// ���ʸ���
//		$array['tpl_leftnavi'] = 'frontparts/leftnavi.tpl';							// ���ʥ�
		$array['tpl_search_products_php'] = ROOT_DIR . 'html/frontparts/bloc/search_products.php';		// ���ʸ���
		$array['tpl_leftnavi'] = ROOT_DIR . 'html/frontparts/bloc/leftnavi.tpl';						// ���ʥ�
		$array['tpl_category_php'] = ROOT_DIR . 'html/frontparts/category.php';		// ���ƥ���
		$array['tpl_pankuzu_php'] = ROOT_DIR . 'html/frontparts/pankuzu.php';		// �ѥ󥯥�
		$array['tpl_tv_products'] = 'frontparts/tv_products.tpl';					// TV�Ҳ���
		$array['tpl_maintitle'] = 'frontparts/maintitle.tpl';						// �����Ф�
		$array['tpl_login_php'] = ROOT_DIR . 'html/frontparts/login.php';			
		$array['tpl_banner'] = 'frontparts/banner.tpl';								// �Хʡ�
		$array['tpl_root_id'] = sfGetRootId();
		$array['tpl_mypage_list'] = 'mypage/list.tpl';								//�ޥ��ڡ������Խ��ꥹ��
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
		
		// PHP5�Ǥ�session�򥹥����Ȥ������˥إå���������������Ƥ���ȷٹ𤬽Ф뤿�ᡢ��˥��å����򥹥����Ȥ���褦���ѹ�
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