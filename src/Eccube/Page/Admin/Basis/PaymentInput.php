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
use Eccube\Framework\UploadFile;
use Eccube\Framework\Helper\PaymentHelper;

/**
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class PaymentInput extends AbstractAdminPage
{
    /** UploadFile インスタンス */
    public $objUpFile;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/payment_input.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'payment';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '支払方法設定';
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
        /* @var $objPayment PaymentHelper */
        $objPayment = Application::alias('eccube.helper.payment');
        $objFormParam = Application::alias('eccube.form_param');
        $mode = $this->getMode();
        $this->lfInitParam($mode, $objFormParam);

        // ファイル管理クラス
        $this->objUpFile = new UploadFile(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
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

                    $objFormParam->addParam('支払方法', 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam('手数料', 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam('利用条件(～円以上)', 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam('利用条件(～円以下)', 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                    $objFormParam->addParam('固定', 'fix');
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
        $this->arrHidden = array_merge((array) $this->arrHidden, (array) $this->objUpFile->getHiddenFileList());
    }

    /* ファイル情報の初期化 */
    public function lfInitFile()
    {
        $this->objUpFile->addFile('ロゴ画像', 'payment_image', array('gif','jpeg','jpg','png'), IMAGE_SIZE, false, CLASS_IMAGE_WIDTH, CLASS_IMAGE_HEIGHT);

        return $this->objUpFile;
    }

    /* パラメーター情報の初期化 */

    /**
     * @param string|null $mode
     * @param FormParam $objFormParam
     */
    public function lfInitParam($mode, &$objFormParam)
    {
        switch ($mode) {
            case 'edit':
                $objFormParam->addParam('支払方法', 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('手数料', 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('利用条件(～円以上)', 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('利用条件(～円以下)', 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('固定', 'fix');
                $objFormParam->addParam('支払いID', 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('課金フラグ', 'charge_flg', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

                break;
            case 'upload_image':
            case 'delete_image':
                $objFormParam->addParam('支払いID', 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('支払方法', 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('手数料', 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('利用条件(～円以上)', 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('利用条件(～円以下)', 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('固定', 'fix');
                $objFormParam->addParam('画像キー', 'image_key', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));

                break;
            case 'pre_edit':
                $objFormParam->addParam('支払いID', 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('課金フラグ', 'charge_flg', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;

            default:
                $objFormParam->addParam('支払方法', 'payment_method', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('手数料', 'charge', PRICE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('利用条件(～円以上)', 'rule_max', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('利用条件(～円以下)', 'upper_rule', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('固定', 'fix');

                break;
        }
    }

    /* DBへデータを登録する */

    /**
     * @param FormParam $objFormParam
     */
    public function lfRegistData(&$objFormParam, PaymentHelper $objPayment, $member_id, $payment_id = '')
    {
        $sqlval = array_merge($objFormParam->getHashArray(), $this->objUpFile->getDBFileList());
        $sqlval['payment_id'] = $payment_id;
        $sqlval['creator_id'] = $member_id;

        if ($sqlval['fix'] != '1') {
            $sqlval['fix'] = 2; // 自由設定
        }

        $objPayment->save($sqlval);
    }

    /*　利用条件の数値チェック */

    /* 入力内容のチェック */

    /**
     * @param FormParam $objFormParam
     */
    public function lfCheckError($post, $objFormParam, PaymentHelper $objPayment)
    {
        // DBのデータを取得
        $arrPaymentData = $objPayment->get($post['payment_id']);

        // 手数料を設定できない場合には、手数料を0にする
        if ($arrPaymentData['charge_flg'] == 2) {
            $objFormParam->setValue('charge', '0');
        }

        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        // 利用条件(下限)チェック
        if ($arrRet['rule_max'] < $arrPaymentData['rule_min'] and $arrPaymentData['rule_min'] != '') {
            $objErr->arrErr['rule'] = '利用条件(下限)は' . $arrPaymentData['rule_min'] .'円以上にしてください。<br>';
        }

        // 利用条件(上限)チェック
        if ($arrRet['upper_rule'] > $arrPaymentData['upper_rule_max'] and $arrPaymentData['upper_rule_max'] != '') {
            $objErr->arrErr['upper_rule'] = '利用条件(上限)は' . $arrPaymentData['upper_rule_max'] .'円以下にしてください。<br>';
        }

        // 利用条件チェック
        $objErr->doFunc(array('利用条件(～円以上)', '利用条件(～円以下)', 'rule_max', 'upper_rule'), array('GREATER_CHECK'));

        return $objErr->arrErr;
    }
}
