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

namespace Eccube\Page\Contact;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;

/**
 * お問い合わせ のページクラス.
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
        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
            $this->tpl_title = 'お問い合わせ';
        } else {
            $this->tpl_title = 'お問い合わせ(入力ページ)';
        }
        $this->httpCacheControl('nocache');

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');

        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
            // @deprecated EC-CUBE 2.11 テンプレート互換用
            $this->CONF = Application::alias('eccube.helper.db')->getBasisData();
        }
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
        $objFormParam = Application::alias('eccube.form_param');

        $this->arrData = isset($_SESSION['customer']) ? $_SESSION['customer'] : '';

        switch ($this->getMode()) {
            case 'confirm':
                // エラーチェック
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $objFormParam->toLower('email02');
                $this->arrErr = $this->lfCheckError($objFormParam);
                // 入力値の取得
                $this->arrForm = $objFormParam->getFormParamList();

                if (Utils::isBlank($this->arrErr)) {
                    // エラー無しで完了画面
                    $this->tpl_mainpage = 'contact/confirm.tpl';
                    $this->tpl_title = 'お問い合わせ(確認ページ)';
                }

                break;

            case 'return':
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrForm = $objFormParam->getFormParamList();

                break;

            case 'complete':
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $objFormParam->checkError();
                $this->arrForm = $objFormParam->getFormParamList();
                if (Utils::isBlank($this->arrErr)) {
                    $this->lfSendMail($this);

                    // 完了ページへ移動する
                    Application::alias('eccube.response')->sendRedirect('complete.php');
                    Application::alias('eccube.response')->actionExit();
                } else {
                    Utils::sfDispSiteError(CUSTOMER_ERROR);
                    Application::alias('eccube.response')->actionExit();
                }
                break;

            default:
                break;
        }

    }

    /**
     * お問い合わせ入力時のパラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('お名前(姓)', 'name01', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お名前(名)', 'name02', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お名前(フリガナ・姓)', 'kana01', STEXT_LEN, 'KVCa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam('お名前(フリガナ・名)', 'kana02', STEXT_LEN, 'KVCa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'KANA_CHECK'));
        $objFormParam->addParam('郵便番号1', 'zip01', ZIP01_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip02', ZIP02_LEN, 'n', array('SPTAB_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('都道府県', 'pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('住所1', 'addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('住所2', 'addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お問い合わせ内容', 'contents', MLTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メールアドレス', 'email', null, 'KVa', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('メールアドレス(確認)', 'email02', null, 'KVa', array('EXIST_CHECK', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('お電話番号1', 'tel01', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お電話番号2', 'tel02', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お電話番号3', 'tel03', TEL_ITEM_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 入力内容のチェックを行なう.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return array        入力チェック結果の配列
     */
    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrForm =  $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrForm);
        $objErr->arrErr = $objFormParam->checkError();
        $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', 'email', 'email02'), array('EQUAL_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * メールの送信を行う。
     *
     * @param LC_Page_Contact $objPage
     * @return void
     */
    public function lfSendMail(&$objPage)
    {
        $CONF = Application::alias('eccube.helper.db')->getBasisData();
        $objPage->tpl_shopname = $CONF['shop_name'];
        $objPage->tpl_infoemail = $CONF['email02'];
        /* @var $helperMail MailHelper */
        $helperMail = Application::alias('eccube.helper.mail');
        $helperMail->setPage($this);
        $helperMail->sfSendTemplateMail(
            $objPage->arrForm['email']['value'],            // to
            $objPage->arrForm['name01']['value'] .' 様',    // to_name
            5,                                              // template_id
            $objPage,                                       // objPage
            $CONF['email03'],                               // from_address
            $CONF['shop_name'],                             // from_name
            $CONF['email02'],                               // reply_to
            $CONF['email02']                                // bcc
        );
    }
}
