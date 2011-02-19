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
        $this->arrPref = $masterData->getMasterData('mtb_pref');
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

        $arrTplVar = new stdClass();
        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        $arrTplVar->arrInfo = $arrInfo;

        $objQuery = new SC_Query();

        if($subject == "" && $header == "" && $footer == "") {
            // メールテンプレート情報の取得
            $where = "template_id = ?";
            $arrRet = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($template_id));
            $arrTplVar->tpl_header = $arrRet[0]['header'];
            $arrTplVar->tpl_footer = $arrRet[0]['footer'];
            $tmp_subject = $arrRet[0]['subject'];
        } else {
            $arrTplVar->tpl_header = $header;
            $arrTplVar->tpl_footer = $footer;
            $tmp_subject = $subject;
        }

        // 受注情報の取得
        $where = "order_id = ?";
        $arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
        $arrOrder = $arrRet[0];
        $arrTplVar->arrOrderDetail = $objQuery->select("*", "dtb_order_detail", $where, array($order_id));

        $objProduct = new SC_Product();
        $objQuery->setOrder('shipping_id');
        $arrRet = $objQuery->select("*", "dtb_shipping", "order_id = ?", array($order_id));
        foreach (array_keys($arrRet) as $key) {
            $objQuery->setOrder('shipping_id');
            $arrItems = $objQuery->select("*", "dtb_shipment_item", "order_id = ? AND shipping_id = ?",
                                          array($order_id, $arrRet[$key]['shipping_id']));
            foreach ($arrItems as $itemKey => $arrDetail) {
                foreach ($arrDetail as $detailKey => $detailVal) {
                    $arrRet[$key]['shipment_item'][$arrDetail['product_class_id']][$detailKey] = $detailVal;
                }

                $arrRet[$key]['shipment_item'][$arrDetail['product_class_id']]['productsClass'] =& $objProduct->getDetailAndProductsClass($arrDetail['product_class_id']);
            }
        }
        $arrTplVar->arrShipping = $arrRet;

        $arrTplVar->Message_tmp = $arrOrder['message'];

        // 顧客情報の取得
        $customer_id = $arrOrder['customer_id'];
        $objQuery->setOrder('customer_id');
        $arrRet = $objQuery->select("point", "dtb_customer", "customer_id = ?", array($customer_id));
        $arrCustomer = isset($arrRet[0]) ? $arrRet[0] : "";

        $arrTplVar->arrCustomer = $arrCustomer;
        $arrTplVar->arrOrder = $arrOrder;

        //その他決済情報
        if($arrOrder['memo02'] != "") {
            $arrOther = unserialize($arrOrder['memo02']);

            foreach($arrOther as $other_key => $other_val){
                if(SC_Utils_Ex::sfTrim($other_val["value"]) == ""){
                    $arrOther[$other_key]["value"] = "";
                }
            }

            $arrTplVar->arrOther = $arrOther;
        }

        // 都道府県変換
        $arrTplVar->arrPref = $this->arrPref;

        $objCustomer = new SC_Customer();
        $arrTplVar->tpl_user_point = $objCustomer->getValue('point');

       if(Net_UserAgent_Mobile::isMobile() === true) {
            $objMailView = new SC_MobileView();
       } else {
            $objMailView = new SC_SiteView();
       }
        // メール本文の取得
        $objMailView->assignobj($arrTplVar);
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

    /**
     * 登録メールを送信する。
     *
     * @param string $secret_key 顧客固有キー
     * @param integer $customer_id 顧客ID
     * @param boolean $is_mobile false(default):PCアドレスにメールを送る true:携帯アドレスにメールを送る 
     * @return boolean true:成功 false:失敗
     */
    function sfSendRegistMail($secret_key, $customer_id = '', $is_mobile = false) {
        // 顧客データの取得
        if(SC_Utils_Ex::sfIsInt($customer_id)) {
            $arrCustomerData = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        }else{
            $arrCustomerData = SC_Helper_Customer_Ex::sfGetCustomerDataFromId('', "secret_key = ?", array($secret_key));
        }
        if(SC_Utils_Ex::isBlank($arrCustomerData)) {
            return false;
        }

        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        
        $objMailText = new SC_SiteView();
        $objMailText->assign("CONF", $CONF);
        $objMailText->assign("name", $arrCustomerData['name01'] . $arrCustomerData['name02']);
        $objMailText->assign("uniqid", $arrCustomerData['secret_key']);
        $objMailText->assignobj($arrCustomerData);
        $objMailText->assignobj($this);

        $objHelperMail  = new SC_Helper_Mail_Ex();

        // 仮会員が有効の場合
        if(CUSTOMER_CONFIRM_MAIL == true and $arrCustomerData['status'] == 1) {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご確認');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
        } else {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご完了');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
        }

        $objMail = new SC_SendMail();
        $objMail->setItem(
            ''                    // 宛先
            , $subject              // サブジェクト
            , $toCustomerMail       // 本文
            , $CONF["email03"]      // 配送元アドレス
            , $CONF["shop_name"]    // 配送元 名前
            , $CONF["email03"]      // reply_to
            , $CONF["email04"]      // return_path
            , $CONF["email04"]      // Errors_to
            , $CONF["email01"]      // Bcc
        );
        // 宛先の設定
        if($is_mobile) {
            $to_addr = $arrCustomerData["email_mobile"];
        }else{
            $to_addr = $arrCustomerData["email"];
        }
        $objMail->setTo($to_addr, $arrCustomerData["name01"] . $arrCustomerData["name02"] ." 様");

        $objMail->sendMail();
        return true;
    }
    
    /**
     * 保存されているメールテンプレートの取得
     * @param array $arrParams 特定IDのテンプレートを取り出したい時は$arrParams['template_id']で指定
     * @return　array メールテンプレート情報を格納した配列
     * @todo   表示順も引数で変更できるように
     */
    function sfGetMailTemplate($arrParams = array()){
        // 初期化
        $where = '';
        $arrValues = array();
        $objQuery =& SC_Query::getSingletonInstance();
        
        // 条件文
        $where = 'del_flg = ?';
        $arrValues[] = 0;
        //template_id指定時
        if (isset($arrParams['template_id'])) {
            $where .= 'AND template_id = ?';
            $arrValues[] = $arrParams['template_id'];
        }
        
        // 表示順
        $objQuery->setOrder("create_date DESC");
        
        $arrResults = $objQuery->select('*', 'dtb_mailmaga_template', $where, $arrValues);
        return $arrResults;
    }
}
?>
