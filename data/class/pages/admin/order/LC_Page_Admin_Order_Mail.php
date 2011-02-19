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
        SC_Utils_Ex::sfIsSuccess(new SC_Session());
        // 検索パラメータの引き継ぎ
        $this->arrSearchHidden = SC_Utils_Ex::sfFilterKey($_POST,"^search_");
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        // POST値の取得
        $objFormParam->setParam($_POST);
        $this->tpl_order_id = $objFormParam->getValue('order_id');

        switch($this->getMode()) {
            case 'pre_edit':
                break;
            case 'return':
                break;
            case 'send':
                // 入力値の変換 TODO ここ気持ち悪いんだ returnの時にやってなかったからもって上でやっていいものなのかどうか。
                $objFormParam->convParam();
                $sendStatus = $this->doSend($objFormParam,
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'));
                if($sendStatus){
                    SC_Response_Ex::sendRedirect(ADMIN_ORDER_URLPATH);
                    exit;
                }
            case 'confirm':
                // 入力値の変換 ここ気持ち悪いんだ
                $objFormParam->convParam();
                $status = $this->confirm($objFormParam,
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'));
                if($status){
                    return ;
                }
                break;
            case 'change':
                $this->changeData($objFormParam);
                break;
        }

        if(SC_Utils_Ex::sfIsInt($objFormParam->getValue('order_id'))) {
            $this->arrMailHistory = $this->getMailHistory($objFormParam->getValue('order_id'));
        }

        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * 指定された注文番号のメール履歴を取得する。
     * @var int order_id
     */
    function getMailHistory($order_id){
        $objQuery =& SC_Query::getSingletonInstance();
        $col = "send_date, subject, template_id, send_id";
        $where = "order_id = ?";
        $objQuery->setOrder("send_date DESC");
        return $objQuery->select($col, "dtb_mail_history", $where, array($order_id));
    }

    /**
     *
     * メールを送る。
     * @param SC_FormParam $objFormParam
     */
    function doSend(&$objFormParam,$order_id, $template_id, $subject, $header, $footer){
        // 入力値の変換
        $objFormParam->convParam();

        $this->arrErr = $objFormParam->checkerror();
        // メールの送信
        if (count($this->arrErr) == 0) {
            // 注文受付メール
            $objMail = new SC_Helper_Mail_Ex();
            $objSendMail = $objMail->sfSendOrderMail($order_id, $template_id, $subject, $header, $footer);
            // TODO $SC_SendMail から送信がちゃんと出来たか確認できたら素敵。
            return true;
        }
        return false;
    }

    /**
     * 確認画面を表示する為の準備
     * @param SC_FormParam $objFormParam
     * @param int $order_id
     * @param int $template_id
     * @param string $subject
     * @param string $header
     * @param string $footer
     */
    function confirm(&$objFormParam,$order_id, $template_id, $subject, $header, $footer){
        // 入力値の引き継ぎ
        $this->arrHidden = $objFormParam->getHashArray();
        $this->arrErr = $objFormParam->checkerror();
        // メールの送信
        if (count($this->arrErr) == 0) {
            // 注文受付メール(送信なし)
            $objMail = new SC_Helper_Mail_Ex();
            $objSendMail = $objMail->sfSendOrderMail(
            $order_id,
            $template_id,
            $subject,
            $header,
            $footer, false);
            
            $this->tpl_subject = $objFormParam->getValue('subject');
            $this->tpl_body = mb_convert_encoding( $objSendMail->body, CHAR_CODE, "auto" );
            $this->tpl_to = $objSendMail->tpl_to;
            $this->tpl_mainpage = 'order/mail_confirm.tpl';
            return true;
        }
        return false;
    }

    /**
     * 
     * テンプレートの文言をフォームに入れる。
     * @param SC_FormParam $objFormParam
     */
    function changeData(&$objFormParam){
        $objFormParam->setValue('template_id', $objFormParam->getValue('template_id'));
        if(SC_Utils_Ex::sfIsInt($objFormParam->getValue('template_id'))) {
            $objQuery =& SC_Query::getSingletonInstance();
            $where = "template_id = ?";
            $mailTemplates = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($objFormParam->getValue('template_id')));
            $objFormParam->setParam($mailTemplates[0]);
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


    /**
     * パラメータ情報の初期化
     * @param SC_FormParam $objFormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("オーダーID", "order_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("テンプレート", "template_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールタイトル", "subject", STEXT_LEN, "KVa",  array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("ヘッダー", "header", LTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("フッター", "footer", LTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    }
}
?>