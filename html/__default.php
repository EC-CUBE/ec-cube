<?php
// {{{ requires
require_once("###require###");
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ユーザーカスタマイズ用のページクラス
 *
 * 管理画面から自動生成される
 *
 * @package Page
 */
class LC_Page_User extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_column_num = 3;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objLayout = new SC_Helper_PageLayout_Ex();

        // レイアウトデザインを取得
        $objLayout->sfGetPageLayout($this);

        // 画面の表示
        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}


// }}}
// {{{ generate page

$objPage = new LC_Page_User();
$objPage->init();
$objPage->process();
register_shutdown_function(array($objPage, "destroy"));


?>
