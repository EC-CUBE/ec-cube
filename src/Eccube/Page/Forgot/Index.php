<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Forgot;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Cookie;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\SendMail;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\View\SiteView;

/**
 * パスワード発行 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    /** フォームパラメーターの配列 */
    public $objFormParam;

    /** 秘密の質問の答え */
    public $arrReminder;

    /** 変更後パスワード */
    public $temp_password;

    /** エラーメッセージ */
    public $errmsg;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_title = 'パスワードを忘れた方';
        $this->tpl_mainpage = 'forgot/index.tpl';
        $this->tpl_mainno = '';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrReminder = $masterData->getMasterData('mtb_reminder');
        $this->device_type = Application::alias('eccube.display')->detectDevice();
        $this->httpCacheControl('nocache');
        // デフォルトログインアドレスロード
        /* @var $objCookie Cookie */
        $objCookie = Application::alias('eccube.cookie');
        $this->tpl_login_email = $objCookie->getCookie('login_email');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');

        switch ($this->getMode()) {
            case 'mail_check':
                $this->lfInitMailCheckParam($objFormParam, $this->device_type);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    $this->errmsg = $this->lfCheckForgotMail($this->arrForm, $this->arrReminder);
                    if (Utils::isBlank($this->errmsg)) {
                        $this->tpl_mainpage = 'forgot/secret.tpl';
                    }
                }
                break;
            case 'secret_check':
                $this->lfInitSecretCheckParam($objFormParam, $this->device_type);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    $this->errmsg = $this->lfCheckForgotSecret($this->arrForm, $this->arrReminder);
                    if (Utils::isBlank($this->errmsg)) {
                        // 完了ページへ移動する
                        $this->tpl_mainpage = 'forgot/complete.tpl';
                        // transactionidを更新させたいので呼び出し元(ログインフォーム側)をリロード。
                        $this->tpl_onload .= 'opener.location.reload(true);';
                    } else {
                        // 秘密の答えが一致しなかった
                        $this->tpl_mainpage = 'forgot/secret.tpl';
                    }
                } else {
                    // 入力値エラー
                    $this->tpl_mainpage = 'forgot/secret.tpl';
                }
                break;
            default:
                break;
        }

        // ポップアップ用テンプレート設定
        if ($this->device_type == DEVICE_TYPE_PC) {
            $this->setTemplate($this->tpl_mainpage);
        }

    }

    /**
     * メールアドレス・名前確認
     *
     * @param  array  $arrForm     フォーム入力値
     * @param  array  $arrReminder リマインダー質問リスト
     * @return string エラー文字列 問題が無ければNULL
     */
    public function lfCheckForgotMail(&$arrForm, &$arrReminder)
    {
        $errmsg = NULL;
        $objQuery = Application::alias('eccube.query');
        $where = '(email = ? OR email_mobile = ?) AND name01 = ? AND name02 = ? AND del_flg = 0';
        $arrVal = array($arrForm['email'], $arrForm['email'], $arrForm['name01'], $arrForm['name02']);
        $result = $objQuery->select('reminder, status', 'dtb_customer', $where, $arrVal);
        if (isset($result[0]['reminder']) and isset($arrReminder[$result[0]['reminder']])) {
            // 会員状態の確認
            if ($result[0]['status'] == '2') {
                // 正会員
                $arrForm['reminder'] = $result[0]['reminder'];
            } elseif ($result[0]['status'] == '1') {
                // 仮会員
                $errmsg = 'ご入力のemailアドレスは現在仮登録中です。<br/>登録の際にお送りしたメールのURLにアクセスし、<br/>本会員登録をお願いします。';
            }
        } else {
            $errmsg = 'お名前に間違いがあるか、このメールアドレスは登録されていません。';
        }

        return $errmsg;
    }

    /**
     * メールアドレス確認におけるパラメーター情報の初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @param  array $device_type  デバイスタイプ
     * @return void
     */
    public function lfInitMailCheckParam(&$objFormParam, $device_type)
    {
        $objFormParam->addParam('お名前(姓)', 'name01', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'NO_SPTAB', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お名前(名)', 'name02', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'NO_SPTAB', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        if ($device_type === DEVICE_TYPE_MOBILE) {
            $objFormParam->addParam('メールアドレス', 'email', null, 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'NO_SPTAB', 'EMAIL_CHAR_CHECK', 'MOBILE_EMAIL_CHECK'));
        } else {
            $objFormParam->addParam('メールアドレス', 'email', null, 'a', array('NO_SPTAB', 'EXIST_CHECK', 'EMAIL_CHECK', 'SPTAB_CHECK', 'EMAIL_CHAR_CHECK'));
        }

        return;
    }

    /**
     * 秘密の質問確認
     *
     * @param  array  $arrForm     フォーム入力値
     * @param  array  $arrReminder リマインダー質問リスト
     * @return string エラー文字列 問題が無ければNULL
     */
    public function lfCheckForgotSecret(&$arrForm, &$arrReminder)
    {
        $errmsg = '';
        $objQuery = Application::alias('eccube.query');
        $cols = 'customer_id, reminder, reminder_answer, salt';
        $table = 'dtb_customer';
        $where = '(email = ? OR email_mobile = ?)'
                    . ' AND name01 = ? AND name02 = ?'
                    . ' AND status = 2 AND del_flg = 0';
        $arrVal = array($arrForm['email'], $arrForm['email'],
                            $arrForm['name01'], $arrForm['name02']);
        $result = $objQuery->select($cols, $table, $where, $arrVal);
        if (isset($result[0]['reminder']) and isset($arrReminder[$result[0]['reminder']])
                and $result[0]['reminder'] == $arrForm['reminder']) {
            $is_authorized = false;
            if (empty($result[0]['salt'])) {
                // 旧バージョン(2.11未満)からの移行を考慮
                if ($result[0]['reminder_answer'] == $arrForm['reminder_answer']) {
                    $is_authorized = true;
                }
            } elseif (Utils::sfIsMatchHashPassword($arrForm['reminder_answer'],
                    $result[0]['reminder_answer'], $result[0]['salt'])) {
                $is_authorized = true;
            }

            if ($is_authorized) {
                // 秘密の答えが一致
                // 新しいパスワードを設定する
                $new_password = GcUtils::gfMakePassword(8);
                if (FORGOT_MAIL == 1) {
                    // メールで変更通知をする
                    /* @var $objDb DbHelper */
                    $objDb = Application::alias('eccube.helper.db');
                    $CONF = $objDb->getBasisData();
                    $this->lfSendMail($CONF, $arrForm['email'], $arrForm['name01'], $new_password);
                }
                $sqlval = array();
                $sqlval['password'] = $new_password;
                Application::alias('eccube.helper.customer')->sfEditCustomerData($sqlval, $result[0]['customer_id']);
                $arrForm['new_password'] = $new_password;
            } else {
                // 秘密の答えが一致しなかった
                $errmsg = '秘密の質問が一致しませんでした。';
            }
        } else {
            //不正なアクセス リマインダー値が前画面と異なる。
            // 新リファクタリング基準ではここで遷移は不許可なのでエラー表示
            //Utils::sfDispSiteError(PAGE_ERROR, '', true);
            $errmsg = '秘密の質問が一致しませんでした。';
        }

        return $errmsg;
    }

    /**
     * 秘密の質問確認におけるパラメーター情報の初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @param  array $device_type  デバイスタイプ
     * @return void
     */
    public function lfInitSecretCheckParam(&$objFormParam, $device_type)
    {
        // メールチェックと同等のチェックを再度行う
        $this->lfInitMailCheckParam($objFormParam, $device_type);
        // 秘密の質問チェックの追加
        $objFormParam->addParam('パスワード確認用の質問', 'reminder', STEXT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('パスワード確認用の質問の答え', 'reminder_answer', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        return;
    }

    /**
     * パスワード変更お知らせメールを送信する.
     *
     * @param  array  $CONF          店舗基本情報の配列
     * @param  string $email         送信先メールアドレス
     * @param  string $customer_name 送信先氏名
     * @param  string $new_password  変更後の新パスワード
     * @return void
     *
     * FIXME: メールテンプレート編集の方に足すのが望ましい
     */
    public function lfSendMail(&$CONF, $email, $customer_name, $new_password)
    {
        // パスワード変更お知らせメール送信
        $objMailText = new SiteView(false);
        $objMailText->setPage($this);
        $objMailText->assign('customer_name', $customer_name);
        $objMailText->assign('new_password', $new_password);
        $toCustomerMail = $objMailText->fetch('mail_templates/forgot_mail.tpl');

        /* @var $objHelperMail MailHelper */
        $objHelperMail = Application::alias('eccube.helper.mail');
        $objHelperMail->setPage($this);

        // メール送信オブジェクトによる送信処理
        /* @var $objMail Sendmail */
        $objMail = Application::alias('eccube.sendmail');
        $objMail->setItem(
            '', //宛先
            $objHelperMail->sfMakeSubject('パスワードを変更いたしました。'),
            $toCustomerMail, //本文
            $CONF['email03'], //配送元アドレス
            $CONF['shop_name'], // 配送元名
            $CONF['email03'], // reply to
            $CONF['email04'], //return_path
            $CONF['email04'] // errors_to
            );
        $objMail->setTo($email, $customer_name . ' 様');
        $objMail->sendMail();

        return;
    }
}
