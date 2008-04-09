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

    /** Smartyインスタンス */
    var $_smarty;
    /** SC_SiteInfoのインスタンス */
    var $objSiteInfo;
    /** ページ出力ベンチマークの開始時間 */
    var $time_start;
    /** 使用しているテンプレートパッケージ名 */
    var $tplName;
    /** displayするtplファイル名 */
    var $template;

    /**
     * コンストラクタ
     *
     * @param boolean $assignSiteInfo サイト情報をassignするかどうか
     */
    function SC_View($assignSiteInfo = true) {
        // ページ出力のベンチマークスタート
        $this->setStartTime();

        // Smarty初期化
        $this->initSmarty();

        $this->tplName = $this->getTemplateName();
        $this->assignDefaultVars();

        // サイト情報をassignする
        if($assignSiteInfo) $this->assignSiteInfo();
    }

    /**
     * Smartyの初期化.
     * 修飾詞やテンプレート関数の定義を行う.
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
        $this->_smarty->force_compile = DEBUG_MODE;
    }

    /**
     * ページ出力ベンチマークの開始時間をセットする.
     *
     * @param void
     * @return void
     */
    function setStartTime() {
        // TODO PEAR::BenchMark使う？
        $this->time_start = time();
    }

    /**
     * デフォルトのテンプレート変数をassignする.
     */
    function assignDefaultVars() {
        $arrDefaultParams = array(
            'URL_DIR' => URL_DIR,
            // FIXME tplNameがnullの場合の処理
            'TPL_PKG_URL' => URL_DIR . USER_DIR . TPL_PKG_DIR . $this->tplName . '/',
            'tpl_site_main' => 'site_main.tpl'
        );
        $this->assignArray($arrDefaultParams);
    }

    /**
     * サイト情報をassignする.
     *
     * @param void
     * @return void
     */
    function assignSiteInfo() {
        if (!defined('LOAD_SITEINFO')) {
            $this->objSiteInfo = new SC_SiteInfo();
            $arrSiteInfo = $this->objSiteInfo->data;

            // 都道府県名を変換
            global $arrPref;
            $arrSiteInfo['pref'] = $arrPref[$arrSiteInfo['pref']];

            // サイト情報を割り当てる
            $this->assign('arrSiteInfo', $arrSiteInfo);

            define('LOAD_SITEINFO', 1);
        }
    }

    /**
     * テンプレートに値を割り当てる
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function assign($key, $value) {
        $this->_smarty->assign($key, $value);
    }

    /**
     * テンプレートの処理結果を取得
     *
     * @param string $template tplファイルのパス
     * @return string 出力結果
     */
    function fetch($template) {
        return $this->_smarty->fetch($template);
    }

    /**
     * テンプレートの処理結果を表示.
     *
     * @param string $template tplファイルのパス
     * @return void
     */
    function display($template, $display = false) {
        $this->template = $template;

        // テンプレート切り替え処理
        $this->initTemplate();

        // グローバルエラーの表示
        $this->displayGlobalError($display);

        // 画面表示
        $this->_smarty->display($this->template);

        // ベンチマーク結果の表示
        $this->displayBenchMark();
    }

    /**
     *  テンプレート切り替え処理を行う.
     *  実装は子クラスで行う.
     */
    function initTemplate() {}

    /**
     * グローバルエラーを表示する.
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
     * ページ出力ベンチマークの結果を表示する.
     * ADMIN_MODEがtrueのときのみ有効.
     *
     * @param void
     * @return void
     */
    function displayBenchMark() {
        if (ADMIN_MODE) {
            $time_end = time();
            $time = $time_end - $this->time_start;
            print("処理時間:" . $time . "秒");
        }
    }

    /**
     * オブジェクトのメンバ変数をassignする.
     *
     * @param object $obj LC_Pageのインスタンス
     * @return void
     */
    function assignObj($obj) {
        $this->assignArray(get_object_vars($obj));
    }

    /**
     * 連想配列をassignする.
     *
     * @param array $arrAssignVars assignする連想配列
     * @return void
     */
    function assignArray($arrAssignVars) {
        foreach ($arrAssignVars as $key => $val) {
            $this->assign($key, $val);
        }
    }

    /**
     * 使用しているテンプレートパッケージ名を取得する
     */
    function getTemplateName() {
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select('top_tpl', 'dtb_baseinfo');

        if (isset($arrRet[0]['top_tpl'])) {
            return $arrRet[0]['top_tpl'];
        }
        return null;
    }
    /**
     * Smartyのデバッグ出力を有効にする.
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

}

class SC_SiteView extends SC_View{
    function SC_SiteView($cart = true) {
        parent::SC_View();

        $this->_smarty->template_dir = TEMPLATE_DIR;
        $this->_smarty->compile_dir = COMPILE_DIR;

        // PHP5ではsessionをスタートする前にヘッダー情報を送信していると警告が出るため、先にセッションをスタートするように変更
        sfDomainSessionStart();

        if($cart){
            $include_dir = realpath(dirname( __FILE__));
            require_once($include_dir . "/SC_CartSession.php");
            $objCartSess = new SC_CartSession();
            $objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
        }
    }

    function initTemplate() {
        // テンプレートを使用していない場合はreturn
        if (empty($this->tplName)) return;

        // 現在使用しているテンプレートパッケージのパス
        // ***/html/user_data/tpl_packages/***/templates/
        $template_dir = TPL_PKG_PATH . $this->tplName . '/templates/';
        // FIXME ここにモバイル用処理を挟みたくない...
        if (defined('MOBILE_TEMPLATE_DIR') && $this->_smarty->template_dir == MOBILE_TEMPLATE_DIR) {
            $template_dir .= 'mobile/';
        }

        /**
         * tpl_mainpageのテンプレート適応処理
         *
         * tpl_mainpageが相対パスであれば
         * ユーザーテンプレートへの絶対パスに変更し
         * そのファイルが存在するばあいはtpl_meinpageを再度assignし上書きする
         */
        $tpl_mainpage  = $this->_smarty->get_template_vars('tpl_mainpage');

        $win = "^[a-zA-Z]{1}:";
        $other = "^\/";
        $pattern = "/($win)|($other)/";

        if (!is_null($tpl_mainpage) && !preg_match($pattern, $tpl_mainpage)) {
            $tpl_pkg_mainpage = $template_dir . $tpl_mainpage;
            if (file_exists($tpl_pkg_mainpage)) {
                $this->assign('tpl_mainpage', $tpl_pkg_mainpage);
            }
        }

        // site_frame.tplのテンプレート適用処理
        if ($this->template == SITE_FRAME) {
            if(file_exists($template_dir . SITE_FRAME)) {
                $this->template = $template_dir . SITE_FRAME;
            }
        // $this->templateがSITE_FRAMEでない場合
        // $objView->display($objPage->tpl_mainpage)など
        } else {
            if(file_exists($template_dir . $this->template)) {
                $this->template = $template_dir . $this->template;
            }
        }

        // site_main.tplのテンプレート適応処理
        $tpl_site_main = $template_dir . 'site_main.tpl';
        if (file_exists($tpl_site_main)) {
            $this->assign('tpl_site_main', $tpl_site_main);
        }
    }

    function fetch($template) {
        $this->template = $template;
        $this->initTemplate();
        return $this->_smarty->fetch($this->template);
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
    // SC_View::getTemplateName()のDBアクセス処理を行わないようにオーバーライド
    function getTemplateName() {}
}

class SC_MobileView extends SC_SiteView {
    function SC_MobileView() {
        parent::SC_SiteView();
        $this->_smarty->template_dir = MOBILE_TEMPLATE_DIR;
        $this->_smarty->compile_dir = MOBILE_COMPILE_DIR;
    }
}
?>
