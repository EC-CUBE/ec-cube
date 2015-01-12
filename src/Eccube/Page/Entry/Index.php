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

namespace Eccube\Page\Entry;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Customer;
use Eccube\Framework\Date;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\MobileUserAgent;
use Eccube\Framework\Response;
use Eccube\Framework\SendMail;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\SiteView;

/**
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    /**
     * Page を初期化する.
     * @return void
     */
    public function init()
    {
        parent::init();
        $masterData         = Application::alias('eccube.db.master_data');
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrJob       = $masterData->getMasterData('mtb_job');
        $this->arrReminder  = $masterData->getMasterData('mtb_reminder');
        $this->arrCountry   = $masterData->getMasterData('mtb_country');
        $this->arrSex       = $masterData->getMasterData('mtb_sex');
        $this->arrMAILMAGATYPE = $masterData->getMasterData('mtb_mail_magazine_type');

        // 生年月日選択肢の取得
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date', BIRTH_YEAR, date('Y'));
        $this->arrYear      = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth     = $objDate->getMonth(true);
        $this->arrDay       = $objDate->getDay(true);

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
     * Page のプロセス
     * @return void
     */
    public function action()
    {
        //決済処理中ステータスのロールバック
        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        $objFormParam = Application::alias('eccube.form_param');

        // PC時は規約ページからの遷移でなければエラー画面へ遷移する
        if ($this->lfCheckReferer() === false) {
            Utils::sfDispSiteError(PAGE_ERROR, '', true);
        }

        Application::alias('eccube.helper.customer')->sfCustomerEntryParam($objFormParam);
        $objFormParam->setParam($_POST);

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST['return'])) {
            $_REQUEST['mode'] = 'return';
        }

        switch ($this->getMode()) {
            case 'confirm':
                if (isset($_POST['submit_address'])) {
                    // 入力エラーチェック
                    $this->arrErr = $this->lfCheckError($_POST);
                    // 入力エラーの場合は終了
                    if (count($this->arrErr) == 0) {
                        // 郵便番号検索文作成
                        $zipcode = $_POST['zip01'] . $_POST['zip02'];

                        // 郵便番号検索
                        $arrAdsList = Utils::sfGetAddress($zipcode);

                        // 郵便番号が発見された場合
                        if (!empty($arrAdsList)) {
                            $data['pref'] = $arrAdsList[0]['state'];
                            $data['addr01'] = $arrAdsList[0]['city']. $arrAdsList[0]['town'];
                            $objFormParam->setParam($data);

                            // 該当無し
                        } else {
                            $this->arrErr['zip01'] = '※該当する住所が見つかりませんでした。<br>';
                        }
                    }
                    break;
                }

                //-- 確認
                $this->arrErr = Application::alias('eccube.helper.customer')->sfCustomerEntryErrorCheck($objFormParam);
                // 入力エラーなし
                if (empty($this->arrErr)) {
                    //パスワード表示
                    $this->passlen      = Utils::sfPassLen(strlen($objFormParam->getValue('password')));

                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title    = '会員登録(確認ページ)';
                }
                break;
            case 'complete':
                //-- 会員登録と完了画面
                $this->arrErr = Application::alias('eccube.helper.customer')->sfCustomerEntryErrorCheck($objFormParam);
                if (empty($this->arrErr)) {
                    $uniqid             = $this->lfRegistCustomerData($this->lfMakeSqlVal($objFormParam));

                    $this->lfSendMail($uniqid, $objFormParam->getHashArray());

                    // 仮会員が無効の場合
                    if (CUSTOMER_CONFIRM_MAIL == false) {
                        // ログイン状態にする
                        /* @var $objCustomer Customer */
                        $objCustomer = Application::alias('eccube.customer');
                        $objCustomer->setLogin($objFormParam->getValue('email'));
                    }

                    // 完了ページに移動させる。
                    Application::alias('eccube.response')->sendRedirect('complete.php', array('ci' => Application::alias('eccube.helper.customer')->sfGetCustomerId($uniqid)));
                }
                break;
            case 'return':
                // quiet.
                break;
            default:
                break;
        }
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * 会員情報の登録
     *
     * @access private
     * @return uniqid
     */
    public function lfRegistCustomerData($sqlval)
    {
        Application::alias('eccube.helper.customer')->sfEditCustomerData($sqlval);

        return $sqlval['secret_key'];
    }

    /**
     * 会員登録に必要なSQLパラメーターの配列を生成する.
     *
     * フォームに入力された情報を元に, SQLパラメーターの配列を生成する.
     * モバイル端末の場合は, email を email_mobile にコピーし,
     * mobile_phone_id に携帯端末IDを格納する.
     *
     * @param FormParam $objFormParam
     * @access private
     * @return $arrResults
     */
    public function lfMakeSqlVal(&$objFormParam)
    {
        $arrForm                = $objFormParam->getHashArray();
        $arrResults             = $objFormParam->getDbArray();

        // 生年月日の作成
        $arrResults['birth']    = Utils::sfGetTimestamp($arrForm['year'], $arrForm['month'], $arrForm['day']);

        // 仮会員 1 本会員 2
        $arrResults['status']   = (CUSTOMER_CONFIRM_MAIL == true) ? '1' : '2';

        /*
         * secret_keyは、テーブルで重複許可されていない場合があるので、
         * 本会員登録では利用されないがセットしておく。
         */
        $arrResults['secret_key'] = Application::alias('eccube.helper.customer')->sfGetUniqSecretKey();

        // 入会時ポイント
        $CONF = Application::alias('eccube.helper.db')->getBasisData();
        $arrResults['point'] = $CONF['welcome_point'];

        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
            // 携帯メールアドレス
            $arrResults['email_mobile']     = $arrResults['email'];
            // PHONE_IDを取り出す
            $arrResults['mobile_phone_id']  =  MobileUserAgent::getId();
        }

        return $arrResults;
    }

    /**
     * 会員登録完了メール送信する
     *
     * @access private
     * @return void
     */
    public function lfSendMail($uniqid, $arrForm)
    {
        $CONF           = Application::alias('eccube.helper.db')->getBasisData();

        $objMailText    = new SiteView();
        $objMailText->setPage($this);
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign('name01', $arrForm['name01']);
        $objMailText->assign('name02', $arrForm['name02']);
        $objMailText->assign('uniqid', $uniqid);
        $objMailText->assignobj($this);

        /* @var $objHelperMail MailHelper */
        $objHelperMail = Application::alias('eccube.helper.mail');
        $objHelperMail->setPage($this);

        // 仮会員が有効の場合
        if (CUSTOMER_CONFIRM_MAIL == true) {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご確認');
            $toCustomerMail = $objMailText->fetch('mail_templates/customer_mail.tpl');
        } else {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご完了');
            $toCustomerMail = $objMailText->fetch('mail_templates/customer_regist_mail.tpl');
        }

        /* @var $objMail Sendmail */
        $objMail = Application::alias('eccube.sendmail');
        $objMail->setItem(
            '',                     // 宛先
            $subject,               // サブジェクト
            $toCustomerMail,        // 本文
            $CONF['email03'],       // 配送元アドレス
            $CONF['shop_name'],     // 配送元 名前
            $CONF['email03'],       // reply_to
            $CONF['email04'],       // return_path
            $CONF['email04'],       // Errors_to
            $CONF['email01']        // Bcc
        );
        // 宛先の設定
        $objMail->setTo($arrForm['email'],
                        $arrForm['name01'] . $arrForm['name02'] .' 様');

        $objMail->sendMail();
    }

    /**
     * kiyaku.php からの遷移の妥当性をチェックする
     *
     * 以下の内容をチェックし, 妥当であれば true を返す.
     * 1. 規約ページからの遷移かどうか
     * 2. PC及びスマートフォンかどうか
     * 3. 自分自身(会員登録ページ)からの遷移はOKとする
     *
     * @access protected
     * @return boolean kiyaku.php からの妥当な遷移であれば true
     */
    public function lfCheckReferer()
    {
        $arrRefererParseUrl = parse_url($_SERVER['HTTP_REFERER']);
        $referer_urlpath = $arrRefererParseUrl['path'];

        $kiyaku_urlpath = ROOT_URLPATH . 'entry/kiyaku.php';

        $arrEntryParseUrl = parse_url(ENTRY_URL);
        $entry_urlpath = $arrEntryParseUrl['path'];

        $allowed_urlpath = array(
            $kiyaku_urlpath,
            $entry_urlpath,
        );

        if (Application::alias('eccube.display')->detectDevice() !== DEVICE_TYPE_MOBILE
            && !in_array($referer_urlpath, $allowed_urlpath)) {
            return false;
        }

        return true;
    }

    /**
     * 入力エラーのチェック.
     *
     * @param  array $arrRequest リクエスト値($_GET)
     * @return array $arrErr エラーメッセージ配列
     */
    public function lfCheckError($arrRequest)
    {
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $objFormParam->addParam('郵便番号1', 'zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_COUNT_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_COUNT_CHECK', 'NUM_CHECK'));
        // // リクエスト値をセット
        $arrData['zip01'] = $arrRequest['zip01'];
        $arrData['zip02'] = $arrRequest['zip02'];
        $objFormParam->setParam($arrData);
        // エラーチェック
        $arrErr = $objFormParam->checkError();

        return $arrErr;
    }
}
