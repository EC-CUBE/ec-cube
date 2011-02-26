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
 * パラメータ管理クラス
 *
 * :XXX: addParam と setParam で言う「パラメータ」が用語として競合しているように感じる。(2009/10/17 Seasoft 塚田)
 *
 * @package SC
 * @author LOCKON CO.,LTD.
 */
class SC_FormParam {

    var $param;
    var $disp_name;
    var $keyname;
    var $length;
    var $convert;
    var $arrCheck;
    var $default;	// 何も入力されていないときに表示する値
    var $input_db;	// DBにそのまま挿入可能か否か
    var $html_disp_name;

    // コンストラクタ
    function SC_FormParam() {
        $this->check_dir = IMAGE_SAVE_REALDIR;
        $this->initParam();
    }

    /**
     * パラメータの初期化
     *
     * @return void
     */
    function initParam() {
        $this->disp_name = array();
        $this->keyname = array();
        $this->length = array();
        $this->convert = array();
        $this->arrCheck = array();
        $this->default = array();
        $this->input_db = array();
    }

    // パラメータの追加
    function addParam($disp_name, $keyname, $length="", $convert="", $arrCheck=array(), $default="", $input_db="true") {
        $this->disp_name[] = $disp_name;
        $this->keyname[] = $keyname;
        $this->length[] = $length;
        $this->convert[] = $convert;
        $this->arrCheck[] = $arrCheck;
        $this->default[] = $default;
        $this->input_db[] = $input_db;
    }

    // パラメータの入力
    // $arrVal	:$arrVal['keyname']・・の配列を一致したキーのインスタンスに格納する
    // $seq		:trueの場合、$arrVal[0]~の配列を登録順にインスタンスに格納する
    function setParam($arrVal, $seq = false) {
        $cnt = 0;
        if(!$seq){
            foreach($this->keyname as $val) {
                if(isset($arrVal[$val])) {
                    $this->setValue($val, $arrVal[$val]);
                }
            }
        } else {
            foreach($this->keyname as $val) {
                $this->param[$cnt] = $arrVal[$cnt];
                $cnt++;
            }
        }
    }

    // 画面表示用タイトル生成
    function setHtmlDispNameArray() {
        $cnt = 0;
        foreach($this->keyname as $val) {
            $find = false;
            foreach($this->arrCheck[$cnt] as $val) {
                if($val == "EXIST_CHECK") {
                    $find = true;
                }
            }

            if($find) {
                $this->html_disp_name[$cnt] = $this->disp_name[$cnt] . '<span class="red">(※ 必須)</span>';
            } else {
                $this->html_disp_name[$cnt] = $this->disp_name[$cnt];
            }
            if($this->default[$cnt] != "") {
                $this->html_disp_name[$cnt] .= ' [省略時初期値: ' . $this->default[$cnt] . ']';
            }
            if($this->input_db[$cnt] == false) {
                $this->html_disp_name[$cnt] .= ' [登録・更新不可] ';
            }
            $cnt++;
        }
    }

    // 画面表示用タイトル取得
    function getHtmlDispNameArray() {
        return $this->html_disp_name;
    }

    // 複数列パラメータの取得
    function setParamList($arrVal, $keyname) {
        // DBの件数を取得する。
        $count = count($arrVal);
        $no = 1;
        for($cnt = 0; $cnt < $count; $cnt++) {
            $key = $keyname.$no;
            if($arrVal[$cnt][$keyname] != "") {
                $this->setValue($key, $arrVal[$cnt][$keyname]);
            }
            $no++;
        }
    }

    function setDBDate($db_date, $year_key = 'year', $month_key = 'month', $day_key = 'day') {

        if (!empty($db_date)) {
            list($y, $m, $d) = preg_split("/[- ]/", $db_date);
            $this->setValue($year_key, $y);
            $this->setValue($month_key, $m);
            $this->setValue($day_key, $d);
        }
    }

