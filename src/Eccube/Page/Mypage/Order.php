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
use Eccube\Framework\CartSession;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Util\Utils;

/**
 * 受注履歴からカート遷移 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Order extends AbstractMypage
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

        //受注詳細データの取得
        $arrOrderDetail = $this->lfGetOrderDetail($_POST['order_id']);

        //ログインしていない、またはDBに情報が無い場合
        if (empty($arrOrderDetail)) {
            Utils::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->lfAddCartProducts($arrOrderDetail);
        Application::alias('eccube.response')->sendRedirect(CART_URL);
    }

    // 受注詳細データの取得
    public function lfGetOrderDetail($order_id)
    {
        $objQuery       = Application::alias('eccube.query');

        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        //customer_idを検証
        $customer_id    = $objCustomer->getValue('customer_id');
        $order_count    = $objQuery->count('dtb_order', 'order_id = ? and customer_id = ?', array($order_id, $customer_id));
        if ($order_count != 1) return array();

        $col    = 'dtb_order_detail.product_class_id, quantity';
        $table  = 'dtb_order_detail LEFT JOIN dtb_products_class ON dtb_order_detail.product_class_id = dtb_products_class.product_class_id';
        $where  = 'order_id = ?';
        $objQuery->setOrder('order_detail_id');
        $arrOrderDetail = $objQuery->select($col, $table, $where, array($order_id));

        return $arrOrderDetail;
    }

    // 商品をカートに追加
    public function lfAddCartProducts($arrOrderDetail)
    {
        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        foreach ($arrOrderDetail as $order_row) {
            $objCartSess->addProduct($order_row['product_class_id'], $order_row['quantity']);
        }
    }
}
