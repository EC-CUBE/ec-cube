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

/*----------------------------------------------------------------------
 * [名称] SC_CheckError
 * [概要] エラーチェッククラス
 *----------------------------------------------------------------------
 */
class SC_CheckError {
    var $arrErr = array();
    var $arrParam;

    // チェック対象の値が含まれる配列をセットする。
    function __construct($array = '') {
        if ($array != '') {
            $this->arrParam = $array;
        } else {
            $this->arrParam = $_POST;
        }

    }

    function doFunc($value, $arrFunc) {
        foreach ($arrFunc as $key) {
            $this->$key($value);
        }
    }

    /**
     * HTMLのタグをチェックする
     *
     * @param array $value value[0] = 項目名 value[1] = 判定対象 value[2] = 許可するタグが格納された配列
     * @return void
     */
    function HTML_TAG_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // HTMLに含まれているタグを抽出する
        preg_match_all('/<\/?([a-z]+)/i', $this->arrParam[$value[1]], $arrTagIncludedHtml = array());

        $arrDiffTag = array_diff($arrTagIncludedHtml[1], $value[2]);

        if (empty($arrDiffTag)) return;

        // 少々荒っぽいが、表示用 HTML に変換する
        foreach ($arrDiffTag as &$tag) {
            $tag = '[' . htmlspecialchars($tag) . ']';
        }
        $html_diff_tag_list = implode(', ', $arrDiffTag);

        $this->arrErr[$value[1]] = t('c_* T_ARG2 contains the tag T_ARG1 which is not allowed. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $html_diff_tag_list));
    }

