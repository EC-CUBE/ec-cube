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
use Eccube\Framework\Display;
use Eccube\Framework\MobileUserAgent;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\MobileHelper;
use Eccube\Framework\Helper\PaymentHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Util\Utils;

/**
 * 購入履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class History extends AbstractMypage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mypageno     = 'index';
        $this->tpl_subtitle     = '購入履歴詳細';
        $this->httpCacheControl('nocache');

        $masterData             = Application::alias('eccube.db.master_data');
        $this->arrMAILTEMPLATE  = $masterData->getMasterData('mtb_mail_template');
        $this->arrPref          = $masterData->getMasterData('mtb_pref');
        $this->arrCountry       = $masterData->getMasterData('mtb_country');
        $this->arrWDAY          = $masterData->getMasterData('mtb_wday');
        $this->arrProductType   = $masterData->getMasterData('mtb_product_type');
        $this->arrCustomerOrderStatus = $masterData->getMasterData('mtb_customer_order_status');
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
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        //決済処理中ステータスのロールバック
        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');

        if (!Utils::sfIsInt($_GET['order_id'])) {
            Utils::sfDispSiteError(CUSTOMER_ERROR);
        }

        $order_id               = $_GET['order_id'];
        $this->is_price_change  = false;

        //受注データの取得
        $this->tpl_arrOrderData = $objPurchase->getOrder($order_id, $objCustomer->getValue('customer_id'));

        if (empty($this->tpl_arrOrderData)) {
            Utils::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->arrShipping      = $this->lfGetShippingDate($objPurchase, $order_id, $this->arrWDAY);

        $this->isMultiple       = count($this->arrShipping) > 1;
        // 支払い方法の取得
        $this->arrPayment       = Application::alias('eccube.helper.payment')->getIDValueList();
        // 受注商品明細の取得
        $this->tpl_arrOrderDetail = $objPurchase->getOrderDetail($order_id);
        foreach ($this->tpl_arrOrderDetail as $product_index => $arrOrderProductDetail) {
            //必要なのは商品の販売金額のみなので、遅い場合は、別途SQL作成した方が良い
            $arrTempProductDetail = $objProduct->getProductsClass($arrOrderProductDetail['product_class_id']);
            // 税計算
            $this->tpl_arrOrderDetail[$product_index]['price_inctax'] = $this->tpl_arrOrderDetail[$product_index]['price']  +
                TaxRuleHelper::calcTax(
                    $this->tpl_arrOrderDetail[$product_index]['price'],
                    $this->tpl_arrOrderDetail[$product_index]['tax_rate'],
                    $this->tpl_arrOrderDetail[$product_index]['tax_rule']
                    );
            $arrTempProductDetail['price02_inctax'] = TaxRuleHelper::sfCalcIncTax(
                    $arrTempProductDetail['price02'],
                    $arrTempProductDetail['product_id'],
                    $arrTempProductDetail['product_class_id']
                    );
            if ($this->tpl_arrOrderDetail[$product_index]['price_inctax'] != $arrTempProductDetail['price02_inctax']) {
                $this->is_price_change = true;
            }
            $this->tpl_arrOrderDetail[$product_index]['product_price_inctax'] = ($arrTempProductDetail['price02_inctax']) ? $arrTempProductDetail['price02_inctax'] : 0 ;
        }

        $this->tpl_arrOrderDetail = $this->setMainListImage($this->tpl_arrOrderDetail);
        $objPurchase->setDownloadableFlgTo($this->tpl_arrOrderDetail);
        // モバイルダウンロード対応処理
        $this->lfSetAU($this->tpl_arrOrderDetail);
        // 受注メール送信履歴の取得
        $this->tpl_arrMailHistory = $this->lfGetMailHistory($order_id);
    }

    /**
     * 受注メール送信履歴の取得
     *
     * @param  integer $order_id 注文番号
     * @return array   受注メール送信履歴の内容
     */
    public function lfGetMailHistory($order_id)
    {
        $objQuery   = Application::alias('eccube.query');
        $col        = 'send_date, subject, template_id, send_id';
        $where      = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');

        return $objQuery->select($col, 'dtb_mail_history', $where, array($order_id));
    }

    /**
     * 受注お届け先情報の取得
     *
     * @param PurchaseHelper $objPurchase object PurchaseHelperクラス
     * @param $order_id integer 注文番号
     * @param $arrWDAY array 曜日データの配列
     * @return array お届け先情報
     */
    public function lfGetShippingDate(&$objPurchase, $order_id, $arrWDAY)
    {
        $arrShipping = $objPurchase->getShippings($order_id);

        foreach ($arrShipping as $shipping_index => $shippingData) {
            foreach ($shippingData as $key => $val) {
                if ($key == 'shipping_date' && Utils::isBlank($val) == false) {
                    // お届け日を整形
                    list($y, $m, $d, $w) = explode(' ', date('Y m d w', strtotime($val)));
                    $arrShipping[$shipping_index]['shipping_date'] = sprintf('%04d/%02d/%02d(%s)', $y, $m, $d, $arrWDAY[$w]);
                }
            }
        }

        return $arrShipping;
    }

    /**
     * 購入履歴商品に画像をセット
     *
     * @param $arrOrderDetail 購入履歴の配列
     * @return array 画像をセットした購入履歴の配列
     */
    public function setMainListImage($arrOrderDetails)
    {
        $i = 0;
        foreach ($arrOrderDetails as $arrOrderDetail) {
            $objQuery = Application::alias('eccube.query');
            $arrProduct = $objQuery->select('main_list_image', 'dtb_products', 'product_id = ?', array($arrOrderDetail['product_id']));
            $arrOrderDetails[$i]['main_list_image'] = $arrProduct[0]['main_list_image'];
            $i++;
        }

        return $arrOrderDetails;
    }

    /**
     * 購入履歴商品にMIMETYPE、ファイル名をセット
     *
     * @param $arrOrderDetail 購入履歴の配列
     * @return array MIMETYPE、ファイル名をセットした購入履歴の配列
     */
    public function lfSetMimetype($arrOrderDetails)
    {
        /* @var $objHelperMobile MobileHelper */
        $objHelperMobile = Application::alias('eccube.helper.mobile');
        $i = 0;
        foreach ($arrOrderDetails as $arrOrderDetail) {
            $objQuery = Application::alias('eccube.query');
            $arrProduct = $objQuery->select('down_realfilename,down_filename', 'dtb_products_class', 'product_id = ? AND product_class_id = ?', array($arrOrderDetail['product_id'],$arrOrderDetail['product_class_id']));
            $arrOrderDetails[$i]['mime_type'] = $objHelperMobile->getMimeType($arrProduct[0]['down_realfilename']);
            $arrOrderDetails[$i]['down_filename'] = $arrProduct[0]['down_filename'];
            $i++;
        }

        return $arrOrderDetails;
    }

    /**
     * 特定キャリア（AU）モバイルダウンロード処理
     * キャリアがAUのモバイル端末からダウンロードする場合は単純に
     * Aタグでダウンロードできないケースがある為、対応する。
     *
     * @param $arrOrderDetail 購入履歴の配列
     */
    public function lfSetAU($arrOrderDetails)
    {
        $this->isAU = false;
        // モバイル端末かつ、キャリアがAUの場合に処理を行う
        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE && MobileUserAgent::getCarrier() == 'ezweb') {
            // MIMETYPE、ファイル名のセット
            $this->tpl_arrOrderDetail = $this->lfSetMimetype($arrOrderDetails);

            // @deprecated 2.12.0 PHP 定数 SID を使うこと
            $this->phpsessid = $_GET['PHPSESSID'];

            $this->isAU = true;
        }
    }
}
