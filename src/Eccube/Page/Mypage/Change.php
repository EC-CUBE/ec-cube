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

namespace Eccube\Page\Mypage;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Util\Utils;

/**
 * 登録内容変更 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Change extends AbstractMypage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_subtitle = '会員登録内容変更(入力ページ)';
        $this->tpl_mypageno = 'change';

        $masterData         = Application::alias('eccube.db.master_data');
        $this->arrReminder  = $masterData->getMasterData('mtb_reminder');
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrCountry   = $masterData->getMasterData('mtb_country');
        $this->arrJob       = $masterData->getMasterData('mtb_job');
        $this->arrMAILMAGATYPE = $masterData->getMasterData('mtb_mail_magazine_type');
        $this->arrSex       = $masterData->getMasterData('mtb_sex');
        $this->httpCacheControl('nocache');

        // 生年月日選択肢の取得
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date', BIRTH_YEAR, date('Y'));
        $this->arrYear      = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth     = $objDate->getMonth(true);
        $this->arrDay       = $objDate->getDay(true);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
    }

    /**
     * Page のプロセス
     * @return void
     */
    public function action()
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        $customer_id = $objCustomer->getValue('customer_id');

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST['return'])) {
            $_REQUEST['mode'] = 'return';
        }

        // パラメーター管理クラス,パラメーター情報の初期化
        $objFormParam = Application::alias('eccube.form_param');
        Application::alias('eccube.helper.customer')->sfCustomerMypageParam($objFormParam);
        $objFormParam->setParam($_POST);    // POST値の取得

        switch ($this->getMode()) {
            // 確認
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
                $this->arrErr = Application::alias('eccube.helper.customer')->sfCustomerMypageErrorCheck($objFormParam);

                // 入力エラーなし
                if (empty($this->arrErr)) {
                    //パスワード表示
                    $this->passlen      = Utils::sfPassLen(strlen($objFormParam->getValue('password')));

                    $this->tpl_mainpage = 'mypage/change_confirm.tpl';
                    $this->tpl_title    = '会員登録(確認ページ)';
                    $this->tpl_subtitle = '会員登録内容変更(確認ページ)';
                }
                break;
            // 会員登録と完了画面
            case 'complete':
                $this->arrErr = Application::alias('eccube.helper.customer')->sfCustomerMypageErrorCheck($objFormParam);

                // 入力エラーなし
                if (empty($this->arrErr)) {
                    // 会員情報の登録
                    $this->lfRegistCustomerData($objFormParam, $customer_id);

                    //セッション情報を最新の状態に更新する
                    $objCustomer->updateSession();

                    // 完了ページに移動させる。
                    Application::alias('eccube.response')->sendRedirect('change_complete.php');
                }
                break;
            // 確認ページからの戻り
            case 'return':
                // quiet.
                break;
            default:
                $objFormParam->setParam(Application::alias('eccube.helper.customer')->sfGetCustomerData($customer_id));
                break;
        }
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     *  会員情報を登録する
     *
     * @param FormParam $objFormParam
     * @param mixed $customer_id
     * @access private
     * @return void
     */
    public function lfRegistCustomerData(&$objFormParam, $customer_id)
    {
        $arrRet             = $objFormParam->getHashArray();
        $sqlval             = $objFormParam->getDbArray();
        $sqlval['birth']    = Utils::sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);

        Application::alias('eccube.helper.customer')->sfEditCustomerData($sqlval, $customer_id);
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
