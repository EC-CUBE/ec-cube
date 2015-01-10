<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Common;

use Eccube\Common\Helper\PluginHelper;
use Eccube\Common\Helper\PageLayoutHelper;

class View
{
    public $_smarty;

    public $objPage;

    // コンストラクタ
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->_smarty = new \Smarty;
        $this->_smarty->left_delimiter = '<!--{';
        $this->_smarty->right_delimiter = '}-->';
        $this->_smarty->plugins_dir = array(realpath(dirname(__FILE__) . '/../../smarty_extends'), 'plugins');
        $this->_smarty->register_modifier('sfDispDBDate', array('\\Eccube\\Common\\Util\\Utils', 'sfDispDBDate'));
        $this->_smarty->register_modifier('sfGetErrorColor', array('\\Eccube\\Common\\Util\\Utils', 'sfGetErrorColor'));
        $this->_smarty->register_modifier('sfTrim', array('\\Eccube\\Common\\Util\\Utils', 'sfTrim'));
        $this->_smarty->register_modifier('sfCalcIncTax', array('\\Eccube\\Common\\Helper\\DbHelper', 'calcIncTax'));
        $this->_smarty->register_modifier('sfPrePoint', array('\\Eccube\\Common\\Util\\Utils', 'sfPrePoint'));
        $this->_smarty->register_modifier('sfGetChecked', array('\\Eccube\\Common\\Util\\Utils', 'sfGetChecked'));
        $this->_smarty->register_modifier('sfTrimURL', array('\\Eccube\\Common\\Util\\Utils', 'sfTrimURL'));
        $this->_smarty->register_modifier('sfMultiply', array('\\Eccube\\Common\\Util\\Utils', 'sfMultiply'));
        $this->_smarty->register_modifier('sfRmDupSlash', array('\\Eccube\\Common\\Util\\Utils', 'sfRmDupSlash'));
        $this->_smarty->register_modifier('sfCutString', array('\\Eccube\\Common\\Util\\Utils', 'sfCutString'));
        $this->_smarty->register_modifier('sfMbConvertEncoding', array('\\Eccube\\Common\\Util\\Utils', 'sfMbConvertEncoding'));
        $this->_smarty->register_modifier('sfGetEnabled', array('\\Eccube\\Common\\Util\\Utils', 'sfGetEnabled'));
        $this->_smarty->register_modifier('sfNoImageMainList', array('\\Eccube\\Common\\Util\\Utils', 'sfNoImageMainList'));
        // XXX register_function で登録すると if で使用できないのではないか？
        $this->_smarty->register_function('sfIsHTTPS', array('\\Eccube\\Common\\Util\\Utils', 'sfIsHTTPS'));
        $this->_smarty->register_function('sfSetErrorStyle', array('\\Eccube\\Common\\Util\\Utils', 'sfSetErrorStyle'));
        $this->_smarty->register_function('printXMLDeclaration', array('\\Eccube\\Common\\Util\\GcUtils', 'printXMLDeclaration'));
        $this->_smarty->default_modifiers = array('script_escape');

        if (ADMIN_MODE == '1') {
            $this->time_start = microtime(true);
        }

        $this->_smarty->force_compile = SMARTY_FORCE_COMPILE_MODE === true;
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
     * Display用にレスポンスを返す
     * 
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
        $this->_smarty->register_prefilter(array(&$this, 'prefilter_transform'));
        $this->_smarty->register_outputfilter(array(&$this, 'outputfilter_transform'));
    }

    /**
     * prefilter用のフィルタ関数。プラグイン用のフックポイント処理を実行
     * @param  string          $source ソース
     * @param  Smarty_Compiler $smarty Smartyのコンパイラクラス
     * @return string          $source ソース
     */
    public function prefilter_transform($source, &$smarty)
    {
        if (!is_null($this->objPage)) {
            // フックポイントを実行.
            $objPlugin = PluginHelper::getSingletonInstance($this->objPage->plugin_activate_flg);
            if (is_object($objPlugin)) {
                $objPlugin->doAction('prefilterTransform', array(&$source, $this->objPage, $smarty->_current_file));
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
    public function outputfilter_transform($source, &$smarty)
    {
        if (!is_null($this->objPage)) {
            // フックポイントを実行.
            $objPlugin = PluginHelper::getSingletonInstance($this->objPage->plugin_activate_flg);
            if (is_object($objPlugin)) {
                $objPlugin->doAction('outputfilterTransform', array(&$source, $this->objPage, $smarty->_current_file));
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
        $this->assign('TPL_URLPATH', PageLayoutHelper::getUserDir($device_type_id, true));

        // ヘッダとフッタを割り当て
        $templatePath = PageLayoutHelper::getTemplatePath($device_type_id);
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
}
