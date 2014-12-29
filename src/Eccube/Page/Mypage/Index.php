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

use Eccube\Framework\Customer;
use Eccube\Framework\Display;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\PaymentHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\Util\Utils;

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractMypage
{
    /** ページナンバー */
    public $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mypageno = 'index';
        if (Display::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '購入履歴一覧';
        }
        $masterData = new MasterData();
        $this->arrCustomerOrderStatus = $masterData->getMasterData('mtb_customer_order_status');

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
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        //決済処理中ステータスのロールバック
        $objPurchase = new PurchaseHelper();
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        $objCustomer = new Customer();
        $customer_id = $objCustomer->getValue('customer_id');

        //ページ送り用
        $this->objNavi = new PageNavi($_REQUEST['pageno'],
                                            $this->lfGetOrderHistory($customer_id),
                                            SEARCH_PMAX,
                                            'eccube.movePage',
                                            NAVI_PMAX,
                                            'pageno=#page#',
                                            Display::detectDevice() !== DEVICE_TYPE_MOBILE);

        $this->arrOrder = $this->lfGetOrderHistory($customer_id, $this->objNavi->start_row);

        switch ($this->getMode()) {
            case 'getList':
                echo Utils::jsonEncode($this->arrOrder);
                Response::actionExit();
                break;
            default:
                break;
        }
        // 支払い方法の取得
        $this->arrPayment = PaymentHelper::getIDValueList();
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;

        $this->json_payment = Utils::jsonEncode($this->arrPayment);
        $this->json_customer_order_status = Utils::jsonEncode($this->arrCustomerOrderStatus);
    }

    /**
     * 受注履歴を返す
     *
     * @param mixed $customer_id
     * @param integer $startno     0以上の場合は受注履歴を返却する -1の場合は件数を返す
     * @access private
     * @return void
     */
    public function lfGetOrderHistory($customer_id, $startno = -1)
    {
        $objQuery   = Query::getSingletonInstance();

        $col        = 'order_id, create_date, payment_id, payment_total, status';
        $from       = 'dtb_order';
        $where      = 'del_flg = 0 AND customer_id = ?';
        $arrWhereVal = array($customer_id);
        $order      = 'order_id DESC';

        if ($startno == -1) {
            return $objQuery->count($from, $where, $arrWhereVal);
        }

        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 表示順序
        $objQuery->setOrder($order);

        //購入履歴の取得
        return $objQuery->select($col, $from, $where, $arrWhereVal);
    }
}
