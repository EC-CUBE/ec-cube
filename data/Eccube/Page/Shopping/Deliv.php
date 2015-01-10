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

use Eccube\Page\AbstractPage;
use Eccube\Common\Customer;
use Eccube\Common\CartSession;
use Eccube\Common\SiteSession;
use Eccube\Common\FormParam;
use Eccube\Common\Response;
use Eccube\Common\Helper\AddressHelper;
use Eccube\Common\Helper\PurchaseHelper;
use Eccube\Common\DB\MasterData;
use Eccube\Common\Util\Utils;

/**
 * お届け先の指定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Deliv extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $masterData = new MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrCountry = $masterData->getMasterData('mtb_country');
        $this->tpl_title = 'お届け先の指定';
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
        $objPurchase = new PurchaseHelper();
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        $objSiteSess = new SiteSession();
        $objCartSess = new CartSession();
        $objCustomer = new Customer();
        $objFormParam = new FormParam();
        $objAddress = new AddressHelper();

        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // ログインチェック
        if (!$objCustomer->isLoginSuccess(true)) {
            Utils::sfDispSiteError(CUSTOMER_ERROR);
        }

        // ダウンロード商品の場合は、支払方法画面に転送
        if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
            $objPurchase->copyFromCustomer($sqlval, $objCustomer, 'shipping');
            $objPurchase->saveShippingTemp($sqlval);
            $objPurchase->saveOrderTemp($this->tpl_uniqid, $sqlval, $objCustomer);
            $objSiteSess->setRegistFlag();

            Response::sendRedirect('payment.php');
            Response::actionExit();
        }

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $arrErr = $objFormParam->checkError();
        if (!Utils::isBlank($arrErr)) {
            Utils::sfDispSiteError(PAGE_ERROR, '', true);
            Response::actionExit();
        }

        $arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
            // 削除
            case 'delete':
                if (!$objAddress->deleteAddress($arrForm['other_deliv_id'], $objCustomer->getValue('customer_id'))) {
                    Utils::sfDispSiteError(FREE_ERROR_MSG, '', false, '別のお届け先を削除できませんでした。');
                    Response::actionExit();
                }
                break;

            // 会員登録住所に送る
            case 'customer_addr':
                $objPurchase->unsetShippingTemp();

                $shipping_id = $arrForm['deliv_check'] == -1 ? 0 : $arrForm['deliv_check'];
                $success = $this->registerDeliv($shipping_id, $this->tpl_uniqid, $objPurchase, $objCustomer, $objAddress);
                if (!$success) {
                    Utils::sfDispSiteError(PAGE_ERROR, '', true);
                }

                $objPurchase->setShipmentItemTempForSole($objCartSess, $shipping_id);
                $objSiteSess->setRegistFlag();

                Response::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                Response::actionExit();
                break;

            // 前のページに戻る
            case 'return':

                // 確認ページへ移動
                Response::sendRedirect(CART_URL);
                Response::actionExit();
                break;

            // お届け先複数指定
            case 'multiple':
                // 複数配送先指定が無効な場合はエラー
                if (USE_MULTIPLE_SHIPPING === false) {
                    Utils::sfDispSiteError(PAGE_ERROR, '', true);
                    Response::actionExit();
                }

                Response::sendRedirect('multiple.php');
                Response::actionExit();
                break;

            default:
                // 配送IDの取得
                $shippingData = $objPurchase->getShippingTemp();
                if (!Utils::isBlank($shippingData)) {
                    $arrShippingId = array_keys($shippingData);
                }
                if (isset($arrShippingId[0])) {
                    $this->arrForm['deliv_check']['value'] = $arrShippingId[0] == 0 ? -1 : $arrShippingId[0];
                }
                break;
        }

        // 登録済み住所を取得
        $addr = array(
            array(
                'other_deliv_id'    => NULL,
                'customer_id'       => $objCustomer->getValue('customer_id'),
                'name01'            => $objCustomer->getValue('name01'),
                'name02'            => $objCustomer->getValue('name02'),
                'kana01'            => $objCustomer->getValue('kana01'),
                'kana02'            => $objCustomer->getValue('kana02'),
                'company_name'      => $objCustomer->getValue('company_name'),
                'country_id'           => $objCustomer->getValue('country_id'),
                'zipcode'           => $objCustomer->getValue('zipcode'),
                'zip01'             => $objCustomer->getValue('zip01'),
                'zip02'             => $objCustomer->getValue('zip02'),
                'pref'              => $objCustomer->getValue('pref'),
                'addr01'            => $objCustomer->getValue('addr01'),
                'addr02'            => $objCustomer->getValue('addr02'),
                'tel01'             => $objCustomer->getValue('tel01'),
                'tel02'             => $objCustomer->getValue('tel02'),
                'tel03'             => $objCustomer->getValue('tel03'),
            )
        );
        $this->arrAddr = array_merge($addr, $objAddress->getList($objCustomer->getValue('customer_id')));
        $this->tpl_addrmax = count($this->arrAddr) - 1; // 会員の住所をカウントしない

    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('その他のお届け先ID', 'other_deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お届け先チェック', 'deliv_check', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
    }

    /**
     * お届け先チェックの値に応じて, お届け先情報を保存する.
     *
     * 会員住所がチェックされている場合は, 会員情報からお届け先を取得する.
     * その他のお届け先がチェックされている場合は, その他のお届け先からお届け先を取得する.
     * お届け先チェックの値が不正な場合は false を返す.
     *
     * @param  integer            $other_deliv_id
     * @param  string             $uniqid         受注一時テーブルのユニークID
     * @param  PurchaseHelper $objPurchase    PurchaseHelper インスタンス
     * @param  Customer        $objCustomer    Customer インスタンス
     * @param AddressHelper $objAddress
     * @return boolean            お届け先チェックの値が妥当な場合 true
     */
    public function registerDeliv($other_deliv_id, $uniqid, &$objPurchase, Customer &$objCustomer, $objAddress)
    {
        $arrValues = array();
        // 会員登録住所がチェックされている場合
        if ($other_deliv_id == 0) {
            $objPurchase->copyFromCustomer($arrValues, $objCustomer, 'shipping');
        // 別のお届け先がチェックされている場合
        } else {
            $arrOtherDeliv = $objAddress->getAddress($other_deliv_id, $objCustomer->getValue('customer_id'));
            if (!$arrOtherDeliv) {
                return false;
            }

            $objPurchase->copyFromOrder($arrValues, $arrOtherDeliv, 'shipping', '');
        }
        $objPurchase->saveShippingTemp($arrValues, $other_deliv_id);
        $objPurchase->saveOrderTemp($uniqid, $arrValues, $objCustomer);

        return true;
    }
}
