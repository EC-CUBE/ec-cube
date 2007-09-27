<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 退会手続き のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Refusal extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/refusal.tpl';
        $this->tpl_title = "MYページ/退会手続き(入力ページ)";
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'refusal';
        $this->tpl_column_num = 1;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        $objQuery = new SC_Query();
        $objSiteSess = new SC_SiteSession();

        //ログイン判定
        if (!$objCustomer->isLoginSuccess()){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }


        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, "mypage/index.php");

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch ($_POST['mode']){
        case 'confirm':

            $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/refusal_confirm.tpl';
            $this->tpl_title = "MYページ/退会手続き(確認ページ)";

            // 確認ページを経由したことを登録
            $objSiteSess->setRegistFlag();
            // hiddenにuniqidを埋め込む
            $this->tpl_uniqid = $objSiteSess->getUniqId();

            break;

        case 'complete':
            // 正しい遷移かどうかをチェック
            $this->lfIsValidMovement($objSiteSess);

            //会員削除
            $objQuery->exec("UPDATE dtb_customer SET del_flg=1, update_date=now() WHERE customer_id=?", array($objCustomer->getValue('customer_id')));

            $objCustomer->EndSession();
            //完了ページへ
            $this->sendRedirect($this->getLocation("./refusal_complete.php"));
            exit;
        }

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

    // 正しい遷移かどうかをチェック
    function lfIsValidMovement(&$objSiteSess) {
        // 確認ページからの遷移かどうかをチェック
        SC_Utils_Ex::sfIsPrePage($objSiteSess);

        // uniqid がPOSTされているかをチェック
        $uniqid = $objSiteSess->getUniqId();
        if ( !empty($_POST['uniqid']) && ($_POST['uniqid'] === $uniqid) ) {
            return;
        } else {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }
    }
}
?>
