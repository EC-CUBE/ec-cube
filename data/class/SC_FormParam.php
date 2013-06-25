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

/**
 * パラメーター管理クラス
 *
 * :XXX: addParam と setParam で言う「パラメーター」が用語として競合しているように感じる。(2009/10/17 Seasoft 塚田)
 *
 * @package SC
 * @author LOCKON CO.,LTD.
 */
class SC_FormParam {

    /**
     * 何も入力されていないときに表示する値
     * キーはキー名
     */
    var $arrValue = array();

    /** 表示名 */
    var $disp_name = array();

    /** キー名 */
    var $keyname = array();

    var $length = array();
    var $convert = array();
    var $arrCheck = array();

    /**
     * 何も入力されていないときに表示する値
     * キーはキー名
     */
    var $arrDefault = array();

    /** DBにそのまま挿入可能か否か */
    var $input_db = array();

    var $html_disp_name = array();

    /**
     * コンストラクタ
     */
    function __construct() {
        $this->check_dir = IMAGE_SAVE_REALDIR;

        // SC_FormParamのフックポイント
        // TODO: debug_backtrace以外にいい方法があれば良いが、一旦これで
        $backtraces = debug_backtrace();
        // 呼び出し元のクラスを取得
        $class = $backtraces[1]['class'];
        $objPage = $backtraces[1]['object'];
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($objPage->plugin_activate_flg);
        if (is_object($objPlugin)) {
            $objPlugin->doAction('SC_FormParam_construct', array($class, $this));
        }
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 #1702
     */
    function initParam() {
        $this->disp_name = array();
        $this->keyname = array();
        $this->length = array();
        $this->convert = array();
        $this->arrCheck = array();
        $this->arrDefault = array();
        $this->input_db = array();
    }

    // パラメーターの追加
    function addParam($disp_name, $keyname, $length = '', $convert = '', $arrCheck = array(), $default = '', $input_db = true) {
        $this->disp_name[] = $disp_name;
        $this->keyname[] = $keyname;
        $this->length[] = $length;
        $this->convert[] = $convert;
        $this->arrCheck[] = $arrCheck;
        // XXX このタイミングで arrValue へ格納するほうがスマートかもしれない。しかし、バリデーションや変換の対象となるので、その良し悪しは気になる。
        $this->arrDefault[$keyname] = $default;
        $this->input_db[] = $input_db;
    }

    // パラメーターの入力
    // $arrVal  :$arrVal['keyname']・・の配列を一致したキーのインスタンスに格納する
    // $seq     :trueの場合、$arrVal[0]~の配列を登録順にインスタンスに格納する
    function setParam($arrVal, $seq = false) {
        if (!is_array($arrVal)) return;
        if (!$seq) {
            foreach ($arrVal as $key => $val) {
                $this->setValue($key, $val);
            }
        } else {
            foreach ($this->keyname as $index => $key) {
                $this->setValue($key, $arrVal[$index]);
            }
        }
    }

    // 画面表示用タイトル生成
    function setHtmlDispNameArray() {
        foreach ($this->keyname as $index => $key) {
            $find = false;
            foreach ($this->arrCheck[$index] as $val) {
                if ($val == 'EXIST_CHECK') {
                    $find = true;
                }
            }

            if ($find) {
                $this->html_disp_name[$index] = t("c_T_ARG1<span class='red'>(* Required)</span>_01", array('T_ARG1' => $this->disp_name[$index]));
            } else {
                $this->html_disp_name[$index] = $this->disp_name[$index];
            }
            if ($this->arrDefault[$key] != '') {
                $this->html_disp_name[$index] .= t('c_[Default value: T_ARG1]_01', array('T_ARG1' => $this->arrDefault[$key]));
            }
            if ($this->input_db[$index] == false) {
                $this->html_disp_name[$index] .= t('c_ [Registration/update not possible] _01');
            }
        }
    }

    // 画面表示用タイトル取得
    function getHtmlDispNameArray() {
        return $this->html_disp_name;
    }

    // 複数列パラメーターの取得
    function setParamList($arrVal2d, $keyname) {
        // DBの件数を取得する。
        $no = 1;
        foreach ($arrVal2d as $arrVal) {
            $key = $keyname . $no;
            $this->setValue($key, $arrVal[$keyname]);
            $no++;
        }
    }

    function setDBDate($db_date, $year_key = 'year', $month_key = 'month', $day_key = 'day') {
        if (empty($db_date)) {
            return;
        }
        list($y, $m, $d) = preg_split('/[- ]/', $db_date);
        $this->setValue($year_key, $y);
        $this->setValue($month_key, $m);
        $this->setValue($day_key, $d);
    }

    // キーに対応した値をセットする。
    function setValue($key, $value) {
        if (!in_array($key, $this->keyname)) {
            // TODO 警告発生
            return;
        }
        $this->arrValue[$key] = $value;
    }

    function toLower($key) {
        if (isset($this->arrValue[$key])) {
            $this->arrValue[$key] = strtolower($this->arrValue[$key]);
        }
    }

    // エラーチェック
    function checkError($br = true) {
        $arrErr = array();

        foreach ($this->keyname as $index => $key) {
            foreach ($this->arrCheck[$index] as $func) {
                $value = $this->getValue($key);
                switch ($func) {
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
                        $this->recursionCheck($this->disp_name[$index], $func,
                            $value, $arrErr, $key, $this->length[$index]);
                        break;
                    // 小文字に変換
                    case 'CHANGE_LOWER':
                        $this->toLower($key);
                        break;
                    // ファイルの存在チェック
                    case 'FILE_EXISTS':
                        if ($value != '' && !file_exists($this->check_dir . $value)) {
                            $arrErr[$key] = t('c_* The file T_ARG1 does not exist. <br />_01', array('T_ARG1' => $this->disp_name[$index]));
                        }
                        break;
                    // ダウンロード用ファイルの存在チェック
                    case 'DOWN_FILE_EXISTS':
                        if ($value != '' && !file_exists(DOWN_SAVE_REALDIR . $value)) {
                            $arrErr[$key] = t('c_* The file T_ARG1 does not exist. <br />_01', array('T_ARG1' => $this->disp_name[$index]));
                        }
                        break;
                    default:
                        $arrErr[$key] = t('c_** Does not support the error check format (T_ARG1) **<br />_01', array('T_ARG1' => $func));
                        break;
                }
            }

            if (isset($arrErr[$key]) && !$br) {
                $arrErr[$key] = preg_replace("/<br(\s+\/)?>/i", '', $arrErr[$key]);
            }
        }
        return $arrErr;
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
     * @param integer $error_last_key エラーメッセージを格納する配列の末端のキー
     * @return void
     */
    function recursionCheck($disp_name, $func, $value, &$arrErr, $error_key,
        $length = 0, $depth = 0, $error_last_key = null
    ) {
        if (is_array($value)) {
            $depth++;
            foreach ($value as $key => $in) {
                $this->recursionCheck($disp_name, $func, $in, $arrErr, $error_key,
                                      $length, $depth, $key);
            }
        } else {
            $objErr = new SC_CheckError_Ex(array(($error_last_key ? $error_last_key : $error_key) => $value));
            $objErr->doFunc(array($disp_name, ($error_last_key ? $error_last_key : $error_key), $length), array($func));
            if (!SC_Utils_Ex::isBlank($objErr->arrErr)) {
                foreach ($objErr->arrErr as $message) {

                    if (!SC_Utils_Ex::isBlank($message)) {
                        // 再帰した場合は多次元配列のエラーメッセージを生成
                        $error_var = '$arrErr[$error_key]';
                        for ($i = 0; $i < $depth; $i++) {
                            // FIXME 二次元以上の対応
                            $error_var .= '[' . $error_last_key . ']';
                        }
                        eval($error_var . ' = $message;');
                    }
                }
            }
        }
    }

    /**
     * フォームの入力パラメーターに応じて, 再帰的に mb_convert_kana 関数を実行する.
     *
     * @return void
     * @see mb_convert_kana
     */
    function convParam() {
        foreach ($this->keyname as $index => $key) {
            if (isset($this->arrValue[$key])) {
                $this->recursionConvParam($this->arrValue[$key], $this->convert[$index]);
            }
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
            foreach ($value as $key => $val) {
                $this->recursionConvParam($value[$key], $convert);
            }
        } else {
            if (!SC_Utils_Ex::isBlank($value)) {
                $value = mb_convert_kana($value, $convert);
            }
        }
    }

    /**
     * 連想配列で返す
     *
     * @param array $arrKey 対象のキー
     * @return array 連想配列
     */
    function getHashArray($arrKey = array()) {
        $arrRet = array();
        foreach ($this->keyname as $keyname) {
            if (empty($arrKey) || in_array($keyname, $arrKey)) {
                $arrRet[$keyname] = $this->getValue($keyname);
            }
        }
        return $arrRet;
    }

    // DB格納用配列の作成
    function getDbArray() {
        $dbArray = array();
        foreach ($this->keyname as $index => $key) {
            if ($this->input_db[$index]) {
                $dbArray[$key] = $this->getValue($key);
            }
        }
        return $dbArray;
    }

    /**
     * 配列の縦横を入れ替えて返す
     *
     * @param array $arrKey 対象のキー
     * @return array 縦横を入れ替えた配列
     */
    function getSwapArray($arrKey = array()) {
        $arrTmp = $this->getHashArray($arrKey);
        return SC_Utils_Ex::sfSwapArray($arrTmp);
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

    // フォームに渡す用のパラメーターを返す
    function getFormParamList() {
        $formParamList = array();
        foreach ($this->keyname as $index => $key) {
            // キー名
            $formParamList[$key]['keyname'] = $key;
            // 表示名
            $formParamList[$key]['disp_name'] = $this->disp_name[$index];
            // 文字数制限
            $formParamList[$key]['length'] = $this->length[$index];
            // 入力値
            $formParamList[$key]['value'] = $this->getValue($key);
        }
        return $formParamList;
    }

    /**
     * キー名の一覧を返す
     *
     * @return array キー名の一覧
     */
    function getKeyList() {
        return $this->keyname;
    }

    // キー名と一致した値を返す
    function getValue($keyname, $default = '') {
        $ret = null;
        foreach ($this->keyname as $key) {
            if ($key == $keyname) {
                $ret = isset($this->arrValue[$key]) ? $this->arrValue[$key] : $this->arrDefault[$key];
                break;
            }
        }

        if (is_array($ret)) {
            foreach ($ret as $key => $value) {
                if (SC_Utils_Ex::isBlank($ret[$key])) {
                    $ret[$key] = $default;
                }
            }
        } else {
            if (SC_Utils_Ex::isBlank($ret)) {
                $ret = $default;
            }
        }
        return $ret;
    }

    /**
     * @deprecated
     */
    function splitParamCheckBoxes($keyname) {
        foreach ($this->keyname as $key) {
            if ($key == $keyname) {
                if (isset($this->arrValue[$key]) && !is_array($this->arrValue[$key])) {
                    $this->arrValue[$key] = explode('-', $this->arrValue[$key]);
                }
            }
        }
    }

    /**
     * 入力パラメーターの先頭及び末尾にある空白文字を削除する.
     *
     * @param boolean $has_wide_space 全角空白も削除する場合 true
     * @return void
     */
    function trimParam($has_wide_space = true) {
        foreach ($this->arrValue as &$value) {
            $this->recursionTrim($value, $has_wide_space);
        }
    }

    /**
     * 再帰的に入力パラメーターの先頭及び末尾にある空白文字を削除する.
     *
     * @param mixed $value 変換する値. 配列の場合は再帰的に実行する.
     * @param boolean $has_wide_space 全角空白も削除する場合 true
     * @return void
     */
    function recursionTrim(&$value, $has_wide_space = true) {
        $pattern = '/^[ 　\r\n\t]*(.*?)[ 　\r\n\t]*$/u';
        if (is_array($value)) {
            foreach ($value as $key => $val) {
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
     * 引数で指定した文字列で始まるパラメーター名の入力値を連想配列で取得する.
     *
     * @param string $prefix パラメーター名の接頭辞
     * @return array 検索結果引き継ぎ用の連想配列.
     */
    function getSearchArray($prefix = 'search_') {
        $arrResults = array();
        foreach ($this->keyname as $key) {
            if (preg_match('/^' . $prefix . '/', $key)) {
                $arrResults[$key] = $this->getValue($key);
            }
        }
        return $arrResults;
    }

    /**
     * 前方互換用
     *
     * 1次キーが添字なのが特徴だったと思われる。
     * @deprecated 2.12.0 必要ならば getFormParamList メソッドに引数を追加するなどで実現可能
     */
    function getFormDispArray() {
        $formDispArray = array();
        foreach ($this->keyname as $index => $key) {
            // キー名
            $formDispArray[$index]['keyname'] = $key;
            // 表示名
            $formDispArray[$index]['disp_name']  = $this->disp_name[$index];
            // 文字数制限
            $formDispArray[$index]['length'] = $this->length[$index];
            // 入力値
            $formDispArray[$index]['value'] = $this->getValue($key);
        }
        return $formDispArray;
    }

    /**
     * パラメーターの削除
     * addParamの逆の関数
     * カスタマイズおよびプラグインで使用されるのを想定
     */
    function removeParam($keyname) {
        $index = array_search($keyname, $this->keyname);

        if ($index !== FALSE) {
            // $this->paramに歯抜けが存在する場合は、NULLで埋めておく。
            // 最後に配列を詰める際に、全ての項目が埋まっている必要がある。
            foreach ($this->keyname as $key => $value) {
                if (!isset($this->param[$key])) {
                    $this->param[$key] = NULL;
                }
            }
            // $this->paramがソートされていない時があるのでソート。
            ksort($this->param);

            // 削除
            unset($this->disp_name[$index]);
            unset($this->keyname[$index]);
            unset($this->length[$index]);
            unset($this->convert[$index]);
            unset($this->arrCheck[$index]);
            unset($this->default[$index]);
            unset($this->input_db[$index]);
            unset($this->param[$index]);

            // 歯抜けになった配列を詰める
            $this->disp_name = array_merge($this->disp_name);
            $this->keyname = array_merge($this->keyname);
            $this->length = array_merge($this->length);
            $this->convert = array_merge($this->convert);
            $this->arrCheck = array_merge($this->arrCheck);
            $this->default = array_merge($this->default);
            $this->input_db = array_merge($this->input_db);
            $this->param = array_merge($this->param);
        }
    }

    /**
     * パラメーター定義の上書き
     *
     * @param string $keyname キー名
     * @param string $target 上書きしたい項目名(disp_name,length,convert等)
     * @param mixed $value 指定した内容に上書きする
     */
    function overwriteParam($keyname, $target, $value) {
        $index = array_search($keyname, $this->keyname);

        if ($index !== FALSE) {
            $this->{$target}[$index] = $value;
        }
    }
}
