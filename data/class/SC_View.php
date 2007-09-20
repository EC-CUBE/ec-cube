<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$SC_VIEW_PHP_DIR = realpath(dirname(__FILE__));
require_once($SC_VIEW_PHP_DIR . "/../module/Smarty/libs/Smarty.class.php");
require_once($SC_VIEW_PHP_DIR . "/../include/php_ini.inc");

class SC_View {

    /** Smarty���󥹥��� */
    var $_smarty;
    /** SC_SiteInfo�Υ��󥹥��� */
    var $objSiteInfo;
    /** �ڡ������ϥ٥���ޡ����γ��ϻ��� */
    var $time_start;

    /**
     * ���󥹥ȥ饯��
     *
     * @param boolean $assignSiteInfo �����Ⱦ����assign���뤫�ɤ���
     */
    function SC_View($assignSiteInfo = true) {
        // �ڡ������ϤΥ٥���ޡ�����������
        $this->setStartTime();

        // Smarty�����
        $this->initSmarty();

        // �����Ⱦ����assign����
        if($assignSiteInfo) $this->assignSiteInfo();
    }

    /**
     * Smarty�ν����.
     * �������ƥ�ץ졼�ȴؿ��������Ԥ�.
     *
     * @param void
     * @return void
     */
    function initSmarty() {
        $SC_VIEW_PHP_DIR = realpath(dirname(__FILE__));

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
        $this->_smarty->plugins_dir = array("plugins", $SC_VIEW_PHP_DIR . "/../smarty_extends");
        $this->_smarty->register_function("sf_mb_convert_encoding","sf_mb_convert_encoding");
        $this->_smarty->register_function("sf_mktime","sf_mktime");
        $this->_smarty->register_function("sf_date","sf_date");
        $this->_smarty->register_function("str_replace","str_replace");
        $this->_smarty->register_function("sfPrintEbisTag","sfPrintEbisTag");
        $this->_smarty->register_function("sfPrintAffTag","sfPrintAffTag");
        $this->_smarty->register_function("sfIsHTTPS","sfIsHTTPS");
        $this->_smarty->default_modifiers = array('script_escape');
    }

    /**
     * �ڡ������ϥ٥���ޡ����γ��ϻ��֤򥻥åȤ���.
     *
     * @param void
     * @return void
     */
    function setStartTime() {
        // TODO PEAR::BenchMark�Ȥ���
        $this->time_start = time();
    }

    /**
     * �ǥե���ȥѥ�᡼����assign����.
     */
    function defaultAssign() {
        $arrDefaultParams = array(
            'URL_DIR' => URL_DIR,
            //'TPL_PKG_DIR' => $this->getTemplatePath()
        );
        $this->assignArray($arrDefaultParams);
    }

    /**
     * �����Ⱦ����assign����.
     *
     * @param void
     * @return void
     */
    function assignSiteInfo() {
        if (!defined('LOAD_SITEINFO')) {
            $this->objSiteInfo = new SC_SiteInfo();
            $arrInfo['arrSiteInfo'] = $this->objSiteInfo->data;

            // ��ƻ�ܸ�̾���Ѵ�
            global $arrPref;
            $arrInfo['arrSiteInfo']['pref'] = $arrPref[$arrInfo['arrSiteInfo']['pref']];

            // �����Ⱦ���������Ƥ�
            $this->assignArray($arrInfo);

            define('LOAD_SITEINFO', 1);
        }
    }

    /**
     * �ƥ�ץ졼�Ȥ��ͤ������Ƥ�
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function assign($key, $value) {
        $this->_smarty->assign($key, $value);
    }

    /**
     * �ƥ�ץ졼�Ȥν�����̤����
     *
     * @param string $template tpl�ե�����Υѥ�
     * @return string ���Ϸ��
     */
    function fetch($template) {
        return $this->_smarty->fetch($template);
    }

    /**
     * �ƥ�ץ졼�Ȥν�����̤�ɽ��.
     *
     * @param string $template tpl�ե�����Υѥ�
     * @return void
     */
    function display($template, $display = false) {
        // �����Х륨�顼��ɽ��
        $this->displayGlobalError($display);

        // ����ɽ��
        $this->_smarty->display($template);

        // �٥���ޡ�����̤�ɽ��
        $this->displayBenchMark();
    }

    /**
     * �����Х륨�顼��ɽ������.
     *
     * @param boolean $display
     * @return void
     */
    function displayGlobalError($display = false) {
        if (!$display) {
            global $GLOBAL_ERR;
            if(!defined('OUTPUT_ERR')) {
                print($GLOBAL_ERR);
                define('OUTPUT_ERR','ON');
            }
        }
    }

