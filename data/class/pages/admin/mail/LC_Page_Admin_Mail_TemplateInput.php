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
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_TemplateInput extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/template_input.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subnavi = 'mail/subnavi.tpl';
        $this->tpl_subtitle = 'テンプレート設定';
        $this->tpl_subno = "template";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMagazineType = $masterData->getMasterData("mtb_magazine_type");
        // arrMagazineTypAll ではないため, unset する.
        unset($this->arrMagazineType['3']);
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

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);


        $this->mode = "regist";

        // idが指定されているときは「編集」表示
        if (!isset($_REQUEST['template_id'])) $_REQUEST['template_id'] = "";
        if ( $_REQUEST['template_id'] ){
            $this->title = "編集";
        } else {
            $this->title = "新規登録";
        }

        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        // モードによる処理分岐
        if ( $_GET['mode'] == 'edit' && SC_Utils_Ex::sfCheckNumLength($_GET['template_id'])===true ){

            // 編集
            $sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? AND del_flg = 0";
            $result = $objQuery->getAll($sql, array($_GET['template_id']));
            $this->arrForm = $result[0];


        } elseif ( $_POST['mode'] == 'regist' ) {

            // 新規登録
            $this->arrForm = $this->lfConvData( $_POST );
            $this->arrErr = $this->lfErrorCheck($this->arrForm);

            if ( ! $this->arrErr ){
                // エラーが無いときは登録・編集
                $this->lfRegistData( $this->arrForm, $_POST['template_id']);
                // 自分を再読込して、完了画面へ遷移
                $this->objDisplay->reload(array("mode" => "complete"));
            }

        } elseif ( $_GET['mode'] == 'complete' ) {

            // 完了画面表示
            $this->tpl_mainpage = 'mail/template_complete.tpl';
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

    function lfRegistData( $arrVal, $id = null ){

        $objQuery = new SC_Query();

        $sqlval['subject'] = $arrVal['subject'];
        $sqlval['mail_method'] = $arrVal['mail_method'];
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['body'] = $arrVal['body'];
        $sqlval['update_date'] = "now()";

        if ( $id ){
            $objQuery->update("dtb_mailmaga_template", $sqlval, "template_id=".$id );
        } else {
            $sqlval['create_date'] = "now()";
            $sqlval['template_id'] = $objQuery->nextVal('dtb_mailmaga_template_template_id');
            $objQuery->insert("dtb_mailmaga_template", $sqlval);
        }
    }

    function lfConvData( $data ){

        // 文字列の変換（mb_convert_kanaの変換オプション）
        $arrFlag = array(
                         "subject" => "KV"
                         ,"body" => "KV"
                         );

        if ( is_array($data) ){
            foreach ($arrFlag as $key=>$line) {
                $data[$key] = mb_convert_kana($data[$key], $line);
            }
        }

        return $data;
    }

    // 入力エラーチェック
    function lfErrorCheck() {
        $objErr = new SC_CheckError();

        $objErr->doFunc(array("メール形式", "mail_method"), array("EXIST_CHECK", "ALNUM_CHECK"));
        $objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("本文", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }
}
?>
