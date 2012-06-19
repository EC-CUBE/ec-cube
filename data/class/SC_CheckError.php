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

        $this->arrErr[$value[1]] = '※ ' . $value[0] . 'に許可されていないタグ ' . $html_diff_tag_list . ' が含まれています。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が入力されていません。<br />';
        } else if (is_array($this->arrParam[$value[1]]) && count($this->arrParam[$value[1]]) == 0) {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が選択されていません。<br />';
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
        $this->createParam($value);
        if (strlen($this->arrParam[$value[0]]) == 0) {
            $this->arrErr[$value[0]] = '※ ' . $value[0] . 'が入力されていません。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'にスペース、タブ、改行のみの入力はできません。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'にスペース、タブ、改行は含めないで下さい。<br />';
        }
    }

    /* ゼロで開始されている数値の判定 */
    function ZERO_START($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) != 0 && preg_match("/^[0]+[0-9]+$/", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'に0で始まる数値が入力されています。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が選択されていません。<br />';
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
        $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[2]] !== $this->arrParam[$value[3]]) {
            $this->arrErr[$value[2]] = '※ ' . $value[0] . 'と' . $value[1] . 'が一致しません。<br />';
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
        $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[2]] == $this->arrParam[$value[3]]) {
            $this->arrErr[$value[2]] = '※ ' . $value[0] . 'と' . $value[1] . 'は、同じ値を使用できません。<br />';
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
        $this->createParam($value);
        // 文字数の取得
        if ($this->arrParam[$value[2]] != '' && $this->arrParam[$value[3]] != '' && ($this->arrParam[$value[2]] > $this->arrParam[$value[3]])) {
            $this->arrErr[$value[2]] = '※ ' . $value[0] . 'は' . $value[1] . 'より大きい値を入力できません。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は' . $value[2] . '字以下で入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は' . $value[2] . '字以上で入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は' . $value[2] . '以下で入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は' . $value[2] . '以上で入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は数字で入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は数字で入力してください。<br />';
        }
    }

    function ALPHA_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if (strlen($this->arrParam[$value[1]]) > 0 && !ctype_alpha($this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は半角英字で入力してください。<br />';
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
            $this->arrErr[$value[1]] .= '※ ' . $value[0] . 'はすべての項目を入力してください。<br />';
        }

        $total_count = 0;
        for ($i = 1; $i <= 3; $i++) {
            if (strlen($this->arrParam[$value[$i]]) > 0 && strlen($this->arrParam[$value[$i]]) > $telItemLen) {
                $this->arrErr[$value[$i]] .= '※ ' . $value[0] . $i . 'は' . $telItemLen . '字以内で入力してください。<br />';
            } else if ($this->numelicCheck($this->arrParam[$value[1]])) {
                $this->arrErr[$value[$i]] .= '※ ' . $value[0] . $i . 'は数字で入力してください。<br />';
            }
            $total_count += strlen($this->arrParam[$value[$i]]);
        }

        // 合計値チェック
        if ($total_count > $telLen) {
            $this->arrErr[$value[3]] .= '※ ' . $value[0] . 'は' . $telLen . '文字以内で入力してください。<br />';
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

        $blank = false;

        // すべての項目がブランクでないか、すべての項目が入力されていない場合はエラーとする。
        for ($i = 1; $i < $max; $i++) {
            if (strlen($this->arrParam[$value[$i]]) <= 0) {
                $blank = true;
            }
        }

        if ($blank) {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が入力されていません。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'はすべての項目を入力して下さい。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が入力されていません。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は先頭の項目から順番に入力して下さい。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'はカタカナで入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'はカタカナで入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は英数字で入力してください。<br />';
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
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^[[:graph:]|[:space:]]+$/i", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は英数記号で入力してください。<br />';
        }
    }

    /*　必須選択の判定　*/
    // 入力値で0が許されない場合エラーを返す
    // value[0] = 項目名 value[1] = 判定対象
    function ZERO_CHECK($value) {
        $this->createParam($value);
        if ($this->arrParam[$value[1]] == '0') {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は1以上を入力してください。<br />';
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
            $this->arrErr[$value[1]] =  "※ $value[0]は$value[2]桁～$value[3]桁で入力して下さい。<br />";
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
            $this->arrErr[$value[1]] =  "※ $value[0]は$value[2]桁で入力して下さい。<br />";
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'の形式が不正です。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'に使用する文字を正しく入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'を正しく入力してください。<br />';
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
                    $this->arrErr[$value[1]] = '※ ' . $value[0] . 'に正しい形式のIPアドレスを入力してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'で許可されている形式は、' . $str_ext . 'です。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $path . 'が見つかりません。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'をアップロードして下さい。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'のファイルサイズは' . $value[2] . $byte . '以下のものを使用してください。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'のファイル名に日本語やスペースは使用しないで下さい。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'のファイル名に日本語やスペースは使用しないで下さい。<br />';
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
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'はすべての項目を入力して下さい。<br />';
            } else if (! checkdate($this->arrParam[$value[2]], $this->arrParam[$value[3]], $this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が正しくありません。<br />';
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
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'はすべての項目を入力して下さい。<br />';
            } else if (! checkdate($this->arrParam[$value[2]], $this->arrParam[$value[3]], $this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が正しくありません。<br />';
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
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'はすべての項目を入力して下さい。<br />';
            } else if (! checkdate($this->arrParam[$value[2]], 1, $this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = '※ ' . $value[0] . 'が正しくありません。<br />';
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
            $this->doFunc(array($value[0].'(年)', $value[1], BIRTH_YEAR), array('NUM_CHECK', 'MIN_CHECK'));
            // 上のチェックでエラーある場合、中断する。
            if (isset($this->arrErr[$value[1]])) {
                return;
            }

            // 年の最大数値制限チェック
            $this->doFunc(array($value[0].'(年)', $value[1], date('Y',strtotime('now'))), array('MAX_CHECK'));
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
        $this->createParam($value);
        if ((strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0 || strlen($this->arrParam[$value[4]]) > 0) && ! checkdate($this->arrParam[$value[3]], $this->arrParam[$value[4]], $this->arrParam[$value[2]])) {
            $this->arrErr[$value[2]] = '※ ' . $value[0] . 'を正しく指定してください。<br />';
        }
        if ((strlen($this->arrParam[$value[5]]) > 0 || strlen($this->arrParam[$value[6]]) > 0 || strlen($this->arrParam[$value[7]]) > 0) && ! checkdate($this->arrParam[$value[6]], $this->arrParam[$value[7]], $this->arrParam[$value[5]])) {
            $this->arrErr[$value[5]] = '※ ' . $value[1] . 'を正しく指定してください。<br />';
        }
        if ((strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]]) > 0) &&  (strlen($this->arrParam[$value[5]]) > 0 || strlen($this->arrParam[$value[6]]) > 0 || strlen($this->arrParam[$value[7]]) > 0)) {

            $date1 = $this->arrParam[$value[2]] .sprintf('%02d', $this->arrParam[$value[3]]) .sprintf('%02d',$this->arrParam[$value[4]]) .'000000';
            $date2 = $this->arrParam[$value[5]] .sprintf('%02d', $this->arrParam[$value[6]]) .sprintf('%02d',$this->arrParam[$value[7]]) .'235959';

            if (($this->arrErr[$value[2]] == '' && $this->arrErr[$value[5]] == '') && $date1 > $date2) {
                $this->arrErr[$value[2]] = '※ ' .$value[0]. 'と' .$value[1]. 'の期間指定が不正です。<br />';
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
        $this->createParam($value);
        if ((strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0 || strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0) && ! checkdate($this->arrParam[$value[3]], $this->arrParam[$value[4]], $this->arrParam[$value[2]])) {
            $this->arrErr[$value[2]] = '※ ' . $value[0] . 'を正しく指定してください。<br />';
        }
        if ((strlen($this->arrParam[$value[8]]) > 0 || strlen($this->arrParam[$value[9]]) > 0 || strlen($this->arrParam[$value[10]]) > 0 || strlen($this->arrParam[$value[11]]) > 0) && ! checkdate($this->arrParam[$value[9]], $this->arrParam[$value[10]], $this->arrParam[$value[8]])) {
            $this->arrErr[$value[8]] = '※ ' . $value[1] . 'を正しく指定してください。<br />';
        }
        if ((strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]]) > 0 && strlen($this->arrParam[$value[5]]) > 0) &&  (strlen($this->arrParam[$value[8]]) > 0 || strlen($this->arrParam[$value[9]]) > 0 || strlen($this->arrParam[$value[10]]) > 0 || strlen($this->arrParam[$value[11]]) > 0)) {

            $date1 = $this->arrParam[$value[2]] .sprintf('%02d', $this->arrParam[$value[3]]) .sprintf('%02d',$this->arrParam[$value[4]]) .sprintf('%02d',$this->arrParam[$value[5]]).sprintf('%02d',$this->arrParam[$value[6]]).sprintf('%02d',$this->arrParam[$value[7]]);
            $date2 = $this->arrParam[$value[8]] .sprintf('%02d', $this->arrParam[$value[9]]) .sprintf('%02d',$this->arrParam[$value[10]]) .sprintf('%02d',$this->arrParam[$value[11]]).sprintf('%02d',$this->arrParam[$value[12]]).sprintf('%02d',$this->arrParam[$value[13]]);

            if (($this->arrErr[$value[2]] == '' && $this->arrErr[$value[8]] == '') && $date1 > $date2) {
                $this->arrErr[$value[2]] = '※ ' .$value[0]. 'と' .$value[1]. 'の期間指定が不正です。<br />';
            }
            if ($date1 == $date2) {
                $this->arrErr[$value[2]] = '※ ' .$value[0]. 'と' .$value[1]. 'の期間指定が不正です。<br />';
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
        $this->createParam($value);
        if ((strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0) && ! checkdate($this->arrParam[$value[3]], 1, $this->arrParam[$value[2]])) {
            $this->arrErr[$value[2]] = '※ ' . $value[0] . 'を正しく指定してください。<br />';
        }
        if ((strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0) && ! checkdate($this->arrParam[$value[5]], 1, $this->arrParam[$value[4]])) {
            $this->arrErr[$value[4]] = '※ ' . $value[1] . 'を正しく指定してください。<br />';
        }
        if ((strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && (strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0))) {

            $date1 = $this->arrParam[$value[2]] .sprintf('%02d', $this->arrParam[$value[3]]);
            $date2 = $this->arrParam[$value[4]] .sprintf('%02d', $this->arrParam[$value[5]]);

            if (($this->arrErr[$value[2]] == '' && $this->arrErr[$value[5]] == '') && $date1 > $date2) {
                $this->arrErr[$value[2]] = '※ ' .$value[0]. 'と' .$value[1]. 'の期間指定が不正です。<br />';
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
            $this->arrErr[$value[1]] = '※ 指定した' . $value[0] . 'は存在しません。<br />';
        }
    }

    // ドメインチェック
    function DOMAIN_CHECK($value) {
        if (isset($this->arrErr[$value[1]])) {
            return;
        }
        if (strlen($this->arrParam[$value[1]]) > 0 && !preg_match("/^\.[^.]+\..+/i", $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'の形式が不正です。<br />';
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は携帯電話のものではありません。<br />';
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
                $this->arrErr[$value[1]] .= '※ すでに会員登録で使用されている' . $value[0] . 'です。<br />';
                break;
            case 2:
                $this->arrErr[$value[1]] .= '※ 退会から一定期間の間は、同じ' . $value[0] . 'を使用することはできません。<br />';
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
     * @example $objErr->doFunc(array('URL', 'contents', $arrReviewDenyURL), array('PROHIBITED_STR_CHECK'));
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
            $this->arrErr[$value[1]] = '※ ' . $value[0] . 'は入力できません。<br />';
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
        $this->createParam($value);
        if ($this->evalCheck($value[1]) === false) {
            $this->arrErr[$value[0]] = '※ ' . $value[0] . ' の形式が不正です。<br />';
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
        foreach ($value as $key) {
            if (is_string($key) || is_int($key)) {
                if (!isset($this->arrParam[$key]))  $this->arrParam[$key] = '';
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
