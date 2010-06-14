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
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_CSV_Ex.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_FileManager_Ex.php");

/**
 * キャンペーン管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_Campaign extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/campaign.tpl';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = "campaign";
        $this->tpl_mainno = 'contents';
        $this->tpl_subtitle = 'キャンペーン管理';
        // カートに商品が入っているにチェックが入っているかチェック
        $this->tpl_onload = "fnIsCartOn();";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        //---- 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $objView = new SC_AdminView();
        $objQuery = new SC_Query();
        $objFormParam = new SC_FormParam();
        $objCSV = new SC_Helper_CSV_Ex();

        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        // フォームの値をセット
        $objFormParam->setParam($_POST);

        // 編集処理の場合は状態を保持
        $this->is_update = isset($_POST['is_update']) ? $_POST['is_update'] : "";

        // フォームの値をテンプレートへ渡す
        $this->arrForm = $objFormParam->getHashArray();
        $campaign_id = isset($_POST['campaign_id']) ? $_POST['campaign_id'] : "";

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
            // 新規登録/編集登録
        case 'regist':
            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($campaign_id, $objQuery, $objFormParam);

            if(count($this->arrErr) <= 0) {
                // 登録
                $this->lfRegistCampaign($campaign_id, $objQuery, $objFormParam);

                // キャンペーンTOPへリダイレクト
                $this->sendRedirect($this->getLocation(URL_CAMPAIGN_TOP));
                exit;
            }

            break;
            // 編集押下時
        case 'update':
            // キャンペーン情報を取得
            $this->arrForm = $this->lfGetCampaign($campaign_id, $objQuery);
            $this->is_update = true;
            break;
            // 削除押下時
        case 'delete':
            // 削除
            $this->lfDeleteCampaign($campaign_id, $objQuery);
            // キャンペーンTOPへリダイレクト
            $this->sendRedirect($this->getLocation(URL_CAMPAIGN_TOP));
            exit;
            break;
            // CSV出力
        case 'csv':
            // オプションの指定
            $option = "ORDER BY create_date DESC";

            // CSV出力タイトル行の作成
            $arrCsvOutput = SC_Utils_Ex::sfSwapArray($objCSV->sfgetCsvOutput(4, 'status = 1'));

            if (count($arrCsvOutput) <= 0) break;

            $arrCsvOutputCols = $arrCsvOutput['col'];
            $arrCsvOutputTitle = $arrCsvOutput['disp_name'];
            $head = SC_Utils_Ex::sfGetCSVList($arrCsvOutputTitle);
            $data = $objCSV->lfGetCSV("dtb_campaign_order", "campaign_id = ?", $option, array($campaign_id), $arrCsvOutputCols);

            // CSVを送信する。
            SC_Utils_Ex::sfCSVDownload($head.$data);
            exit;
            break;
        default:
            break;
        }

        // キャンペーン一覧取得
        $this->arrCampaign = $this->lfGetCampaignList($objQuery);
        $this->campaign_id = $campaign_id;

        // キャンペーン期間用
        $objDate = new SC_Date();
        $this->arrYear = $objDate->getYear(min(date('Y'), $this->arrForm['start_year']));
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        $this->arrHour = $objDate->getHour();
        $this->arrMinutes = $objDate->getMinutes();

        //---- ページ表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
     * 入力情報の初期化
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("キャンペーン名", "campaign_name", MTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        $objFormParam->addParam("開始日時", "start_year", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始日時", "start_month", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始日時", "start_day", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始日時", "start_hour", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始日時", "start_minute", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("停止日時", "end_year", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("停止日時", "end_month", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("停止日時", "end_day", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("停止日時", "end_hour", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("停止日時", "end_minute", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("ディレクトリ名", "directory_name", MTEXT_LEN, "KVa", array("EXIST_CHECK","ALNUM_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("申込数制御", "limit_count", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("重複申込制御", "orverlapping_flg", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("カートに商品を入れる", "cart_flg", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("送料無料設定", "deliv_free_flg", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

    }

    /*
     * 関数名：lfErrorCheck()
     * 引数1 ：キャンペーンID
     * 説明　：エラーチェック
     * 戻り値：エラー文字格納配列
     */
    function lfErrorCheck($campaign_id = "", &$objQuery, &$objFormParam) {

        $arrList = $objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrList);
        $objErr->arrErr = $objFormParam->checkError();

        $objErr->doFunc(array("開始日時", "start_year", "start_month", "start_day", "start_hour", "start_minute", "0"), array("CHECK_DATE2"));
        $objErr->doFunc(array("停止日時", "end_year", "end_month", "end_day", "end_hour", "end_minute", "0"), array("CHECK_DATE2"));
        $objErr->doFunc(array("開始日時", "停止日時", "start_year", "start_month", "start_day", "start_hour", "start_minute", "00", "end_year", "end_month", "end_day", "end_hour", "end_minute", "59"), array("CHECK_SET_TERM2"));

        if (!is_writable(CAMPAIGN_TEMPLATE_PATH)) {
            $objErr->arrErr['campaign_template_path'] = "※" . CAMPAIGN_TEMPLATE_PATH . " へ書き込み権限を与えてください。 <br/>";
        }
        if (!is_writable(CAMPAIGN_PATH)) {
            $objErr->arrErr['campaign_path'] = "※" . CAMPAIGN_PATH . " へ書き込み権限を与えてください。<br/>";
        }

        if(count($objErr->arrErr) <= 0) {

            // 編集時用に元のディレクトリ名を取得する。
            if($campaign_id != "") {
                $directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($campaign_id));
            } else {
                $directory_name = "";
            }

            // 同名のフォルダが存在する場合はエラー
            if(file_exists(CAMPAIGN_TEMPLATE_PATH.$arrList['directory_name']) && $directory_name != $arrList['directory_name']) {
                $objErr->arrErr['directory_name'] = "※ 同名のディレクトリがすでに存在します。<br/>";
            }
            $ret = $objQuery->get("dtb_campaign", "directory_name", "directory_name = ? AND del_flg = 0", array($arrList['directory_name']));
            // DBにすでに登録されていないかチェック
            if($ret != "" && $directory_name != $arrList['directory_name']) {
                $objErr->arrErr['directory_name'] = "※ すでに登録されているディレクトリ名です。<br/>";
            }
        }

        return $objErr->arrErr;
    }

    /*
     * 関数名：lfRegistCampaign()
     * 引数1 ：キャンペーンID(アップデート時に指定)
     * 説明　：キャンペーン登録/更新
     * 戻り値：無し
     */
    function lfRegistCampaign($campaign_id = "", &$objQuery, &$objFormParam) {

        $objSiteInfo = new SC_SiteInfo();
        $arrInfo = $objSiteInfo->data;
        $arrList = $objFormParam->getHashArray();

        // 開始日時・終了日時整形
        $start_date = $arrList['start_year']."-".sprintf("%02d", $arrList['start_month'])."-".sprintf("%02d", $arrList['start_day'])." ".sprintf("%02d", $arrList['start_hour']).":".sprintf("%02d", $arrList['start_minute']).":00";
        $end_date = $arrList['end_year']."-".sprintf("%02d", $arrList['end_month'])."-".sprintf("%02d", $arrList['end_day'])." ".sprintf("%02d", $arrList['end_hour']).":".sprintf("%02d", $arrList['end_minute']).":00";

        // ポイントレートは設定されていなければ0を挿入
        if($arrInfo['point_rate'] == "") $arrInfo['point_rate'] = "0";
        // フラグは設定されていなければ0を挿入
        if(!$arrList['limit_count']) $arrList['limit_count'] = "0";
        if(!$arrList['orverlapping_flg']) $arrList['orverlapping_flg'] = "0";
        if(!$arrList['cart_flg']) $arrList['cart_flg'] = "0";
        if(!$arrList['deliv_free_flg']) $arrList['deliv_free_flg'] = "0";

        $sqlval['campaign_name'] = $arrList['campaign_name'];
        $sqlval['campaign_point_rate'] = $arrInfo['point_rate'];
        $sqlval['start_date'] = $start_date;
        $sqlval['end_date'] = $end_date;
        $sqlval['directory_name'] = $arrList['directory_name'];
        $sqlval['limit_count'] = $arrList['limit_count'];
        $sqlval['orverlapping_flg'] = $arrList['orverlapping_flg'];
        $sqlval['cart_flg'] = $arrList['cart_flg'];
        $sqlval['deliv_free_flg'] = $arrList['deliv_free_flg'];
        $sqlval['update_date'] = "now()";

        // キャンペーンIDで指定されている場合はupdate
        if($campaign_id != "") {

            // 元のディレクトリ名を取得名
            $directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($campaign_id));
            // ファイル名を変更
            @rename(CAMPAIGN_TEMPLATE_PATH . $directory_name , CAMPAIGN_TEMPLATE_PATH . $arrList['directory_name']);
            @rename(CAMPAIGN_PATH . $directory_name , CAMPAIGN_PATH . $arrList['directory_name']);

            // update
            $objQuery->update("dtb_campaign", $sqlval, "campaign_id = ?", array($campaign_id));

        } else {

            // キャンペーンページディレクトリ作成
            $this->lfCreateTemplate(CAMPAIGN_TEMPLATE_PATH, $arrList['directory_name'], $objFormParam);

            $sqlval['create_date'] = "now()";
            // insert
            $objQuery->insert("dtb_campaign", $sqlval);
        }
    }

    /*
     * 関数名：lfGetCampaignList()
     * 説明　：キャンペーン一覧を取得
     * 戻り値：キャンペーン一覧配列
     */
    function lfGetCampaignList(&$objQuery) {

        $col = "campaign_id,campaign_name,directory_name,total_count";
        $objQuery->setOrder("update_date DESC");
        $arrRet = $objQuery->select($col, "dtb_campaign", "del_flg = 0");

        return $arrRet;
    }

    /*
     * 関数名：lfGetCampaign()
     * 引数1 ：キャンペーンID
     * 説明　：キャンペーン情報取得
     * 戻り値：キャンペーン情報配列
     */
    function lfGetCampaign($campaign_id, &$objQuery) {

        $col = "campaign_id,campaign_name,start_date,end_date,directory_name,limit_count,orverlapping_flg,cart_flg,deliv_free_flg";
        $arrRet = $objQuery->select($col, "dtb_campaign", "campaign_id = ?", array($campaign_id));

        // 開始日時・停止日時を分解
        $start_date = (date("Y/m/d/H/i/s" , strtotime($arrRet[0]['start_date'])));
        list($arrRet[0]['start_year'],$arrRet[0]['start_month'],$arrRet[0]['start_day'],$arrRet[0]['start_hour'], $arrRet[0]['start_minute'], $arrRet[0]['start_second']) = split("/", $start_date);
        $end_date = (date("Y/m/d/H/i/s" , strtotime($arrRet[0]['end_date'])));
        list($arrRet[0]['end_year'],$arrRet[0]['end_month'],$arrRet[0]['end_day'],$arrRet[0]['end_hour'], $arrRet[0]['end_minute'], $arrRet[0]['end_second']) = split("/", $end_date);

        return $arrRet[0];
    }

    /*
     * 関数名：lfDeleteCampaign()
     * 引数1 ：キャンペーンID
     * 説明　：キャンペーン削除
     * 戻り値：無し
     */
    function lfDeleteCampaign($campaign_id, &$objQuery) {
        $objFileManager = new SC_Helper_FileManager_Ex();

        // ディレクトリ名を取得名
        $directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($campaign_id));
        // ファイルを削除
        $objFileManager->sfDeleteDir(CAMPAIGN_TEMPLATE_PATH . $directory_name);
        $objFileManager->sfDeleteDir(CAMPAIGN_PATH . $directory_name);

        $sqlval['del_flg'] = 1;
        $sqlval['update_date'] = "now()";
        // delete
        $objQuery->update("dtb_campaign", $sqlval, "campaign_id = ?", array($campaign_id));
    }

    /*
     * 関数名：lfCreateTemplate()
     * 引数1 ：ディレクトリパス
     * 引数2 ：作成ファイル名
     * 説明　：キャンペーンの初期テンプレート作成
     * 戻り値：無し
     */
    function lfCreateTemplate($dir, $file, &$objFormParam) {

        $objFileManager = new SC_Helper_FileManager_Ex();
        $arrRet = $objFormParam->getHashArray();


        // 作成ファイルディレクトリ
        $create_dir = $dir . $file;
        $create_active_dir = $create_dir . "/" . CAMPAIGN_TEMPLATE_ACTIVE;
        $create_end_dir = $create_dir . "/" . CAMPAIGN_TEMPLATE_END;
        // デフォルトファイルディレクトリ
        $default_dir = TEMPLATE_DIR . CAMPAIGN_TEMPLATE_DIR;
        $default_active_dir = $default_dir . "/" . CAMPAIGN_TEMPLATE_ACTIVE;
        $default_end_dir = $default_dir . "/" . CAMPAIGN_TEMPLATE_END;

        $ret = $objFileManager->sfCreateFile($create_dir, 0755);
        $ret = $objFileManager->sfCreateFile($create_active_dir, 0755);
        $ret = $objFileManager->sfCreateFile($create_end_dir, 0755);

        // キャンペーン実行PHPをコピー
        $ret = $objFileManager->sfCreateFile(CAMPAIGN_PATH . $file);
        copy(HTML_PATH . CAMPAIGN_TEMPLATE_DIR . "index.php", CAMPAIGN_PATH . $file . "/index.php");
        copy(HTML_PATH . CAMPAIGN_TEMPLATE_DIR . "application.php", CAMPAIGN_PATH . $file . "/application.php");
        copy(HTML_PATH . CAMPAIGN_TEMPLATE_DIR . "complete.php", CAMPAIGN_PATH . $file . "/complete.php");
        copy(HTML_PATH . CAMPAIGN_TEMPLATE_DIR . "entry.php", CAMPAIGN_PATH . $file . "/entry.php");

        // デフォルトテンプレート作成(キャンペーン中)
        $header = $this->lfGetFileContents($default_active_dir."header.tpl");
        SC_Utils_Ex::sfWriteFile($header, $create_active_dir."header.tpl", "w");
        $contents = $this->lfGetFileContents($default_active_dir."contents.tpl");
        if(!$arrRet['cart_flg']) {
            $contents .= "\n" . '<!--{*ログインフォーム*}-->' . "\n";
            $contents .= $this->lfGetFileContents(CAMPAIGN_BLOC_PATH . "login.tpl");
            $contents .= '<!--{*会員登録フォーム*}-->'."\n";
            $contents .= $this->lfGetFileContents(CAMPAIGN_BLOC_PATH . "entry.tpl");
        }
        SC_Utils_Ex::sfWriteFile($contents, $create_active_dir."contents.tpl", "w");
        $footer = $this->lfGetFileContents($default_active_dir."footer.tpl");
        SC_Utils_Ex::sfWriteFile($footer, $create_active_dir."footer.tpl", "w");

        // サイトフレーム作成
        $site_frame  = $header."\n";
        $site_frame .= '<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>'."\n";
        $site_frame .= '<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>'."\n";
        $site_frame .= '<!--{include file=$tpl_mainpage}-->'."\n";
        $site_frame .= $footer."\n";
        SC_Utils_Ex::sfWriteFile($site_frame, $create_active_dir."site_frame.tpl", "w");

        /* デフォルトテンプレート作成(キャンペーン終了) */
        $header = $this->lfGetFileContents($default_end_dir."header.tpl");
        SC_Utils_Ex::sfWriteFile($header, $create_end_dir."header.tpl", "w");
        $contents = $this->lfGetFileContents($default_end_dir."contents.tpl");
        SC_Utils_Ex::sfWriteFile($contents, $create_end_dir."contents.tpl", "w");
        $footer = $this->lfGetFileContents($default_end_dir."footer.tpl");
        SC_Utils_Ex::sfWriteFile($footer, $create_end_dir."footer.tpl", "w");
    }

    /*
     * 関数名：lfGetFileContents()
     * 引数1 ：ファイルパス
     * 説明　：ファイル読込
     * 戻り値：無し
     */
    function lfGetFileContents($read_file) {

        if(file_exists($read_file)) {
            $contents = file_get_contents($read_file);
        } else {
            $contents = "";
        }

        return $contents;
    }
}
?>
