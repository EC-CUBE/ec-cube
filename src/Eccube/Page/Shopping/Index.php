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

namespace Eccube\Page\Shopping;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CartSession;
use Eccube\Framework\Cookie;
use Eccube\Framework\Customer;
use Eccube\Framework\Date;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\SiteSession;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Util\Utils;

/**
 * ショッピングログインのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_title = 'ログイン';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrCountry = $masterData->getMasterData('mtb_country');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrJob = $masterData->getMasterData('mtb_job');
        $this->tpl_onload = 'eccube.toggleDeliveryForm();';

        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date', BIRTH_YEAR, date('Y'), strtotime('now'));
        $this->arrYear = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);

        $this->httpCacheControl('nocache');
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
     * Page のプロセス.
     *
     * @return void
     */
    public function action()
    {
        //決済処理中ステータスのロールバック
        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        /* @var $objSiteSess SiteSession */
        $objSiteSess = Application::alias('eccube.site_session');
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        /* @var $objCookie Cookie */
        $objCookie = Application::alias('eccube.cookie');
        $objFormParam = Application::alias('eccube.form_param');

        $nonmember_mainpage = 'shopping/nonmember_input.tpl';
        $nonmember_title = 'お客様情報入力';

        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // ログイン済みの場合は次画面に遷移
        if ($objCustomer->isLoginSuccess(true)) {
            Application::alias('eccube.response')->sendRedirect(
                    $this->getNextlocation($this->cartKey, $this->tpl_uniqid,
                                           $objCustomer, $objPurchase,
                                           $objSiteSess, $objCartSess));
            Application::alias('eccube.response')->actionExit();
        // 非会員かつ, ダウンロード商品の場合はエラー表示
        } else {
            if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
                $msg = 'ダウンロード商品を含むお買い物は、会員登録が必要です。<br/>'
                     . 'お手数ですが、会員登録をお願いします。';
                Utils::sfDispSiteError(FREE_ERROR_MSG, $objSiteSess, false, $msg);
                Application::alias('eccube.response')->actionExit();
            }
        }

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();
        }

        switch ($this->getMode()) {
            // ログイン実行
            case 'login':
                $this->lfInitLoginFormParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->trimParam();
                $objFormParam->convParam();
                $objFormParam->toLower('login_email');
                $this->arrErr = $objFormParam->checkError();

                // ログイン判定
                if (Utils::isBlank($this->arrErr)
                    && $objCustomer->doLogin($objFormParam->getValue('login_email'),
                                             $objFormParam->getValue('login_pass'))) {
                    // クッキー保存判定
                    if ($objFormParam->getValue('login_memory') == '1' && strlen($objFormParam->getValue('login_email')) >= 1) {
                        $objCookie->setCookie('login_email', $objFormParam->getValue('login_email'));
                    } else {
                        $objCookie->setCookie('login_email', '');
                    }

                    // モバイルサイトで携帯アドレスの登録が無い場合、携帯アドレス登録ページへ遷移
                    if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
                        if (!$objCustomer->hasValue('email_mobile')) {
                            Application::alias('eccube.response')->sendRedirectFromUrlPath('entry/email_mobile.php');
                            Application::alias('eccube.response')->actionExit();
                        }
                    // スマートフォンの場合はログイン成功を返す
                    } elseif (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                        echo Utils::jsonEncode(array('success' =>
                                                     $this->getNextLocation($this->cartKey, $this->tpl_uniqid,
                                                                            $objCustomer, $objPurchase,
                                                                            $objSiteSess, $objCartSess)));
                        Application::alias('eccube.response')->actionExit();
                    }

                    Application::alias('eccube.response')->sendRedirect(
                            $this->getNextLocation($this->cartKey, $this->tpl_uniqid,
                                                   $objCustomer, $objPurchase,
                                                   $objSiteSess));
                    Application::alias('eccube.response')->actionExit();
                // ログインに失敗した場合
                } else {
                    // 仮登録の場合
                    if (Application::alias('eccube.helper.customer')->checkTempCustomer($objFormParam->getValue('login_email'))) {
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
                break;
            // お客様情報登録
            case 'nonmember_confirm':
                $this->tpl_mainpage = $nonmember_mainpage;
                $this->tpl_title = $nonmember_title;
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);

                if (Utils::isBlank($this->arrErr)) {
                    $this->lfRegistData($this->tpl_uniqid, $objPurchase, $objCustomer, $objFormParam);

                    $arrParams = $objFormParam->getHashArray();
                    $shipping_id = $arrParams['deliv_check'] == '1' ? 1 : 0;
                    $objPurchase->setShipmentItemTempForSole($objCartSess, $shipping_id);

                    $objSiteSess->setRegistFlag();

                    Application::alias('eccube.response')->sendRedirect(SHOPPING_PAYMENT_URLPATH);
                    Application::alias('eccube.response')->actionExit();
                }
                break;

            // 前のページに戻る
            case 'return':
                Application::alias('eccube.response')->sendRedirect(CART_URL);
                Application::alias('eccube.response')->actionExit();
                break;

            // 複数配送ページへ遷移
            case 'multiple':
                // 複数配送先指定が無効な場合はエラー
                if (USE_MULTIPLE_SHIPPING === false) {
                    Utils::sfDispSiteError(PAGE_ERROR, '', true);
                    Application::alias('eccube.response')->actionExit();
                }

                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);

                if (Utils::isBlank($this->arrErr)) {
                    $this->lfRegistData($this->tpl_uniqid, $objPurchase, $objCustomer, $objFormParam, true);

                    $objSiteSess->setRegistFlag();

                    Application::alias('eccube.response')->sendRedirect(MULTIPLE_URLPATH);
                    Application::alias('eccube.response')->actionExit();
                }
                $this->tpl_mainpage = $nonmember_mainpage;
                $this->tpl_title = $nonmember_title;
                break;

            // お客様情報入力ページの表示
            case 'nonmember':
                $this->tpl_mainpage = $nonmember_mainpage;
                $this->tpl_title = $nonmember_title;
                $this->lfInitParam($objFormParam);
                // ※breakなし

            default:
                // 前のページから戻ってきた場合は, お客様情報入力ページ
                if (isset($_GET['from']) && $_GET['from'] == 'nonmember') {
                    $this->tpl_mainpage = $nonmember_mainpage;
                    $this->tpl_title = $nonmember_title;
                    $this->lfInitParam($objFormParam);
                } else {
                    // 通常はログインページ
                    $this->lfInitLoginFormParam($objFormParam);
                }

                $this->setFormParams($objFormParam, $objPurchase, $this->tpl_uniqid);
                break;
        }

        // 入力値の取得
        $this->arrForm = $objFormParam->getFormParamList();

        // 記憶したメールアドレスを取得
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if (!Utils::isBlank($this->tpl_login_email)) {
            $this->tpl_login_memory = '1';
        }
    }

    /**
     * お客様情報入力時のパラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        Application::alias('eccube.helper.customer')->sfCustomerCommonParam($objFormParam, 'order_');
        Application::alias('eccube.helper.customer')->sfCustomerRegisterParam($objFormParam, false, false, 'order_');

        // 不要なパラメーターの削除
        // XXX: 共通化したことをうまく使えば、以前あった購入同時会員登録も復活出来そうですが
        $objFormParam->removeParam('order_password');
        $objFormParam->removeParam('order_password02');
        $objFormParam->removeParam('order_reminder');
        $objFormParam->removeParam('order_reminder_answer');
        $objFormParam->removeParam('order_mailmaga_flg');

        $objFormParam->addParam('別のお届け先', 'deliv_check', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        Application::alias('eccube.helper.customer')->sfCustomerCommonParam($objFormParam, 'shipping_');
    }

    /**
     * ログイン時のパラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitLoginFormParam(&$objFormParam)
    {
        $objFormParam->addParam('記憶する', 'login_memory', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('メールアドレス', 'login_email', '', 'a', array('EXIST_CHECK', 'EMAIL_CHECK', 'SPTAB_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('パスワード', 'login_pass', PASSWORD_MAX_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'SPTAB_CHECK'));

        if ($this->tpl_valid_phone_id) {
            // 携帯端末IDが登録されている場合、メールアドレス入力欄が省略される
            $arrCheck4login_email = $objFormParam->getParamSetting('login_email', 'arrCheck');
            $key = array_search('EXIST_CHECK', $arrCheck4login_email);
            unset($arrCheck4login_email[$key]);
            $objFormParam->overwriteParam('login_email', 'arrCheck', $arrCheck4login_email);
        }
    }

    /**
     * ログイン済みの場合の遷移先を取得する.
     *
     * 商品種別IDが, ダウンロード商品の場合は, 会員情報を受注一時情報に保存し,
     * 支払方法選択画面のパスを返す.
     * それ以外は, お届け先選択画面のパスを返す.
     *
     * @param  integer            $product_type_id 商品種別ID
     * @param  string             $uniqid          受注一時テーブルのユニークID
     * @param  Customer        $objCustomer     Customer インスタンス
     * @param  PurchaseHelper $objPurchase     PurchaseHelper インスタンス
     * @param  SiteSession     $objSiteSess     SiteSession インスタンス
     * @return string             遷移先のパス
     */
    public function getNextLocation($product_type_id, $uniqid, Customer &$objCustomer, &$objPurchase, SiteSession &$objSiteSess, &$objCartSess)
    {
        $objPurchase->setDefaultPurchase($uniqid, $product_type_id, $objCustomer, $objCartSess);
        switch ($product_type_id) {
            case PRODUCT_TYPE_DOWNLOAD:
                $objPurchase->unsetAllShippingTemp(true);
                $objPurchase->saveOrderTemp($uniqid, array(), $objCustomer);
                break;
            case PRODUCT_TYPE_NORMAL:
            default:
                break;
        }
        $objSiteSess->setRegistFlag();
        return 'confirm.php';
    }

    /**
     * データの一時登録を行う.
     *
     * 非会員向けの処理
     * @param integer            $uniqid       受注一時テーブルのユニークID
     * @param PurchaseHelper $objPurchase  PurchaseHelper インスタンス
     * @param Customer        $objCustomer  Customer インスタンス
     * @param FormParam       $objFormParam FormParam インスタンス
     * @param boolean            $isMultiple   複数配送の場合 true
     */
    public function lfRegistData($uniqid, &$objPurchase, Customer &$objCustomer, &$objFormParam, $isMultiple = false)
    {
        $arrParams = $objFormParam->getHashArray();

        // 注文者をお届け先とする配列を取得
        $arrShippingOwn = array();
        $objPurchase->copyFromOrder($arrShippingOwn, $arrParams);

        // 都度入力されたお届け先
        $arrShipping = $objPurchase->extractShipping($arrParams);

        if ($isMultiple) {
            $objPurchase->unsetOneShippingTemp(0);
            $objPurchase->unsetOneShippingTemp(1);
            $objPurchase->saveShippingTemp($arrShippingOwn, 0);
            if ($arrParams['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($arrShipping, 1);
            }
        } else {
            $objPurchase->unsetAllShippingTemp(true);
            if ($arrParams['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($arrShipping, 1);
            } else {
                $objPurchase->saveShippingTemp($arrShippingOwn, 0);
            }
        }

        $arrValues = $objFormParam->getDbArray();

        // 登録データの作成
        $arrValues['order_birth'] = Utils::sfGetTimestamp($arrParams['order_year'], $arrParams['order_month'], $arrParams['order_day']);
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
        $arrValues['customer_id'] = '0';
        $objPurchase->saveOrderTemp($uniqid, $arrValues, $objCustomer);
    }

    /**
     * 入力内容のチェックを行う.
     *
     * 追加の必須チェック, 相関チェックを行うため, CheckError を使用する.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return array        エラー情報の配
     */
    public function lfCheckError(&$objFormParam)
    {
        $arrParams = $objFormParam->getHashArray();

        $objErr = Application::alias('eccube.helper.customer')->sfCustomerCommonErrorCheck($objFormParam, 'order_');

        // 別のお届け先チェック
        if (isset($arrParams['deliv_check']) && $arrParams['deliv_check'] == '1') {
            $objErr2 = Application::alias('eccube.helper.customer')->sfCustomerCommonErrorCheck($objFormParam, 'shipping_');
            $objErr->arrErr = array_merge((array) $objErr->arrErr, (array) $objErr2->arrErr);
        } else {
            // shipping系のエラーは無視
            foreach ($objErr->arrErr as $key => $val) {
                if (substr($key, 0, strlen('shipping_')) == 'shipping_') {
                    unset($objErr->arrErr[$key]);
                }
            }
        }

        // 複数項目チェック
        $objErr->doFunc(array('生年月日', 'order_year', 'order_month', 'order_day'), array('CHECK_BIRTHDAY'));
        $objErr->doFunc(array('メールアドレス', 'メールアドレス（確認）', 'order_email', 'order_email02'), array('EQUAL_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * 入力済みの購入情報をフォームに設定する.
     *
     * 受注一時テーブル, セッションの配送情報から入力済みの購入情報を取得し,
     * フォームに設定する.
     *
     * @param  FormParam       $objFormParam FormParam インスタンス
     * @param  PurchaseHelper $objPurchase  PurchaseHelper インスタンス
     * @param  integer            $uniqid       購入一時情報のユニークID
     * @return void
     */
    public function setFormParams(&$objFormParam, &$objPurchase, $uniqid)
    {
        $arrOrderTemp = $objPurchase->getOrderTemp($uniqid);
        if (Utils::isBlank($arrOrderTemp)) {
            $arrOrderTemp = array(
                'order_email' => '',
                'order_birth' => '',
            );
        }
        $arrShippingTemp = $objPurchase->getShippingTemp();

        $objFormParam->setParam($arrOrderTemp);
        /*
         * count($arrShippingTemp) > 1 は複数配送であり,
         * $arrShippingTemp[0] は注文者が格納されている
         */
        if (count($arrShippingTemp) > 1) {
            $objFormParam->setParam($arrShippingTemp[1]);
        } else {
            if ($arrOrderTemp['deliv_check'] == 1) {
                $objFormParam->setParam($arrShippingTemp[1]);
            } else {
                $objFormParam->setParam($arrShippingTemp[0]);
            }
        }
        $objFormParam->setValue('order_email02', $arrOrderTemp['order_email']);
        $objFormParam->setDBDate($arrOrderTemp['order_birth'], 'order_year', 'order_month', 'order_day');
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