    /**
     * �ڡ������ϥ٥���ޡ����η�̤�ɽ������.
     * ADMIN_MODE��true�ΤȤ��Τ�ͭ��.
     *
     * @param void
     * @return void
     */
    function displayBenchMark() {
        if (ADMIN_MODE) {
            $time_end = time();
            $time = $time_end - $this->time_start;
            print("��������:" . $time . "��");
        }
    }

    /**
     * ���֥������ȤΥ����ѿ���assign����.
     *
     * @param object $obj LC_Page�Υ��󥹥���
     * @return void
     */
    function assignObj($obj) {
        $this->assignArray(get_object_vars($obj));
    }

    /**
     * Ϣ�������assign����.
     *
     * @param array $arrAssignVars assign����Ϣ������
     * @return void
     */
    function assignArray($arrAssignVars) {
        foreach ($arrAssignVars as $key => $val) {
            $this->assign($key, $val);
        }
    }

    /**
     * ���Ѥ��Ƥ���ƥ�ץ졼�ȥѥå������Υѥ����������
     */
    function getTemplatePath() {
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select('top_tpl', 'dtb_baseinfo');

        if (isset($arrRet[0]['top_tpl'])) {
            $selectTemplate = $arrRet[0]['top_tpl'];
            $TPL_PKG_PATH = USER_PATH . "packages/${selectTemplate}/";

            $TPL_PKG_DIR = URL_DIR . USER_DIR . "packages/${selectTemplate}/";
            $this->assign('TPL_PKG_DIR', $TPL_PKG_DIR);

            return $TPL_PKG_PATH;
        }
        return null;
    }
    /**
     * Smarty�ΥǥХå����Ϥ�ͭ���ˤ���.
     *
     * @param void
     * @return void
     */
    function debug($var = true){
        $this->_smarty->debugging = $var;
    }
}

class SC_AdminView extends SC_View{
    function SC_AdminView() {
        parent::SC_View(false);
        $this->_smarty->template_dir = TEMPLATE_ADMIN_DIR;
        $this->_smarty->compile_dir = COMPILE_ADMIN_DIR;
    }

    function display($template) {
        $tpl_mainpage = $this->_smarty->get_template_vars('tpl_mainpage');
        $template_dir = $this->getTemplatePath();

        if ($template_dir) {
            $template_dir .= 'templates/admin/';

            // tpl_mainpage��main_frame.tpl��ξ��¸�ߤ�����Τߥƥ�ץ졼�ȥѥå������ǽ���
            if (file_exists($template_dir . $tpl_mainpage)
                && file_exists($template_dir . $template)) {

                $this->_smarty->template_dir = $template_dir;
            }
        }

        $this->_smarty->display($template);
    }
}

class SC_SiteView extends SC_View{
    function SC_SiteView($cart = true) {
        parent::SC_View();

        $this->_smarty->template_dir = TEMPLATE_DIR;
        $this->_smarty->compile_dir = COMPILE_DIR;

        // PHP5�Ǥ�session�򥹥����Ȥ������˥إå���������������Ƥ���ȷٹ𤬽Ф뤿�ᡢ��˥��å����򥹥����Ȥ���褦���ѹ�
        sfDomainSessionStart();

        if($cart){
            $include_dir = realpath(dirname( __FILE__));
            require_once($include_dir . "/SC_CartSession.php");
            $objCartSess = new SC_CartSession();
            $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
        }
    }

    function display($template) {
        $tpl_mainpage = $this->_smarty->get_template_vars('tpl_mainpage');
        $template_dir = $this->getTemplatePath();

        if ($template_dir) {
            $template_dir .= 'templates/';

            if (
                (file_exists($template_dir . $tpl_mainpage) || file_exists($tpl_mainpage))
                && file_exists($template_dir . $template)) {

                $this->_smarty->template_dir = $template_dir;
            }
        }

        $this->_smarty->display($template);
    }
}

class SC_UserView extends SC_SiteView{
    function SC_UserView($template_dir, $compile_dir = COMPILE_DIR) {
        parent::SC_SiteView();
        $this->_smarty->template_dir = $template_dir;
        $this->_smarty->compile_dir = $compile_dir;
    }

    function display($template) {
        $this->_smarty->display($template);
    }
}

class SC_InstallView extends SC_View{
    function SC_InstallView($template_dir, $compile_dir = COMPILE_DIR) {
        parent::SC_View(false);
        $this->_smarty->template_dir = $template_dir;
        $this->_smarty->compile_dir = $compile_dir;
    }
}

class SC_MobileView extends SC_SiteView {
    function SC_MobileView() {
        parent::SC_SiteView();
        $this->_smarty->template_dir = MOBILE_TEMPLATE_DIR;
        $this->_smarty->compile_dir = MOBILE_COMPILE_DIR;
    }
}

?>
