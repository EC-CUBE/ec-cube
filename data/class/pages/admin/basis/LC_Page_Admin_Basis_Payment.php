<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Payment extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/payment.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'payment';
        $this->tpl_subtitle = '支払方法設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
            case 'delete':
            // ランク付きレコードの削除
            $objDb->sfDeleteRankRecord("dtb_payment", "payment_id", $_POST['payment_id']);
            // 再表示
            $this->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_payment", "payment_id", $_POST['payment_id']);
            // 再表示
            $this->reload();
            break;
        case 'down':
            $objDb->sfRankDown("dtb_payment", "payment_id", $_POST['payment_id']);
            // 再表示
            $this->reload();
            break;
        }

        $this->arrDelivList = $objDb->sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
        $this->arrPaymentListFree = $this->lfGetPaymentList(2);

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // 配送業者一覧の取得
    function lfGetPaymentList($fix = 1) {
        $objQuery = new SC_Query();
        // 配送業者一覧の取得
        $col = "payment_id, payment_method, charge, rule, upper_rule, note, deliv_id, fix, charge_flg";
        $where = "del_flg = 0";
    //	$where .= " AND fix = ?";
        $table = "dtb_payment";
        $objQuery->setorder("rank DESC");
        $arrRet = $objQuery->select($col, $table, $where);
        return $arrRet;
    }
}
?>
