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
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Regist extends LC_Page {

    // {{{ properties

    /** 設定情報 */
    var $CONF;

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
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        //$objView = new SC_SiteView();
        $objSiteInfo = $objView->objSiteInfo;
        $objCustomer = new SC_Customer();
        $objDb = new SC_Helper_DB_Ex();
        $this->CONF = $objDb->sfGetBasisData();

        // キャンペーンからの登録の場合の処理

        if(!empty($_GET["cp"])) {
            $etc_val['cp'] = $_GET['cp'];
        }

        //--　本登録完了のためにメールから接続した場合
        if ($_GET["mode"] == "regist") {
            //-- 入力チェック
            $this->arrErr = $this->lfErrorCheck($_GET);
            if ($this->arrErr) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", true, $this->arrErr["id"]);
            
            } else {
                $registSecretKey = $this->lfRegistData($_GET);			//本会員登録（フラグ変更）
                $this->lfSendRegistMail($registSecretKey);				//本会員登録完了メール送信

                // ログイン済みの状態にする。
                $objQuery = new SC_Query();
                $arrRet = $objQuery->select("customer_id, email", "dtb_customer", "secret_key = ?", array($registSecretKey));
                $objCustomer->setLogin($arrRet[0]['email']);
                $etc_val['ci'] = $arrRet[0]['customer_id'];
                $_SERVER['QUERY_STRING'] = NULL;
                $this->objDisplay->redirect($this->getLocation("./complete.php", $etc_val));
                exit;
            }

        //--　それ以外のアクセスは無効とする
        } else {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", true, "無効なアクセスです。");
        }

        //----　ページ表示
        //$objView->assignobj($this);
        //$objView->display(SITE_FRAME);
    }


    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        parent::mobileProcess();
        $this->mobileAction();
        $this->sendResponse();
    }

    /**
     * Page のAction(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        //$objView = new SC_MobileView();
        $objSiteInfo = $objView->objSiteInfo;
        $objCustomer = new SC_Customer();
        $objDb = new SC_Helper_DB_Ex();
        $this->CONF = $objDb->sfGetBasisData();

        //--　本登録完了のためにメールから接続した場合
        if ($_GET["mode"] == "regist") {

            //-- 入力チェック
            $this->arrErr = $this->lfErrorCheck($_GET);
            if ($this->arrErr) {
                $this->tpl_mainpage = 'regist/error.tpl';
                $this->tpl_title = 'エラー';

            } else {
                $registSecretKey = $this->lfRegistData($_GET);			//本会員登録（フラグ変更）
                $this->lfSendRegistMail($registSecretKey);				//本会員登録完了メール送信

                // ログイン済みの状態にする。
                $objQuery = new SC_Query();
                $email = $objQuery->get("dtb_customer", "email", "secret_key = ?", array($registSecretKey));
                $objCustomer->setLogin($email);
                $this->objDisplay->redirect($this->getLocation("./complete.php"));
                exit;
            }

            //--　それ以外のアクセスは無効とする
        } else {
            $this->arrErr["id"] = "無効なアクセスです。";
            $this->tpl_mainpage = 'regist/error.tpl';
            $this->tpl_title = 'エラー';
        }

        //----　ページ表示
        //$objView->assignobj($this);
        //$objView->display(SITE_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    //---- 登録
    function lfRegistData($array) {
        $objQuery = new SC_Query();

        do {
            $secret = SC_Utils_Ex::sfGetUniqRandomId("r");
        } while( ($result = $objQuery->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($secret)) ) != 0);

        $sql = "SELECT email FROM dtb_customer WHERE secret_key = ? AND status = 1";
        $email = $objQuery->getOne($sql, array($array["id"]));

        $objQuery->begin();
        $arrRegist["secret_key"] = $secret;	//　本登録ID発行
        $arrRegist["status"] = 2;
        $arrRegist["update_date"] = "NOW()";

        $where = "secret_key = ? AND status = 1";

        $arrRet = $objQuery->select("point", "dtb_customer", $where, array($array["id"]));

        $objQuery->update("dtb_customer", $arrRegist, $where, array($array["id"]));

        /* 購入時の自動会員登録は行わないためDEL
        // 購入時登録の場合、その回の購入を会員購入とみなす。
        // 会員情報の読み込み
        $where1 = "secret_key = ? AND status = 2";
        $customer = $objQuery->select("*", "dtb_customer", $where1, array($secret));
        // 初回購入情報の読み込み
        $order_temp_id = $objQuery->get("dtb_order_temp", "order_temp_id");
        // 購入情報の更新
        if ($order_temp_id != null) {
            $arrCustomer['customer_id'] = $customer[0]['customer_id'];
            $where3 = "order_temp_id = ?";
            $objQuery->update("dtb_order_temp", $arrCustomer, $where3, array($order_temp_id));
            $objQuery->update("dtb_order", $arrCustomer, $where3, array($order_temp_id));
        }
        */

        $sql = "SELECT mailmaga_flg FROM dtb_customer WHERE email = ?";
        $result = $objQuery->getOne($sql, array($email));

        switch($result) {
        // 仮HTML
        case '4':
            $arrRegistMail["mailmaga_flg"] = 1;
            break;
        // 仮TEXT
        case '5':
            $arrRegistMail["mailmaga_flg"] = 2;
            break;
        // 仮なし
        case '6':
            $arrRegistMail["mailmaga_flg"] = 3;
            break;
        default:
            $arrRegistMail["mailmaga_flg"] = $result;
            break;
        }

        $objQuery->update("dtb_customer", $arrRegistMail, "email = " . SC_Utils_Ex::sfQuoteSmart($email). " AND del_flg = 0");
        $objQuery->commit();

        return $secret;		// 本登録IDを返す
    }

    //---- 入力エラーチェック
    function lfErrorCheck($array) {
        $objQuery = new SC_Query();
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("仮登録ID", 'id'), array("EXIST_CHECK"));
        if (! EregI("^[[:alnum:]]+$",$array["id"] )) {
            $objErr->arrErr["id"] = "無効なURLです。メールに記載されている本会員登録用URLを再度ご確認ください。";
        }
        if (! $objErr->arrErr["id"]) {

            $sql = "SELECT customer_id FROM dtb_customer WHERE secret_key = ? AND status = 1 AND del_flg = 0";
            $result = $objQuery->getOne($sql, array($array["id"]));

            if (! is_numeric($result)) {
                $objErr->arrErr["id"] = "※ 既に会員登録が完了しているか、無効なURLです。<br>";
                return $objErr->arrErr;

            }
        }

        return $objErr->arrErr;
    }

    //---- 正会員登録完了メール送信
    function lfSendRegistMail($registSecretKey) {
        $objQuery = new SC_Query();
        $objHelperMail = new SC_Helper_Mail_Ex();

        //-- 姓名を取得
        $sql = "SELECT email, name01, name02 FROM dtb_customer WHERE secret_key = ?";
        $result = $objQuery->getAll($sql, array($registSecretKey));
        $data = $result[0];

        //--　メール送信
        $objMailText = new SC_SiteView();
        $objMailText->assign("CONF", $this->CONF);
        $objMailText->assign("name01", $data["name01"]);
        $objMailText->assign("name02", $data["name02"]);
        $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
        $subject = $objHelperMail->sfMakesubject('会員登録が完了しました。');
        $objMail = new SC_SendMail();

        $objMail->setItem(
                              ''                                // 宛先
                            , $subject                          // サブジェクト
                            , $toCustomerMail                   // 本文
                            , $this->CONF["email03"]            // 配送元アドレス
                            , $this->CONF["shop_name"]          // 配送元 名前
                            , $this->CONF["email03"]            // reply_to
                            , $this->CONF["email04"]            // return_path
                            , $this->CONF["email04"]            // Errors_to
                        );
        // 宛先の設定
        $name = $data["name01"] . $data["name02"] ." 様";
        $objMail->setTo($data["email"], $name);
        $objMail->sendMail();
    }
}
?>