    /**
     * 必須入力の判定
     *
     * 受け取りがない場合エラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象
     * @return void
     */
    function EXIST_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (!is_array($this->arrParam[$value[1]]) && strlen($this->arrParam[$value[1]]) == 0) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 is blank. <br />_01', array('T_ARG1' => $value[0]));
        } else if (is_array($this->arrParam[$value[1]]) && count($this->arrParam[$value[1]]) == 0) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 is not selected. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 必須入力の判定(逆順)
     *
     * 受け取りがない場合エラーを返す
     * @param array $value value[0] = 判定対象 value[1] = 項目名
     * @return void
     */
    function EXIST_CHECK_REVERSE($value) {
        if (isset($this->arrErr[$value[0]])) {
            return;
        }
        // $this->createParam($value);
        if (strlen($this->arrParam[$value[0]]) == 0) {
            $this->arrErr[$value[0]] = t('c_* T_ARG1 is blank. <br />_01', array('T_ARG1' => $value[1]));
        }
    }

    /**
     * スペース、タブの判定
     *
     * 受け取りがない場合エラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象
     * @return void
     */
    function SPTAB_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) != 0 && preg_match("/^[ 　\t\r\n]+$/", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Spaces, tabs and line breaks are not possible in T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * スペース、タブの判定
     *
     * 受け取りがない場合エラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象
     * @return void
     */
    function NO_SPTAB($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) != 0 && preg_match("/[　 \t\r\n]+/u", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Do not include spaces, tabs or line breaks in T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /* ゼロで開始されている数値の判定 */
    function ZERO_START($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) != 0 && preg_match("/^[0]+[0-9]+$/", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* A numerical value that starts with 0 has been entered for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 必須選択の判定
     *
     * プルダウンなどで選択されていない場合エラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象
     * @return void
     */
    function SELECT_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) == 0) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 is not selected. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 同一性の判定
     *
     * 入力が指定文字数以上ならエラーを返す
     * @param array $value value[0] = 項目名1 value[1] = 項目名2 value[2] = 判定対象文字列1  value[3] = 判定対象文字列2
     * @return void
     */
    function EQUAL_CHECK($value) {
        if (isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
            return;
        }
        // $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[2]] !== $this->arrParam[$value[3]]) {
            $this->arrErr[$value[2]] = t('c_* T_ARG1 and T_ARG2 do not match. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
        }
    }

    /**
     * 値が異なることの判定
     *
     * 入力が指定文字数以上ならエラーを返す
     * @param array $value value[0] = 項目名1 value[1] = 項目名2 value[2] = 判定対象文字列1  value[3] = 判定対象文字列2
     * @return void
     */
    function DIFFERENT_CHECK($value) {
        if (isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
            return;
        }
        // $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[2]] == $this->arrParam[$value[3]]) {
            $this->arrErr[$value[2]] = t('c_* The same value cannot be used for T_ARG1 and T_ARG2. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
        }
    }

    /**
     * 値の大きさを比較する value[2] < value[3]でなければエラー
     *
     * 入力が指定文字数以上ならエラーを返す
     * @param array $value value[0] = 項目名1 value[1] = 項目名2 value[2] = 判定対象文字列1  value[3] = 判定対象文字列2
     * @return void
     */
    function GREATER_CHECK($value) {
        if (isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
            return;
        }
        // $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[2]] != '' && $this->arrParam[$value[3]] != '' && ($this->arrParam[$value[2]] > $this->arrParam[$value[3]])) {
            $this->arrErr[$value[2]] = t('c_* It is not possible to enter a larger value for T_ARG1 than for T_ARG2. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
        }
    }

    /**
     * 最大文字数制限の判定
     *
     * 入力が指定文字数以上ならエラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列  value[2] = 最大文字数(半角も全角も1文字として数える)
     * @return void
     */
    function MAX_LENGTH_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // 文字数の取得
        if (mb_strlen($this->arrParam[$value[1]]) > $value[2]) {
            $this->arrErr[$value[1]] = t('c_* For T_ARG1, enter T_ARG2  characters or less. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[2]));
        }
    }

    /**
     * 最小文字数制限の判定
     *
     * 入力が指定文字数未満ならエラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列 value[2] = 最小文字数(半角も全角も1文字として数える)
     * @return void
     */
    function MIN_LENGTH_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // 文字数の取得
        if (mb_strlen($this->arrParam[$value[1]]) < $value[2]) {
            $this->arrErr[$value[1]] = t_plural($value[2], 'c_* For T_ARG1, enter T_COUNT character or more. <br />_01', 'c_* For T_ARG1, enter T_COUNT characters or more. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 最大文字数制限の判定
     *
     * 入力が最大数以上ならエラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列  value[2] = 最大数]
     * @return void
     */
    function MAX_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[1]] > $value[2]) {
            $this->arrErr[$value[1]] = t('c_* For T_ARG1, enter T_ARG2 or smaller. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[2]));
        }
    }

    /**
     * 最小数値制限の判定
     *
     * 入力が最小数未満ならエラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列  value[2] = 最小数
     * @return void
     */
    function MIN_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if ($this->arrParam[$value[1]] < $value[2]) {
            $this->arrErr[$value[1]] = t_plural($value[2], 'c_* Enter T_COUNT or higher for T_ARG1. <br />_01', 'c_* Enter T_COUNT or higher for T_ARG1. <br />_02', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 数字の判定
     *
     * 入力文字が数字以外ならエラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列
     * @return void
     */
    function NUM_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if ($this->numelicCheck($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter only numbers for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 小数点を含む数字の判定
     *
     * 入力文字が数字以外ならエラーを返す
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列
     * @return void
     */
    function NUM_POINT_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !is_numeric($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter only numbers for T_ARG1. <br />_02', array('T_ARG1' => $value[0]));
        }
    }

    function ALPHA_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !ctype_alpha($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter alphabetical characters for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * 電話番号の判定
     *
     * 数字チェックと文字数チェックを実施する。
     * @param array $value 各要素は以下の通り。<br>
     *     [0]: 項目名<br>
     *     [1]: 電番1項目目<br>
     *     [2]: 電番2項目目<br>
     *     [3]: 電番3項目目<br>
     *     [4]: 電話番号各項目制限 (指定なしの場合、TEL_ITEM_LEN)<br>
     *     [5]: 電話番号総数 (指定なしの場合、TEL_LEN)
     * @return void
     */
    function TEL_CHECK($value) {
        $telItemLen = isset($value[4]) ? $value[4] : TEL_ITEM_LEN;
        $telLen = isset($value[5]) ? $value[5] : TEL_LEN;

        if (isset($this->arrErr[$value[1]]) || isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
            return;
        }
        $this->createParam($value);
        $cnt = 0;

        for ($i = 1; $i <= 3; $i++) {
            if (strlen($this->arrParam[$value[$i]]) > 0) {
                $cnt++;
            }
        }

        // すべての項目が満たされていない場合を判定(一部だけ入力されている状態)
        if ($cnt > 0 && $cnt < 3) {
            $this->arrErr[$value[1]] .= t('c_* Enter all items for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }

        $total_count = 0;
        for ($i = 1; $i <= 3; $i++) {
            if (strlen($this->arrParam[$value[$i]]) > 0 && strlen($this->arrParam[$value[$i]]) > $telItemLen) {
                $this->arrErr[$value[$i]] .= t('c_* T_ARG1 must be T_ARG2 characters or less. <br />_01', array('T_ARG1' => $value[0] . $i, 'T_ARG2' => $telItemLen));
            } else if ($this->numelicCheck($this->arrParam[$value[1]])) {
                $this->arrErr[$value[$i]] .= t('c_* Enter numbers for T_ARG1. <br />_01', array('T_ARG1' => $value[0] . $i));
            }
            $total_count += strlen($this->arrParam[$value[$i]]);
        }

        // 合計値チェック
        if ($total_count > $telLen) {
            $this->arrErr[$value[3]] .= t('c_* For T_ARG1, enter within a text length of T_ARG2. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $telLen));
        }
    }

    /* 関連項目が完全に満たされているか判定
        value[0]    : 項目名
        value[1]    : 判定対象要素名
    */
    function FULL_EXIST_CHECK($value) {
        $max = count($value);
        $this->createParam($value);
        // 既に該当項目にエラーがある場合は、判定しない。
        for ($i = 1; $i < $max; $i++) {
            if (isset($this->arrErr[$value[$i]])) {
                return;
            }
        }

        // すべての項目が入力されていない場合はエラーとする。
        $blank = false;

        for ($i = 1; $i < $max; $i++) {
            if (strlen($this->arrParam[$value[$i]]) <= 0) {
                $blank = true;
            }
        }

        if ($blank) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 is blank. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /* 関連項目がすべて満たされているか判定
        value[0]    : 項目名
        value[1]    : 判定対象要素名
    */
    function ALL_EXIST_CHECK($value) {
        $max = count($value);

        // 既に該当項目にエラーがある場合は、判定しない。
        for ($i = 1; $i < $max; $i++) {
            if (isset($this->arrErr[$value[$i]])) {
                return;
            }
        }

        $blank = false;
        $input = false;

        // すべての項目がブランクでないか、すべての項目が入力されていない場合はエラーとする。
        for ($i = 1; $i < $max; $i++) {
            if (strlen($this->arrParam[$value[$i]]) <= 0) {
                $blank = true;
            } else {
                $input = true;
            }
        }

        if ($blank && $input) {
            $this->arrErr[$value[1]] = t('c_* Enter all items for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /* 関連項目がどれか一つ満たされているか判定
        value[0]    : 項目名
        value[1]    : 判定対象要素名
    */
    function ONE_EXIST_CHECK($value) {
        $max = count($value);
        $this->createParam($value);
        // 既に該当項目にエラーがある場合は、判定しない。
        for ($i = 1; $i < $max; $i++) {
            if (isset($this->arrErr[$value[$i]])) {
                return;
            }
        }

        $input = false;

        // すべての項目がブランクでないか、すべての項目が入力されていない場合はエラーとする。
        for ($i = 1; $i < $max; $i++) {
            if (strlen($this->arrParam[$value[$i]]) > 0) {
                $input = true;
            }
        }

        if (!$input) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 is blank. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /* 上位の項目が満たされているか判定
        value[0]    : 項目名
        value[1]    : 判定対象要素名
    */
    function TOP_EXIST_CHECK($value) {
        $max = count($value);
        $this->createParam($value);

        // 既に該当項目にエラーがある場合は、判定しない。
        for ($i = 1; $i < $max; $i++) {
            if (isset($this->arrErr[$value[$i]])) {
                return;
            }
        }

        $blank = false;
        $error = false;

        // すべての項目がブランクでないか、すべての項目が入力されていない場合はエラーとする。
        for ($i = 1; $i < $max; $i++) {
            if (strlen($this->arrParam[$value[$i]]) <= 0) {
                $blank = true;
            } else {
                if ($blank) {
                    $error = true;
                }
            }
        }

        if ($error) {
            $this->arrErr[$value[1]] = t('c_* Enter items in order, starting at the beginning. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　カタカナの判定　*/
    // 入力文字がカナ以外ならエラーを返す
    // value[0] = 項目名 value[1] = 判定対象文字列
    function KANA_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^[ァ-ヶｦ-ﾟー]+$/u", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* For T_ARG1, enter katakana characters. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　カタカナの判定2 (タブ、スペースは許可する) */
    // 入力文字がカナ以外ならエラーを返す
    // value[0] = 項目名 value[1] = 判定対象文字列
    function KANABLANK_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^([　 \t\r\n]|[ァ-ヶ]|[ー])+$/u", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* For T_ARG1, enter katakana characters. <br />_02', array('T_ARG1' => $value[0]));
        }
    }

    /*　英数字の判定　*/
    // 入力文字が英数字以外ならエラーを返す
    // value[0] = 項目名 value[1] = 判定対象文字列
    function ALNUM_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !ctype_alnum($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter alphanumeric characters for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　英数記号の判定　*/
    // 入力文字が英数記号以外ならエラーを返す
    // value[0] = 項目名 value[1] = 判定対象文字列
    function GRAPH_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^[[:graph:][:space:]]+$/i", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter alphanumeric symbols for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　必須選択の判定　*/
    // 入力値で0が許されない場合エラーを返す
    // value[0] = 項目名 value[1] = 判定対象
    function ZERO_CHECK($value) {
        $this->createParam($value);
        if ($this->arrParam[$value[1]] == '0') {
            $this->arrErr[$value[1]] = t('c_* Enter at least 1 for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　桁数の判定 (最小最大)*/
    // 入力文字の桁数判定　→　最小桁数＜入力文字列＜最大桁数
    // value[0] = 項目名 value[1] = 判定対象文字列 value[2] = 最小桁数 value[3] = 最大桁数
    function NUM_RANGE_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // $this->arrParam[$value[0]] = mb_convert_kana($this->arrParam[$value[0]], 'n');
        $count = strlen($this->arrParam[$value[1]]);
        if (($count > 0) && $value[2] > $count || $value[3] < $count) {
            $this->arrErr[$value[1]] =  t('c_*  T_ARG1 must be  between T_ARG2 - T_ARG3 digits. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[2], 'T_ARG3' => $value[3]));
        }
    }

    /*　桁数の判定　*/
    // 入力文字の桁数判定　→　入力文字列 = 桁数　以外はNGの場合
    // value[0] = 項目名 value[1] = 判定対象文字列 value[2] = 桁数
    function NUM_COUNT_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        $count = strlen($this->arrParam[$value[1]]);
        if (($count > 0) && $count != $value[2]) {
            $this->arrErr[$value[1]] =  t_plural($value[2], 'c_* For T_ARG1, enter T_COUNT digit. <br />_01', 'c_* For T_ARG1, enter T_COUNT digits. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * メールアドレス形式の判定
     *
     * @param array $value 各要素は以下の通り。<br>
     *     [0]: 項目名<br>
     *     [1]: 判定対象を格納している配列キー
     * @return void
     */
    function EMAIL_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }

        $this->createParam($value);

        // 入力がない場合処理しない
        if (strlen($this->arrParam[$value[1]]) === 0) {
            return;
        }

        $wsp           = '[\x20\x09]';
        $vchar         = '[\x21-\x7e]';
        $quoted_pair   = "\\\\(?:$vchar|$wsp)";
        $qtext         = '[\x21\x23-\x5b\x5d-\x7e]';
        $qcontent      = "(?:$qtext|$quoted_pair)";
        $quoted_string = "\"$qcontent*\"";
        $atext         = '[a-zA-Z0-9!#$%&\'*+\-\/\=?^_`{|}~]';
        $dot_atom      = "$atext+(?:[.]$atext+)*";
        $local_part    = "(?:$dot_atom|$quoted_string)";
        $domain        = $dot_atom;
        $addr_spec     = "{$local_part}[@]$domain";

        $dot_atom_loose   = "$atext+(?:[.]|$atext)*";
        $local_part_loose = "(?:$dot_atom_loose|$quoted_string)";
        $addr_spec_loose  = "{$local_part_loose}[@]$domain";

        if (RFC_COMPLIANT_EMAIL_CHECK) {
            $regexp = "/\A{$addr_spec}\z/";
        } else {
            // 携帯メールアドレス用に、..や.@を許容する。
            $regexp = "/\A{$addr_spec_loose}\z/";
        }

        if (!preg_match($regexp, $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* The T_ARG1 format is incorrect. <br />_01', array('T_ARG1' => $value[0]));
            return;
        }

        // 最大文字数制限の判定 (#871)
        $arrValueTemp = $value;
        $arrValueTemp[2] = 256;
        $this->MAX_LENGTH_CHECK($arrValueTemp);
    }

    /*　メールアドレスに使用できる文字の判定　*/
    //　メールアドレスに使用する文字を正規表現で判定する
    //  value[0] = 項目名 value[1] = 判定対象メールアドレス
    function EMAIL_CHAR_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^[a-zA-Z0-9_\.@\+\?-]+$/i",$this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter the characters to be used in T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　URL形式の判定　*/
    //　URLを正規表現で判定する。デフォルトでhttp://があってもOK
    //  value[0] = 項目名 value[1] = 判定対象URL
    function URL_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("@^https?://+($|[a-zA-Z0-9_~=:&\?\.\/-])+$@i", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Enter T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　IPアドレスの判定　*/
    //  value[0] = 項目名 value[1] = 判定対象IPアドレス文字列
    function IP_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        //改行コードが含まれている場合には配列に変換
        $params = str_replace("\r",'',$this->arrParam[$value[1]]);
        if (!empty($params)) {
            if (strpos($params,"\n") === false) {
                $params .= "\n";
            }
            $params = explode("\n",$params);
            foreach ($params as $param) {
                $param = trim($param);
                if (long2ip(ip2long($param)) != trim($param) && !empty($param)) {
                    $this->arrErr[$value[1]] = t('c_* Enter an IP address in the proper format in T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
                }
            }
        }
    }

    /*　拡張子の判定　*/
    // 受け取りがない場合エラーを返す
    // value[0] = 項目名 value[1] = 判定対象 value[2]=array(拡張子)
    function FILE_EXT_CHECK($value) {
        if (isset($this->arrErr[$value[1]]) || count($value[2]) == 0) {
            return;
        }
        $this->createParam($value);

        $match = false;
        if (strlen($_FILES[$value[1]]['name']) >= 1) {
            $filename = $_FILES[$value[1]]['name'];

            foreach ($value[2] as $check_ext) {
                $match = preg_match('/' . preg_quote('.' . $check_ext) . '$/i', $filename) >= 1;
                if ($match === true) {
                    break 1;
                }
            }
        }
        if ($match === false) {
            $str_ext = implode('・', $value[2]);
            $this->arrErr[$value[1]] = t('c_* The format permitted for T_ARG1 is T_ARG2. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $str_ext));
        }
    }

    /* ファイルが存在するかチェックする */
    // 受け取りがない場合エラーを返す
    // value[0] = 項目名 value[1] = 判定対象  value[2] = 指定ディレクトリ
    function FIND_FILE($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }

        $this->createParam($value);
        if ($value[2] != '') {
            $dir = $value[2];
        } else {
            $dir = IMAGE_SAVE_REALDIR;
        }

        $path = $dir . '/' . $this->arrParam[$value[1]];
        $path = str_replace('//', '/', $path);

        if ($this->arrParam[$value[1]] != '' && !file_exists($path)) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 cannot be found. <br />_01', array('T_ARG1' => $path));
        }
    }

    /*　ファイルが上げられたか確認　*/
    // 受け取りがない場合エラーを返す
    // value[0] = 項目名 value[1] = 判定対象  value[2] = 指定サイズ(KB)
    function FILE_EXIST_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (!($_FILES[$value[1]]['size'] != '' && $_FILES[$value[1]]['size'] > 0)) {
            $this->arrErr[$value[1]] = t('c_* Upload T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　ファイルサイズの判定　*/
    // 受け取りがない場合エラーを返す
    // value[0] = 項目名 value[1] = 判定対象  value[2] = 指定サイズ(KB)
    function FILE_SIZE_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if ($_FILES[$value[1]]['size'] > $value[2] *  1024) {
            $byte = 'KB';
            if ($value[2] >= 1000) {
                $value[2] = $value[2] / 1000;
                $byte = 'MB';
            }
            $this->arrErr[$value[1]] = t('c_* For the T_ARG1 file size, use a size that is T_ARG2T_ARG3 or less. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[2], 'T_ARG3' => $byte));
        }
    }

    /*　ファイル名の判定　*/
    // 入力文字が英数字,'_','-'以外ならエラーを返す
    // value[0] = 項目名 value[1] = 判定対象文字列
    function FILE_NAME_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($_FILES[$value[1]]['name']) > 0 && !preg_match("/^[[:alnum:]_\.-]+$/i", $_FILES[$value[1]]['name'])) {
            $this->arrErr[$value[1]] = t('c_* Do not use Japanese or spaces in the file name for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　ファイル名の判定(アップロード以外の時)　*/
    // 入力文字が英数字,'_','-'以外ならエラーを返す
    // value[0] = 項目名 value[1] = 判定対象文字列
    function FILE_NAME_CHECK_BY_NOUPLOAD($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^[[:alnum:]_\.-]+$/i", $this->arrParam[$value[1]]) || preg_match('/[\\]/' ,$this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* Do not use Japanese or spaces in the file name for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    //日付チェック
    // value[0] = 項目名
    // value[1] = YYYY
    // value[2] = MM
    // value[3] = DD
    function CHECK_DATE($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // 少なくともどれか一つが入力されている。
        if ($this->arrParam[$value[1]] > 0 || $this->arrParam[$value[2]] > 0 || $this->arrParam[$value[3]] > 0) {
            // 年月日のどれかが入力されていない。
            if (!(strlen($this->arrParam[$value[1]]) > 0 && strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0)) {
                $this->arrErr[$value[1]] = t('c_* Enter all items for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
            } else if (! checkdate($this->arrParam[$value[2]], $this->arrParam[$value[3]], $this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = t('c_* T_ARG1 is not correct. <br />_01', array('T_ARG1' => $value[0]));
            }
        }
    }

    //日付チェック
    // value[0] = 項目名
    // value[1] = YYYY
    // value[2] = MM
    // value[3] = DD
    // value[4] = HH
    // value[5] = mm
    function CHECK_DATE2($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // 少なくともどれか一つが入力されている。
        if ($this->arrParam[$value[1]] > 0 || $this->arrParam[$value[2]] > 0 || $this->arrParam[$value[3]] > 0 || $this->arrParam[$value[4]] >= 0 || $this->arrParam[$value[5]] >= 0) {
            // 年月日時のどれかが入力されていない。
            if (!(strlen($this->arrParam[$value[1]]) > 0 && strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]]) > 0 && strlen($this->arrParam[$value[5]]) > 0)) {
                $this->arrErr[$value[1]] = t('c_* Enter all items for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
            } else if (! checkdate($this->arrParam[$value[2]], $this->arrParam[$value[3]], $this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = t('c_* T_ARG1 is not correct. <br />_01', array('T_ARG1' => $value[0]));
            }
        }
    }

    //日付チェック
    // value[0] = 項目名
    // value[1] = YYYY
    // value[2] = MM
    function CHECK_DATE3($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        // 少なくともどれか一つが入力されている。
        if ($this->arrParam[$value[1]] > 0 || $this->arrParam[$value[2]] > 0) {
            // 年月日時のどれかが入力されていない。
            if (!(strlen($this->arrParam[$value[1]]) > 0 && strlen($this->arrParam[$value[2]]) > 0)) {
                $this->arrErr[$value[1]] = t('c_* Enter all items for T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
            } else if (! checkdate($this->arrParam[$value[2]], 1, $this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = t('c_* T_ARG1 is not correct. <br />_01', array('T_ARG1' => $value[0]));
            }
        }
    }

    //誕生日チェック
    // value[0] = 項目名
    // value[1] = YYYY
    // value[2] = MM
    // value[3] = DD
    function CHECK_BIRTHDAY($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }

        $this->createParam($value);
        // 年が入力されている。
        if ($this->arrParam[$value[1]] > 0) {

            // 年の数字チェック、最小数値制限チェック
            $this->doFunc(array($value[0].'(' . t('c_Year_01') . ')', $value[1], BIRTH_YEAR), array('NUM_CHECK', 'MIN_CHECK'));
            // 上のチェックでエラーある場合、中断する。
            if (isset($this->arrErr[$value[1]])) {
                return;
            }

            // 年の最大数値制限チェック
            $this->doFunc(array($value[0].'(' . t('c_Year_01') . ')', $value[1], date('Y',strtotime('now'))), array('MAX_CHECK'));
            // 上のチェックでエラーある場合、中断する。
            if (isset($this->arrErr[$value[1]])) {
                return;
            }
        }

        // XXX createParam() が二重に呼ばれる問題を抱える
        $this->CHECK_DATE($value);
    }

    /*-----------------------------------------------------------------*/
    /*  CHECK_SET_TERM
    /*  年月日に別れた2つの期間の妥当性をチェックし、整合性と期間を返す
    /*  引数 (開始年,開始月,開始日,終了年,終了月,終了日)
    /*  戻値 array(１，２，３)
    /*          １．開始年月日 (YYYYMMDD 000000)
    /*          ２．終了年月日 (YYYYMMDD 235959)
    /*          ３．エラー (0 = OK, 1 = NG)
    /*-----------------------------------------------------------------*/
    // value[0] = 項目名1
    // value[1] = 項目名2
    // value[2] = start_year
    // value[3] = start_month
    // value[4] = start_day
    // value[5] = end_year
    // value[6] = end_month
    // value[7] = end_day
    function CHECK_SET_TERM($value) {

        // 期間指定
        if (isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[5]])) {
            return;
        }
        // $this->createParam($value);
        if ((strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0 || strlen($this->arrParam[$value[4]]) > 0) && ! checkdate($this->arrParam[$value[3]], $this->arrParam[$value[4]], $this->arrParam[$value[2]])) {
            $this->arrErr[$value[2]] = t('c_* Specify T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[0]));
        }
        if ((strlen($this->arrParam[$value[5]]) > 0 || strlen($this->arrParam[$value[6]]) > 0 || strlen($this->arrParam[$value[7]]) > 0) && ! checkdate($this->arrParam[$value[6]], $this->arrParam[$value[7]], $this->arrParam[$value[5]])) {
            $this->arrErr[$value[5]] = t('c_* Specify T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[1]));
        }
        if ((strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]]) > 0) &&  (strlen($this->arrParam[$value[5]]) > 0 || strlen($this->arrParam[$value[6]]) > 0 || strlen($this->arrParam[$value[7]]) > 0)) {

            $date1 = $this->arrParam[$value[2]] .sprintf('%02d', $this->arrParam[$value[3]]) .sprintf('%02d',$this->arrParam[$value[4]]) .'000000';
            $date2 = $this->arrParam[$value[5]] .sprintf('%02d', $this->arrParam[$value[6]]) .sprintf('%02d',$this->arrParam[$value[7]]) .'235959';

            if (($this->arrErr[$value[2]] == '' && $this->arrErr[$value[5]] == '') && $date1 > $date2) {
                $this->arrErr[$value[2]] = t('c_* The period specification for T_ARG1 and T_ARG2 is not correct. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
            }
        }
    }

    /*-----------------------------------------------------------------*/
    /*  CHECK_SET_TERM2
    /*  年月日時に別れた2つの期間の妥当性をチェックし、整合性と期間を返す
    /*  引数 (開始年,開始月,開始日,開始時間,開始分,開始秒,
    /*        終了年,終了月,終了日,終了時間,終了分,終了秒)
    /*  戻値 array(１，２，３)
    /*          １．開始年月日 (YYYYMMDDHHmmss)
    /*          ２．終了年月日 (YYYYMMDDHHmmss)
    /*          ３．エラー (0 = OK, 1 = NG)
    /*-----------------------------------------------------------------*/
    // value[0] = 項目名1
    // value[1] = 項目名2
    // value[2] = start_year
    // value[3] = start_month
    // value[4] = start_day
    // value[5] = start_hour
    // value[6] = start_minute
    // value[7] = start_second
    // value[8] = end_year
    // value[9] = end_month
    // value[10] = end_day
    // value[11] = end_hour
    // value[12] = end_minute
    // value[13] = end_second

    /*-----------------------------------------------------------------*/
    function CHECK_SET_TERM2($value) {

        // 期間指定
        if (isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[8]])) {
            return;
        }
        // $this->createParam($value);
        if ((strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0 || strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0) && ! checkdate($this->arrParam[$value[3]], $this->arrParam[$value[4]], $this->arrParam[$value[2]])) {
            $this->arrErr[$value[2]] = t('c_* Specify T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[0]));
        }
        if ((strlen($this->arrParam[$value[8]]) > 0 || strlen($this->arrParam[$value[9]]) > 0 || strlen($this->arrParam[$value[10]]) > 0 || strlen($this->arrParam[$value[11]]) > 0) && ! checkdate($this->arrParam[$value[9]], $this->arrParam[$value[10]], $this->arrParam[$value[8]])) {
            $this->arrErr[$value[8]] = t('c_* Specify T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[1]));
        }
        if ((strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]]) > 0 && strlen($this->arrParam[$value[5]]) > 0) &&  (strlen($this->arrParam[$value[8]]) > 0 || strlen($this->arrParam[$value[9]]) > 0 || strlen($this->arrParam[$value[10]]) > 0 || strlen($this->arrParam[$value[11]]) > 0)) {

            $date1 = $this->arrParam[$value[2]] .sprintf('%02d', $this->arrParam[$value[3]]) .sprintf('%02d',$this->arrParam[$value[4]]) .sprintf('%02d',$this->arrParam[$value[5]]).sprintf('%02d',$this->arrParam[$value[6]]).sprintf('%02d',$this->arrParam[$value[7]]);
            $date2 = $this->arrParam[$value[8]] .sprintf('%02d', $this->arrParam[$value[9]]) .sprintf('%02d',$this->arrParam[$value[10]]) .sprintf('%02d',$this->arrParam[$value[11]]).sprintf('%02d',$this->arrParam[$value[12]]).sprintf('%02d',$this->arrParam[$value[13]]);

            if (($this->arrErr[$value[2]] == '' && $this->arrErr[$value[8]] == '') && $date1 > $date2) {
                $this->arrErr[$value[2]] = t('c_* The period specification for T_ARG1 and T_ARG2 is not correct. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
            }
            if ($date1 == $date2) {
                $this->arrErr[$value[2]] = t('c_* The period specification for T_ARG1 and T_ARG2 is not correct. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
            }

        }
    }

    /*-----------------------------------------------------------------*/
    /*  CHECK_SET_TERM3
    /*  年月に別れた2つの期間の妥当性をチェックし、整合性と期間を返す
    /*　引数 (開始年,開始月,終了年,終了月)
    /*　戻値 array(１，２，３)
    /*          １．開始年月日 (YYYYMMDD 000000)
    /*          ２．終了年月日 (YYYYMMDD 235959)
    /*          ３．エラー (0 = OK, 1 = NG)
    /*-----------------------------------------------------------------*/
    // value[0] = 項目名1
    // value[1] = 項目名2
    // value[2] = start_year
    // value[3] = start_month
    // value[4] = end_year
    // value[5] = end_month
    function CHECK_SET_TERM3($value) {

        // 期間指定
        if (isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[4]])) {
            return;
        }
        // $this->createParam($value);
        if ((strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0) && ! checkdate($this->arrParam[$value[3]], 1, $this->arrParam[$value[2]])) {
            $this->arrErr[$value[2]] = t('c_* Specify T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[0]));
        }
        if ((strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0) && ! checkdate($this->arrParam[$value[5]], 1, $this->arrParam[$value[4]])) {
            $this->arrErr[$value[4]] = t('c_* Specify T_ARG1 correctly. <br />_01', array('T_ARG1' => $value[1]));
        }
        if ((strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && (strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0))) {

            $date1 = $this->arrParam[$value[2]] .sprintf('%02d', $this->arrParam[$value[3]]);
            $date2 = $this->arrParam[$value[4]] .sprintf('%02d', $this->arrParam[$value[5]]);

            if (($this->arrErr[$value[2]] == '' && $this->arrErr[$value[5]] == '') && $date1 > $date2) {
                $this->arrErr[$value[2]] = t('c_* The period specification for T_ARG1 and T_ARG2 is not correct. <br />_01', array('T_ARG1' => $value[0], 'T_ARG2' => $value[1]));
            }
        }
    }

    //ディレクトリ存在チェック
    function DIR_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (!is_dir($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* The designated T_ARG1 does not exist. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    // ドメインチェック
    function DOMAIN_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^\.[^.]+\..+/i", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* The T_ARG1 format is incorrect. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /*　携帯メールアドレスの判定　*/
    //　メールアドレスを正規表現で判定する
    // value[0] = 項目名 value[1] = 判定対象メールアドレス
    function MOBILE_EMAIL_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        $objMobile = new SC_Helper_Mobile_Ex();
        if (strlen($this->arrParam[$value[1]]) > 0 && !$objMobile->gfIsMobileMailAddress($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 is not for mobile phones. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * CHECK_REGIST_CUSTOMER_EMAIL
     *
     * メールアドレスが会員登録されているか調べる
     * @param array $value value[0] = 項目名 value[1] = 判定対象メールアドレス
     * @access public
     * @return void
     */
    function CHECK_REGIST_CUSTOMER_EMAIL($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);

        $register_user_flg =  SC_Helper_Customer_Ex::sfCheckRegisterUserFromEmail($this->arrParam[$value[1]]);
        switch ($register_user_flg) {
            case 1:
                $this->arrErr[$value[1]] .= t('c_* T_ARG1 already used in member registration. <br />_01', array('T_ARG1' => $value[0]));
                break;
            case 2:
                $this->arrErr[$value[1]] .= t('c_* For a certain period of time after membership withdrawal, it is not possible to use the same T_ARG1. <br />_01', array('T_ARG1' => $value[0]));
                break;
            default:
                break;
        }
    }

    /**
     * 禁止文字列のチェック
     * value[0] = 項目名 value[1] = 判定対象文字列
     * value[2] = 入力を禁止する文字列(配列)
     *
     * @example $objErr->doFunc(array(t('c_URL_01'), 'contents', $arrReviewDenyURL), array('PROHIBITED_STR_CHECK'));
     */
    function PROHIBITED_STR_CHECK($value) {
        if (isset($this->arrErr[$value[1]]) || empty($this->arrParam[$value[1]])) {
            return;
        }
        $this->createParam($value);
        $targetStr     = $this->arrParam[$value[1]];
        $prohibitedStr = str_replace(array('|', '/'), array('\|', '\/'), $value[2]);

        $pattern = '/' . join('|', $prohibitedStr) . '/i';
        if (preg_match_all($pattern, $targetStr, $matches = array())) {
            $this->arrErr[$value[1]] = t('c_* T_ARG1 cannot be entered. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * パラメーターとして適切な文字列かチェックする.
     *
     * @access private
     * @param array $value [0] => 項目名, [1] => 評価する文字列
     * @return void
     */
    function EVAL_CHECK($value) {
        if (isset($this->arrErr[$value[0]])) {
            return;
        }
        // $this->createParam($value);
        if ($this->evalCheck($value[1]) === false) {
            $this->arrErr[$value[0]] = t('c_* The T_ARG1 format is incorrect. <br />_01', array('T_ARG1' => $value[0]));
        }
    }

    /**
     * パラメーターとして適切な文字列かチェックする.(サブルーチン)
     *
     * 下記を満たす場合を真とする。
     * ・PHPコードとして評価可能であること。
     * ・評価した結果がスカラデータ(定数に指定できる値)であること。
     * 本メソッドの利用や改訂にあたっては、eval 関数の危険性を意識する必要がある。
     * @access private
     * @param string 評価する文字列
     * @return bool パラメーターとして適切な文字列か
     */
    function evalCheck($value) {
        return @eval('return is_scalar(' . $value . ');');
    }

    /**
     * 未定義の $this->arrParam に空要素を代入する.
     *
     * @access private
     * @param array $value 配列
     * @return void
     */
    function createParam($value) {
         foreach ($value as $val_key => $key) {
             if ($val_key != 0 && (is_string($key) || is_int($key))) {
                 if (!is_numeric($key) && preg_match('/^[a-z0-9_]+$/i', $key)) {
                     if (!isset($this->arrParam[$key])) $this->arrParam[$key] = '';
                     if (strlen($this->arrParam[$key]) > 0
                           && (preg_match('/^[[:alnum:]\-\_]*[\.\/\\\\]*\.\.(\/|\\\\)/',$this->arrParam[$key]) || !preg_match('/\A[^\x00-\x08\x0b\x0c\x0e-\x1f\x7f]+\z/u', $this->arrParam[$key]))) {
                         $this->arrErr[$value[1]] = '※ ' . $value[0] . ' is not a valid character.<br />';
                     }
                 } else if (preg_match('/[^a-z0-9_]/i', $key)) {
                     trigger_error('', E_USER_ERROR);
                 }
             }
         }
    }

    /**
     * 値が数字だけかどうかチェックする
     *
     * @access private
     * @param string $string チェックする文字列
     * @return boolean 値が10進数の数値表現のみの場合 true
     */
    function numelicCheck($string) {
        /*
         * XXX 10進数の数値表現か否かを調べたいだけだが,
         * ctype_digit() は文字列以外 false を返す.
         * string ではなく int 型の数値が入る場合がある.
         */
        $string = (string) $string;
        return strlen($string) > 0 && !ctype_digit($string);
    }
}