    // キーに対応した値をセットする。
    function setValue($key, $param) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $key) {
                $this->param[$cnt] = $param;
                // 複数一致の場合もあるので break してはいけない。
            }
            $cnt++;
        }
    }

    function toLower($key) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $key) {
                $this->param[$cnt] = strtolower($this->param[$cnt]);
                // 複数一致の場合もあるので break してはいけない。
            }
            $cnt++;
        }
    }

    // エラーチェック
    function checkError($br = true, $keyname = "") {
        // 連想配列の取得
        $arrRet = $this->getHashArray($keyname);
        $objErr->arrErr = array();

        $cnt = 0;
        foreach($this->keyname as $val) {
            foreach($this->arrCheck[$cnt] as $func) {
                if (!isset($this->param[$cnt])) $this->param[$cnt] = "";
                switch($func) {
                case 'EXIST_CHECK':
                case 'NUM_CHECK':
                case 'EMAIL_CHECK':
                case 'EMAIL_CHAR_CHECK':
                case 'ALNUM_CHECK':
                case 'GRAPH_CHECK':
                case 'KANA_CHECK':
                case 'URL_CHECK':
                case 'IP_CHECK':
                case 'SPTAB_CHECK':
                case 'ZERO_CHECK':
                case 'ALPHA_CHECK':
                case 'ZERO_START':
                case 'FIND_FILE':
                case 'NO_SPTAB':
                case 'DIR_CHECK':
                case 'DOMAIN_CHECK':
                case 'FILE_NAME_CHECK':
                case 'MOBILE_EMAIL_CHECK':
                case 'MAX_LENGTH_CHECK':
                case 'MIN_LENGTH_CHECK':
                case 'NUM_COUNT_CHECK':
                case 'KANABLANK_CHECK':
                case 'SELECT_CHECK':
                case 'FILE_NAME_CHECK_BY_NOUPLOAD':
                    $this->recursionCheck($this->disp_name[$cnt], $func,
                                          $this->param[$cnt], $objErr->arrErr,
                                          $val, $this->length[$cnt]);
                    break;
                // 小文字に変換
                case 'CHANGE_LOWER':
                    $this->param[$cnt] = strtolower($this->param[$cnt]);
                    break;
                // ファイルの存在チェック
                case 'FILE_EXISTS':
                    if($this->param[$cnt] != "" && !file_exists($this->check_dir . $this->param[$cnt])) {
                        $objErr->arrErr[$val] = "※ " . $this->disp_name[$cnt] . "のファイルが存在しません。<br>";
                    }
                    break;
                // ダウンロード用ファイルの存在チェック
                case 'DOWN_FILE_EXISTS':
                    if($this->param[$cnt] != "" && !file_exists(DOWN_SAVE_REALDIR . $this->param[$cnt])) {
                        $objErr->arrErr[$val] = "※ " . $this->disp_name[$cnt] . "のファイルが存在しません。<br>";
                    }
                    break;
                default:
                    $objErr->arrErr[$val] = "※※　エラーチェック形式($func)には対応していません　※※ <br>";
                    break;
                }
            }

            if (isset($objErr->arrErr[$val]) && !$br) {
                $objErr->arrErr[$val] = ereg_replace("<br>$", "", $objErr->arrErr[$val]);
            }
            $cnt++;
        }
        return $objErr->arrErr;
    }

    /**
     * SC_CheckError::doFunc() を再帰的に実行する.
     *
     * 再帰実行した場合は, エラーメッセージを多次元配列で格納する
     *
     * TODO 二次元以上のエラーメッセージへの対応
     *
     * @param string $disp_name 表示名
     * @param string $func チェック種別
     * @param mixed $value チェック対象の値. 配列の場合は再帰的にチェックする.
     * @param array $arrErr エラーメッセージを格納する配列
     * @param string $error_key エラーメッセージを格納する配列のキー
     * @param integer $length チェック対象の値の長さ
     * @param integer $depth 再帰実行した場合の深度
     * @param integer $recursion_count 再帰実行した回数
     * @return void
     */
    function recursionCheck($disp_name, $func, $value, &$arrErr, $error_key,
                            $length = 0, $depth = 0, $recursion_count = 0) {
        if (is_array($value)) {
            $depth++;
            $recursion_count = 0;
            foreach ($value as $in) {
                $this->recursionCheck($disp_name, $func, $in, $arrErr, $error_key,
                                      $length, $depth, $recursion_count);
                $recursion_count++;
            }
        } else {
            $objErr = new SC_CheckError(array(0 => $value));
            $objErr->doFunc(array($disp_name, 0, $length), array($func));
            if (!SC_Utils_Ex::isBlank($objErr->arrErr)) {
                foreach($objErr->arrErr as $message) {

                    if(!SC_Utils_Ex::isBlank($message)) {
                        // 再帰した場合は多次元配列のエラーメッセージを生成
                        $error_var = '$arrErr[$error_key]';
                        for ($i = 0; $i < $depth; $i++) {
                            // FIXME 二次元以上の対応
                            $error_var .= '[' . $recursion_count . ']';
                        }
                        eval($error_var . ' = $message;');
                    }
                }
            }
        }
    }

    /**
     * フォームの入力パラメータに応じて, 再帰的に mb_convert_kana 関数を実行する.
     *
     * @return voi
     * @see mb_convert_kana
     */
    function convParam() {
        $cnt = 0;
        foreach ($this->keyname as $val) {
            if (!isset($this->param[$cnt])) $this->param[$cnt] = "";
            $this->recursionConvParam($this->param[$cnt], $this->convert[$cnt]);
            $cnt++;
        }
    }

    /**
     * 再帰的に mb_convert_kana を実行する.
     *
     * @param mixed $value 変換する値. 配列の場合は再帰的に実行する.
     * @param string $convert mb_convert_kana の変換オプション
     */
    function recursionConvParam(&$value, $convert) {
        if (is_array($value)) {
            foreach (array_keys($value) as $key) {
                $this->recursionConvParam($value[$key], $convert);
            }
        } else {
            if (!SC_Utils_Ex::isBlank($value)) {
                $value = mb_convert_kana($value, $convert);
            }
        }
    }

    // 連想配列の作成
    function getHashArray($keyname = "") {
        $arrRet = array();
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($keyname == "" || $keyname == $val) {
                $arrRet[$val] = isset($this->param[$cnt]) ? $this->param[$cnt] : "";
                $cnt++;
            }
        }
        return $arrRet;
    }

    // DB格納用配列の作成
    function getDbArray() {
        $cnt = 0;
        foreach ($this->keyname as $val) {
            if ($this->input_db[$cnt]) {
                $arrRet[$val] = isset($this->param[$cnt]) ? $this->param[$cnt] : "";
            }
            $cnt++;
        }
        return $arrRet;
    }

    // 配列の縦横を入れ替えて返す
    function getSwapArray($arrKey) {
        foreach($arrKey as $keyname) {
            $arrVal = $this->getValue($keyname);
            $max = count($arrVal);
            for($i = 0; $i < $max; $i++) {
                $arrRet[$i][$keyname] = $arrVal[$i];
            }
        }
        return $arrRet;
    }

    // 項目名一覧の取得
    function getTitleArray() {
        return $this->disp_name;
    }

    // 項目数を返す
    function getCount() {
        $count = count($this->keyname);
        return $count;
    }

    // フォームに渡す用のパラメータを返す
    function getFormParamList() {
        $cnt = 0;
        foreach($this->keyname as $val) {

            // キー名
            $arrRet[$val]['keyname'] = $this->keyname[$cnt];
            // 文字数制限
            $arrRet[$val]['length'] = $this->length[$cnt];
            // 入力値
            if (isset($this->param[$cnt])) {
                $arrRet[$val]['value'] = $this->param[$cnt];
            }

            if (!isset($this->param[$cnt])) $this->param[$cnt] = "";

            if($this->default[$cnt] != "" && $this->param[$cnt] == "") {
                $arrRet[$val]['value'] = $this->default[$cnt];
            }

            $cnt++;
        }
        return $arrRet;
    }

    // キー名の一覧を返す
    function getKeyList() {
        foreach($this->keyname as $val) {
            $arrRet[] = $val;
        }
        return $arrRet;
    }

    // キー名と一致した値を返す
    function getValue($keyname,$default="") {
        $cnt = 0;
        $ret = null;
        foreach($this->keyname as $val) {
            if($val == $keyname) {
                $ret = isset($this->param[$cnt]) ? $this->param[$cnt] : "";
                break;
            }
            $cnt++;
        }
        if(is_null($ret)){
            $ret = $default;
        }
        return $ret;
    }

    /**
     * @deprecated
     */
    function splitParamCheckBoxes($keyname) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $keyname) {
                if(isset($this->param[$cnt]) && !is_array($this->param[$cnt])) {
                    $this->param[$cnt] = explode("-", $this->param[$cnt]);
                }
            }
            $cnt++;
        }
    }

    /**
     * 入力パラメータの先頭及び末尾にある空白文字を削除する.
     *
     * @param boolean $has_wide_space 全角空白も削除する場合 true
     * @return void
     */
    function trimParam($has_wide_space = true) {
        $cnt = 0;
        foreach ($this->keyname as $val) {
            if (!isset($this->param[$cnt])) $this->param[$cnt] = "";
            $this->recursionTrim($this->param[$cnt], $has_wide_space);
            $cnt++;
        }
    }

    /**
     * 再帰的に入力パラメータの先頭及び末尾にある空白文字を削除する.
     *
     * @param mixed $value 変換する値. 配列の場合は再帰的に実行する.
     * @param boolean $has_wide_space 全角空白も削除する場合 true
     * @return void
     */
    function recursionTrim(&$value, $has_wide_space = true) {
        $pattern = '/^[ 　\r\n\t]*(.*?)[ 　\r\n\t]*$/u';
        if (is_array($value)) {
            foreach (array_keys($value) as $key) {
                $this->recursionTrim($value[$key], $convert);
            }
        } else {
            if (!SC_Utils_Ex::isBlank($value)) {
                if ($has_wide_space) {
                    $value = preg_replace($pattern, '$1', $value);
                }
                $value = trim($value);
            }
        }
    }

    /**
     * 検索結果引き継ぎ用の連想配列を取得する.
     *
     * 引数で指定した文字列で始まるパラメータ名の入力値を連想配列で取得する.
     *
     * @param string $prefix パラメータ名の接頭辞
     * @return array 検索結果引き継ぎ用の連想配列.
     */
    function getSearchArray($prefix = 'search_') {
        $cnt = 0;
        $arrResults = array();
        foreach ($this->keyname as $key) {
            if (preg_match('/^' . $prefix . '/', $key)) {
                $arrResults[$key] = isset($this->param[$cnt])
                    ? $this->param[$cnt] : "";
            }
            $cnt++;
        }
        return $arrResults;
    }
}
?>
