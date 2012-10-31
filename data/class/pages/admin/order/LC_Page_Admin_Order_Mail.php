<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Ex.php';

/**
 * 受注メール管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Mail extends LC_Page_Admin_Order_Ex {

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
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'index';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '受注管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE = $masterData->getMasterData('mtb_mail_template');
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
        $post = $_POST;
        //一括送信用の処理
        if (array_key_exists('mail_order_id',$post) and $post['mode'] == 'mail_select'){
            $post['order_id_array'] = implode(',',$post['mail_order_id']);
        } else if(!array_key_exists('order_id_array',$post)){
            $post['order_id_array'] = $post['order_id'];
        }

        //一括送信処理変数チェック(ここですべきかは課題)
        if (preg_match("/^[0-9|\,]*$/",$post['order_id_array'])){
            $this->order_id_array = $post['order_id_array'];
        } else {
            //エラーで元に戻す
            SC_Response_Ex::sendRedirect(ADMIN_ORDER_URLPATH);
            SC_Response_Ex::actionExit();
        }

        //メール本文の確認例は初めの1受注とする
        if (!SC_Utils_Ex::isBlank($this->order_id_array)){
            $order_id_array = split(',',$this->order_id_array);
            $post['order_id'] = intval($order_id_array[0]);
            $this->order_id_count = count($order_id_array);
        }

        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // POST値の取得
        $objFormParam->setParam($post);
        $objFormParam->convParam();
        $this->tpl_order_id = $objFormParam->getValue('order_id');

        // 検索パラメーターの引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        switch ($this->getMode()) {
            case 'pre_edit':
            case 'mail_select':
                break;
            case 'return':
                break;
            case 'send':
                $sendStatus = $this->doSend($objFormParam);
                if ($sendStatus === true) {

                    SC_Response_Ex::sendRedirect(ADMIN_ORDER_URLPATH);
                    SC_Response_Ex::actionExit();
                } else {
                    $this->arrErr = $sendStatus;
                }
            case 'confirm':
                $status = $this->confirm($objFormParam);
                if ($status === true) {
                    $this->arrHidden = $objFormParam->getHashArray();

                    return ;
                } else {
                    $this->arrErr = $status;
                }
                break;
            case 'change':
                $objFormParam =  $this->changeData($objFormParam);
                break;
        }

        if (SC_Utils_Ex::sfIsInt($objFormParam->getValue('order_id'))) {
            $this->arrMailHistory = $this->getMailHistory($objFormParam->getValue('order_id'));
        }

        $this->arrForm = $objFormParam->getFormParamList();

    }

    /**
     * 指定された注文番号のメール履歴を取得する。
     * @var int order_id
     */
    function getMailHistory($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'send_date, subject, template_id, send_id';
        $where = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');
        return $objQuery->select($col, 'dtb_mail_history', $where, array($order_id));
    }

    /**
     *
     * メールを送る。
     * @param SC_FormParam $objFormParam
     */
    function doSend(&$objFormParam) {
        $arrErr = $objFormParam->checkerror();

        // メールの送信
        if (count($arrErr) == 0) {
            // 注文受付メール(複数受注ID対応)
            $order_id_array = explode(',',$this->order_id_array);
            foreach ($order_id_array as $order_id){
                $objMail = new SC_Helper_Mail_Ex();
                $objMail->setPage($this);
                $objSendMail = $objMail->sfSendOrderMail($order_id,
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'));
            }
            // TODO $SC_SendMail から送信がちゃんと出来たか確認できたら素敵。
            return true;
        }
        return $arrErr;
    }

    /**
     * 確認画面を表示する為の準備
     * @param SC_FormParam $objFormParam
     */
    function confirm(&$objFormParam) {
        $arrErr = $objFormParam->checkerror();
        // メールの送信
        if (count($arrErr) == 0) {
            // 注文受付メール(送信なし)
            $objMail = new SC_Helper_Mail_Ex();
            $objMail->setPage($this);
            $objSendMail = $objMail->sfSendOrderMail(
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'), false);

            $this->tpl_subject = $objFormParam->getValue('subject');
            $this->tpl_body = mb_convert_encoding($objSendMail->body, CHAR_CODE, 'auto');
            $this->tpl_to = $objSendMail->tpl_to;
            $this->tpl_mainpage = 'order/mail_confirm.tpl';
            return true;
        }
        return $arrErr;
    }

    /**
     *
     * テンプレートの文言をフォームに入れる。
     * @param SC_FormParam $objFormParam
     */
    function changeData(&$objFormParam) {
        if (SC_Utils_Ex::sfIsInt($objFormParam->getValue('template_id'))) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $where = 'template_id = ?';
            $mailTemplates = $objQuery->select('subject, header, footer', 'dtb_mailtemplate', $where, array($objFormParam->getValue('template_id')));
            if (!is_null($mailTemplates)) {
                foreach (array('subject','header','footer') as $key) {
                    $objFormParam->setValue($key,$mailTemplates[$key]);
                }
            }
            $objFormParam->setParam($mailTemplates[0]);
        } else {
            foreach (array('subject','header','footer') as $key) {
                $objFormParam->setValue($key,'');
            }
        }
        return $objFormParam;
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
     * パラメーター情報の初期化
     * @param SC_FormParam $objFormParam
     */
    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEMPLATE'), 'template_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MAIL_SUBJECT'), 'subject', STEXT_LEN, 'KVa',  array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_HEADER'), 'header', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_FOOTER'), 'footer', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
    }
}
