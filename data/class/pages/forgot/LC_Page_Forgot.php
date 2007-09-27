<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * パスワード発行 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Forgot.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Forgot extends LC_Page {

    // {{{ properties

    /** エラーメッセージ */
    var $errmsg;

    /** 秘密の質問の答え */
    var $arrReminder;

    /** 変更後パスワード */
    var $temp_password;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'forgot/index.tpl';
        $this->tpl_mainno = '';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_SiteView();
        $objSess = new SC_Session();

        // 店舗基本情報を取得
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sf_getBasisData();

        $masterData = new SC_DB_MasterData_Ex();
        $arrReminder = $masterData->getMasterData("mtb_reminder");

        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_POST['email'])) $_POST['email'] = "";

        if ( $_POST['mode'] == 'mail_check' ){
            //メアド入力時
            $_POST['email'] = strtolower($_POST['email']);
            $sql = "SELECT * FROM dtb_customer WHERE email = ? AND status = 2 AND del_flg = 0";
            $result = $conn->getAll($sql, array($_POST['email']) );

            // 本会員登録済みの場合
            if (isset($result[0]['reminder']) &&  $result[0]['reminder']){
                // 入力emailが存在する
                $_SESSION['forgot']['email'] = $_POST['email'];
                $_SESSION['forgot']['reminder'] = $result[0]['reminder'];
                // ヒミツの答え入力画面
                $this->Reminder = $arrReminder[$_SESSION['forgot']['reminder']];
                $this->tpl_mainpage = 'forgot/secret.tpl';
            } else {
                $sql = "SELECT customer_id FROM dtb_customer WHERE email = ? AND status = 1 AND del_flg = 0";	//仮登録中の確認
                $result = $conn->getAll($sql, array($_POST['email']) );
                if ($result) {
                    $this->errmsg = "ご入力のemailアドレスは現在仮登録中です。<br>登録の際にお送りしたメールのURLにアクセスし、<br>本会員登録をお願いします。";
                } else {		//　登録していない場合
                    $this->errmsg = "ご入力のemailアドレスは登録されていません";
                }
            }

        } elseif( $_POST['mode'] == 'secret_check' ){
            //ヒミツの答え入力時

            if ( $_SESSION['forgot']['email'] ) {
                // ヒミツの答えの回答が正しいかチェック

                $sql = "SELECT * FROM dtb_customer WHERE email = ? AND del_flg = 0";
                $result = $conn->getAll($sql, array($_SESSION['forgot']['email']) );
                $data = $result[0];

                if ( $data['reminder_answer'] === $_POST['input_reminder'] ){
                    // ヒミツの答えが正しい

                    // 新しいパスワードを設定する
                    $this->temp_password = GC_Utils_Ex::gfMakePassword(8);

                    if(FORGOT_MAIL == 1) {
                        // メールで変更通知をする
                        $this->lfSendMail($CONF, $_SESSION['forgot']['email'], $data['name01'], $this->temp_password);
                    }

                    // DBを書き換える
                    $sql = "UPDATE dtb_customer SET password = ?, update_date = now() WHERE customer_id = ?";
                    $conn->query( $sql, array( sha1($this->temp_password . ":" . AUTH_MAGIC) ,$data['customer_id']) );

                    // 完了画面の表示
                    $this->tpl_mainpage = 'forgot/complete.tpl';

                    // セッション変数の解放
                    $_SESSION['forgot'] = array();
                    unset($_SESSION['forgot']);

                } else {
                    // ヒミツの答えが正しくない

                    $this->Reminder = $arrReminder[$_SESSION['forgot']['reminder']];
                    $this->errmsg = "パスワードを忘れたときの質問に対する回答が正しくありません";
                    $this->tpl_mainpage = 'forgot/secret.tpl';

                }


            } else {
                // アクセス元が不正または、セッション保持期間が切れている
                $this->errmsg = "emailアドレスを再度登録してください。<br />前回の入力から時間が経っていますと、本メッセージが表示される可能性があります。";
            }
        }

        // デフォルト入力
        if($_POST['email'] != "") {
            // POST値を入力
            $this->tpl_login_email = $_POST['email'];
        } else {
            // クッキー値を入力
            $this->tpl_login_email = $objCookie->getCookie('login_email');
        }

        //----　ページ表示
        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
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
     * パスワード変更お知らせメールを送信する.
     *
     * @param array $CONF 店舗基本情報の配列
     * @param string $email 送信先メールアドレス
     * @param string $customer_name 送信先氏名
     * @param string $temp_password 変更後のパスワード
     * @return void
     */
    function lfSendMail($CONF, $email, $customer_name, $temp_password){
        //　パスワード変更お知らせメール送信
        $this->customer_name = $customer_name;
        $this->temp_password = $temp_password;
        $objMailText = new SC_SiteView();
        $objMailText->assignobj($this);

        $toCustomerMail = $objMailText->fetch("mail_templates/forgot_mail.tpl");
        $objMail = new GC_SendMail();

        $objMail->setItem(
                              ''								//　宛先
                            , "パスワードが変更されました" ."【" .$CONF["shop_name"]. "】"		//　サブジェクト
                            , $toCustomerMail					//　本文
                            , $CONF["email03"]					//　配送元アドレス
                            , $CONF["shop_name"]				//　配送元　名前
                            , $CONF["email03"]					//　reply_to
                            , $CONF["email04"]					//　return_path
                            , $CONF["email04"]					//  Errors_to

                                                            );
        $objMail->setTo($email, $customer_name ." 様");
        $objMail->sendMail();
    }
}
?>
