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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * 店舗基本情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/index.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
        $this->tpl_subtitle = 'SHOPマスタ';

        //定休日用配列
        $this->arrRegularHoliday[0] = '日';
        $this->arrRegularHoliday[1] = '月';
        $this->arrRegularHoliday[2] = '火';
        $this->arrRegularHoliday[3] = '水';
        $this->arrRegularHoliday[4] = '木';
        $this->arrRegularHoliday[5] = '金';
        $this->arrRegularHoliday[6] = '土';
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
        $objSess = new SC_Session();
        $objQuery = new SC_Query();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $cnt = $objQuery->count("dtb_baseinfo");

        if ($cnt > 0) {
            $this->tpl_mode = "update";
        } else {
            $this->tpl_mode = "insert";
        }
        if($this->getMode()!=null) {
            // POSTデータの引き継ぎ
            $this->arrForm = $_POST;

            // 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm);
            $this->arrForm['regular_holiday_ids'] = $_POST['regular_holiday_ids'];  // 定休日情報を付加
            // 入力データのエラーチェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);

            if(count($this->arrErr) == 0) {
                switch($this->getMode()) {
                case 'update':
                    $this->lfUpdateData($this->arrForm);	// 既存編集
                    break;
                case 'insert':
                    $this->lfInsertData($this->arrForm);	// 新規作成
                    break;
                default:
                    break;
                }
                $this->tpl_onload = "fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '" . DISABLED_RGB . "'); window.alert('SHOPマスタの登録が完了しました。');";
            }
            if( empty($this->arrForm['regular_holiday_ids']) ) {
                $this->arrSel = array();
            } else {
                $this->arrSel = $this->arrForm['regular_holiday_ids'];
            }
        } else {
            $arrCol = $this->lfGetCol();
            $col	= SC_Utils_Ex::sfGetCommaList($arrCol);
            $arrRet = $objQuery->select($col, "dtb_baseinfo");
            $this->arrForm = $arrRet[0];

            $regular_holiday_ids = explode('|', $this->arrForm['regular_holiday_ids']);
            $this->arrForm['regular_holiday_ids'] = $regular_holiday_ids;
            $this->tpl_onload = "fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '" . DISABLED_RGB . "');";
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

    // 基本情報用のカラムを取り出す。
    function lfGetCol() {
        $arrCol = array(
            "company_name",
            "company_kana",
            "shop_name",
            "shop_kana",
            "shop_name_eng",
            "zip01",
            "zip02",
            "pref",
            "addr01",
            "addr02",
            "tel01",
            "tel02",
            "tel03",
            "fax01",
            "fax02",
            "fax03",
            "business_hour",
            "email01",
            "email02",
            "email03",
            "email04",
            "tax",
            "tax_rule",
            "free_rule",
            "good_traded",
            "message",
            "regular_holiday_ids",
            "latitude",
            "longitude",
            "downloadable_days",
            "downloadable_days_unlimited"
        );
        return $arrCol;
    }

    function lfUpdateData($array) {
        $objQuery = new SC_Query();
        $arrCol = $this->lfGetCol();
        foreach($arrCol as $val) {
            //配列の場合は、パイプ区切りの文字列に変換
            if(is_array($array[$val])) {
                $sqlval[$val] = implode("|", $array[$val]);
            } else {
                $sqlval[$val] = $array[$val];
            }
        }
        $sqlval['update_date'] = 'Now()';
        // UPDATEの実行
        $ret = $objQuery->update("dtb_baseinfo", $sqlval);
    }

    function lfInsertData($array) {
        $objQuery = new SC_Query();
        $arrCol = $this->lfGetCol();
        foreach($arrCol as $val) {
            $sqlval[$val] = $array[$val];
        }
        $sqlval['update_date'] = 'Now()';
        // INSERTの実行
        $ret = $objQuery->insert("dtb_baseinfo", $sqlval);
    }


    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // 人物基本情報

        // スポット商品
        $arrConvList['company_name'] = "KVa";
        $arrConvList['company_kana'] = "KVC";
        $arrConvList['shop_name'] = "KVa";
        $arrConvList['shop_kana'] = "KVC";
        $arrConvList['shop_name_eng'] = "a";
        $arrConvList['addr01'] = "KVa";
        $arrConvList['addr02'] = "KVa";
        $arrConvList['zip01'] = "n";
        $arrConvList['zip02'] = "n";
        $arrConvList['tel01'] = "n";
        $arrConvList['tel02'] = "n";
        $arrConvList['tel03'] = "n";
        $arrConvList['fax01'] = "n";
        $arrConvList['fax02'] = "n";
        $arrConvList['fax03'] = "n";
        $arrConvList['email01'] = "a";
        $arrConvList['email02'] = "a";
        $arrConvList['email03'] = "a";
        $arrConvList['email04'] = "a";
        $arrConvList['tax'] = "n";
        $arrConvList['free_rule'] = "n";
        $arrConvList['business_hour'] = "KVa";
        $arrConvList['good_traded'] = "";
        $arrConvList['message'] = "";
        $arrConvList['downloadable_days'] = "n";
        $arrConvList['downloadable_days_unlimited'] = "n";

        return SC_Utils_Ex::mbConvertKanaWithArray($array, $arrConvList);
    }

    // 入力エラーチェック
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("会社名", "company_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("会社名(カナ)", "company_kana", STEXT_LEN), array("KANA_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("店名", "shop_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("店名(カナ)", "shop_kana", STEXT_LEN), array("KANA_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("店名(英語表記)", "shop_name_eng", MTEXT_LEN), array("GRAPH_CHECK","MAX_LENGTH_CHECK"));
        // 郵便番号チェック
        $objErr->doFunc(array("郵便番号1","zip01",ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK","NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2","zip02",ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK","NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        // 住所チェック
        $objErr->doFunc(array("都道府県", "pref"), array("EXIST_CHECK"));
        $objErr->doFunc(array("住所1", "addr01", MTEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("住所2", "addr02", MTEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        // メールチェック
        $objErr->doFunc(array('商品注文受付メールアドレス', "email01", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('問い合わせ受付メールアドレス', "email02", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メール送信元メールアドレス', "email03", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('送信エラー受付メールアドレス', "email04", STEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK","MAX_LENGTH_CHECK"));

        // 電話番号チェック
        $objErr->doFunc(array("TEL", "tel01", "tel02", "tel03"), array("TEL_CHECK"));
        $objErr->doFunc(array("FAX", "fax01", "fax02", "fax03"), array("TEL_CHECK"));
        // その他
        $objErr->doFunc(array("消費税率", "tax", PERCENTAGE_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("送料無料条件", "free_rule", PRICE_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("店舗営業時間", "business_hour", STEXT_LEN), array("MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("取扱商品", "good_traded", LLTEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メッセージ", "message", LLTEXT_LEN), array("MAX_LENGTH_CHECK"));
        if(!isset($array['downloadable_days_unlimited']) && $array['downloadable_days_unlimited'] != "1") {
            $objErr->doFunc(array("ダウンロード可能日数", "downloadable_days", DOWNLOAD_DAYS_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        $objErr->doFunc(array("緯度", "latitude", STEXT_LEN), array("NUM_POINT_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("経度", "longitude", STEXT_LEN), array("NUM_POINT_CHECK", "MAX_LENGTH_CHECK"));
        return $objErr->arrErr;
    }
}
?>
