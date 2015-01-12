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

namespace Eccube\Page\Admin\Order;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\PageNavi;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\PaymentHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\DB\MasterData;

/**
 * 対応状況管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Status extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/status.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'status';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '対応状況管理';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrORDERSTATUS = $masterData->getMasterData('mtb_order_status');
        $this->arrORDERSTATUS_COLOR = $masterData->getMasterData('mtb_order_status_color');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
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
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');

        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        // 入力値の変換
        $objFormParam->convParam();

        $this->arrForm = $objFormParam->getHashArray();

        //支払方法の取得
        $this->arrPayment = Application::alias('eccube.helper.payment')->getIDValueList();

        switch ($this->getMode()) {
            case 'update':
                switch ($objFormParam->getValue('change_status')) {
                    // 削除
                    case 'delete':
                        $this->lfDelete($objFormParam->getValue('move'));
                        break;
                    // 更新
                    default:
                        $this->lfStatusMove($objFormParam->getValue('change_status'), $objFormParam->getValue('move'));
                        break;
                }
                break;

            case 'search':
            default:
                break;
        }

        // 対応状況
        $status = $objFormParam->getValue('status');
        if (strlen($status) === 0) {
                //デフォルトで新規受付一覧表示
                $status = ORDER_NEW;
        }
        $this->SelectedStatus = $status;
        //検索結果の表示
        $this->lfStatusDisp($status, $objFormParam->getValue('search_pageno'));
    }

    /**
     *  パラメーター情報の初期化
     *  @param FormParam
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('注文番号', 'order_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('変更前対応状況', 'status', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ページ番号', 'search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        if ($this->getMode() == 'update') {
            $objFormParam->addParam('変更後対応状況', 'change_status', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $objFormParam->addParam('移動注文番号', 'move', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        }
    }

    /**
     *  入力内容のチェック
     *  @param FormParam
     */
    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrRet = $objFormParam->getHashArray();
        $arrErr = $objFormParam->checkError();
        if (is_null($objFormParam->getValue('search_pageno'))) {
            $objFormParam->setValue('search_pageno', 1);
        }
    }

    // 対応状況一覧の表示
    public function lfStatusDisp($status,$pageno)
    {
        $objQuery = Application::alias('eccube.query');

        $select ='*';
        $from = 'dtb_order';
        $where = 'del_flg = 0 AND status = ?';
        $arrWhereVal = array($status);
        $order = 'order_id DESC';

        $linemax = $objQuery->count($from, $where, $arrWhereVal);
        $this->tpl_linemax = $linemax;

        // ページ送りの処理
        $page_max = ORDER_STATUS_MAX;

        // ページ送りの取得
        /* @var $objNavi PageNavi */
        $objNavi = Application::alias('eccube.page_navi', $pageno, $linemax, $page_max, 'eccube.moveSearchPage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
        $startno = $objNavi->start_row;

        $this->tpl_pageno = $pageno;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);

        //表示順序
        $objQuery->setOrder($order);

        //検索結果の取得
        $this->arrStatus = $objQuery->select($select, $from, $where, $arrWhereVal);
    }

    /**
     * 対応状況の更新
     */
    public function lfStatusMove($statusId, $arrOrderId)
    {
        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        $objQuery = Application::alias('eccube.query');

        if (!isset($arrOrderId) || !is_array($arrOrderId)) {
            return false;
        }
        $masterData = Application::alias('eccube.db.master_data');
        $arrORDERSTATUS = $masterData->getMasterData('mtb_order_status');

        $objQuery->begin();

        foreach ($arrOrderId as $orderId) {
            $objPurchase->sfUpdateOrderStatus($orderId, $statusId);
        }

        $objQuery->commit();

        $this->tpl_onload = "window.alert('選択項目を" . $arrORDERSTATUS[$statusId] . "へ移動しました。');";

        return true;
    }

    /**
     * 受注テーブルの論理削除
     */
    public function lfDelete($arrOrderId)
    {
        $objQuery = Application::alias('eccube.query');

        if (!isset($arrOrderId) || !is_array($arrOrderId)) {
            return false;
        }

        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        foreach ($arrOrderId as $orderId) {
            $objPurchase->cancelOrder($orderId, ORDER_CANCEL, true);
        }

        $this->tpl_onload = "window.alert('選択項目を削除しました。');";

        return true;
    }
}
