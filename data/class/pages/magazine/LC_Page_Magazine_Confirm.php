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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メルマガ管理 のページクラス.
 *
 * FIXME dtb_customer_mail なんて無いよ...
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Magazine_Confirm extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'magazine/confirm.tpl';		// メインテンプレート
        $this->tpl_title .= 'メルマガ確認';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $objConn = new SC_DbConn();
        $this->arrForm = $_POST;

        // 登録
        if (isset($_REQUEST['btnRegist'])) {
            $this->arrErr = $this->lfMailErrorCheck($this->arrForm, "regist", $objConn);

            // エラーがなければ
            if (count($this->arrErr) == 0) {
                // 確認
                $this->arrForm['kind'] = 'メルマガ登録';
                $this->arrForm['type'] = 'regist';
                $this->arrForm['mail'] = $this->arrForm['regist'];
            } else {
                $this->tpl_mainpage = 'magazine/index.tpl';
                $this->tpl_title = 'メルマガ登録・解除';
            }
            // 解除
        } elseif (isset($_REQUEST['btnCancel'])) {
            $this->arrErr = $this->lfMailErrorCheck($this->arrForm, "cancel", $objConn);

            // エラーがなければ
            if (count($this->arrErr) == 0) {
                // 確認
                $this->arrForm['kind'] = 'メルマガ解除';
                $this->arrForm['type'] = 'cancel';
                $this->arrForm['mail'] = $this->arrForm['cancel'];
            } else {
                $this->tpl_mainpage = 'magazine/index.tpl';
                $this->tpl_title = 'メルマガ登録・解除';
            }
            // 完了
        } elseif ($_REQUEST['mode'] == 'regist' or $_REQUEST['mode'] == 'cancel') {
            $objMailText = new SC_MobileView();
            $helperMail = new SC_Helper_Mail_Ex();
            $objQuery = new SC_Query();
            //　登録
            if ($_REQUEST['mode'] == 'regist') {
                $uniqId = $this->lfRegistData($_POST["email"], $objConn);

                $subject = $helperMail->fMakesubject($objQuery, $objMailText, $this, 'メルマガ登録のご確認');
                //　解除
            } elseif ($_REQUEST['mode'] == 'cancel') {
                $uniqId = $this->lfGetSecretKey($_POST["email"], $objConn);
                $subject = $helperMail->sfMakesubject('メルマガ解除のご確認');
            }
            $objDb = new SC_Helper_DB_Ex();
            $CONF = $objDb->sf_getBasisData();
            $this->CONF = $CONF;
            $this->tpl_url = SC_Utils_Ex::gfAddSessionId(MOBILE_SSL_URL . "magazine/" . $_REQUEST['mode'] . ".php?id=" . $uniqId);


            $objMailText->assignobj($this);
            $toCustomerMail = $objMailText->fetch("mail_templates/mailmagazine_" . $_REQUEST['mode'] . ".tpl");
            $objMail = new SC_SendMail();
            $objMail->setItem(
                              ''									//　宛先
                              , $subject							//　サブジェクト
                              , $toCustomerMail					//　本文
                              , $CONF["email03"]					//　配送元アドレス
                              , $CONF["shop_name"]				//　配送元　名前
                              , $CONF["email03"]					//　reply_to
                              , $CONF["email04"]					//　return_path
                              , $CONF["email04"]					//  Errors_to
                              , $CONF["email01"]					//  Bcc
                              );
            // 宛先の設定
            $objMail->setTo($_POST["email"], $_POST["email"]);
            $objMail->sendMail();

            // 完了ページに移動させる。
            $this->objDisplay->redirect($this->getLocation("./complete.php",
                                array(session_name() => session_id())));
            exit;
        } else {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $objView = new SC_MobileView();
        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);
        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    //---- 入力エラーチェック
    function lfMailErrorCheck($array, $dataName, &$objConn) {
        $objErr = new SC_CheckError($array);
        $objErr->doFunc(
                        array('メールアドレス', $dataName, MTEXT_LEN) ,
                        array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK",
                              "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "MOBILE_EMAIL_CHECK"));

        // 入力エラーがなければ
        if (count($objErr->arrErr) == 0) {
            // メルマガの登録有無
            $flg = $this->lfIsRegistData($array[$dataName], $objConn);

            // 登録の時
            if ($dataName == 'regist' and $flg == true) {
                $objErr->arrErr[$dataName] = "既に登録されています。<br>";
                // 解除の時
            } elseif ($dataName == 'cancel' and $flg == false) {
                $objErr->arrErr[$dataName] = "メルマガ登録がされていません。<br>";
            }
        }

        return $objErr->arrErr;
    }


    //---- メルマガ登録
    function lfRegistData ($email, &$objConn) {

        $count = 1;
        while ($count != 0) {
            $uniqid = SC_Utils_Ex::sfGetUniqRandomId("t");
            $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_mail WHERE secret_key = ?", array($uniqid));
        }

        $arrRegist["email"] = $email;			// メールアドレス
        $arrRegist["mail_flag"] = 5;			// 登録状態
        $arrRegist["secret_key"] = $uniqid;		// ID発行
        $arrRegist["create_date"] = "now()"; 	// 作成日
        $arrRegist["update_date"] = "now()"; 	// 更新日

        //-- 仮登録実行
        $objConn->query("BEGIN");

        $objQuery = new SC_Query();

        //--　既にメルマガ登録しているかの判定
        $sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
        $mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

        if ($mailResult == 1) {
            $objQuery->update("dtb_customer_mail", $arrRegist, "email = " . SC_Utils_Ex::sfQuoteSmart($arrRegist["email"]));
        } else {
            $objQuery->insert("dtb_customer_mail", $arrRegist);
        }
        $objConn->query("COMMIT");

        return $uniqid;
    }

    // 登録されているキーの取得
    function lfGetSecretKey ($email, &$objConn) {
        $sql = "SELECT secret_key FROM dtb_customer_mail WHERE email = ?";
        $uniqid = $objConn->getOne($sql, array($email));

        if ($uniqid == '') {
            $count = 1;
            while ($count != 0) {
                $uniqid = SC_Utils_Ex::sfGetUniqRandomId("t");
                $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_mail WHERE secret_key = ?", array($uniqid));
            }

            $objQuery = new SC_Query();
            $objQuery->update("dtb_customer_mail", array('secret_key' => $uniqid), "email = " . SC_Utils_Ex::sfQuoteSmart($email));
        }

        return $uniqid;
    }

    // 既に登録されているかどうか
    function lfIsRegistData ($email, &$objConn) {
        $sql = "SELECT email, mailmaga_flg FROM dtb_customer_mail WHERE email = ?";
        $mailResult = $objConn->getRow($sql, array($email));

        // NULLも購読とみなす
        if (count($mailResult) == 0 or ($mailResult['mailmaga_flg'] != null and $mailResult['mailmaga_flg'] != 2 )) {
            return false;
        } else {
            return true;
        }
    }
}
?>
