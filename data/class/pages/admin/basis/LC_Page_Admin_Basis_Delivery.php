<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 配送業者設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Delivery extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/delivery.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
        $this->tpl_subtitle = '配送業者設定';
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
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'delete':
            // ランク付きレコードの削除
            $objDb->sfDeleteRankRecord("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            // 再表示
            SC_Utils_Ex::sfReload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            // 再表示
            SC_Utils_Ex::sfReload();
            break;
        case 'down':
            $objDb->sfRankDown("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            // 再表示
            SC_Utils_Ex::sfReload();
            break;
        default:
            break;
        }

        // 配送業者一覧の取得
        $col = "deliv_id, name, service_name";
        $where = "del_flg = 0";
        $table = "dtb_deliv";
        $objQuery->setorder("rank DESC");
        $this->arrDelivList = $objQuery->select($col, $table, $where);

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
}
?>
