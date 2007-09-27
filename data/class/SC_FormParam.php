<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* パラメータ管理クラス */
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
        $this->check_dir = IMAGE_SAVE_DIR;
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
                $this->html_disp_name[$cnt] = $this->disp_name[$cnt] . "<span class='red'>(※ 必須)</span>";
            } else {
                $this->html_disp_name[$cnt] = $this->disp_name[$cnt];
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
            list($y, $m, $d) = split("[- ]", $db_date);
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
                break;
            }
            $cnt++;
        }
    }

    function toLower($key) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $key) {
                $this->param[$cnt] = strtolower($this->param[$cnt]);
                break;
            }
            $cnt++;
        }
    }

    // エラーチェック
    function checkError($br = true, $keyname = "") {
        // 連想配列の取得
        $arrRet = $this->getHashArray($keyname);
        $objErr = new SC_CheckError($arrRet);
        $cnt = 0;
        foreach($this->keyname as $val) {
            foreach($this->arrCheck[$cnt] as $func) {
                switch($func) {
                case 'EXIST_CHECK':
                case 'NUM_CHECK':
                case 'EMAIL_CHECK':
                case 'EMAIL_CHAR_CHECK':
                case 'ALNUM_CHECK':
                case 'KANA_CHECK':
                case 'URL_CHECK':
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

                    if(!is_array($this->param[$cnt])) {
                        $objErr->doFunc(array($this->disp_name[$cnt], $val), array($func));
                    } else {
                        $max = count($this->param[$cnt]);
                        for($i = 0; $i < $max; $i++) {
                            $objSubErr = new SC_CheckError($this->param[$cnt]);
                            $objSubErr->doFunc(array($this->disp_name[$cnt], $i), array($func));
                            if(count($objSubErr->arrErr) > 0) {
                                foreach($objSubErr->arrErr as $mess) {
                                    if($mess != "") {
                                        $objErr->arrErr[$val] = $mess;
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'MAX_LENGTH_CHECK':
                case 'NUM_COUNT_CHECK':
                    if(!is_array($this->param[$cnt])) {
                        $objErr->doFunc(array($this->disp_name[$cnt], $val, $this->length[$cnt]), array($func));
                    } else {
                        $max = count($this->param[$cnt]);
                        for($i = 0; $i < $max; $i++) {
                            $objSubErr = new SC_CheckError($this->param[$cnt]);
                            $objSubErr->doFunc(array($this->disp_name[$cnt], $i, $this->length[$cnt]), array($func));
                            if(count($objSubErr->arrErr) > 0) {
                                foreach($objSubErr->arrErr as $mess) {
                                    if($mess != "") {
                                        $objErr->arrErr[$val] = $mess;
                                    }
                                }
                            }
                        }
                    }
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

    // 入力文字の変換
    function convParam() {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  「全角」英字を「半角」英字に変換
         */
        $cnt = 0;
        foreach ($this->keyname as $val) {
            if (!isset($this->param[$cnt])) $this->param[$cnt] = "";

            if(!is_array($this->param[$cnt])) {
                if($this->convert[$cnt] != "") {
                    $this->param[$cnt] = mb_convert_kana($this->param[$cnt] ,$this->convert[$cnt]);
                }
            } else {
                $max = count($this->param[$cnt]);
                for($i = 0; $i < $max; $i++) {
                    if($this->convert[$cnt] != "") {
                        $this->param[$cnt][$i] = mb_convert_kana($this->param[$cnt][$i] ,$this->convert[$cnt]);
                    }
                }
            }
            $cnt++;
        }
    }

    // 連想配列の作成
    function getHashArray($keyname = "") {
        $arrRet = array();
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($keyname == "" || $keyname == $val) {
                $arrRet[$val] = $this->param[$cnt];
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
                $arrRet[$val] = $this->param[$cnt];
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

            /*
             * :XXX: isset() でチェックできない
             */
            if (empty($this->param[$cnt])) $this->param[$cnt] = "";

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
    function getValue($keyname) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $keyname) {
                $ret = isset($this->param[$cnt]) ? $this->param[$cnt] : "";
                break;
            }
            $cnt++;
        }
        return $ret;
    }

    function splitCheckBoxes($keyname) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $keyname) {
                $this->param[$cnt] = sfSplitCheckBoxes($this->param[$cnt]);
            }
            $cnt++;
        }
    }

    function splitParamCheckBoxes($keyname) {
        $cnt = 0;
        foreach($this->keyname as $val) {
            if($val == $keyname) {
                if(isset($this->param[$cnt]) && !is_array($this->param[$cnt])) {
                    $this->param[$cnt] = split("-", $this->param[$cnt]);
                }
            }
            $cnt++;
        }
    }
}
?>
