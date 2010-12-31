<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * メール設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Mail extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/mail.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'mail';
        $this->tpl_subtitle = 'メール設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objQuery = new SC_Query();
        $objSess = new SC_Session();
        $masterData = new SC_DB_MasterData_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);


        $this->arrMailTEMPLATE = $masterData->getMasterData("mtb_mail_template");

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        if ( $_POST['mode'] == 'id_set'){
            // テンプレートプルダウン変更時

            if ( SC_Utils_Ex::sfCheckNumLength( $_POST['template_id']) ){
                $sql = "SELECT * FROM dtb_mailtemplate WHERE template_id = ?";
                $result = $objQuery->getAll($sql, array($_POST['template_id']) );
                if ( $result ){
                    $this->arrForm = $result[0];
                } else {
                    $this->arrForm['template_id'] = $_POST['template_id'];
                }
            }

        } elseif ( $_POST['mode'] == 'regist' && SC_Utils_Ex::sfCheckNumLength( $_POST['template_id']) ){

            // POSTデータの引き継ぎ
            $this->arrForm = $this->lfConvertParam($_POST);
            $this->arrErr = $this->fnErrorCheck($this->arrForm);

            if ( $this->arrErr ){
                // エラーメッセージ
                $this->tpl_msg = "エラーが発生しました";

            } else {
                // 正常
                $this->lfRegist($objQuery, $this->arrForm);

                // 完了メッセージ
                $this->tpl_onload = "window.alert('メール設定が完了しました。テンプレートを選択して内容をご確認ください。');";
                unset($this->arrForm);
            }

        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfRegist(&$objQuery, $data ){

        $data['creator_id'] = $_SESSION['member_id'];

        $sql = "SELECT * FROM dtb_mailtemplate WHERE template_id = ?";
        $result = $objQuery->getAll($sql, array($_POST['template_id']) );
        if ( $result ){
            $sql_where = "template_id = ". addslashes($_POST['template_id']);
            $objQuery->query("UPDATE dtb_mailtemplate SET template_id = ?, subject = ?,header = ?, footer = ?,creator_id = ?, update_date = now() WHERE ".$sql_where, $data);
        }else{
            $objQuery->query("INSERT INTO dtb_mailtemplate (template_id,subject,header,footer,creator_id,update_date,create_date) values ( ?,?,?,?,?,now(),now() )", $data);
        }

    }


    function lfConvertParam($array) {

        $new_array["template_id"] = $array["template_id"];
        $new_array["subject"] = mb_convert_kana($array["subject"] ,"KV");
        $new_array["header"] = mb_convert_kana($array["header"] ,"KV");
        $new_array["footer"] = mb_convert_kana($array["footer"] ,"KV");

        return $new_array;
    }

    /* 入力エラーのチェック */
    function fnErrorCheck($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("テンプレート",'template_id'), array("EXIST_CHECK"));
        $objErr->doFunc(array("メールタイトル",'subject',MTEXT_LEN,"BIG"), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ヘッダー",'header',LTEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("フッター",'footer',LTEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }
}
?>
