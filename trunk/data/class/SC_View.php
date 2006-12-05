<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$SC_VIEW_PHP_DIR = realpath(dirname(__FILE__));
require_once($SC_VIEW_PHP_DIR . "/../module/Smarty/libs/Smarty.class.php");
require_once($SC_VIEW_PHP_DIR . "/../include/php_ini.inc");

class SC_View {
	
    var $_smarty;
	var $objSiteInfo; // �����Ⱦ���
	
    // ���󥹥ȥ饯��
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
		$this->_smarty->register_function("sfTrim", "sfTrim");
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
		$this->_smarty->register_function("str_replace","str_replace");
		$this->_smarty->register_function("sfPrintEbisTag","sfPrintEbisTag");
		$this->_smarty->register_function("sfPrintAffTag","sfPrintAffTag");
		
		if(ADMIN_MODE == '1') {		
			$this->time_start = time();
		}

		// �����Ⱦ�����������
		if($siteinfo) {
			if(!defined('LOAD_SITEINFO')) {
				$this->objSiteInfo = new SC_SiteInfo();
				$arrInfo['arrSiteInfo'] = $this->objSiteInfo->data;
				
				// ��ƻ�ܸ�̾���Ѵ�
				global $arrPref;
				$arrInfo['arrSiteInfo']['pref'] = $arrPref[$arrInfo['arrSiteInfo']['pref']];
				
	 			// �����Ⱦ���������Ƥ�
				foreach ($arrInfo as $key => $value){
					$this->_smarty->assign($key, $value);
				}
				
				define('LOAD_SITEINFO', 1);
			}
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
    function display($template, $no_error = false) {
		if(!$no_error) {
			global $GLOBAL_ERR;
			if(!defined('OUTPUT_ERR')) {
				print($GLOBAL_ERR);
				define('OUTPUT_ERR','ON');
			}
		}
		
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
    function SC_SiteView($cart = true) {
    	parent::SC_View();
		$this->_smarty->template_dir = TEMPLATE_DIR;
		$this->_smarty->compile_dir = COMPILE_DIR;
		$this->initpath();
		
		// PHP5�Ǥ�session�򥹥����Ȥ������˥إå���������������Ƥ���ȷٹ𤬽Ф뤿�ᡢ��˥��å����򥹥����Ȥ���褦���ѹ�
		sfDomainSessionStart();
		
		if($cart){
			$include_dir = realpath(dirname( __FILE__));
			require_once($include_dir . "/SC_CartSession.php");
			$objCartSess = new SC_CartSession();
			$objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
		}
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