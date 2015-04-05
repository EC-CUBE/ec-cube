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

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Framework\Helper\PageLayoutHelper;

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
        $this->_smarty = Application::alias('smarty');

        if (ADMIN_MODE == '1') {
            $this->time_start = microtime(true);
        }

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
        // EC-CUBE 2.13 互換用置換
        $source = preg_replace('/'.$smarty->_quote_replace($smarty->left_delimiter).'include_php /', $smarty->left_delimiter.'render ', $source);

        if (!is_null($this->objPage)) {
            // フックポイントを実行.
            /* @var $objPlugin PluginHelper */
            $objPlugin = Application::alias('eccube.helper.plugin', $this->objPage->plugin_activate_flg);
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
            /* @var $objPlugin PluginHelper */
            $objPlugin = Application::alias('eccube.helper.plugin', $this->objPage->plugin_activate_flg);
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
        /* @var $PageLayoutHelper PageLayoutHelper */
        $PageLayoutHelper = Application::alias('eccube.helper.page_layout');

        // テンプレート変数を割り当て
        $this->assign('TPL_URLPATH', $PageLayoutHelper->getUserDir($device_type_id, true));

        // ヘッダとフッタを割り当て
        $templatePath = $PageLayoutHelper->getTemplatePath($device_type_id);
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
