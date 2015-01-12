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

namespace Eccube\Page\FrontParts;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Cookie;
use Eccube\Framework\Customer;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\MobileHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Util\Utils;

/**
 * ログインチェック のページクラス.
 *
 * TODO mypage/LC_Page_Mypage_LoginCheck と統合
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class LoginCheck extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
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
        //決済処理中ステータスのロールバック
        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        // 会員管理クラス
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        // クッキー管理クラス
        /* @var $objCookie Cookie */
        $objCookie = Application::alias('eccube.cookie');
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // リクエスト値をフォームにセット
        $objFormParam->setParam($_POST);

        $url = htmlspecialchars($_POST['url'], ENT_QUOTES);

        // モードによって分岐
        switch ($this->getMode()) {
            case 'login':
                // --- ログイン

                // 入力値のエラーチェック
                $objFormParam->trimParam();
                $objFormParam->toLower('login_email');
                $arrErr = $objFormParam->checkError();

                // エラーの場合はエラー画面に遷移
                if (count($arrErr) > 0) {
                    if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                        echo $this->lfGetErrorMessage(TEMP_LOGIN_ERROR);
                        Application::alias('eccube.response')->actionExit();
                    } else {
                        Utils::sfDispSiteError(TEMP_LOGIN_ERROR);
                        Application::alias('eccube.response')->actionExit();
                    }
                }

                // 入力チェック後の値を取得
                $arrForm = $objFormParam->getHashArray();

                // クッキー保存判定
                if ($arrForm['login_memory'] == '1' && $arrForm['login_email'] != '') {
                    $objCookie->setCookie('login_email', $arrForm['login_email']);
                } else {
                    $objCookie->setCookie('login_email', '');
                }

                // 遷移先の制御
                if (count($arrErr) == 0) {
                    // ログイン処理
                    if ($objCustomer->doLogin($arrForm['login_email'], $arrForm['login_pass'])) {
                        if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_MOBILE) {
                            // ログインが成功した場合は携帯端末IDを保存する。
                            $objCustomer->updateMobilePhoneId();

                            /*
                             * email がモバイルドメインでは無く,
                             * 携帯メールアドレスが登録されていない場合
                             */
                            /* @var $objMobile MobileHelper */
                            $objMobile = Application::alias('eccube.helper.mobile');
                            if (!$objMobile->gfIsMobileMailAddress($objCustomer->getValue('email'))) {
                                if (!$objCustomer->hasValue('email_mobile')) {
                                    Application::alias('eccube.response')->sendRedirectFromUrlPath('entry/email_mobile.php');
                                    Application::alias('eccube.response')->actionExit();
                                }
                            }
                        }

                        // --- ログインに成功した場合
                        if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                            echo Utils::jsonEncode(array('success' => $url));
                        } else {
                            Application::alias('eccube.response')->sendRedirect($url);
                        }
                        Application::alias('eccube.response')->actionExit();
                    } else {
                        // --- ログインに失敗した場合

                        // ブルートフォースアタック対策
                        // ログイン失敗時に遅延させる
                        sleep(LOGIN_RETRY_INTERVAL);

                        $arrForm['login_email'] = strtolower($arrForm['login_email']);
                        $objQuery = Application::alias('eccube.query');
                        $where = '(email = ? OR email_mobile = ?) AND status = 1 AND del_flg = 0';
                        $exists = $objQuery->exists('dtb_customer', $where, array($arrForm['login_email'], $arrForm['login_email']));
                        // ログインエラー表示 TODO リファクタリング
                        if ($exists) {
                            if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                                echo $this->lfGetErrorMessage(TEMP_LOGIN_ERROR);
                                Application::alias('eccube.response')->actionExit();
                            } else {
                                Utils::sfDispSiteError(TEMP_LOGIN_ERROR);
                                Application::alias('eccube.response')->actionExit();
                            }
                        } else {
                            if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                                echo $this->lfGetErrorMessage(SITE_LOGIN_ERROR);
                                Application::alias('eccube.response')->actionExit();
                            } else {
                                Utils::sfDispSiteError(SITE_LOGIN_ERROR);
                                Application::alias('eccube.response')->actionExit();
                            }
                        }
                    }
                } else {
                    // XXX 到達しない？
                    // 入力エラーの場合、元のアドレスに戻す。
                    Application::alias('eccube.response')->sendRedirect($url);
                    Application::alias('eccube.response')->actionExit();
                }

                break;
            case 'logout':
                // --- ログアウト

                // ログイン情報の解放
                $objCustomer->EndSession();
                // 画面遷移の制御
                $mypage_url_search = strpos('.'.$url, 'mypage');
                if ($mypage_url_search == 2) {
                    // マイページログイン中はログイン画面へ移行
                    Application::alias('eccube.response')->sendRedirectFromUrlPath('mypage/login.php');
                } else {
                    // 上記以外の場合、トップへ遷移
                    Application::alias('eccube.response')->sendRedirect(TOP_URL);
                }
                Application::alias('eccube.response')->actionExit();

                break;
            default:
                break;
        }

    }

    /**
     * パラメーター情報の初期化.
     *
     * @param  FormParam $objFormParam パラメーター管理クラス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('記憶する', 'login_memory', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('メールアドレス', 'login_email', MTEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('パスワード', 'login_pass', PASSWORD_MAX_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * エラーメッセージを JSON 形式で返す.
     *
     * TODO リファクタリング
     * この関数は主にスマートフォンで使用します.
     *
     * @param integer エラーコード
     * @return string JSON 形式のエラーメッセージ
     * @see LC_PageError
     */
    public function lfGetErrorMessage($error)
    {
        switch ($error) {
            case TEMP_LOGIN_ERROR:
                $msg = "メールアドレスもしくはパスワードが正しくありません。\n本登録がお済みでない場合は、仮登録メールに記載されているURLより本登録を行ってください。";
                break;
            case SITE_LOGIN_ERROR:
            default:
                $msg = 'メールアドレスもしくはパスワードが正しくありません。';
        }

        return Utils::jsonEncode(array('login_error' => $msg));
    }
}
