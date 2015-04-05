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

namespace Eccube\Page\Admin\Basis;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\DeliveryHelper;
use Eccube\Framework\Helper\PaymentHelper;

/**
 * 配送方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class DeliveryInput extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/delivery_input.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type');
        $this->arrPayments = Application::alias('eccube.helper.payment')->getIDValueList();
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '配送方法設定';
        $this->mode = $this->getMode();
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
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($this->mode, $objFormParam);
        $objFormParam->setParam($_POST);

        // 入力値の変換
        $objFormParam->convParam();
        $this->arrErr = $this->lfCheckError($objFormParam);

        switch ($this->mode) {
            case 'edit':
                if (count($this->arrErr) == 0) {
                    $objFormParam->setValue('deliv_id', $this->lfRegistData($objFormParam->getHashArray(), $_SESSION['member_id']));
                    $this->tpl_onload = "window.alert('配送方法設定が完了しました。');";
                }
                break;
            case 'pre_edit':
                if (count($this->arrErr) > 0) {
                    trigger_error('', E_USER_ERROR);
                }
                $this->lfGetDelivData($objFormParam);
                break;
            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();
    }

    /* パラメーター情報の初期化 */
    public function lfInitParam($mode, &$objFormParam)
    {
        $objFormParam = Application::alias('eccube.form_param');

        switch ($mode) {
            case 'edit':
                $objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('配送業者名', 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('名称', 'service_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('説明', 'remark', LLTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                $objFormParam->addParam('伝票No.確認URL', 'confirm_url', URL_LEN, 'n', array('URL_CHECK', 'MAX_LENGTH_CHECK'), 'http://');
                $objFormParam->addParam('取扱商品種別', 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('取扱支払方法', 'payment_ids', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

                for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
                    $objFormParam->addParam("お届け時間$cnt", "deliv_time$cnt", STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                }

                if (INPUT_DELIV_FEE) {
                    for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                        $objFormParam->addParam("配送料", "fee$cnt", PRICE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
                    }
                }
                break;

            case 'pre_edit':
                $objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;

            default:
                break;
        }
    }

    /**
     * 配送情報を登録する
     *
     * @return $deliv_id
     */
    public function lfRegistData($arrRet, $member_id)
    {
        /* @var $objDelivery DeliveryHelper */
        $objDelivery = Application::alias('eccube.helper.delivery');

        // 入力データを渡す。
        $sqlval['deliv_id'] = $arrRet['deliv_id'];
        $sqlval['name'] = $arrRet['name'];
        $sqlval['service_name'] = $arrRet['service_name'];
        $sqlval['remark'] = $arrRet['remark'];
        $sqlval['confirm_url'] = $arrRet['confirm_url'];
        $sqlval['product_type_id'] = $arrRet['product_type_id'];
        $sqlval['creator_id'] = $member_id;

        // お届け時間
        $sqlval['deliv_time'] = array();
        for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
            $keyname = "deliv_time$cnt";
            if ($arrRet[$keyname] != '') {
                $sqlval['deliv_time'][$cnt] = $arrRet[$keyname];
            }
        }

        // 配送料
        if (INPUT_DELIV_FEE) {
            $sqlval['deliv_fee'] = array();
            // 配送料金の設定
            for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                $keyname = "fee$cnt";
                if ($arrRet[$keyname] != '') {
                    $fee = array();
                    $fee['fee_id'] = $cnt;
                    $fee['fee'] = $arrRet[$keyname];
                    $fee['pref'] = $cnt;
                    $sqlval['deliv_fee'][$cnt] = $fee;
                }
            }
        }

        // 支払い方法
        $sqlval['payment_ids'] = array();
        foreach ($arrRet['payment_ids'] as $payment_id) {
            $sqlval['payment_ids'][] = $payment_id;
        }

        $deliv_id = $objDelivery->save($sqlval);

        return $deliv_id;
    }

    /* 配送業者情報の取得 */
    public function lfGetDelivData(&$objFormParam)
    {
        /* @var $objDelivery DeliveryHelper */
        $objDelivery = Application::alias('eccube.helper.delivery');

        $deliv_id = $objFormParam->getValue('deliv_id');

        // パラメーター情報の初期化
        $this->lfInitParam('edit', $objFormParam);

        $arrDeliv = $objDelivery->get($deliv_id);

        // お届け時間
        $deliv_times = array();
        foreach ($arrDeliv['deliv_time'] as $value) {
            $deliv_times[]['deliv_time'] = $value;
        }
        $objFormParam->setParamList($deliv_times, 'deliv_time');
        unset($arrDeliv['deliv_time']);
        // 配送料金
        $deliv_fee = array();
        foreach ($arrDeliv['deliv_fee'] as $value) {
            $deliv_fee[]['fee'] = $value['fee'];
        }
        $objFormParam->setParamList($deliv_fee, 'fee');
        unset($arrDeliv['deliv_fee']);
        // 支払方法
        $objFormParam->setValue('payment_ids', $arrDeliv['payment_ids']);
        unset($arrDeliv['payment_ids']);
        // 配送業者
        $objFormParam->setParam($arrDeliv);
    }

    /* 入力内容のチェック */
    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        if (!isset($objErr->arrErr['name'])) {
            // 既存チェック
            /* @var $objDelivery DeliveryHelper */
            $objDelivery = Application::alias('eccube.helper.delivery');
            if ($objDelivery->checkExist($arrRet)) {
                $objErr->arrErr['service_name'] = '※ 同じ名称の組み合わせは登録できません。<br>';
            }
        }

        return $objErr->arrErr;
    }
}
