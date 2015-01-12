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
use Eccube\Page\AbstractPage;
use Eccube\Framework\Customer;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\AddressHelper;
use Eccube\Framework\Util\Utils;

/**
 * お届け先追加 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class DeliveryAddr extends AbstractPage
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
        $this->tpl_title    = 'お届け先の追加･変更';
        $masterData         = Application::alias('eccube.db.master_data');
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrCountry   = $masterData->getMasterData('mtb_country');
        $this->httpCacheControl('nocache');
        $this->validUrl = array(MYPAGE_DELIVADDR_URLPATH,
                                DELIV_URLPATH,
                                MULTIPLE_URLPATH);
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
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        /* @var $objAddress AddressHelper */
        $objAddress = Application::alias('eccube.helper.address');
        $ParentPage  = MYPAGE_DELIVADDR_URLPATH;

        // GETでページを指定されている場合には指定ページに戻す
        if (isset($_GET['page'])) {
            $ParentPage = htmlspecialchars($_GET['page'], ENT_QUOTES);
        } elseif (isset($_POST['ParentPage'])) {
            $ParentPage = htmlspecialchars($_POST['ParentPage'], ENT_QUOTES);
        }

        // 正しい遷移かをチェック
        $arrParentPageList = array(DELIV_URLPATH, MYPAGE_DELIVADDR_URLPATH, MULTIPLE_URLPATH);
        if (!Utils::isBlank($ParentPage) && !in_array($ParentPage, $arrParentPageList)) {
            // 遷移が正しくない場合、デフォルトであるマイページの配送先追加の画面を設定する
            $ParentPage  = MYPAGE_DELIVADDR_URLPATH;
        }

        $this->ParentPage = $ParentPage;

        /*
         * ログイン判定 及び 退会判定
         * 未ログインでも, 複数配送設定ページからのアクセスの場合は表示する
         *
         * TODO 購入遷移とMyPageで別クラスにすべき
         */
        if (!$objCustomer->isLoginSuccess(true) && $ParentPage != MULTIPLE_URLPATH) {
            $this->tpl_onload = "eccube.changeParentUrl('". $ParentPage ."'); window.close();";
        }

        // other_deliv_id のあるなしで追加か編集か判定しているらしい
        $_SESSION['other_deliv_id'] = $_REQUEST['other_deliv_id'];

        // パラメーター管理クラス,パラメーター情報の初期化
        $objFormParam   = Application::alias('eccube.form_param');
        $objAddress->setFormParam($objFormParam);
        $objFormParam->setParam($_POST);

        switch ($this->getMode()) {
            // 入力は必ずedit
            case 'edit':
                $this->arrErr = $objAddress->errorCheck($objFormParam);
                // 入力エラーなし
                if (empty($this->arrErr)) {
                    // TODO ここでやるべきではない
                    if (in_array($_POST['ParentPage'], $this->validUrl)) {
                        $this->tpl_onload = "eccube.changeParentUrl('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
                    } else {
                        Utils::sfDispSiteError(CUSTOMER_ERROR);
                    }

                    if ($objCustomer->isLoginSuccess(true)) {
                        $this->lfRegistData($objAddress, $objFormParam, $objCustomer->getValue('customer_id'));
                    } else {
                        $this->lfRegistDataNonMember($objFormParam);
                    }

                    if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_MOBILE) {
                        // モバイルの場合、元のページに遷移
                        Application::alias('eccube.response')->sendRedirect($this->getLocation($_POST['ParentPage']));
                        Application::alias('eccube.response')->actionExit();
                    }
                }
                break;
            case 'multiple':
                // 複数配送先用
                break;
            default :

                if ($_GET['other_deliv_id'] != '') {
                    $arrOtherDeliv = $objAddress->getAddress($_SESSION['other_deliv_id'], $objCustomer->getValue('customer_id'));

                    //不正アクセス判定
                    if (!$objCustomer->isLoginSuccess(true) || !$arrOtherDeliv) {
                        Utils::sfDispSiteError(CUSTOMER_ERROR);
                    }

                    //別のお届け先情報取得
                    $objFormParam->setParam($arrOtherDeliv);
                }
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();
        if (Application::alias('eccube.display')->detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_mainpage = 'mypage/delivery_addr.tpl';
        } else {
            $this->setTemplate('mypage/delivery_addr.tpl');
        }

    }

    /* 登録実行 */

    /**
     * @param AddressHelper $objAddress
     * @param FormParam $objFormParam
     */
    public function lfRegistData(AddressHelper $objAddress, FormParam $objFormParam, $customer_id)
    {
        $arrRet     = $objFormParam->getHashArray();
        $sqlval     = $objFormParam->getDbArray();

        $sqlval['other_deliv_id'] = $arrRet['other_deliv_id'];
        $sqlval['customer_id'] = $customer_id;

        if (!$objAddress->registAddress($sqlval)) {
            Utils::sfDispSiteError(FREE_ERROR_MSG, '', false, '別のお届け先を登録できませんでした。');
            Application::alias('eccube.response')->actionExit();
        }
    }

    /**
     * @param FormParam $objFormParam
     */
    public function lfRegistDataNonMember($objFormParam)
    {
        $arrRegistColumn = $objFormParam->getDbArray();
        foreach ($arrRegistColumn as $key => $val) {
            $arrRegist['shipping_' . $key ] = $val;
        }
        if (count($_SESSION['shipping']) >= DELIV_ADDR_MAX) {
            Utils::sfDispSiteError(FREE_ERROR_MSG, '', false, '別のお届け先最大登録数に達しています。');
        } else {
            $_SESSION['shipping'][] = $arrRegist;
        }
    }
}
