<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メールテンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_Template extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/template.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subnavi = 'mail/subnavi.tpl';
        $this->tpl_subno = "template";
        $this->tpl_subtitle = 'テンプレート設定';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMagazineType = $masterData->getMasterData("mtb_magazine_type");
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

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_GET['id'])) $_GET['id'] = "";

        if ( $_GET['mode'] == "delete" && SC_Utils_Ex::sfCheckNumLength($_GET['id'])===true ){

            // メール担当の画像があれば削除しておく
            $sql = "SELECT charge_image FROM dtb_mailmaga_template WHERE template_id = ?";
            $result = $conn->getOne($sql, array($_GET["id"]));
            if (strlen($result) > 0) {
                @unlink(IMAGE_SAVE_DIR. $result);
            }

            // 登録削除
            $sql = "UPDATE dtb_mailmaga_template SET del_flg = 1 WHERE template_id = ?";
            $conn->query($sql, array($_GET['id']));
            $this->reload();
        }


        $sql = "SELECT *, (substring(create_date, 1, 19)) as disp_date FROM dtb_mailmaga_template WHERE del_flg = 0 ORDER BY create_date DESC";
        $this->list_data = $conn->getAll($sql);

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
