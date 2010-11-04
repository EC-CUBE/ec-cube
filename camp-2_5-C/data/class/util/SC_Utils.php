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

/**
 * 各種ユーティリティクラス.
 *
 * 主に static 参照するユーティリティ系の関数群
 *
 * :XXX: 内部でインスタンスを生成している関数は, Helper クラスへ移動するべき...
 *
 * @package Util
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_Utils.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_Utils {

    /**
     * サイト管理情報から値を取得する。
     * データが存在する場合、必ず1以上の数値が設定されている。
     * 0を返した場合は、呼び出し元で対応すること。
     *
     * @param $control_id 管理ID
     * @param $dsn DataSource
     * @return $control_flg フラグ
     */
    function sfGetSiteControlFlg($control_id, $dsn = "") {

        // データソース
        if($dsn == "") {
            if(defined('DEFAULT_DSN')) {
                $dsn = DEFAULT_DSN;
            } else {
                return;
            }
        }

        // クエリ生成
        $target_column = "control_flg";
        $table_name = "dtb_site_control";
        $where = "control_id = ?";
        $arrval = array($control_id);
        $control_flg = 0;

        // クエリ発行
        $objQuery = new SC_Query($dsn, true, true);
        $arrSiteControl = $objQuery->select($target_column, $table_name, $where, $arrval);

        // データが存在すればフラグを取得する
        if (count($arrSiteControl) > 0) {
            $control_flg = $arrSiteControl[0]["control_flg"];
        }

        return $control_flg;
    }

    // インストール初期処理
    function sfInitInstall() {
        // インストール済みが定義されていない。
        if (!defined('ECCUBE_INSTALL')) {
            $phpself = $_SERVER['PHP_SELF'];
            if( !ereg('/install/', $phpself) ) {
                $path = substr($phpself, 0, strpos($phpself, basename($phpself)));
                $install_url = SC_Utils::searchInstallerPath($path);
                header('Location: ' . $install_url);
                exit;
            }
        }
        $path = HTML_PATH . "install/index.php";
        if(file_exists($path)) {
            SC_Utils::sfErrorHeader("&gt;&gt; /install/index.phpは、インストール完了後にファイルを削除してください。");
        }
    }

    /**
     * インストーラのパスを検索し, URL を返す.
     *
     * $path と同階層に install/index.php があるか検索する.
     * 存在しない場合は上位階層を再帰的に検索する.
     * インストーラのパスが見つかった場合は, その URL を返す.
     * DocumentRoot まで検索しても見つからない場合は /install/index.php を返す.
     *
     * @param string $path 検索対象のパス
     * @return string インストーラの URL
     */
    function searchInstallerPath($path) {
        $installer = 'install/index.php';

        if (SC_Utils::sfIsHTTPS()) {
            $proto = "https://";
        } else {
            $proto = "http://";
        }
        $host = $proto . $_SERVER['SERVER_NAME'];
        if ($path == '/') {
            return $host . $path . $installer;
        }
        if (substr($path, -1, 1) != '/') {
            $path .= $path . '/';
        }
        $installer_url = $host . $path . $installer;
        $resources = fopen(SC_Utils::getRealURL($installer_url), 'r');
        if ($resources === false) {
            $installer_url = SC_Utils::searchInstallerPath($path . '../');
        }
        return $installer_url;
    }

    /**
     * 相対パスで記述された URL から絶対パスの URL を取得する.
     *
     * この関数は, http(s):// から始まる URL を解析し, 相対パスで記述されていた
     * 場合, 絶対パスに変換して返す
     *
     * 例)
     * http://www.example.jp/aaa/../index.php
     * ↓
     * http://www.example.jp/index.php
     *
     * @param string $url http(s):// から始まる URL
     * @return string $url を絶対パスに変換した URL
     */
    function getRealURL($url) {
        $parse = parse_url($url);
        $tmp = split('/', $parse['path']);
        $results = array();
        foreach ($tmp as $v) {
            if ($v == '' || $v == '.') {
                // queit.
            } elseif ($v == '..') {
                array_pop($results);
            } else {
                array_push($results, $v);
            }
        }

        $path = join('/', $results);
        return $parse['scheme'] . '://' . $parse['host'] . '/' . $path;
    }

    // 装飾付きエラーメッセージの表示
    function sfErrorHeader($mess, $print = false) {
        global $GLOBAL_ERR;
        $GLOBAL_ERR.="<div style='color: #F00; font-weight: bold; font-size: 12px;"
            . "background-color: #FEB; text-align: center; padding: 5px;'>";
        $GLOBAL_ERR.= $mess;
        $GLOBAL_ERR.= "</div>";
        if($print) {
            print($GLOBAL_ERR);
        }
    }

    /* エラーページの表示 */
    function sfDispError($type) {

        require_once(CLASS_EX_PATH . "page_extends/error/LC_Page_Error_DispError_Ex.php");

        $objPage = new LC_Page_Error_DispError_Ex();
        register_shutdown_function(array($objPage, "destroy"));
        $objPage->init();
        $objPage->type = $type;
        $objPage->process();
        exit;
    }

    /* サイトエラーページの表示 */
    function sfDispSiteError($type, $objSiteSess = "", $return_top = false, $err_msg = "") {
        global $objCampaignSess;

        require_once(CLASS_EX_PATH . "page_extends/error/LC_Page_Error_Ex.php");

        $objPage = new LC_Page_Error_Ex();
        register_shutdown_function(array($objPage, "destroy"));
        $objPage->init();
        $objPage->type = $type;
        $objPage->objSiteSess = $objSiteSess;
        $objPage->return_top = $return_top;
        $objPage->err_msg = $err_msg;
        $objPage->is_mobile = (defined('MOBILE_SITE')) ? true : false;
        $objPage->process();
        exit;
    }

    /**
     * 例外エラーページの表示
     *
     * @param string $debugMsg デバッグ用のメッセージ
     * @return void
     */
    function sfDispException($debugMsg = null) {
        require_once(CLASS_EX_PATH . "page_extends/error/LC_Page_Error_SystemError_Ex.php");

        $objPage = new LC_Page_Error_SystemError_Ex();
        register_shutdown_function(array($objPage, "destroy"));
        $objPage->init();
        if (!is_null($debugMsg)) {
            $objPage->addDebugMsg($debugMsg);
        }
        if (function_exists("debug_backtrace")) {
            $objPage->backtrace = debug_backtrace();
        }
        GC_Utils_Ex::gfPrintLog($objPage->sfGetErrMsg());
        $objPage->process();

        exit();
    }

    /* 認証の可否判定 */
    function sfIsSuccess($objSess, $disp_error = true) {
        $ret = $objSess->IsSuccess();
        if($ret != SUCCESS) {
            if($disp_error) {
                // エラーページの表示
                SC_Utils::sfDispError($ret);
            }
            return false;
        }
        // リファラーチェック(CSRFの暫定的な対策)
        // 「リファラ無」 の場合はスルー
        // 「リファラ有」 かつ 「管理画面からの遷移でない」 場合にエラー画面を表示する
        if ( empty($_SERVER['HTTP_REFERER']) ) {
            // TODO 警告表示させる？
            // sfErrorHeader('>> referrerが無効になっています。');
        } else {
            $domain  = SC_Utils::sfIsHTTPS() ? SSL_URL : SITE_URL;
            $pattern = sprintf('|^%s.*|', $domain);
            $referer = $_SERVER['HTTP_REFERER'];

            // 管理画面から以外の遷移の場合はエラー画面を表示
            if (!preg_match($pattern, $referer)) {
                if ($disp_error) SC_Utils::sfDispError(INVALID_MOVE_ERRORR);
                return false;
            }
        }
        return true;
    }

    /**
     * 文字列をアスタリスクへ変換する.
     *
     * @param string $passlen 変換する文字列
     * @return string アスタリスクへ変換した文字列
     */
    function lfPassLen($passlen){
        $ret = "";
        for ($i=0;$i<$passlen;true){
            $ret.="*";
            $i++;
        }
        return $ret;
    }

    /**
     * HTTPSかどうかを判定
     *
     * @return bool
     */
    function sfIsHTTPS () {
        // HTTPS時には$_SERVER['HTTPS']には空でない値が入る
        // $_SERVER['HTTPS'] != 'off' はIIS用
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  正規の遷移がされているかを判定
     *  前画面でuniqidを埋め込んでおく必要がある
     *  @param  obj  SC_Session, SC_SiteSession
     *  @return bool
     */
    function sfIsValidTransition($objSess) {
        // 前画面からPOSTされるuniqidが正しいものかどうかをチェック
        $uniqid = $objSess->getUniqId();
        if ( !empty($_POST['uniqid']) && ($_POST['uniqid'] === $uniqid) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 前のページで正しく登録が行われたか判定
     *
     * @deprecated SC_SiteSession::isPrePage() を使用して下さい
     */
    function sfIsPrePage(&$objSiteSess) {
        $ret = $objSiteSess->isPrePage();
        if($ret != true) {
            // エラーページの表示
            SC_Utils::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }
    }

    /**
     * @deprecated SC_CartSession クラスを使用してください
     */
    function sfCheckNormalAccess(&$objSiteSess, &$objCartSess) {
        // ユーザユニークIDの取得
        $uniqid = $objSiteSess->getUniqId();

        $cartkeys = $objCartSess->getKeys();

        foreach ($cartKeys as $cartKey) {
            // 購入ボタンを押した時のカート内容がコピーされていない場合のみコピーする。
            $objCartSess->saveCurrentCart($uniqid, $cartKey);
            // POSTのユニークIDとセッションのユニークIDを比較(ユニークIDがPOSTされていない場合はスルー)
            $ret = $objSiteSess->checkUniqId();
            if($ret != true) {
                // エラーページの表示
                SC_Utils_Ex::sfDispSiteError(CANCEL_PURCHASE, $objSiteSess);
            }

            // カート内が空でないか || 購入ボタンを押してから変化がないか
            $quantity = $objCartSess->getTotalQuantity($cartKey);
            $ret = $objCartSess->checkChangeCart($cartKey);
            if($ret == true || !($quantity > 0)) {
                // カート情報表示に強制移動する
                // FIXME false を返して, Page クラスで遷移させるべき...
                if (defined("MOBILE_SITE")) {
                    header("Location: ". MOBILE_URL_CART_TOP
                           . "?" . session_name() . "=" . session_id());
                } else {
                    header("Location: ".URL_CART_TOP);
                }
                exit;
            }
        }
        return $uniqid;
    }

    /* DB用日付文字列取得 */
    function sfGetTimestamp($year, $month, $day, $last = false) {
        if($year != "" && $month != "" && $day != "") {
            if($last) {
                $time = "23:59:59";
            } else {
                $time = "00:00:00";
            }
            $date = $year."-".$month."-".$day." ".$time;
        } else {
            $date = "";
        }
        return     $date;
    }

    /**
     *  INT型の数値チェック
     *  ・FIXME: マイナス値の扱いが不明確
     *  ・XXX: INT_LENには収まるが、INT型の範囲を超えるケースに対応できないのでは?
     *
     *  @param mixed $value
     *  @return bool
     */
    //
    function sfIsInt($value) {
        if (strlen($value) >= 1 && strlen($value) <= INT_LEN && is_numeric($value)) {
            return true;
        }
        return false;
    }

    /*
     * 桁が0で埋められているかを判定する
     *
     * @param string $value 検査対象
     * @return boolean 0で埋められている
     */
    function sfIsZeroFilling($value) {
        if (strlen($value) > 1 && $value{0} === '0')
            return true;
        return false;
    }

    function sfCSVDownload($data, $prefix = ""){

        if($prefix == "") {
            $dir_name = SC_Utils::sfUpDirName();
            $file_name = $dir_name . date("ymdHis") .".csv";
        } else {
            $file_name = $prefix . date("ymdHis") .".csv";
        }

        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename=${file_name}");
        Header("Content-type: application/octet-stream; name=${file_name}");
        Header("Cache-Control: ");
        Header("Pragma: ");

        if (mb_internal_encoding() == CHAR_CODE){
            $data = mb_convert_encoding($data,'SJIS-Win',CHAR_CODE);
        }

        /* データを出力 */
        echo $data;
    }

    /* 1階層上のディレクトリ名を取得する */
    function sfUpDirName() {
        $path = $_SERVER['PHP_SELF'];
        $arrVal = split("/", $path);
        $cnt = count($arrVal);
        return $arrVal[($cnt - 2)];
    }




    /**
     * 現在のサイトを更新（ただしポストは行わない）
     *
     * @deprecated LC_Page::reload() を使用して下さい.
     */
    function sfReload($get = "") {
        if ($_SERVER["SERVER_PORT"] == "443" ){
            $url = ereg_replace(URL_DIR . "$", "", SSL_URL);
        } else {
            $url = ereg_replace(URL_DIR . "$", "", SITE_URL);
        }

        if($get != "") {
            header("Location: ". $url . $_SERVER['PHP_SELF'] . "?" . $get);
        } else {
            header("Location: ". $url . $_SERVER['PHP_SELF']);
        }
        exit;
    }

    // チェックボックスの値をマージ
    function sfMergeCBValue($keyname, $max) {
        $conv = "";
        $cnt = 1;
        for($cnt = 1; $cnt <= $max; $cnt++) {
            if ($_POST[$keyname . $cnt] == "1") {
                $conv.= "1";
            } else {
                $conv.= "0";
            }
        }
        return $conv;
    }

    // html_checkboxesの値をマージして2進数形式に変更する。
    function sfMergeCheckBoxes($array, $max) {
        $ret = "";
        if(is_array($array)) {
            foreach($array as $val) {
                $arrTmp[$val] = "1";
            }
        }
        for($i = 1; $i <= $max; $i++) {
            if(isset($arrTmp[$i]) && $arrTmp[$i] == "1") {
                $ret.= "1";
            } else {
                $ret.= "0";
            }
        }
        return $ret;
    }


    // html_checkboxesの値をマージして「-」でつなげる。
    function sfMergeParamCheckBoxes($array) {
        $ret = '';
        if(is_array($array)) {
            foreach($array as $val) {
                if($ret != "") {
                    $ret.= "-$val";
                } else {
                    $ret = $val;
                }
            }
        } else {
            $ret = $array;
        }
        return $ret;
    }

    // html_checkboxesの値をマージしてSQL検索用に変更する。
    function sfSearchCheckBoxes($array) {
        $max = max($array);
        $ret = '';
        for ($i = 1; $i <= $max; $i++) {
            $ret .= in_array($i, $array) ? '1' : '_';
        }
        if (strlen($ret) != 0) {
            $ret .= '%';
        }
        return $ret;
    }

    // 2進数形式の値をhtml_checkboxes対応の値に切り替える
    function sfSplitCheckBoxes($val) {
        $arrRet = array();
        $len = strlen($val);
        for($i = 0; $i < $len; $i++) {
            if(substr($val, $i, 1) == "1") {
                $arrRet[] = ($i + 1);
            }
        }
        return $arrRet;
    }

    // チェックボックスの値をマージ
    function sfMergeCBSearchValue($keyname, $max) {
        $conv = "";
        $cnt = 1;
        for($cnt = 1; $cnt <= $max; $cnt++) {
            if ($_POST[$keyname . $cnt] == "1") {
                $conv.= "1";
            } else {
                $conv.= "_";
            }
        }
        return $conv;
    }

    // チェックボックスの値を分解
    function sfSplitCBValue($val, $keyname = "") {
        $arr = array();
        $len = strlen($val);
        $no = 1;
        for ($cnt = 0; $cnt < $len; $cnt++) {
            if($keyname != "") {
                $arr[$keyname . $no] = substr($val, $cnt, 1);
            } else {
                $arr[] = substr($val, $cnt, 1);
            }
            $no++;
        }
        return $arr;
    }

    // キーと値をセットした配列を取得
    function sfArrKeyValue($arrList, $keyname, $valname, $len_max = "", $keysize = "") {
        $arrRet = array();
        $max = count($arrList);

        if($len_max != "" && $max > $len_max) {
            $max = $len_max;
        }

        for($cnt = 0; $cnt < $max; $cnt++) {
            if($keysize != "") {
                $key = SC_Utils::sfCutString($arrList[$cnt][$keyname], $keysize);
            } else {
                $key = $arrList[$cnt][$keyname];
            }
            $val = $arrList[$cnt][$valname];

            if(!isset($arrRet[$key])) {
                $arrRet[$key] = $val;
            }

        }
        return $arrRet;
    }

    // キーと値をセットした配列を取得(値が複数の場合)
    function sfArrKeyValues($arrList, $keyname, $valname, $len_max = "", $keysize = "", $connect = "") {

        $max = count($arrList);

        if($len_max != "" && $max > $len_max) {
            $max = $len_max;
        }

        for($cnt = 0; $cnt < $max; $cnt++) {
            if($keysize != "") {
                $key = SC_Utils::sfCutString($arrList[$cnt][$keyname], $keysize);
            } else {
                $key = $arrList[$cnt][$keyname];
            }
            $val = $arrList[$cnt][$valname];

            if($connect != "") {
                $arrRet[$key].= "$val".$connect;
            } else {
                $arrRet[$key][] = $val;
            }
        }
        return $arrRet;
    }

    // 配列の値をカンマ区切りで返す。
    function sfGetCommaList($array, $space=true, $arrPop = array()) {
        if (count($array) > 0) {
            $line = "";
            foreach($array as $val) {
                if (!in_array($val, $arrPop)) {
                    if ($space) {
                        $line .= $val . ", ";
                    } else {
                        $line .= $val . ",";
                    }
                }
            }
            if ($space) {
                $line = ereg_replace(", $", "", $line);
            } else {
                $line = ereg_replace(",$", "", $line);
            }
            return $line;
        } else {
            return false;
        }

    }

    /* 配列の要素をCSVフォーマットで出力する。*/
    function sfGetCSVList($array) {
        $line = "";
        if (count($array) > 0) {
            foreach($array as $key => $val) {
                $val = mb_convert_encoding($val, CHAR_CODE, CHAR_CODE);
                $line .= "\"".$val."\",";
            }
            $line = ereg_replace(",$", "\r\n", $line);
        }else{
            return false;
        }
        return $line;
    }

    /* 配列の要素をPDFフォーマットで出力する。*/
    function sfGetPDFList($array) {
        foreach($array as $key => $val) {
            $line .= "\t".$val;
        }
        $line.="\n";
        return $line;
    }



    /*-----------------------------------------------------------------*/
    /*    check_set_term
    /*    年月日に別れた2つの期間の妥当性をチェックし、整合性と期間を返す
    /*　引数 (開始年,開始月,開始日,終了年,終了月,終了日)
    /*　戻値 array(１，２，３）
    /*          １．開始年月日 (YYYY/MM/DD 000000)
    /*            ２．終了年月日 (YYYY/MM/DD 235959)
    /*            ３．エラー ( 0 = OK, 1 = NG )
    /*-----------------------------------------------------------------*/
    function sfCheckSetTerm ( $start_year, $start_month, $start_day, $end_year, $end_month, $end_day ) {

        // 期間指定
        $error = 0;
        if ( $start_month || $start_day || $start_year){
            if ( ! checkdate($start_month, $start_day , $start_year) ) $error = 1;
        } else {
            $error = 1;
        }
        if ( $end_month || $end_day || $end_year){
            if ( ! checkdate($end_month ,$end_day ,$end_year) ) $error = 2;
        }
        if ( ! $error ){
            $date1 = $start_year ."/".sprintf("%02d",$start_month) ."/".sprintf("%02d",$start_day) ." 000000";
            $date2 = $end_year   ."/".sprintf("%02d",$end_month)   ."/".sprintf("%02d",$end_day)   ." 235959";
            if ($date1 > $date2) $error = 3;
        } else {
            $error = 1;
        }
        return array($date1, $date2, $error);
    }

    // エラー箇所の背景色を変更するためのfunction SC_Viewで読み込む
    function sfSetErrorStyle(){
        return 'style="background-color:'.ERR_COLOR.'"';
    }

    /* DBに渡す数値のチェック
     * 10桁以上はオーバーフローエラーを起こすので。
     */
    function sfCheckNumLength( $value ){
        if ( ! is_numeric($value)  ){
            return false;
        }

        if ( strlen($value) > 9 ) {
            return false;
        }

        return true;
    }

    // 一致した値のキー名を取得
    function sfSearchKey($array, $word, $default) {
        foreach($array as $key => $val) {
            if($val == $word) {
                return $key;
            }
        }
        return $default;
    }

    function sfGetErrorColor($val) {
        if($val != "") {
            return "background-color:" . ERR_COLOR;
        }
        return "";
    }

    function sfGetEnabled($val) {
        if( ! $val ) {
            return " disabled=\"disabled\"";
        }
        return "";
    }

    function sfGetChecked($param, $value) {
        if ((string)$param === (string)$value) {
            return "checked=\"checked\"";
        }
        return "";
    }

    function sfTrim($str) {
        $ret = mb_ereg_replace("^[　 \n\r]*", "", $str);
        $ret = mb_ereg_replace("[　 \n\r]*$", "", $ret);
        return $ret;
    }

    /**
     * 税金額を返す
     *
     * ・店舗基本情報に基づいた計算は SC_Helper_DB::sfTax() を使用する
     *
     * @param integer $price 計算対象の金額
     * @param integer $tax 税率(%単位)
     *     XXX integer のみか不明
     * @param integer $tax_rule 端数処理
     * @return integer 税金額
     */
    function sfTax($price, $tax, $tax_rule) {
        $real_tax = $tax / 100;
        $ret = $price * $real_tax;
        switch($tax_rule) {
        // 四捨五入
        case 1:
            $ret = round($ret);
            break;
        // 切り捨て
        case 2:
            $ret = floor($ret);
            break;
        // 切り上げ
        case 3:
            $ret = ceil($ret);
            break;
        // デフォルト:切り上げ
        default:
            $ret = ceil($ret);
            break;
        }
        return $ret;
    }

    /**
     * 税金付与した金額を返す
     *
     * ・店舗基本情報に基づいた計算は SC_Helper_DB::sfTax() を使用する
     *
     * @param integer $price 計算対象の金額
     * @param integer $tax 税率(%単位)
     *     XXX integer のみか不明
     * @param integer $tax_rule 端数処理
     * @return integer 税金付与した金額
     */
    function sfPreTax($price, $tax, $tax_rule) {
        return $price + SC_Utils_Ex::sfTax($price, $tax, $tax_rule);
    }

    // 桁数を指定して四捨五入
    function sfRound($value, $pow = 0){
        $adjust = pow(10 ,$pow-1);

        // 整数且つ0出なければ桁数指定を行う
        if(SC_Utils::sfIsInt($adjust) and $pow > 1){
            $ret = (round($value * $adjust)/$adjust);
        }

        $ret = round($ret);

        return $ret;
    }

    /* ポイント付与 */
    function sfPrePoint($price, $point_rate, $rule = POINT_RULE, $product_id = "") {
        $real_point = $point_rate / 100;
        $ret = $price * $real_point;
        switch($rule) {
        // 四捨五入
        case 1:
            $ret = round($ret);
            break;
        // 切り捨て
        case 2:
            $ret = floor($ret);
            break;
        // 切り上げ
        case 3:
            $ret = ceil($ret);
            break;
        // デフォルト:切り上げ
        default:
            $ret = ceil($ret);
            break;
        }
        return $ret;
    }

    /* 規格分類の件数取得 */
    function sfGetClassCatCount() {
        $sql = "select count(dtb_class.class_id) as count, dtb_class.class_id ";
        $sql.= "from dtb_class inner join dtb_classcategory on dtb_class.class_id = dtb_classcategory.class_id ";
        $sql.= "where dtb_class.del_flg = 0 AND dtb_classcategory.del_flg = 0 ";
        $sql.= "group by dtb_class.class_id, dtb_class.name";
        $objQuery = new SC_Query();
        $arrList = $objQuery->getAll($sql);
        // キーと値をセットした配列を取得
        $arrRet = SC_Utils::sfArrKeyValue($arrList, 'class_id', 'count');

        return $arrRet;
    }

    function sfGetProductClassId($product_id, $classcategory_id1, $classcategory_id2) {
        // $where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
        $where = "product_id = ?";
        $objQuery = new SC_Query();
        // $ret = $objQuery->get("dtb_products_class", "product_class_id", $where, Array($product_id, $classcategory_id1, $classcategory_id2));
        $ret = $objQuery->get("dtb_products_class", "product_class_id", $where, Array($product_id));
        return $ret;
    }

    /* 文末の「/」をなくす */
    function sfTrimURL($url) {
        $ret = ereg_replace("[/]+$", "", $url);
        return $ret;
    }

    /* DBから取り出した日付の文字列を調整する。*/
    function sfDispDBDate($dbdate, $time = true) {
        list($y, $m, $d, $H, $M) = split("[- :]", $dbdate);

        if(strlen($y) > 0 && strlen($m) > 0 && strlen($d) > 0) {
            if ($time) {
                $str = sprintf("%04d/%02d/%02d %02d:%02d", $y, $m, $d, $H, $M);
            } else {
                $str = sprintf("%04d/%02d/%02d", $y, $m, $d, $H, $M);
            }
        } else {
            $str = "";
        }
        return $str;
    }

    /* 配列をキー名ごとの配列に変更する */
    function sfSwapArray($array, $isColumnName = true) {
        $arrRet = array();
        $max = count($array);
        for($i = 0; $i < $max; $i++) {
            $j = 0;
            foreach($array[$i] as $key => $val) {
                if ($isColumnName) {
                    $arrRet[$key][] = $val;
                } else {
                    $arrRet[$j][] = $val;
                }
                $j++;
            }
        }
        return $arrRet;
    }

    /**
     * 連想配列から新たな配列を生成して返す.
     *
     * $requires が指定された場合, $requires に含まれるキーの値のみを返す.
     *
     * @param array 連想配列
     * @param array 必須キーの配列
     * @return array 連想配列の値のみの配列
     */
    function getHash2Array($hash, $requires = array()) {
        $array = array();
        $i = 0;
        foreach ($hash as $key => $val) {
            if (!empty($requires)) {
                if (in_array($key, $requires)) {
                    $array[$i] = $val;
                    $i++;
                }
            } else {
                $array[$i] = $val;
                $i++;
            }
        }
        return $array;
    }

    /* かけ算をする（Smarty用) */
    function sfMultiply($num1, $num2) {
        return ($num1 * $num2);
    }

    /**
     * 加算ポイントの計算
     *
     * ・店舗基本情報に基づいた計算は SC_Helper_DB::sfGetAddPoint() を使用する
     *
     * @param integer $totalpoint
     * @param integer $use_point
     * @param integer $point_rate
     * @return integer 加算ポイント
     */
    function sfGetAddPoint($totalpoint, $use_point, $point_rate) {
        // 購入商品の合計ポイントから利用したポイントのポイント換算価値を引く方式
        $add_point = $totalpoint - intval($use_point * ($point_rate / 100));

        if($add_point < 0) {
            $add_point = '0';
        }
        return $add_point;
    }

    /* 一意かつ予測されにくいID */
    function sfGetUniqRandomId($head = "") {
        // 予測されないようにランダム文字列を付与する。
        $random = GC_Utils_Ex::gfMakePassword(8);
        // 同一ホスト内で一意なIDを生成
        $id = uniqid($head);
        return ($id . $random);
    }

    /**
     * ドメイン間で有効なセッションのスタート
     * 共有SSL対応のための修正により、この関数は廃止します。
     * セッションはrequire.phpを読み込んだ際に開始されます。
     */
    function sfDomainSessionStart() {
        /**
         * 2.1.1ベータからはSC_SessionFactory_UseCookie::initSession()で処理するため、
         * ここでは何も処理しない
         */
        if (defined('SESSION_KEEP_METHOD')) {
            return;
        }

        if (session_id() === "") {

            session_set_cookie_params(0, "/", DOMAIN_NAME);

            if (!ini_get("session.auto_start")) {
                // セッション開始
                session_start();
            }
        }
    }

    /* 文字列に強制的に改行を入れる */
    function sfPutBR($str, $size) {
        $i = 0;
        $cnt = 0;
        $line = array();
        $ret = "";

        while($str[$i] != "") {
            $line[$cnt].=$str[$i];
            $i++;
            if(strlen($line[$cnt]) > $size) {
                $line[$cnt].="<br />";
                $cnt++;
            }
        }

        foreach($line as $val) {
            $ret.=$val;
        }
        return $ret;
    }

    // 二回以上繰り返されているスラッシュ[/]を一つに変換する。
    function sfRmDupSlash($istr){
        if(ereg("^http://", $istr)) {
            $str = substr($istr, 7);
            $head = "http://";
        } else if(ereg("^https://", $istr)) {
            $str = substr($istr, 8);
            $head = "https://";
        } else {
            $str = $istr;
        }
        $str = ereg_replace("[/]+", "/", $str);
        $ret = $head . $str;
        return $ret;
    }

    /**
     * テキストファイルの文字エンコーディングを変換する.
     *
     * $filepath に存在するテキストファイルの文字エンコーディングを変換する.
     * 変換前の文字エンコーディングは, mb_detect_order で設定した順序で自動検出する.
     * 変換後は, 変換前のファイル名に「enc_」というプレフィクスを付与し,
     * $out_dir で指定したディレクトリへ出力する
     *
     * TODO $filepath のファイルがバイナリだった場合の扱い
     * TODO fwrite などでのエラーハンドリング
     *
     * @access public
     * @param string $filepath 変換するテキストファイルのパス
     * @param string $enc_type 変換後のファイルエンコーディングの種類を表す文字列
     * @param string $out_dir 変換後のファイルを出力するディレクトリを表す文字列
     * @return string 変換後のテキストファイルのパス
     */
    function sfEncodeFile($filepath, $enc_type, $out_dir) {
        $ifp = fopen($filepath, "r");

        // 正常にファイルオープンした場合
        if ($ifp !== false) {

            $basename = basename($filepath);
            $outpath = $out_dir . "enc_" . $basename;

            $ofp = fopen($outpath, "w+");

            while(!feof($ifp)) {
                $line = fgets($ifp);
                $line = mb_convert_encoding($line, $enc_type, "auto");
                fwrite($ofp,  $line);
            }

            fclose($ofp);
            fclose($ifp);
        }
        // ファイルが開けなかった場合はエラーページを表示
          else {
              SC_Utils::sfDispError('');
              exit;
        }
        return     $outpath;
    }

    function sfCutString($str, $len, $byte = true, $commadisp = true) {
        if($byte) {
            if(strlen($str) > ($len + 2)) {
                $ret =substr($str, 0, $len);
                $cut = substr($str, $len);
            } else {
                $ret = $str;
                $commadisp = false;
            }
        } else {
            if(mb_strlen($str) > ($len + 1)) {
                $ret = mb_substr($str, 0, $len);
                $cut = mb_substr($str, $len);
            } else {
                $ret = $str;
                $commadisp = false;
            }
        }

        // 絵文字タグの途中で分断されないようにする。
        if (isset($cut)) {
            // 分割位置より前の最後の [ 以降を取得する。
            $head = strrchr($ret, '[');

            // 分割位置より後の最初の ] 以前を取得する。
            $tail_pos = strpos($cut, ']');
            if ($tail_pos !== false) {
                $tail = substr($cut, 0, $tail_pos + 1);
            }

            // 分割位置より前に [、後に ] が見つかった場合は、[ から ] までを
            // 接続して絵文字タグ1個分になるかどうかをチェックする。
            if ($head !== false && $tail_pos !== false) {
                $subject = $head . $tail;
                if (preg_match('/^\[emoji:e?\d+\]$/', $subject)) {
                    // 絵文字タグが見つかったので削除する。
                    $ret = substr($ret, 0, -strlen($head));
                }
            }
        }

        if($commadisp){
            $ret = $ret . "...";
        }
        return $ret;
    }

    // 年、月、締め日から、先月の締め日+1、今月の締め日を求める。
    function sfTermMonth($year, $month, $close_day) {
        $end_year = $year;
        $end_month = $month;

        // 開始月が終了月と同じか否か
        $same_month = false;

        // 該当月の末日を求める。
        $end_last_day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

        // 月の末日が締め日より少ない場合
        if($end_last_day < $close_day) {
            // 締め日を月末日に合わせる
            $end_day = $end_last_day;
        } else {
            $end_day = $close_day;
        }

        // 前月の取得
        $tmp_year = date("Y", mktime(0, 0, 0, $month, 0, $year));
        $tmp_month = date("m", mktime(0, 0, 0, $month, 0, $year));
        // 前月の末日を求める。
        $start_last_day = date("d", mktime(0, 0, 0, $month, 0, $year));

        // 前月の末日が締め日より少ない場合
        if ($start_last_day < $close_day) {
            // 月末日に合わせる
            $tmp_day = $start_last_day;
        } else {
            $tmp_day = $close_day;
        }

        // 先月の末日の翌日を取得する
        $start_year = date("Y", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
        $start_month = date("m", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
        $start_day = date("d", mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));

        // 日付の作成
        $start_date = sprintf("%d/%d/%d 00:00:00", $start_year, $start_month, $start_day);
        $end_date = sprintf("%d/%d/%d 23:59:59", $end_year, $end_month, $end_day);

        return array($start_date, $end_date);
    }

    // PDF用のRGBカラーを返す
    function sfGetPdfRgb($hexrgb) {
        $hex = substr($hexrgb, 0, 2);
        $r = hexdec($hex) / 255;

        $hex = substr($hexrgb, 2, 2);
        $g = hexdec($hex) / 255;

        $hex = substr($hexrgb, 4, 2);
        $b = hexdec($hex) / 255;

        return array($r, $g, $b);
    }

    // 再帰的に多段配列を検索して一次元配列(Hidden引渡し用配列)に変換する。
    function sfMakeHiddenArray($arrSrc, $arrDst = array(), $parent_key = "") {
        if(is_array($arrSrc)) {
            foreach($arrSrc as $key => $val) {
                if($parent_key != "") {
                    $keyname = $parent_key . "[". $key . "]";
                } else {
                    $keyname = $key;
                }
                if(is_array($val)) {
                    $arrDst = SC_Utils::sfMakeHiddenArray($val, $arrDst, $keyname);
                } else {
                    $arrDst[$keyname] = $val;
                }
            }
        }
        return $arrDst;
    }

    // DB取得日時をタイムに変換
    function sfDBDatetoTime($db_date) {
        $date = ereg_replace("\..*$","",$db_date);
        $time = strtotime($date);
        return $time;
    }

    /**
     * テンプレートを切り替えて出力する
     *
     * @deprecated 2008/04/02以降使用不可
     */
    function sfCustomDisplay(&$objPage, $is_mobile = false) {
        $basename = basename($_SERVER["REQUEST_URI"]);

        if($basename == "") {
            $path = $_SERVER["REQUEST_URI"] . DIR_INDEX_URL;
        } else {
            $path = $_SERVER["REQUEST_URI"];
        }

        if(isset($_GET['tpl']) && $_GET['tpl'] != "") {
            $tpl_name = $_GET['tpl'];
        } else {
            $tpl_name = ereg_replace("^/", "", $path);
            $tpl_name = ereg_replace("/", "_", $tpl_name);
            $tpl_name = ereg_replace("(\.php$|\.html$)", ".tpl", $tpl_name);
        }

        $template_path = TEMPLATE_FTP_DIR . $tpl_name;
echo $template_path;
        if($is_mobile === true) {
            $objView = new SC_MobileView();
            $objView->assignobj($objPage);
            $objView->display(SITE_FRAME);
        } else if(file_exists($template_path)) {
            $objView = new SC_UserView(TEMPLATE_FTP_DIR, COMPILE_FTP_DIR);
            $objView->assignobj($objPage);
            $objView->display($tpl_name);
        } else {
            $objView = new SC_SiteView();
            $objView->assignobj($objPage);
            $objView->display(SITE_FRAME);
        }
    }

    // PHPのmb_convert_encoding関数をSmartyでも使えるようにする
    function sf_mb_convert_encoding($str, $encode = 'CHAR_CODE') {
        return  mb_convert_encoding($str, $encode);
    }

    // PHPのmktime関数をSmartyでも使えるようにする
    function sf_mktime($format, $hour=0, $minute=0, $second=0, $month=1, $day=1, $year=1999) {
        return  date($format,mktime($hour, $minute, $second, $month, $day, $year));
    }

    // PHPのdate関数をSmartyでも使えるようにする
    function sf_date($format, $timestamp = '') {
        return  date( $format, $timestamp);
    }

    // チェックボックスの型を変換する
    function sfChangeCheckBox($data , $tpl = false){
        if ($tpl) {
            if ($data == 1){
                return 'checked';
            }else{
                return "";
            }
        }else{
            if ($data == "on"){
                return 1;
            }else{
                return 2;
            }
        }
    }

    // 2つの配列を用いて連想配列を作成する
    function sfarrCombine($arrKeys, $arrValues) {

        if(count($arrKeys) <= 0 and count($arrValues) <= 0) return array();

        $keys = array_values($arrKeys);
        $vals = array_values($arrValues);

        $max = max( count( $keys ), count( $vals ) );
        $combine_ary = array();
        for($i=0; $i<$max; $i++) {
            $combine_ary[$keys[$i]] = $vals[$i];
        }
        if(is_array($combine_ary)) return $combine_ary;

        return false;
    }

    /* 子ID所属する親IDを取得する */
    function sfGetParentsArraySub($arrData, $pid_name, $id_name, $child) {
        $max = count($arrData);
        $parent = "";
        for($i = 0; $i < $max; $i++) {
            if($arrData[$i][$id_name] == $child) {
                $parent = $arrData[$i][$pid_name];
                break;
            }
        }
        return $parent;
    }

    /* 階層構造のテーブルから与えられたIDの兄弟を取得する */
    function sfGetBrothersArray($arrData, $pid_name, $id_name, $arrPID) {
        $max = count($arrData);

        $arrBrothers = array();
        foreach($arrPID as $id) {
            // 親IDを検索する
            for($i = 0; $i < $max; $i++) {
                if($arrData[$i][$id_name] == $id) {
                    $parent = $arrData[$i][$pid_name];
                    break;
                }
            }
            // 兄弟IDを検索する
            for($i = 0; $i < $max; $i++) {
                if($arrData[$i][$pid_name] == $parent) {
                    $arrBrothers[] = $arrData[$i][$id_name];
                }
            }
        }
        return $arrBrothers;
    }

    /* 階層構造のテーブルから与えられたIDの直属の子を取得する */
    function sfGetUnderChildrenArray($arrData, $pid_name, $id_name, $parent) {
        $max = count($arrData);

        $arrChildren = array();
        // 子IDを検索する
        for($i = 0; $i < $max; $i++) {
            if($arrData[$i][$pid_name] == $parent) {
                $arrChildren[] = $arrData[$i][$id_name];
            }
        }
        return $arrChildren;
    }

    /**
     * SQLシングルクォート対応
     * @deprecated SC_Query::quote() を使用すること
     */
    function sfQuoteSmart($in){

        if (is_int($in) || is_double($in)) {
            return $in;
        } elseif (is_bool($in)) {
            return $in ? 1 : 0;
        } elseif (is_null($in)) {
            return 'NULL';
        } else {
            return "'" . str_replace("'", "''", $in) . "'";
        }
    }

    // ディレクトリを再帰的に生成する
    function sfMakeDir($path) {
        static $count = 0;
        $count++;  // 無限ループ回避
        $dir = dirname($path);
        if(ereg("^[/]$", $dir) || ereg("^[A-Z]:[\\]$", $dir) || $count > 256) {
            // ルートディレクトリで終了
            return;
        } else {
            if(is_writable(dirname($dir))) {
                if(!file_exists($dir)) {
                    mkdir($dir);
                    GC_Utils::gfPrintLog("mkdir $dir");
                }
            } else {
                SC_Utils::sfMakeDir($dir);
                if(is_writable(dirname($dir))) {
                    if(!file_exists($dir)) {
                        mkdir($dir);
                        GC_Utils::gfPrintLog("mkdir $dir");
                    }
                }
           }
        }
        return;
    }

    // ディレクトリ以下のファイルを再帰的にコピー
    function sfCopyDir($src, $des, $mess = "", $override = false){
        if(!is_dir($src)){
            return false;
        }

        $oldmask = umask(0);
        $mod= stat($src);

        // ディレクトリがなければ作成する
        if(!file_exists($des)) {
            if(!mkdir($des, $mod[2])) {
                print("path:" . $des);
            }
        }

        $fileArray=glob( $src."*" );
        if (is_array($fileArray)) {
            foreach( $fileArray as $key => $data_ ){
                // CVS管理ファイルはコピーしない
                if(ereg("/CVS/Entries", $data_)) {
                    break;
                }
                if(ereg("/CVS/Repository", $data_)) {
                    break;
                }
                if(ereg("/CVS/Root", $data_)) {
                    break;
                }

                mb_ereg("^(.*[\/])(.*)",$data_, $matches);
                $data=$matches[2];
                if( is_dir( $data_ ) ){
                    $mess = SC_Utils::sfCopyDir( $data_.'/', $des.$data.'/', $mess);
                }else{
                    if(!$override && file_exists($des.$data)) {
                        $mess.= $des.$data . "：ファイルが存在します\n";
                    } else {
                        if(@copy( $data_, $des.$data)) {
                            $mess.= $des.$data . "：コピー成功\n";
                        } else {
                            $mess.= $des.$data . "：コピー失敗\n";
                        }
                    }
                    $mod=stat($data_ );
                }
            }
        }
        umask($oldmask);
        return $mess;
    }

    // 指定したフォルダ内のファイルを全て削除する
    function sfDelFile($dir){
        if(file_exists($dir)) {
            $dh = opendir($dir);
            // フォルダ内のファイルを削除
            while($file = readdir($dh)){
                if ($file == "." or $file == "..") continue;
                $del_file = $dir . "/" . $file;
                if(is_file($del_file)){
                    $ret = unlink($dir . "/" . $file);
                }else if (is_dir($del_file)){
                    $ret = SC_Utils::sfDelFile($del_file);
                }

                if(!$ret){
                    return $ret;
                }
            }

            // 閉じる
            closedir($dh);

            // フォルダを削除
            return rmdir($dir);
        }
    }

    /*
     * 関数名：sfWriteFile
     * 引数1 ：書き込むデータ
     * 引数2 ：ファイルパス
     * 引数3 ：書き込みタイプ
     * 引数4 ：パーミッション
     * 戻り値：結果フラグ 成功なら true 失敗なら false
     * 説明　：ファイル書き出し
     */
    function sfWriteFile($str, $path, $type, $permission = "") {
        //ファイルを開く
        if (!($file = fopen ($path, $type))) {
            return false;
        }

        //ファイルロック
        flock ($file, LOCK_EX);
        //ファイルの書き込み
        fputs ($file, $str);
        //ファイルロックの解除
        flock ($file, LOCK_UN);
        //ファイルを閉じる
        fclose ($file);
        // 権限を指定
        if($permission != "") {
            chmod($path, $permission);
        }

        return true;
    }

    /**
     * ブラウザに強制的に送出する
     *
     * @param boolean|string $output 半角スペース256文字+改行を出力するか。または、送信する文字列を指定。
     * @return void
     */
    function sfFlush($output = false, $sleep = 0){
        // 出力をバッファリングしない(==日本語自動変換もしない)
        while (@ob_end_flush());

        if ($output === true) {
            // IEのために半角スペース256文字+改行を出力
            //echo str_repeat(' ', 256) . "\n";
            echo str_pad('', 256) . "\n";
        } else if ($output !== false) {
            echo $output;
        }

        // 出力をフラッシュする
        flush();

        ob_start();

        // 時間のかかる処理
        sleep($sleep);
    }

    // @versionの記載があるファイルからバージョンを取得する。
    function sfGetFileVersion($path) {
        if(file_exists($path)) {
            $src_fp = fopen($path, "rb");
            if($src_fp) {
                while (!feof($src_fp)) {
                    $line = fgets($src_fp);
                    if(ereg("@version", $line)) {
                        $arrLine = split(" ", $line);
                        $version = $arrLine[5];
                    }
                }
                fclose($src_fp);
            }
        }
        return $version;
    }

    // 指定したURLに対してPOSTでデータを送信する
    function sfSendPostData($url, $arrData, $arrOkCode = array()){
        require_once(DATA_PATH . "module/Request.php");

        // 送信インスタンス生成
        $req = new HTTP_Request($url);

        $req->addHeader('User-Agent', 'DoCoMo/2.0　P2101V(c100)');
        $req->setMethod(HTTP_REQUEST_METHOD_POST);

        // POSTデータ送信
        $req->addPostDataArray($arrData);

        // エラーが無ければ、応答情報を取得する
        if (!PEAR::isError($req->sendRequest())) {

            // レスポンスコードがエラー判定なら、空を返す
            $res_code = $req->getResponseCode();

            if(!in_array($res_code, $arrOkCode)){
                $response = "";
            }else{
                $response = $req->getResponseBody();
            }

        } else {
            $response = "";
        }

        // POSTデータクリア
        $req->clearPostData();

        return $response;
    }

    /**
     * $array の要素を $arrConvList で指定した方式で mb_convert_kana を適用する.
     *
     * @param array $array 変換する文字列の配列
     * @param array $arrConvList mb_convert_kana の適用ルール
     * @return array 変換後の配列
     * @see mb_convert_kana
     */
    function mbConvertKanaWithArray($array, $arrConvList) {
        foreach ($arrConvList as $key => $val) {
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    /**
     * 配列の添字が未定義の場合は空文字を代入して定義する.
     *
     * @param array $array 添字をチェックする配列
     * @param array $defineIndexes チェックする添字
     * @return array 添字を定義した配列
     */
    function arrayDefineIndexes($array, $defineIndexes) {
        foreach ($defineIndexes as $key) {
            if (!isset($array[$key])) $array[$key] = "";
        }
        return $array;
    }

    /**
     * $arrSrc のうち、キーが $arrKey に含まれるものを返す
     *
     * $arrSrc に含まない要素は返されない。
     *
     * @param array $arrSrc
     * @param array $arrKey
     * @return array
     */
    function sfArrayIntersectKeys($arrSrc, $arrKey) {
        $arrRet = array();
        foreach ($arrKey as $key) {
            if (isset($arrSrc[$key])) $arrRet[$key] = $arrSrc[$key];
        }
        return $arrRet;
    }

    /**
     * XML宣言を出力する.
     *
     * XML宣言があると問題が発生する UA は出力しない.
     *
     * @return string XML宣言の文字列
     */
    function printXMLDeclaration() {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (!preg_match("/MSIE/", $ua) || preg_match("/MSIE 7/", $ua)) {
            print("<?xml version='1.0' encoding='" . CHAR_CODE . "'?>\n");
        }
    }

    /*
     * 関数名：sfGetFileList()
     * 説明　：指定パス配下のディレクトリ取得
     * 引数1 ：取得するディレクトリパス
     */
    function sfGetFileList($dir) {
        $arrFileList = array();
        $arrDirList = array();

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $cnt = 0;
                // 行末の/を取り除く
                while (($file = readdir($dh)) !== false) $arrDir[] = $file;
                $dir = ereg_replace("/$", "", $dir);
                // アルファベットと数字でソート
                natcasesort($arrDir);
                foreach($arrDir as $file) {
                    // ./ と ../を除くファイルのみを取得
                    if($file != "." && $file != "..") {

                        $path = $dir."/".$file;
                        // SELECT内の見た目を整えるため指定文字数で切る
                        $file_name = SC_Utils::sfCutString($file, FILE_NAME_LEN);
                        $file_size = SC_Utils::sfCutString(SC_Utils::sfGetDirSize($path), FILE_NAME_LEN);
                        $file_time = date("Y/m/d", filemtime($path));

                        // ディレクトリとファイルで格納配列を変える
                        if(is_dir($path)) {
                            $arrDirList[$cnt]['file_name'] = $file;
                            $arrDirList[$cnt]['file_path'] = $path;
                            $arrDirList[$cnt]['file_size'] = $file_size;
                            $arrDirList[$cnt]['file_time'] = $file_time;
                            $arrDirList[$cnt]['is_dir'] = true;
                        } else {
                            $arrFileList[$cnt]['file_name'] = $file;
                            $arrFileList[$cnt]['file_path'] = $path;
                            $arrFileList[$cnt]['file_size'] = $file_size;
                            $arrFileList[$cnt]['file_time'] = $file_time;
                            $arrFileList[$cnt]['is_dir'] = false;
                        }
                        $cnt++;
                    }
                }
                closedir($dh);
            }
        }

        // フォルダを先頭にしてマージ
        return array_merge($arrDirList, $arrFileList);
    }

    /*
     * 関数名：sfGetDirSize()
     * 説明　：指定したディレクトリのバイト数を取得
     * 引数1 ：ディレクトリ
     */
    function sfGetDirSize($dir) {
        if(file_exists($dir)) {
            // ディレクトリの場合下層ファイルの総量を取得
            if (is_dir($dir)) {
                $handle = opendir($dir);
                while ($file = readdir($handle)) {
                    // 行末の/を取り除く
                    $dir = ereg_replace("/$", "", $dir);
                    $path = $dir."/".$file;
                    if ($file != '..' && $file != '.' && !is_dir($path)) {
                        $bytes += filesize($path);
                    } else if (is_dir($path) && $file != '..' && $file != '.') {
                        // 下層ファイルのバイト数を取得する為、再帰的に呼び出す。
                        $bytes += SC_Utils::sfGetDirSize($path);
                    }
                }
            } else {
                // ファイルの場合
                $bytes = filesize($dir);
            }
        }
        // ディレクトリ(ファイル)が存在しない場合は0byteを返す
        if($bytes == "") $bytes = 0;

        return $bytes;
    }

    /*
     * 関数名：sfDeleteDir()
     * 説明　：指定したディレクトリを削除
     * 引数1 ：削除ファイル
     */
    function sfDeleteDir($dir) {
        $arrResult = array();
        if(file_exists($dir)) {
            // ディレクトリかチェック
            if (is_dir($dir)) {
                if ($handle = opendir("$dir")) {
                    $cnt = 0;
                    while (false !== ($item = readdir($handle))) {
                        if ($item != "." && $item != "..") {
                            if (is_dir("$dir/$item")) {
                                sfDeleteDir("$dir/$item");
                            } else {
                                $arrResult[$cnt]['result'] = @unlink("$dir/$item");
                                $arrResult[$cnt]['file_name'] = "$dir/$item";
                            }
                        }
                        $cnt++;
                    }
                }
                closedir($handle);
                $arrResult[$cnt]['result'] = @rmdir($dir);
                $arrResult[$cnt]['file_name'] = "$dir/$item";
            } else {
                // ファイル削除
                $arrResult[0]['result'] = @unlink("$dir");
                $arrResult[0]['file_name'] = "$dir";
            }
        }

        return $arrResult;
    }

    /*
     * 関数名：sfGetFileTree()
     * 説明　：ツリー生成用配列取得(javascriptに渡す用)
     * 引数1 ：ディレクトリ
     * 引数2 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
     */
    function sfGetFileTree($dir, $tree_status) {

        $cnt = 0;
        $arrTree = array();
        $default_rank = count(split('/', $dir));

        // 文末の/を取り除く
        $dir = ereg_replace("/$", "", $dir);
        // 最上位層を格納(user_data/)
        if(sfDirChildExists($dir)) {
            $arrTree[$cnt]['type'] = "_parent";
        } else {
            $arrTree[$cnt]['type'] = "_child";
        }
        $arrTree[$cnt]['path'] = $dir;
        $arrTree[$cnt]['rank'] = 0;
        $arrTree[$cnt]['count'] = $cnt;
        // 初期表示はオープン
        if($_POST['mode'] != '') {
            $arrTree[$cnt]['open'] = lfIsFileOpen($dir, $tree_status);
        } else {
            $arrTree[$cnt]['open'] = true;
        }
        $cnt++;

        sfGetFileTreeSub($dir, $default_rank, $cnt, $arrTree, $tree_status);

        return $arrTree;
    }

    /*
     * 関数名：sfGetFileTree()
     * 説明　：ツリー生成用配列取得(javascriptに渡す用)
     * 引数1 ：ディレクトリ
     * 引数2 ：デフォルトの階層(/区切りで　0,1,2・・・とカウント)
     * 引数3 ：連番
     * 引数4 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
     */
    function sfGetFileTreeSub($dir, $default_rank, &$cnt, &$arrTree, $tree_status) {

        if(file_exists($dir)) {
            if ($handle = opendir("$dir")) {
                while (false !== ($item = readdir($handle))) $arrDir[] = $item;
                // アルファベットと数字でソート
                natcasesort($arrDir);
                foreach($arrDir as $item) {
                    if ($item != "." && $item != "..") {
                        // 文末の/を取り除く
                        $dir = ereg_replace("/$", "", $dir);
                        $path = $dir."/".$item;
                        // ディレクトリのみ取得
                        if (is_dir($path)) {
                            $arrTree[$cnt]['path'] = $path;
                            if(sfDirChildExists($path)) {
                                $arrTree[$cnt]['type'] = "_parent";
                            } else {
                                $arrTree[$cnt]['type'] = "_child";
                            }

                            // 階層を割り出す
                            $arrCnt = split('/', $path);
                            $rank = count($arrCnt);
                            $arrTree[$cnt]['rank'] = $rank - $default_rank + 1;
                            $arrTree[$cnt]['count'] = $cnt;
                            // フォルダが開いているか
                            $arrTree[$cnt]['open'] = lfIsFileOpen($path, $tree_status);
                            $cnt++;
                            // 下層ディレクトリ取得の為、再帰的に呼び出す
                            sfGetFileTreeSub($path, $default_rank, $cnt, $arrTree, $tree_status);
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    /*
     * 関数名：sfDirChildExists()
     * 説明　：指定したディレクトリ配下にファイルがあるか
     * 引数1 ：ディレクトリ
     */
    function sfDirChildExists($dir) {
        if(file_exists($dir)) {
            if (is_dir($dir)) {
                $handle = opendir($dir);
                while ($file = readdir($handle)) {
                    // 行末の/を取り除く
                    $dir = ereg_replace("/$", "", $dir);
                    $path = $dir."/".$file;
                    if ($file != '..' && $file != '.' && is_dir($path)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
     * 関数名：lfIsFileOpen()
     * 説明　：指定したファイルが前回開かれた状態にあったかチェック
     * 引数1 ：ディレクトリ
     * 引数2 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
     */
    function lfIsFileOpen($dir, $tree_status) {
        $arrTreeStatus = split('\|', $tree_status);
        if(in_array($dir, $arrTreeStatus)) {
            return true;
        }

        return false;
    }

    /*
     * 関数名：sfDownloadFile()
     * 引数1 ：ファイルパス
     * 説明　：ファイルのダウンロード
     */
    function sfDownloadFile($file) {
         // ファイルの場合はダウンロードさせる
        Header("Content-disposition: attachment; filename=".basename($file));
        Header("Content-type: application/octet-stream; name=".basename($file));
        Header("Cache-Control: ");
        Header("Pragma: ");
        echo (sfReadFile($file));
    }

    /*
     * 関数名：sfCreateFile()
     * 引数1 ：ファイルパス
     * 引数2 ：パーミッション
     * 説明　：ファイル作成
     */
    function sfCreateFile($file, $mode = "") {
        // 行末の/を取り除く
        if($mode != "") {
            $ret = @mkdir($file, $mode);
        } else {
            $ret = @mkdir($file);
        }

        return $ret;
    }

    /*
     * 関数名：sfReadFile()
     * 引数1 ：ファイルパス
     * 説明　：ファイル読込
     */
    function sfReadFile($filename) {
        $str = "";
        // バイナリモードでオープン
        $fp = @fopen($filename, "rb" );
        //ファイル内容を全て変数に読み込む
        if($fp) {
            $str = @fread($fp, filesize($filename)+1);
        }
        @fclose($fp);

        return $str;
    }

   /**
     * CSV出力用データ取得
     *
     * @return string
     */
    function getCSVData($array, $arrayIndex) {
        for ($i = 0; $i < count($array); $i++){
            // インデックスが設定されている場合
            if (is_array($arrayIndex) && 0 < count($arrayIndex)){
                for ($j = 0; $j < count($arrayIndex); $j++ ){
                    if ( $j > 0 ) $return .= ",";
                    $return .= "\"";
                    $return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";
                }
            } else {
                for ($j = 0; $j < count($array[$i]); $j++ ){
                    if ( $j > 0 ) $return .= ",";
                    $return .= "\"";
                    $return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
                }
            }
            $return .= "\n";
        }
        return $return;
    }

   /**
     * 配列をテーブルタグで出力する。
     *
     * @return string
     */
    function getTableTag($array) {
        $html = "<table>";
        $html.= "<tr>";
        foreach($array[0] as $key => $val) {
            $html.="<th>$key</th>";
        }
        $html.= "</tr>";

        $cnt = count($array);

        for($i = 0; $i < $cnt; $i++) {
            $html.= "<tr>";
            foreach($array[$i] as $val) {
                $html.="<td>$val</td>";
            }
            $html.= "</tr>";
        }
        return $html;
    }

   /**
     * 一覧-メイン画像のファイル指定がない場合、専用の画像ファイルに書き換える。
     *
     * @param string &$filename ファイル名
     * @return string
     */
    function sfNoImageMainList($filename = '') {
        if (strlen($filename) == 0 || substr($filename, -1, 1) == '/') {
            $filename .= 'noimage_main_list.jpg';
        }
        return $filename;
    }

   /**
     * 詳細-メイン画像のファイル指定がない場合、専用の画像ファイルに書き換える。
     *
     * @param string &$filename ファイル名
     * @return string
     */
    function sfNoImageMain($filename = '') {
        if (strlen($filename) == 0 || substr($filename, -1, 1) == '/') {
            $filename .= 'noimage_main.png';
        }
        return $filename;
    }

    /* デバッグ用 ------------------------------------------------------------------------------------------------*/
    function sfPrintR($obj) {
        print("<div style='font-size: 12px;color: #00FF00;'>\n");
        print("<strong>**デバッグ中**</strong><br />\n");
        print("<pre>\n");
        //print_r($obj);
        var_dump($obj);
        print("</pre>\n");
        print("<strong>**デバッグ中**</strong></div>\n");
    }

    /**
     * ポイント使用するかの判定
     *
     * @param integer $status 対応状況
     * @return boolean 使用するか(顧客テーブルから減算するか)
     */
    function sfIsUsePoint($status) {
        switch ($status) {
            case ORDER_CANCEL:      // キャンセル
                return false;
            default:
                break;
        }

        return true;
    }

    /**
     * ポイント加算するかの判定
     *
     * @param integer $status 対応状況
     * @return boolean 加算するか
     */
    function sfIsAddPoint($status) {
        switch ($status) {
            case ORDER_NEW:         // 新規注文
            case ORDER_PAY_WAIT:    // 入金待ち
            case ORDER_PRE_END:     // 入金済み
            case ORDER_CANCEL:      // キャンセル
            case ORDER_BACK_ORDER:  // 取り寄せ中
                return false;

            case ORDER_DELIV:       // 発送済み
                return true;

            default:
                break;
        }

        return false;
    }

    /**
     * ランダムな文字列を取得する
     *
     * @param integer $length 文字数
     * @return string ランダムな文字列
     */
    function sfGetRandomString($length = 1) {
        require_once(dirname(__FILE__) . '/../../module/Text/Password.php');
        return Text_Password::create($length);
    }

    /**
     * 現在の URL を取得する
     *
     * @return string 現在のURL
     */
    function sfGetUrl() {
        $url = '';

        if (SC_Utils_Ex::sfIsHTTPS()) {
            $url = "https://";
        } else {
            $url = "http://";
        }

        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?' . $_SERVER['QUERY_STRING'];

        return $url;
    }

    /**
     * バックトレースをテキスト形式で出力する
     *
     * @return string テキストで表現したバックトレース
     */
    function sfBacktraceToString($arrBacktrace) {
        $string = '';

        foreach (array_reverse($arrBacktrace) as $backtrace) {
            if (strlen($backtrace['class']) >= 1) {
                $func = $backtrace['class'] . $backtrace['type'] . $backtrace['function'];
            } else {
                $func = $backtrace['function'];
            }

            $string .= $backtrace['file'] . " " . $backtrace['line'] . ":" . $func . "\n";
        }

        return $string;
    }

    /**
     * 管理機能かを判定
     *
     * @return bool 管理機能か
     */
    function sfIsAdminFunction() {
        return defined('ADMIN_FUNCTION') && ADMIN_FUNCTION;
    }

    /**
     * フロント機能かを判定
     *
     * @return bool フロント機能か
     */
    function sfIsFrontFunction() {
        return SC_Utils_Ex::sfIsPcSite() || SC_Utils_Ex::sfIsMobileSite();
    }

    /**
     * フロント機能PCサイトかを判定
     *
     * @return bool フロント機能PCサイトか
     */
    function sfIsPcSite() {
        return defined('FRONT_FUNCTION_PC_SITE') && FRONT_FUNCTION_PC_SITE;
    }

    /**
     * フロント機能モバイル機能かを判定
     *
     * @return bool フロント機能モバイル機能か
     */
    function sfIsMobileSite() {
        return defined('MOBILE_SITE') && MOBILE_SITE;
    }

    /**
     * インストール機能かを判定
     *
     * @return bool インストール機能か
     */
    function sfIsInstallFunction() {
        return defined('INSTALL_FUNCTION') && INSTALL_FUNCTION;
    }

    // 郵便番号から住所の取得
    function sfGetAddress($zipcode) {

        $objQuery = new SC_Query(ZIP_DSN);

        $masterData = new SC_DB_MasterData_Ex();
        $arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        // インデックスと値を反転させる。
        $arrREV_PREF = array_flip($arrPref);

        // 郵便番号検索文作成
        $zipcode = mb_convert_kana($zipcode ,"n");
        $sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

        $data_list = $objQuery->getAll($sqlse, array($zipcode));
        if (empty($data_list)) return array();

        /*
         総務省からダウンロードしたデータをそのままインポートすると
         以下のような文字列が入っているので 対策する。
         ・（１・１９丁目）
         ・以下に掲載がない場合
        */
        $town =  $data_list[0]['town'];
        $town = ereg_replace("（.*）$","",$town);
        $town = ereg_replace("以下に掲載がない場合","",$town);
        $data_list[0]['town'] = $town;
        $data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

        return $data_list;
    }

    /**
     * プラグインが配置されているディレクトリ(フルパス)を取得する
     *
     * @param string $file プラグイン情報ファイル(info.php)のパス
     * @return SimpleXMLElement プラグイン XML
     */
    function sfGetPluginFullPathByRequireFilePath($file) {
        return str_replace('\\', '/', dirname($file)) . '/';
    }

    /**
     * プラグインのパスを取得する
     *
     * @param string $pluginFullPath プラグインが配置されているディレクトリ(フルパス)
     * @return SimpleXMLElement プラグイン XML
     */
    function sfGetPluginPathByPluginFullPath($pluginFullPath) {
        return basename(rtrim($pluginFullPath, '/'));
    }

    /**
     * プラグイン情報配列の基本形を作成する
     *
     * @param string $file プラグイン情報ファイル(info.php)のパス
     * @return array プラグイン情報配列
     */
    function sfMakePluginInfoArray($file) {
        $fullPath = SC_Utils_Ex::sfGetPluginFullPathByRequireFilePath($file);

        return
            array(
                // パス
                'path' => SC_Utils_Ex::sfGetPluginPathByPluginFullPath($fullPath),
                // プラグイン名
                'name' => '未定義',
                // フルパス
                'fullpath' => $fullPath,
                // バージョン
                'version' => null,
                // 著作者
                'auther' => '未定義',
            )
        ;
    }

    /**
     * プラグイン情報配列を取得する
     *
     * TODO include_once を利用することで例外対応をサボタージュしているのを改善する。
     *
     * @param string $path プラグインのディレクトリ名
     * @return array プラグイン情報配列
     */
    function sfGetPluginInfoArray($path) {
        return (array)include_once(PLUGIN_PATH . "$path/plugin_info.php");
    }

    /**
     * プラグイン XML を読み込む
     *
     * TODO 空だったときを考慮
     *
     * @return SimpleXMLElement プラグイン XML
     */
    function sfGetPluginsXml() {
        return simplexml_load_file(PLUGIN_PATH . 'plugins.xml');
    }

    /**
     * プラグイン XML を書き込む
     *
     * @param SimpleXMLElement $pluginsXml プラグイン XML
     * @return integer ファイルに書き込まれたバイト数を返します。
     */
    function sfPutPluginsXml($pluginsXml) {
        if (!($pluginsXml instanceof SimpleXMLElement)) SC_Utils_Ex::sfDispException();

        $xml = $pluginsXml->asXML();
        if (strlen($xml) == 0) SC_Utils_Ex::sfDispException();

        $return = file_put_contents(PLUGIN_PATH . 'plugins.xml', $pluginsXml->asXML());
        if ($return === false) SC_Utils_Ex::sfDispException();

        return $return;
    }

    function sfLoadPluginInfo($filenamePluginInfo) {
        return (array)include_once $filenamePluginInfo;
    }

    /**
     * 現在の Unix タイムスタンプを float (秒単位) でマイクロ秒まで返す
     *
     * PHP4の上位互換用途。
     * FIXME PHP4でテストする。(現状全くテストしていない。)
     * @param SimpleXMLElement $pluginsXml プラグイン XML
     * @return integer ファイルに書き込まれたバイト数を返します。
     */
    function sfMicrotimeFloat() {
        $microtime = microtime(true);
        if (is_string($microtime)) {
            list($usec, $sec) = explode(" ", microtime());
            return ((float)$usec + (float)$sec);
        }
        return $microtime;
    }

    /**
     * 変数が空白かどうかをチェックする.
     *
     * 引数 $val が空白かどうかをチェックする. 空白の場合は true.
     * 以下の文字は空白と判断する.
     * - " " (ASCII 32 (0x20)), 通常の空白
     * - "\t" (ASCII 9 (0x09)), タブ
     * - "\n" (ASCII 10 (0x0A)), リターン
     * - "\r" (ASCII 13 (0x0D)), 改行
     * - "\0" (ASCII 0 (0x00)), NULバイト
     * - "\x0B" (ASCII 11 (0x0B)), 垂直タブ
     *
     * 引数 $val が配列の場合は, 空の配列の場合 true を返す.
     *
     * 引数 $greedy が true の場合は, 全角スペース, ネストした空の配列も
     * 空白と判断する.
     *
     * @param mixed $val チェック対象の変数
     * @param boolean $greedy "貧欲"にチェックを行う場合 true
     * @return boolean $val が空白と判断された場合 true
     */
    function isBlank($val, $greedy = true) {
        if (is_array($val)) {
            if ($greedy) {
                foreach ($val as $in) {
                    if (!SC_Utils::isBlank($in, $greedy)) {
                        return false;
                    }
                }
            } else {
                return empty($val);
            }
        }

        if ($greedy) {
            $val = preg_replace("/　/", "", $val);
        }

        $val = trim($val);
        if (strlen($val) > 0) {
            return false;
        }
        return true;
    }
}
?>
