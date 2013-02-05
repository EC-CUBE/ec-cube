<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_PaymentInput extends LC_Page_Admin_Ex {

    // {{{ properties

    /** SC_UploadFile インスタンス */
    var $objUpFile;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/payment_input.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'payment';
        $this->tpl_maintitle = t('c_Basic information_01');
        $this->tpl_subtitle = t('LC_Page_Admin_Basis_PaymentInput_002');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        $objPayment = new SC_Helper_Payment_Ex();
        $objFormParam = new SC_FormParam_Ex();
        $mode = $this->getMode();
        $this->lfInitParam($mode, $objFormParam);

        // ファイル管理クラス
        $this->objUpFile = new SC_UploadFile(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        // ファイル情報の初期化
        $this->objUpFile = $this->lfInitFile();
        // Hiddenからのデータを引き継ぐ
        $this->objUpFile->setHiddenFileList($_POST);

        switch ($mode) {
            case 'edit':
                $objFormParam->setParam($_REQUEST);
                $objFormParam->convParam();
                $post = $objFormParam->getHashArray();
                $this->arrErr = $this->lfCheckError($post, $objFormParam, $objPayment);
                $this->charge_flg = $post['charge_flg'];
                if (count($this->arrErr) == 0) {
                    $this->lfRegistData($objFormParam, $objPayment, $_SESSION['member_id'], $post['payment_id']);
                    $this->objUpFile->moveTempFile();
                    $this->tpl_onload = "location.href = './payment.php'; return;";
                }
                $this->tpl_payment_id = $post['payment_id'];
                break;
            // 画像のアップロード
            case 'upload_image':
                $objFormParam->setParam($_REQUEST);
                $objFormParam->convParam();
                $post = $objFormParam->getHashArray();
                // ファイル存在チェック
                $this->arrErr = $this->objUpFile->checkExists($post['image_key']);
                // 画像保存処理
                $this->arrErr[$post['image_key']] = $this->objUpFile->makeTempFile($post['image_key']);
                $this->tpl_payment_id = $post['payment_id'];
                break;
            // 画像の削除
            case 'delete_image':
                $objFormParam->setParam($_REQUEST);
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError();
                $post = $objFormParam->getHashArray();
                if (count($this->arrErr) == 0) {
                    $this->objUpFile->deleteFile($post['image_key']);
                }
                $this->tpl_payment_id = $post['payment_id'];
                break;

            case 'pre_edit':
                $objFormParam->setParam($_REQUEST);
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError();
                $post = $objFormParam->getHashArray();
                if (count($this->arrErr) == 0) {
                    $arrRet = $objPayment->get($post['payment_id']);

                    $objFormParam->addParam(t('PARAM_LABEL_PAYMENT_METHOD'), 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam(t('c_Processing fee_01'), 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam(t('c_Usage conditions(-$ Above)_01'), 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam(t('c_Usage conditions(-$ Less than)_01'), 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam(t('c_Fixed_01'), 'fix');
                    $objFormParam->setParam($arrRet);

                    $this->charge_flg = $arrRet['charge_flg'];
                    $this->objUpFile->setDBFileList($arrRet);
                }
                $this->tpl_payment_id = $post['payment_id'];
                break;
            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();

        // FORM表示用配列を渡す。
        $this->arrFile = $this->objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);
        // HIDDEN用に配列を渡す。
        $this->arrHidden = array_merge((array)$this->arrHidden, (array)$this->objUpFile->getHiddenFileList());

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* ファイル情報の初期化 */
    function lfInitFile() {
        $this->objUpFile->addFile(t('c_Logo image_01'), 'payment_image', array('gif','jpeg','jpg','png'), IMAGE_SIZE, false, CLASS_IMAGE_WIDTH, CLASS_IMAGE_HEIGHT);
        return $this->objUpFile;
    }

    /* パラメーター情報の初期化 */
    function lfInitParam($mode, &$objFormParam) {

        switch ($mode) {
            case 'edit':
                $objFormParam->addParam(t('PARAM_LABEL_PAYMENT_METHOD'), 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Processing fee_01'), 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Usage conditions(-$ Above)_01'), 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Usage conditions(-$ Less than)_01'), 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Fixed_01'), 'fix');
                $objFormParam->addParam(t('c_Payment ID_01'), 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Billing flag_01'), 'charge_flg', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

                break;
            case 'upload_image':
            case 'delete_image':
                $objFormParam->addParam(t('c_Payment ID_01'), 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('PARAM_LABEL_PAYMENT_METHOD'), 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Processing fee_01'), 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Usage conditions(-$ Above)_01'), 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Usage conditions(-$ Less than)_01'), 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Fixed_01'), 'fix');
                $objFormParam->addParam(t('c_Image key_01'), 'image_key', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));

                break;
            case 'pre_edit':
                $objFormParam->addParam(t('c_Payment ID_01'), 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Billing flag_01'), 'charge_flg', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;

            default:
                $objFormParam->addParam(t('PARAM_LABEL_PAYMENT_METHOD'), 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Processing fee_01'), 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Usage conditions(-$ Above)_01'), 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Usage conditions(-$ Less than)_01'), 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Fixed_01'), 'fix');

                break;
        }
    }

    /* DBへデータを登録する */
    function lfRegistData(&$objFormParam, SC_Helper_Payment_Ex $objPayment, $member_id, $payment_id = '') {

        $sqlval = array_merge($objFormParam->getHashArray(), $this->objUpFile->getDBFileList());
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['payment_id'] = $payment_id;
        $sqlval['creator_id'] = $member_id;

        if ($sqlval['fix'] != '1') {
            $sqlval['fix'] = 2; // 自由設定
        }

        $objPayment->save($sqlval);
    }

    /*　利用条件の数値チェック */

    /* 入力内容のチェック */
    function lfCheckError($post, $objFormParam, SC_Helper_Payment_Ex $objPayment) {

        // DBのデータを取得
        $arrPaymentData = $objPayment->get($post['payment_id']);

        // 手数料を設定できない場合には、手数料を0にする
        if ($arrPaymentData['charge_flg'] == 2) {
            $objFormParam->setValue('charge', '0');
        }

        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        // 利用条件(下限)チェック
        if ($arrRet['rule_max'] < $arrPaymentData['rule_min'] and $arrPaymentData['rule_min'] != '') {
            $objErr->arrErr['rule'] = t('c_Make the usage conditions (lower limit) &#036; T_ARG1 or more.<br>_01', array('T_ARG1', $arrPaymentData['rule_min']));
        }

        // 利用条件(上限)チェック
        if ($arrRet['upper_rule'] > $arrPaymentData['upper_rule_max'] and $arrPaymentData['upper_rule_max'] != '') {
            $objErr->arrErr['rule'] = t('c_Make the usage conditions (max) &#036; T_ARG1 or less.<br>_01', array('T_ARG1', $arrPaymentData['upper_rule_max']));
        }

        // 利用条件チェック
        $objErr->doFunc(array(t('c_Usage conditions(-$ Above)_01'), t('c_Usage conditions(-$ Less than)_01'), 'rule_max', 'upper_rule'), array('GREATER_CHECK'));

        return $objErr->arrErr;
    }
}
