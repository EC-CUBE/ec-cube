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
use Eccube\Framework\CheckError;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Fpdf;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * 帳票出力 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Pdf extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/pdf_input.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'pdf';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '帳票出力';

        $this->SHORTTEXT_MAX = STEXT_LEN;
        $this->MIDDLETEXT_MAX = MTEXT_LEN;
        $this->LONGTEXT_MAX = LTEXT_LEN;

        $this->arrType[0]  = '納品書';

        $this->arrDownload[0] = 'ブラウザに開く';
        $this->arrDownload[1] = 'ファイルに保存';
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
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date', 1901);
        $objDate->setStartYear(RELEASE_YEAR);
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // パラメーター管理クラス
        $this->objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($this->objFormParam);
        $this->objFormParam->setParam($_POST);
        // 入力値の変換
        $this->objFormParam->convParam();

        // どんな状態の時に isset($arrRet) == trueになるんだ? これ以前に$arrRet無いが、、、、
        if (!isset($arrRet)) $arrRet = array();
        switch ($this->getMode()) {
            case 'confirm':

                $status = $this->createPdf($this->objFormParam);
                if ($status === true) {
                    Application::alias('eccube.response')->actionExit();
                } else {
                    $this->arrErr = $status;
                }
                break;
            default:
                $this->arrForm = $this->createFromValues($_GET['order_id'], $_POST['pdf_order_id']);
                break;
        }
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     *
     * PDF作成フォームのデフォルト値の生成
     */
    public function createFromValues($order_id, $pdf_order_id)
    {
        // ここが$arrFormの初登場ということを明示するため宣言する。
        $arrForm = array();
        // タイトルをセット
        $arrForm['title'] = 'お買上げ明細書(納品書)';

        // 今日の日付をセット
        $arrForm['year']  = date('Y');
        $arrForm['month'] = date('m');
        $arrForm['day']   = date('d');

        // メッセージ
        $arrForm['msg1'] = 'このたびはお買上げいただきありがとうございます。';
        $arrForm['msg2'] = '下記の内容にて納品させていただきます。';
        $arrForm['msg3'] = 'ご確認くださいますよう、お願いいたします。';

        // 注文番号があったら、セットする
        if (Utils::sfIsInt($order_id)) {
            $arrForm['order_id'][0] = $order_id;
        } elseif (is_array($pdf_order_id)) {
            sort($pdf_order_id);
            foreach ($pdf_order_id AS $key=>$val) {
                $arrForm['order_id'][] = $val;
            }
        }

        return $arrForm;
    }

    /**
     *
     * PDFの作成
     * @param FormParam $objFormParam
     */
    public function createPdf(&$objFormParam)
    {
        $arrErr = $this->lfCheckError($objFormParam);
        $arrRet = $objFormParam->getHashArray();

    //タイトルが入力されていなければ、デフォルトのタイトルを表示
    if($arrRet['title'] == '') $arrRet['title'] = 'お買上げ明細書(納品書)';

        $this->arrForm = $arrRet;
        // エラー入力なし
        if (count($arrErr) == 0) {
            $objFpdf = new Fpdf($arrRet['download'], $arrRet['title']);
            foreach ($arrRet['order_id'] AS $key => $val) {
                $arrPdfData = $arrRet;
                $arrPdfData['order_id'] = $val;
                $objFpdf->setData($arrPdfData);
            }
            $objFpdf->createPdf();

            return true;
        } else {
            return $arrErr;
        }
    }

    /**
     *  パラメーター情報の初期化
     *  @param FormParam
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('注文番号', 'order_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('注文番号', 'pdf_order_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('発行日', 'year', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('発行日', 'month', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('発行日', 'day', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('帳票の種類', 'type', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ダウンロード方法', 'download', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('帳票タイトル', 'title', STEXT_LEN, 'KVa', array ('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('帳票メッセージ1行目', 'msg1', STEXT_LEN*3/5, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('帳票メッセージ2行目', 'msg2', STEXT_LEN*3/5, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('帳票メッセージ3行目', 'msg3', STEXT_LEN*3/5, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('備考1行目', 'etc1', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('備考2行目', 'etc2', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('備考3行目', 'etc3', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ポイント表記', 'disp_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     *  入力内容のチェック
     *  @var FormParam
     * @param FormParam $objFormParam
     */

    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrParams = $objFormParam->getHashArray();
        $arrErr = $objFormParam->checkError();
        /* @var $objErr CheckError */
        $objError = Application::alias('eccube.check_error', $arrParams);

        $year = $objFormParam->getValue('year');
        if (!is_numeric($year)) {
            $arrErr['year'] = '発行年は数値で入力してください。';
        }

        $month = $objFormParam->getValue('month');
        if (!is_numeric($month)) {
            $arrErr['month'] = '発行月は数値で入力してください。';
        } elseif (0 >= $month && 12 < $month) {
            $arrErr['month'] = '発行月は1〜12の間で入力してください。';
        }

        $day = $objFormParam->getValue('day');
        if (!is_numeric($day)) {
            $arrErr['day'] = '発行日は数値で入力してください。';
        } elseif (0 >= $day && 31 < $day) {
            $arrErr['day'] = '発行日は1〜31の間で入力してください。';
        }

        $objError->doFunc(array('発行日', 'year', 'month', 'day'), array('CHECK_DATE'));
        $arrErr = array_merge($arrErr, $objError->arrErr);

        return $arrErr;
    }
}
