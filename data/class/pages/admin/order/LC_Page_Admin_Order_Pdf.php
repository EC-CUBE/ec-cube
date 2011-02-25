<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_EX_REALDIR . "page_extends/admin/LC_Page_Admin_Ex.php");
require_once(CLASS_REALDIR . "SC_Fpdf.php");

/**
 * 帳票出力 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Pdf extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/pdf_input.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'pdf';
        $this->tpl_subtitle = '帳票出力';

        $this->SHORTTEXT_MAX = STEXT_LEN;
        $this->MIDDLETEXT_MAX = MTEXT_LEN;
        $this->LONGTEXT_MAX = LTEXT_LEN;

        $this->arrType[0]  = "納品書";

        $this->arrDownload[0] = "ブラウザに開く";
        $this->arrDownload[1] = "ファイルに保存";
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
        $objDb = new SC_Helper_DB_Ex();
        $objDate = new SC_Date(1901);
        $objDate->setStartYear(RELEASE_YEAR);
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam($this->objFormParam);
        $this->objFormParam->setParam($_POST);
        // 入力値の変換
        $this->objFormParam->convParam();
        
        // どんな状態の時に isset($arrRet) == trueになるんだ? これ以前に$arrRet無いが、、、、
        if (!isset($arrRet)) $arrRet = array();
        switch($this->getMode()) {
            case "confirm":
                $status = $this->createPdf($this->objFormParam);
                if($status === true){
                    exit;
                }else{
                    $this->arrErr = $status;
                }
                break;
            default:
                $this->arrForm = $this->createFromValues($_GET['order_id'],$_POST['pdf_order_id']);
                break;
        }
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     *
     * PDF作成フォームのデフォルト値の生成
     */
    function createFromValues($order_id,$pdf_order_id){
        // ここが$arrFormの初登場ということを明示するため宣言する。
        $arrForm = array();
        // タイトルをセット
        $arrForm['title'] = "お買上げ明細書(納品書)";

        // 今日の日付をセット
        $arrForm['year']  = date("Y");
        $arrForm['month'] = date("m");
        $arrForm['day']   = date("d");

        // メッセージ
        $arrForm['msg1'] = 'このたびはお買上げいただきありがとうございます。';
        $arrForm['msg2'] = '下記の内容にて納品させていただきます。';
        $arrForm['msg3'] = 'ご確認くださいますよう、お願いいたします。';

        // 注文番号があったら、セットする
        if(SC_Utils_Ex::sfIsInt($order_id)) {
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
     * @param SC_FormParam $objFormParam
     */
    function createPdf(&$objFormParam){
        
        $arrErr = $this->lfCheckError($objFormParam);
        $arrRet = $objFormParam->getHashArray();

        $this->arrForm = $arrRet;
        // エラー入力なし
        if (count($arrErr) == 0) {
            $objFpdf = new SC_Fpdf($arrRet['download'], $arrRet['title']);
            foreach ($arrRet['order_id'] AS $key => $val) {
                $arrPdfData = $arrRet;
                $arrPdfData['order_id'] = $val;
                $objFpdf->setData($arrPdfData);
            }
            $objFpdf->createPdf();
            return true;
        }else{
            return $arrErr;
        }
    }



    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     *  パラメータ情報の初期化 
     *  @param SC_FormParam 
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("注文番号", "order_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("注文番号", "pdf_order_id", INT_LEN, "n", array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("発行日", "year", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("発行日", "month", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("発行日", "day", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("帳票の種類", "type", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("ダウンロード方法", "download", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("帳票タイトル", "title", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("帳票メッセージ1行目", "msg1", STEXT_LEN*3/5, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("帳票メッセージ2行目", "msg2", STEXT_LEN*3/5, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("帳票メッセージ3行目", "msg3", STEXT_LEN*3/5, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("備考1行目", "etc1", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("備考2行目", "etc2", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("備考3行目", "etc3", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ポイント表記", "disp_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     *  入力内容のチェック
     *  @var SC_FormParam
     */

    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet = $objFormParam->getHashArray();
        $arrErr = $objFormParam->checkError();

        $year = $objFormParam->getValue('year');
        if(!is_numeric($year)){
            $arrErr['year'] = "発行年は数値で入力してください。";
        }

        $month = $objFormParam->getValue('month');
        if(!is_numeric($month)){
            $arrErr['month'] = "発行月は数値で入力してください。";
        }else if(0 >= $month && 12 < $month){
                   
            $arrErr['month'] = "発行月は1〜12の間で入力してください。";
        }
        
        $day = $objFormParam->getValue('day');
        if(!is_numeric($day)){
            $arrErr['day'] = "発行日は数値で入力してください。";
        }else if(0 >= $day && 31 < $day){
                   
            $arrErr['day'] = "発行日は1〜31の間で入力してください。";
        }

        return $arrErr;
    }


}

