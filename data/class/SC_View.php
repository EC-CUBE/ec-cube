<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

class SC_View
{
    public $_smarty;

    public $objPage;

    public function __construct() {
       $this->init();
    }

    public function init()
    {
        $this->_smarty = new SmartyBC();
        $this->_smarty->left_delimiter = '<!--{';
        $this->_smarty->right_delimiter = '}-->';
        $this->_smarty->allow_php_tag = true;
        $this->_smarty->register_modifier('sfDispDBDate', array('SC_Utils_Ex', 'sfDispDBDate'));
        $this->_smarty->register_modifier('sfGetErrorColor', array('SC_Utils_Ex', 'sfGetErrorColor'));
        $this->_smarty->register_modifier('sfTrim', array('SC_Utils_Ex', 'sfTrim'));
        $this->_smarty->register_modifier('sfCalcIncTax', array('SC_Helper_DB_Ex', 'sfCalcIncTax'));
        $this->_smarty->register_modifier('sfPrePoint', array('SC_Utils_Ex', 'sfPrePoint'));
        $this->_smarty->register_modifier('sfGetChecked', array('SC_Utils_Ex', 'sfGetChecked'));
        $this->_smarty->register_modifier('sfTrimURL', array('SC_Utils_Ex', 'sfTrimURL'));
        $this->_smarty->register_modifier('sfMultiply', array('SC_Utils_Ex', 'sfMultiply'));
        $this->_smarty->register_modifier('sfRmDupSlash', array('SC_Utils_Ex', 'sfRmDupSlash'));
        $this->_smarty->register_modifier('sfCutString', array('SC_Utils_Ex', 'sfCutString'));
        $this->_smarty->register_modifier('sfMbConvertEncoding', array('SC_Utils_Ex', 'sfMbConvertEncoding'));
        $this->_smarty->register_modifier('sfGetEnabled', array('SC_Utils_Ex', 'sfGetEnabled'));
        $this->_smarty->register_modifier('sfNoImageMainList', array('SC_Utils_Ex', 'sfNoImageMainList'));
        // XXX register_function で登録すると if で使用できないのではないか？
        $this->_smarty->register_function('sfIsHTTPS', array('SC_Utils_Ex', 'sfIsHTTPS'));
        $this->_smarty->register_function('sfSetErrorStyle', array('SC_Utils_Ex', 'sfSetErrorStyle'));
        $this->_smarty->register_function('printXMLDeclaration', array('GC_Utils_Ex', 'printXMLDeclaration'));
        $this->_smarty->default_modifiers = array('script_escape');
        $this->_smarty->security_policy = null;

        $this->_smarty->register_function('include_php_ex', array($this, 'include_php_ex'));
        //$this->_smarty->plugins_dir=array('plugins', realpath(dirname(__FILE__)) . '/../smarty_extends');
        $this->_smarty->plugins_dir = array(
            'plugins',
            DATA_REALDIR . '../vendor/smarty/smarty/libs/plugins/', // defaultのplugnのpath
            DATA_REALDIR . 'smarty_extends/',
        );


        if (ADMIN_MODE == '1') {
            $this->time_start = microtime(true);
        }

        //$this->_smarty->force_compile = SMARTY_FORCE_COMPILE_MODE === true;
        $this->_smarty->force_compile = true;
        // 各filterをセットします.
        $this->registFilter();
    }

    // テンプレートに値を割り当てる

    /**
     * @param string $val1
     */
    public function assign($val1, $val2)
    {
        $this->_smarty->assign($val1, $val2);
    }

    // テンプレートの処理結果を取得
    public function fetch($template)
    {
        return $this->_smarty->fetch($template);
    }

    /**
     * SC_Display用にレスポンスを返す
     * @global string $GLOBAL_ERR
     * @param  array   $template
     * @param  boolean $no_error
     * @return string
     */
    public function getResponse($template, $no_error = false)
    {
        if (!$no_error) {
            global $GLOBAL_ERR;
            if (!defined('OUTPUT_ERR')) {
                // GLOBAL_ERR を割り当て
                $this->assign('GLOBAL_ERR', $GLOBAL_ERR);
                define('OUTPUT_ERR', 'ON');
            }
        }
        $res =  $this->_smarty->fetch($template);
        if (ADMIN_MODE == '1') {
            $time_end = microtime(true);
            $time = $time_end - $this->time_start;
            $res .= '処理時間: ' . sprintf('%.3f', $time) . '秒';
        }

        return $res;
    }

