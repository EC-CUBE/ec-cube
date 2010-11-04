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

/**
 * メール関連 のヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Mail {

    /** メールテンプレートのパス */
    var $arrMAILTPLPATH;

    /**
     * コンストラクタ.
     */
    function SC_Helper_Mail() {
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMAILTPLPATH =  $masterData->getMasterData("mtb_mail_tpl_path");
        $this->arrPref = $masterData->getMasterData("mtb_pref",
                                 array("pref_id", "pref_name", "rank"));
    }

    /* DBに登録されたテンプレートメールの送信 */
    function sfSendTemplateMail($to, $to_name, $template_id, &$objPage, $from_address = "", $from_name = "", $reply_to = "") {

        $objQuery = new SC_Query();
        // メールテンプレート情報の取得
        $where = "template_id = ?";
        $arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($template_id));
        $objPage->tpl_header = $arrRet[0]['header'];
        $objPage->tpl_footer = $arrRet[0]['footer'];
        $tmp_subject = $arrRet[0]['subject'];

        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;

        $objMailView = new SC_SiteView();
        // メール本文の取得
        $objMailView->assignobj($objPage);
        $body = $objMailView->fetch($this->arrMAILTPLPATH[$template_id]);

        // メール送信処理
        $objSendMail = new SC_SendMail_Ex();
        if ($from_address == "") $from_address = $arrInfo['email03'];
        if ($from_name == "") $from_name = $arrInfo['shop_name'];
        if ($reply_to == "") $reply_to = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $tosubject = $this->sfMakeSubject($tmp_subject);
        
        $objSendMail->setItem('', $tosubject, $body, $from_address, $from_name, $reply_to, $error, $error);
        $objSendMail->setTo($to, $to_name);
        $objSendMail->sendMail();    // メール送信
    }

    /* 受注完了メール送信 */
    function sfSendOrderMail($order_id, $template_id, $subject = "", $header = "", $footer = "", $send = true) {

        $objPage = new LC_Page();
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        $objPage->arrInfo = $arrInfo;

        $objQuery = new SC_Query();

        if($subject == "" && $header == "" && $footer == "") {
            // メールテンプレート情報の取得
            $where = "template_id = ?";
            $arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($template_id));
            $objPage->tpl_header = $arrRet[0]['header'];
            $objPage->tpl_footer = $arrRet[0]['footer'];
            $tmp_subject = $arrRet[0]['subject'];
        } else {
            $objPage->tpl_header = $header;
            $objPage->tpl_footer = $footer;
            $tmp_subject = $subject;
        }

        // 受注情報の取得
        $where = "order_id = ?";
        $arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
        $arrOrder = $arrRet[0];
        $arrOrderDetail = $objQuery->select("*", "dtb_order_detail", $where, array($order_id));

        $objPage->Message_tmp = $arrOrder['message'];

        // 顧客情報の取得
        $customer_id = $arrOrder['customer_id'];
        $arrRet = $objQuery->select("point", "dtb_customer", "customer_id = ?", array($customer_id));
        $arrCustomer = isset($arrRet[0]) ? $arrRet[0] : "";

        $objPage->arrCustomer = $arrCustomer;
        $objPage->arrOrder = $arrOrder;

        //その他決済情報
        if($arrOrder['memo02'] != "") {
            $arrOther = unserialize($arrOrder['memo02']);

            foreach($arrOther as $other_key => $other_val){
                if(SC_Utils_Ex::sfTrim($other_val["value"]) == ""){
                    $arrOther[$other_key]["value"] = "";
                }
            }

            $objPage->arrOther = $arrOther;
        }

        // 都道府県変換
        $objPage->arrOrder['deliv_pref'] = $this->arrPref[$objPage->arrOrder['deliv_pref']];

        $objPage->arrOrderDetail = $arrOrderDetail;

        $objCustomer = new SC_Customer();
        $objPage->tpl_user_point = $objCustomer->getValue('point');

        $objMailView = new SC_SiteView();
        // メール本文の取得
        $objMailView->assignobj($objPage);
        $body = $objMailView->fetch($this->arrMAILTPLPATH[$template_id]);

        // メール送信処理
        $objSendMail = new SC_SendMail_Ex();
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $tosubject = $this->sfMakeSubject($tmp_subject);

        $objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
        $objSendMail->setTo($arrOrder["order_email"], $arrOrder["order_name01"] . " ". $arrOrder["order_name02"] ." 様");


        // 送信フラグ:trueの場合は、送信する。
        if($send) {
            if ($objSendMail->sendMail()) {
                $this->sfSaveMailHistory($order_id, $template_id, $tosubject, $body);
            }
        }

        return $objSendMail;
    }

    // テンプレートを使用したメールの送信
    function sfSendTplMail($to, $tmp_subject, $tplpath, &$objPage) {
        $objMailView = new SC_SiteView();
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        // メール本文の取得
        $objPage->tpl_shopname=$arrInfo['shop_name'];
        $objPage->tpl_infoemail = $arrInfo['email02'];
        $objMailView->assignobj($objPage);
        $body = $objMailView->fetch($tplpath);
        // メール送信処理
        $objSendMail = new SC_SendMail_Ex();
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $tosubject = $this->sfMakeSubject($tmp_subject);
        
        $objSendMail->setItem($to, $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
        $objSendMail->sendMail();
    }

    // 通常のメール送信
    function sfSendMail($to, $tmp_subject, $body) {
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        // メール送信処理
        $objSendMail = new SC_SendMail_Ex();
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $tosubject = $this->sfMakeSubject($tmp_subject);
        
        $objSendMail->setItem($to, $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
        $objSendMail->sendMail();
    }

    //件名にテンプレートを用いる
    function sfMakeSubject($subject) {
        $objQuery = new SC_Query();
        $objMailView = new SC_SiteView();
        $objTplAssign = new stdClass;
        
        $arrInfo = $objQuery->select("*","dtb_baseinfo");
        $arrInfo = $arrInfo[0];
        $objTplAssign->tpl_shopname=$arrInfo['shop_name'];
        $objTplAssign->tpl_infoemail=$subject; // 従来互換
        $objTplAssign->tpl_mailtitle=$subject;
        $objMailView->assignobj($objTplAssign);
        $subject = $objMailView->fetch('mail_templates/mail_title.tpl');
        return $subject;
    }

    // メール配信履歴への登録
    function sfSaveMailHistory($order_id, $template_id, $subject, $body) {
        $sqlval['subject'] = $subject;
        $sqlval['order_id'] = $order_id;
        $sqlval['template_id'] = $template_id;
        $sqlval['send_date'] = "Now()";
        if (!isset($_SESSION['member_id'])) $_SESSION['member_id'] = "";
        if($_SESSION['member_id'] != "") {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        } else {
            $sqlval['creator_id'] = '0';
        }
        $sqlval['mail_body'] = $body;

        $objQuery = new SC_Query();
        $sqlval['send_id'] = $objQuery->nextVal("dtb_mail_history_send_id");
        $objQuery->insert("dtb_mail_history", $sqlval);
    }

    /* 会員登録があるかどうかのチェック(仮会員を含まない) */
    function sfCheckCustomerMailMaga($email) {
        $col = "email, mailmaga_flg, customer_id";
        $from = "dtb_customer";
        $where = "(email = ? OR email_mobile = ?) AND status = 2 AND del_flg = 0";
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select($col, $from, $where, array($email));
        // 会員のメールアドレスが登録されている
        if(!empty($arrRet[0]['customer_id'])) {
            return true;
        }
        return false;
    }
}
?>
