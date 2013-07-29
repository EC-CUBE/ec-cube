<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 購入履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_History extends LC_Page_AbstractMypage_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mypageno     = 'index';
        $this->tpl_subtitle     = '購入履歴詳細';
        $this->httpCacheControl('nocache');

        $masterData             = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE  = $masterData->getMasterData('mtb_mail_template');
        $this->arrPref          = $masterData->getMasterData('mtb_pref');
        $this->arrWDAY          = $masterData->getMasterData('mtb_wday');
        $this->arrProductType   = $masterData->getMasterData('mtb_product_type');
        $this->arrCustomerOrderStatus = $masterData->getMasterData('mtb_customer_order_status');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action()
    {
        $objCustomer    = new SC_Customer_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objProduct  = new SC_Product();

        if (!SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $order_id               = $_GET['order_id'];
        $this->is_price_change  = false;

        //受注データの取得
        $this->tpl_arrOrderData = $objPurchase->getOrder($order_id, $objCustomer->getValue('customer_id'));

        if (empty($this->tpl_arrOrderData)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->arrShipping      = $this->lfGetShippingDate($objPurchase, $order_id, $this->arrWDAY);

        $this->isMultiple       = count($this->arrShipping) > 1;
        // 支払い方法の取得
        $this->arrPayment       = SC_Helper_Payment_Ex::getIDValueList();
        // 受注商品明細の取得
        $this->tpl_arrOrderDetail = $objPurchase->getOrderDetail($order_id);
        foreach ($this->tpl_arrOrderDetail as $product_index => $arrOrderProductDetail) {
            //必要なのは商品の販売金額のみなので、遅い場合は、別途SQL作成した方が良い
            $arrTempProductDetail = $objProduct->getProductsClass($arrOrderProductDetail['product_class_id']);
            if($this->tpl_arrOrderDetail[$product_index]['price'] != $arrTempProductDetail['price02']) {
                $this->is_price_change = true;
            }
            $this->tpl_arrOrderDetail[$product_index]['product_price'] = ($arrTempProductDetail['price02'])?$arrTempProductDetail['price02']:0;
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
     * @param integer $order_id 注文番号
     * @return array 受注メール送信履歴の内容
     */
    function lfGetMailHistory($order_id)
    {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $col        = 'send_date, subject, template_id, send_id';
        $where      = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');

        return $objQuery->select($col, 'dtb_mail_history', $where, array($order_id));
    }

    /**
     * 受注お届け先情報の取得
     *
     * @param $objPurchase object SC_Helper_Purchaseクラス
     * @param $order_id integer 注文番号
     * @param $arrWDAY array 曜日データの配列
     * @return array お届け先情報
     */
    function lfGetShippingDate(&$objPurchase, $order_id, $arrWDAY)
    {
        $arrShipping = $objPurchase->getShippings($order_id);

        foreach ($arrShipping as $shipping_index => $shippingData) {
            foreach ($shippingData as $key => $val) {
                if ($key == 'shipping_date' && SC_Utils_Ex::isBlank($val) == false) {
                    // お届け日を整形
                    list($y, $m, $d, $w) = explode(' ', date('Y m d w' , strtotime($val)));
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
    function setMainListImage($arrOrderDetails)
    {
        $i = 0;
        foreach ($arrOrderDetails as $arrOrderDetail) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
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
    function lfSetMimetype($arrOrderDetails)
    {
        $objHelperMobile = new SC_Helper_Mobile_Ex();
        $i = 0;
        foreach ($arrOrderDetails as $arrOrderDetail) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
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
     * @param integer $order_id 注文番号
     * @param $arrOrderDetail 購入履歴の配列
     */
    function lfSetAU($arrOrderDetails)
    {
        $this->isAU = false;
        // モバイル端末かつ、キャリアがAUの場合に処理を行う
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE && SC_MobileUserAgent::getCarrier() == 'ezweb') {
            // MIMETYPE、ファイル名のセット
            $this->tpl_arrOrderDetail = $this->lfSetMimetype($arrOrderDetails);

            // @deprecated 2.12.0 PHP 定数 SID を使うこと
            $this->phpsessid = $_GET['PHPSESSID'];

            $this->isAU = true;
        }
    }
}
