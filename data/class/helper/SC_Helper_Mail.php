<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
    function sfSendTemplateMail($to, $to_name, $template_id, &$objPage) {

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
        $body = $objMailView->fetch($arrMAILTPLPATH[$template_id]);

        // メール送信処理
        $objSendMail = new GC_SendMail();
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $tosubject = $tmp_subject;
        $objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error);
        $objSendMail->setTo($to, $to_name);
        $objSendMail->sendMail();	// メール送信
    }

    /* 受注完了メール送信 */
    function sfSendOrderMail($order_id, $template_id, $subject = "", $header = "", $footer = "", $send = true) {
        global $arrMAILTPLPATH;

        $objPage = new LC_Page();
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        $objPage->arrInfo = $arrInfo;

        $objQuery = new SC_Query();

        if($subject == "" && $header == "" && $footer == "") {
            // メールテンプレート情報の取得
            $where = "template_id = ?";
            $arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array('1'));
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
        $arrCustomer = $arrRet[0];

        $objPage->arrCustomer = $arrCustomer;
        $objPage->arrOrder = $arrOrder;

        //その他決済情報
        if($arrOrder['memo02'] != "") {
            $arrOther = unserialize($arrOrder['memo02']);

            foreach($arrOther as $other_key => $other_val){
                if(sfTrim($other_val["value"]) == ""){
                    $arrOther[$other_key]["value"] = "";
                }
            }

            $objPage->arrOther = $arrOther;
        }

        // 都道府県変換
        $objPage->arrOrder['deliv_pref'] = $arrPref[$objPage->arrOrder['deliv_pref']];

        $objPage->arrOrderDetail = $arrOrderDetail;

        $objCustomer = new SC_Customer();
        $objPage->tpl_user_point = $objCustomer->getValue('point');

        $objMailView = new SC_SiteView();
        // メール本文の取得
        $objMailView->assignobj($objPage);
        $body = $objMailView->fetch($arrMAILTPLPATH[$template_id]);

        // メール送信処理
        $objSendMail = new GC_SendMail();
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];

        $tosubject = SC_Utils::sfMakeSubject($objQuery, $objMailView,
                                             $objPage, $tmp_subject);

        $objSendMail->setItem('', $tosubject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
        $objSendMail->setTo($arrOrder["order_email"], $arrOrder["order_name01"] . " ". $arrOrder["order_name02"] ." 様");


        // 送信フラグ:trueの場合は、送信する。
        if($send) {
            if ($objSendMail->sendMail()) {
                $this->fSaveMailHistory($order_id, $template_id, $tosubject, $body);
            }
        }

        return $objSendMail;
    }

    // テンプレートを使用したメールの送信
    function sfSendTplMail($to, $subject, $tplpath, &$objPage) {
        $objMailView = new SC_SiteView();
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        // メール本文の取得
        $objPage->tpl_shopname=$arrInfo['shop_name'];
        $objPage->tpl_infoemail = $arrInfo['email02'];
        $objMailView->assignobj($objPage);
        $body = $objMailView->fetch($tplpath);
        // メール送信処理
        $objSendMail = new GC_SendMail();
        $to = mb_encode_mimeheader($to);
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $objSendMail->setItem($to, $subject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
        $objSendMail->sendMail();
    }

    // 通常のメール送信
    function sfSendMail($to, $subject, $body) {
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        // メール送信処理
        $objSendMail = new GC_SendMail();
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $objSendMail->setItem($to, $subject, $body, $from, $arrInfo['shop_name'], $from, $error, $error, $bcc);
        $objSendMail->sendMail();
    }

    //件名にテンプレートを用いる
    function sfMakeSubject(&$objQuery, &$objMailView, &$objPage, $subject){

        $arrInfo = $objQuery->select("*","dtb_baseinfo");
        $arrInfo = $arrInfo[0];
        $objPage->tpl_shopname=$arrInfo['shop_name'];
        $objPage->tpl_infoemail=$subject;
        $objMailView->assignobj($objPage);
        $mailtitle = $objMailView->fetch('mail_templates/mail_title.tpl');
        $ret = $mailtitle.$subject;
        return $ret;
    }

    // メール配信履歴への登録
    function sfSaveMailHistory($order_id, $template_id, $subject, $body) {
        $sqlval['subject'] = $subject;
        $sqlval['order_id'] = $order_id;
        $sqlval['template_id'] = $template_id;
        $sqlval['send_date'] = "Now()";
        if($_SESSION['member_id'] != "") {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        } else {
            $sqlval['creator_id'] = '0';
        }
        $sqlval['mail_body'] = $body;

        $objQuery = new SC_Query();
        $objQuery->insert("dtb_mail_history", $sqlval);
    }

    /* 会員のメルマガ登録があるかどうかのチェック(仮会員を含まない) */
    function sfCheckCustomerMailMaga($email) {
        $col = "email, mailmaga_flg, customer_id";
        $from = "dtb_customer";
        $where = "email = ? AND status = 2";
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select($col, $from, $where, array($email));
        // 会員のメールアドレスが登録されている
        if($arrRet[0]['customer_id'] != "") {
            return true;
        }
        return false;
    }
}
?>