    /**
     * Pageオブジェクトをセットします.
     * @param  LC_Page_Ex $objPage
     * @return void
     */
    public function setPage($objPage)
    {
       $this->objPage = $objPage;
    }

    /**
     * Smartyのfilterをセットします.
     * @return void
     */
    public function registFilter()
    {
        $this->_smarty->registerFilter('pre', array($this, 'prefilter_transform'));
        $this->_smarty->registerFilter("output", array($this, 'outputfilter_transform'));

    }

    /**
     * prefilter用のフィルタ関数。プラグイン用のフックポイント処理を実行
     * @param  string          $source ソース
     * @param  Smarty_Compiler $smarty Smartyのコンパイラクラス
     * @return string          $source ソース
     */
    public function prefilter_transform($source, $smarty)
    {
        if (!is_null($this->objPage)) {
            // フックポイントを実行.
            $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->objPage->plugin_activate_flg);
            if (is_object($objPlugin)) {
                $objPlugin->doAction('prefilterTransform', array($source, $this->objPage, $smarty->_current_file));
            }
        }

        return $source;
    }

    /**
     * outputfilter用のフィルタ関数。プラグイン用のフックポイント処理を実行
     * @param  string          $source ソース
     * @param  Smarty_Compiler $smarty Smartyのコンパイラクラス
     * @return string          $source ソース
     */
    public function outputfilter_transform($source, $smarty)
    {
        if (!is_null($this->objPage)) {
            // フックポイントを実行.
            $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->objPage->plugin_activate_flg);
            if (is_object($objPlugin)) {
                $objPlugin->doAction('outputfilterTransform', array($source, $this->objPage, $smarty->_current_file));
            }
        }

        return $source;
    }

    // テンプレートの処理結果を表示
    public function display($template, $no_error = false)
    {
        if (!$no_error) {
            global $GLOBAL_ERR;
            if (!defined('OUTPUT_ERR')) {
                // GLOBAL_ERR を割り当て
                $this->assign('GLOBAL_ERR', $GLOBAL_ERR);
                define('OUTPUT_ERR', 'ON');
            }
        }

        $this->_smarty->display($template);
        if (ADMIN_MODE == '1') {
            $time_end = microtime(true);
            $time = $time_end - $this->time_start;
            echo '処理時間: ' . sprintf('%.3f', $time) . '秒';
        }
    }

    // オブジェクト内の変数を全て割り当てる。
    public function assignobj($obj)
    {
        $data = get_object_vars($obj);

        foreach ($data as $key => $value) {
            $this->_smarty->assign($key, $value);
        }
    }

    // 連想配列内の変数を全て割り当てる。
    public function assignarray($array)
    {
        foreach ($array as $key => $val) {
            $this->_smarty->assign($key, $val);
        }
    }

    /**
     * テンプレートパスをアサインする.
     *
     * @param integer $device_type_id 端末種別ID
     */
    public function assignTemplatePath($device_type_id)
    {
        // テンプレート変数を割り当て
        $this->assign('TPL_URLPATH', SC_Helper_PageLayout_Ex::getUserDir($device_type_id, true));

        // ヘッダとフッタを割り当て
        $templatePath = SC_Helper_PageLayout_Ex::getTemplatePath($device_type_id);
        $header_tpl = $templatePath . 'header.tpl';
        $footer_tpl = $templatePath . 'footer.tpl';

        $this->assign('header_tpl', $header_tpl);
        $this->assign('footer_tpl', $footer_tpl);
    }

    // デバッグ
    public function debug($var = true)
    {
        $this->_smarty->debugging = $var;
    }

    public function include_php_ex($params, $smarty) {

        // todo 要pathチェック
        if ($params['items']['php_path'] == false) {
            $smarty->trigger_error("{include_php} file '". $params['file'] ."' is not readable", E_USER_NOTICE);
        }
        include_once $params['items']['php_path'];
    }
}
