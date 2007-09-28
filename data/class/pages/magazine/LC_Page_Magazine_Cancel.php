<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メルマガ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Magazine extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'magazine/cancel.tpl';
        $this->tpl_title .= 'メルマガ解除完了';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {

        $objQuery = new SC_Query();

        // secret_keyの取得
        $key = $_GET['id'];

        if (empty($key) or !$this->lfExistKey($key, $objQuery))  {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", false, "", true);
        } else {
            $this->lfChangeData($key, $objQuery);
        }

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);

        $objView = new SC_MobileView();
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

    // メルマガの解除を完了させる
    function lfChangeData($key, &$objQuery) {
        $arrUpdate['mail_flag'] = 3;
        $arrUpdate['secret_key'] = NULL;
        $result = $objQuery->update("dtb_customer_mail", $arrUpdate, "secret_key = " . SC_Utils_Ex::sfQuoteSmart($key));
    }

    // キーが存在するかどうか
    function lfExistKey($key, &$objQuery) {
        $sql = "SELECT count(*) FROM dtb_customer_mail WHERE secret_key = ?";
        $result = $objQuery->getOne($sql, array($key));

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }
}
?>
