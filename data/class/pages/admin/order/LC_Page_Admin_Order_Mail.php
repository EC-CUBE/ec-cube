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
require_once(CLASS_FILE_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * 受注メール管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Mail extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/mail.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'index';
        $this->tpl_subtitle = '受注管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE = $masterData->getMasterData("mtb_mail_template");
        $this->httpCacheControl('nocache');
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
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // 検索パラメータの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                $this->arrSearchHidden[$key] = $val;
            }
        }

        $this->tpl_order_id = $_POST['order_id'];

        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);

        $objMail = new SC_Helper_Mail_Ex();

        switch($_POST['mode']) {
        case 'pre_edit':
            break;
        case 'return':
            // POST値の取得
            $objFormParam->setParam($_POST);
            break;
        case 'send':
            // POST値の取得
            $objFormParam->setParam($_POST);
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $objFormParam->checkerror();
            // メールの送信
            if (count($this->arrErr) == 0) {
                // 注文受付メール
                $objMail->sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['header'], $_POST['footer']);
            }
            $this->objDisplay->redirect($this->getLocation(URL_SEARCH_ORDER));
            exit;
            break;
        case 'confirm':
            // POST値の取得
            $objFormParam->setParam($_POST);
            // 入力値の変換
            $objFormParam->convParam();
            // 入力値の引き継ぎ
            $this->arrHidden = $objFormParam->getHashArray();
            $this->arrErr = $objFormParam->checkerror();
            // メールの送信
            if (count($this->arrErr) == 0) {
                // 注文受付メール(送信なし)
                $objSendMail = $objMail->sfSendOrderMail($_POST['order_id'], $_POST['template_id'], $_POST['subject'], $_POST['header'], $_POST['footer'], false);
                // 確認ページの表示
                $this->tpl_subject = $_POST['subject'];
                $this->tpl_body = mb_convert_encoding( $objSendMail->body, CHAR_CODE, "auto" );
                $this->tpl_to = $objSendMail->tpl_to;
                $this->tpl_mainpage = 'order/mail_confirm.tpl';
                return;
            }
            break;
        case 'change':
            // POST値の取得
            $objFormParam->setValue('template_id', $_POST['template_id']);
            if(SC_Utils_Ex::sfIsInt($_POST['template_id'])) {
                $objQuery = new SC_Query();
                $where = "template_id = ?";
                $arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($_POST['template_id']));
                $objFormParam->setParam($arrRet[0]);
            }
            break;
        }

        $objQuery = new SC_Query();
        $col = "send_date, subject, template_id, send_id";
        $where = "order_id = ?";
        $objQuery->setOrder("send_date DESC");

        if(SC_Utils_Ex::sfIsInt($_POST['order_id'])) {
            $this->arrMailHistory = $objQuery->select($col, "dtb_mail_history", $where, array($_POST['order_id']));
        }

        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


    /* パラメータ情報の初期化 */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("テンプレート", "template_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールタイトル", "subject", STEXT_LEN, "KVa",  array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("ヘッダー", "header", LTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("フッター", "footer", LTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    }
}
?>
