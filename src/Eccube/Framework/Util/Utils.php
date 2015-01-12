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

namespace Eccube\Framework\Util;

use Eccube\Application;
use Eccube\Framework\Display;
use Eccube\Framework\Query;
use Eccube\Framework\Session;
use Eccube\Framework\Helper\FileManagerHelper;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\DB\MasterData;
use Net_URL;
use Services_JSON;

/**
 * 各種ユーティリティクラス.
 *
 * 主に static 参照するユーティリティ系の関数群
 *
 * :XXX: 内部でインスタンスを生成している関数は, Helper クラスへ移動するべき...
 *
 * @package Util
 * @author LOCKON CO.,LTD.
 */
class Utils
{
    // インストール初期処理
    public static function sfInitInstall()
    {
        if (
            !defined('ECCUBE_INSTALL')              // インストール済みが定義されていない。
            && !GcUtils::isInstallFunction()    // インストール中でない。
        ) {
            $install_url = static::getInstallerPath();
            header('Location: ' . $install_url);
            exit;
        }
        $path = HTML_REALDIR . 'install/' . DIR_INDEX_FILE;
        if (file_exists($path)) {
            static::sfErrorHeader('&gt;&gt; /install/' . DIR_INDEX_FILE . ' は、インストール完了後にファイルを削除してください。');
        }
    }

    /**
     * インストーラーの URL を返す
     *
     * @return string インストーラーの URL
     */
    public static function getInstallerPath()
    {
        /* @var $netUrl Net_URL */
        $netUrl = Application::alias('pear.net.url');

        $installer = 'install/' . DIR_INDEX_PATH;
        // XXX メソッド名は add で始まるが、実際には置換を行う
        $netUrl->addRawQueryString('');
        $current_url = $netUrl->getURL();
        $current_url = dirname($current_url) . '/';
        // XXX 先頭の / を含まない。
        $urlpath = substr($_SERVER['SCRIPT_FILENAME'], strlen(HTML_REALDIR));
        // / を 0、/foo/ を 1 としたディレクトリー階層数
        $dir_level = substr_count($urlpath, '/');
        $installer_url = str_repeat('../', $dir_level) . $installer;

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
     * @param  string $url http(s):// から始まる URL
     * @return string $url を絶対パスに変換した URL
     */
    public static function getRealURL($url)
    {
        $parse = parse_url($url);
        $tmp = explode('/', $parse['path']);
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

        return $parse['scheme'] . '://' . $parse['host'] . ':' . $parse['port'] .'/' . $path;
    }

    // 装飾付きエラーメッセージの表示

    /**
     * @param string $mess
     */
    public static function sfErrorHeader($mess, $print = false)
    {
        global $GLOBAL_ERR;
        $GLOBAL_ERR.= '<div id="errorHeader">';
        $GLOBAL_ERR.= $mess;
        $GLOBAL_ERR.= '</div>';
        if ($print) {
            echo $GLOBAL_ERR;
        }
    }

    /* エラーページの表示 */
    public static function sfDispError($type)
    {
        $objPage = new \Eccube\Page\Error\DispError();
        $objPage->init();
        $objPage->type = $type;
        $objPage->process();
        exit;
    }

    /* サイトエラーページの表示 */
    public static function sfDispSiteError($type, $objSiteSess = '', $return_top = false, $err_msg = '')
    {
        $objPage = new \Eccube\Page\Error\Index();
        $objPage->init();
        $objPage->type = $type;
        $objPage->objSiteSess = $objSiteSess;
        $objPage->return_top = $return_top;
        $objPage->err_msg = $err_msg;
        $objPage->is_mobile = Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE;
        $objPage->process();
        exit;
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 trigger_error($debugMsg, E_USER_ERROR) を使用すること
     */
    public function sfDispException($debugMsg = null)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        trigger_error($debugMsg, E_USER_ERROR);
    }

    /**
     * 認証の可否判定
     *
     * @param Session $objSess
     * @param bool $disp_error
     * @return bool
     */
    public static function sfIsSuccess(Session $objSess, $disp_error = true)
    {
        $ret = $objSess->IsSuccess();
        if ($ret != SUCCESS) {
            if ($disp_error) {
                // エラーページの表示
                static::sfDispError($ret);
            }

            return false;
        }
        // リファラーチェック(CSRFの暫定的な対策)
        // 「リファラ無」 の場合はスルー
        // 「リファラ有」 かつ 「管理画面からの遷移でない」 場合にエラー画面を表示する
        if (empty($_SERVER['HTTP_REFERER'])) {
            // TODO 警告表示させる？
            // sfErrorHeader('>> referrerが無効になっています。');
        } else {
            $domain  = static::sfIsHTTPS() ? HTTPS_URL : HTTP_URL;
            $pattern = sprintf('|^%s.*|', $domain);
            $referer = $_SERVER['HTTP_REFERER'];

            // 管理画面から以外の遷移の場合はエラー画面を表示
            if (!preg_match($pattern, $referer)) {
                if ($disp_error) static::sfDispError(INVALID_MOVE_ERRORR);
                return false;
            }
        }

        return true;
    }

    /**
     * 文字列をアスタリスクへ変換する.
     *
     * @param  string $passlen 変換する文字列
     * @return string アスタリスクへ変換した文字列
     */
    public static function sfPassLen($passlen)
    {
        $ret = '';
        for ($i=0;$i<$passlen;true) {
            $ret.='*';
            $i++;
        }

        return $ret;
    }

    /**
     * HTTPSかどうかを判定
     *
     * @return bool
     */
    public static function sfIsHTTPS()
    {
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
     *
     *  使用されていない.
     *
     *  @param  obj  Session, SiteSession
     *  @return bool
     */
    public function sfIsValidTransition($objSess)
    {
        // 前画面からPOSTされるuniqidが正しいものかどうかをチェック
        $uniqid = $objSess->getUniqId();
        if (!empty($_POST['uniqid']) && ($_POST['uniqid'] === $uniqid)) {
            return true;
        } else {
            return false;
        }
    }

    /* DB用日付文字列取得 */
    public static function sfGetTimestamp($year, $month, $day, $last = false)
    {
        if ($year != '' && $month != '' && $day != '') {
            if ($last) {
                $time = '23:59:59';
            } else {
                $time = '00:00:00';
            }
            $date = $year.'-'.$month.'-'.$day.' '.$time;
        } else {
            $date = '';
        }

        return $date;
    }

    /* DB用日付日時文字列取得 */

    /**
     * @param string $month
     * @param string $day
     * @param string $hour
     * @param string $minutes
     */
    public static function sfGetTimestampistime($year, $month, $day, $hour, $minutes, $last = false)
    {
        if ($year != '' && $month != '' && $day != '' && $hour != '' && $minutes != '') {
            if ($last) {
                $time = $hour.':'.$minutes.':59';
            } else {
                $time = $hour.':'.$minutes.':00';
            }
            $date = $year.'-'.$month.'-'.$day.' '.$time;
        } else {
            $date = '';
        }

        return $date;
    }

    /**
     *  INT型の数値チェック
     *  ・FIXME: マイナス値の扱いが不明確
     *  ・XXX: INT_LENには収まるが、INT型の範囲を超えるケースに対応できないのでは?
     *
     *  @param mixed $value
     *  @return bool
     */
    public static function sfIsInt($value)
    {
        if (strlen($value) >= 1 && strlen($value) <= INT_LEN && is_numeric($value)) {
            return true;
        }

        return false;
    }

    /**
     * 桁が0で埋められているかを判定する
     *
     * @param  string  $value 検査対象
     * @return boolean 0で埋められている
     */
    public static function sfIsZeroFilling($value)
    {
        if (strlen($value) > 1 && $value{0} === '0')

            return true;

        return false;
    }

    /**
     * @param string $data
     */
    public static function sfGetCSVData($data, $prefix = '')
    {
        if ($prefix == '') {
            $dir_name = static::sfUpDirName();
            $file_name = $dir_name . date('ymdHis') .'.csv';
        } else {
            $file_name = $prefix . date('ymdHis') .'.csv';
        }

        if (mb_internal_encoding() == CHAR_CODE) {
            $data = mb_convert_encoding($data, 'SJIS-Win', CHAR_CODE);
        }

        /* データを出力 */

        return array($file_name, $data);
    }

    /* 1階層上のディレクトリ名を取得する */
    public static function sfUpDirName()
    {
        $path = $_SERVER['SCRIPT_NAME'];
        $arrVal = explode('/', $path);
        $cnt = count($arrVal);

        return $arrVal[($cnt - 2)];
    }

    // チェックボックスの値をマージ
    /**
     * @deprecated
     */
    public function sfMergeCBValue($keyname, $max)
    {
        $conv = '';
        $cnt = 1;
        for ($cnt = 1; $cnt <= $max; $cnt++) {
            if ($_POST[$keyname . $cnt] == '1') {
                $conv.= '1';
            } else {
                $conv.= '0';
            }
        }

        return $conv;
    }

    // html_checkboxesの値をマージして2進数形式に変更する。
    /**
     * @deprecated
     */
    public function sfMergeCheckBoxes($array, $max)
    {
        $ret = '';
        $arrTmp = array();
        if (is_array($array)) {
            foreach ($array as $val) {
                $arrTmp[$val] = '1';
            }
        }
        for ($i = 1; $i <= $max; $i++) {
            if (isset($arrTmp[$i]) && $arrTmp[$i] == '1') {
                $ret.= '1';
            } else {
                $ret.= '0';
            }
        }

        return $ret;
    }

    // html_checkboxesの値をマージして「-」でつなげる。
    /**
     * @deprecated
     */
    public function sfMergeParamCheckBoxes($array)
    {
        $ret = '';
        if (is_array($array)) {
            foreach ($array as $val) {
                if ($ret != '') {
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
    /**
     * @deprecated
     */
    public function sfSearchCheckBoxes($array)
    {
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
    /**
     * @deprecated
     */
    public function sfSplitCheckBoxes($val)
    {
        $arrRet = array();
        $len = strlen($val);
        for ($i = 0; $i < $len; $i++) {
            if (substr($val, $i, 1) == '1') {
                $arrRet[] = ($i + 1);
            }
        }

        return $arrRet;
    }

    // チェックボックスの値をマージ
    /**
     * @deprecated
     */
    public function sfMergeCBSearchValue($keyname, $max)
    {
        $conv = '';
        $cnt = 1;
        for ($cnt = 1; $cnt <= $max; $cnt++) {
            if ($_POST[$keyname . $cnt] == '1') {
                $conv.= '1';
            } else {
                $conv.= '_';
            }
        }

        return $conv;
    }

    // チェックボックスの値を分解
    /**
     * @deprecated
     */
    public function sfSplitCBValue($val, $keyname = '')
    {
        $arr = array();
        $len = strlen($val);
        $no = 1;
        for ($cnt = 0; $cnt < $len; $cnt++) {
            if ($keyname != '') {
                $arr[$keyname . $no] = substr($val, $cnt, 1);
            } else {
                $arr[] = substr($val, $cnt, 1);
            }
            $no++;
        }

        return $arr;
    }

    // キーと値をセットした配列を取得

    /**
     * @param string $valname
     * @param string $keyname
     */
    public static function sfArrKeyValue($arrList, $keyname, $valname, $len_max = '', $keysize = '')
    {
        $arrRet = array();
        $max = count($arrList);

        if ($len_max != '' && $max > $len_max) {
            $max = $len_max;
        }

        for ($cnt = 0; $cnt < $max; $cnt++) {
            if ($keysize != '') {
                $key = static::sfCutString($arrList[$cnt][$keyname], $keysize);
            } else {
                $key = $arrList[$cnt][$keyname];
            }
            $val = $arrList[$cnt][$valname];

            if (!isset($arrRet[$key])) {
                $arrRet[$key] = $val;
            }

        }

        return $arrRet;
    }

    /**
     * キーと値をセットした配列を取得(値が複数の場合)
     * 使用されていない
     */
    public function sfArrKeyValues($arrList, $keyname, $valname, $len_max = '', $keysize = '', $connect = '')
    {
        $max = count($arrList);

        if ($len_max != '' && $max > $len_max) {
            $max = $len_max;
        }

        $keyValues = array();
        for ($cnt = 0; $cnt < $max; $cnt++) {
            if ($keysize != '') {
                $key = static::sfCutString($arrList[$cnt][$keyname], $keysize);
            } else {
                $key = $arrList[$cnt][$keyname];
            }
            $val = $arrList[$cnt][$valname];

            if ($connect != '') {
                $keyValues[$key].= "$val".$connect;
            } else {
                $keyValues[$key][] = $val;
            }
        }

        return $keyValues;
    }

    // 配列の値をカンマ区切りで返す。
    public static function sfGetCommaList($array, $space=true, $arrPop = array())
    {
        if (count($array) > 0) {
            $line = '';
            foreach ($array as $val) {
                if (!in_array($val, $arrPop)) {
                    if ($space) {
                        $line .= $val . ', ';
                    } else {
                        $line .= $val . ',';
                    }
                }
            }
            if ($space) {
                $line = preg_replace("/, $/", '', $line);
            } else {
                $line = preg_replace("/,$/", '', $line);
            }

            return $line;
        } else {
            return false;
        }

    }

    /* 配列の要素をCSVフォーマットで出力する。*/
    public static function sfGetCSVList($array)
    {
        $line = '';
        if (count($array) > 0) {
            foreach ($array as $val) {
                $val = mb_convert_encoding($val, CHAR_CODE, CHAR_CODE);
                $line .= '"' .$val. '",';
            }
            $line = preg_replace("/,$/", "\r\n", $line);
        } else {
            return false;
        }

        return $line;
    }

    /**
     *    check_set_term
     *    年月日に別れた2つの期間の妥当性をチェックし、整合性と期間を返す
     *　引数 (開始年,開始月,開始日,終了年,終了月,終了日)
     *　戻値 array(１，２，３）
     *          １．開始年月日 (YYYY/MM/DD 000000)
     *            ２．終了年月日 (YYYY/MM/DD 235959)
     *            ３．エラー (0 = OK, 1 = NG)
     *
     * 使用されていない
     */
    public function sfCheckSetTerm($start_year, $start_month, $start_day, $end_year, $end_month, $end_day)
    {
        // 期間指定
        $error = 0;
        if ($start_month || $start_day || $start_year) {
            if (! checkdate($start_month, $start_day, $start_year)) $error = 1;
        } else {
            $error = 1;
        }
        if ($end_month || $end_day || $end_year) {
            if (! checkdate($end_month, $end_day, $end_year)) $error = 2;
        }
        if (! $error) {
            $date1 = $start_year .'/'.sprintf('%02d', $start_month) .'/'.sprintf('%02d', $start_day) .' 000000';
            $date2 = $end_year   .'/'.sprintf('%02d', $end_month)   .'/'.sprintf('%02d', $end_day)   .' 235959';
            if ($date1 > $date2) $error = 3;
        } else {
            $error = 1;
        }

        return array($date1, $date2, $error);
    }

    // エラー箇所の背景色を変更するためのfunction Viewで読み込む
    public function sfSetErrorStyle()
    {
        return 'style="background-color:'.ERR_COLOR.'"';
    }

    // 一致した値のキー名を取得
    public function sfSearchKey($array, $word, $default)
    {
        foreach ($array as $key => $val) {
            if ($val == $word) {
                return $key;
            }
        }

        return $default;
    }

    public function sfGetErrorColor($val)
    {
        if ($val != '') {
            return 'background-color:' . ERR_COLOR;
        }

        return '';
    }

    public function sfGetEnabled($val)
    {
        if (! $val) {
            return ' disabled="disabled"';
        }

        return '';
    }

    public function sfGetChecked($param, $value)
    {
        if ((string) $param === (string) $value) {
            return 'checked="checked"';
        }

        return '';
    }

    public function sfTrim($str)
    {
        $ret = preg_replace("/^[　 \n\r]*/u", '', $str);
        $ret = preg_replace("/[　 \n\r]*$/u", '', $ret);

        return $ret;
    }

    /**
     * 税金額を返す
     *
     * ・店舗基本情報に基づいた計算は \Eccube\Helper\Application::alias('eccube.helper.db')->tax() を使用する
     *
     * @param integer $price 計算対象の金額
     * @param integer $tax   税率(%単位)
     *     XXX integer のみか不明
     * @param  integer $tax_rule 端数処理
     * @return double 税金額
     */
    public static function sfTax($price, $tax, $tax_rule)
    {
        $real_tax = $tax / 100;
        $ret = $price * $real_tax;
        $ret = TaxRuleHelper::roundByCalcRule($ret, $tax_rule);

        return $ret;
    }

    /**
     * 税金付与した金額を返す
     *
     * ・店舗基本情報に基づいた計算は \Eccube\Helper\Application::alias('eccube.helper.db')->tax() を使用する
     *
     * @param integer $price 計算対象の金額
     * @param integer $tax   税率(%単位)
     *     XXX integer のみか不明
     * @param  integer $tax_rule 端数処理
     * @return double 税金付与した金額
     */
    public static function sfCalcIncTax($price, $tax, $tax_rule)
    {
        return $price + static::sfTax($price, $tax, $tax_rule);
    }

    /**
     * 桁数を指定して四捨五入
     *
     * 使用されていない
     */
    public function sfRound($value, $pow = 0)
    {
        $adjust = pow(10, $pow-1);

        // 整数且つ0でなければ桁数指定を行う
        if (static::sfIsInt($adjust) and $pow > 1) {
            $ret = (round($value * $adjust)/$adjust);
        }

        $ret = round($ret);

        return $ret;
    }

    /**
     * ポイント付与
     * $product_id が使われていない。
     * @param  int   $price
     * @param  float $point_rate
     * @param  int   $rule
     * @return double
     */
    public static function sfPrePoint($price, $point_rate, $rule = POINT_RULE)
    {
        $real_point = $point_rate / 100;
        $ret = $price * $real_point;
        $ret = TaxRuleHelper::roundByCalcRule($ret, $rule);

        return $ret;
    }

    /* 規格分類の件数取得 */
    public static function sfGetClassCatCount()
    {
        $sql = 'select count(dtb_class.class_id) as count, dtb_class.class_id ';
        $sql.= 'from dtb_class inner join dtb_classcategory on dtb_class.class_id = dtb_classcategory.class_id ';
        $sql.= 'where dtb_class.del_flg = 0 AND dtb_classcategory.del_flg = 0 ';
        $sql.= 'group by dtb_class.class_id, dtb_class.name';

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $arrList = $objQuery->getAll($sql);
        // キーと値をセットした配列を取得
        $arrRet = static::sfArrKeyValue($arrList, 'class_id', 'count');

        return $arrRet;
    }

    /**
     * 商品IDとカテゴリIDから商品規格IDを取得する
     * @param  int $product_id
     * @param  int $classcategory_id1 デフォルト値0
     * @param  int $classcategory_id2 デフォルト値0
     * @return int
     */
    public static function sfGetProductClassId($product_id, $classcategory_id1=0, $classcategory_id2=0)
    {
        $where = 'product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?';
        if (!$classcategory_id1) { //NULLが入ってきた場合への対策
          $classcategory_id1 = 0;
        }
        if (!$classcategory_id2) {
          $classcategory_id2 = 0;
        }
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $ret = $objQuery->get('product_class_id', 'dtb_products_class', $where, Array($product_id, $classcategory_id1, $classcategory_id2));

        return $ret;
    }

    /* 文末の「/」をなくす */
    public static function sfTrimURL($url)
    {
        $ret = rtrim($url, '/');

        return $ret;
    }

    /* DBから取り出した日付の文字列を調整する。*/
    public function sfDispDBDate($dbdate, $time = true)
    {
        list($y, $m, $d, $H, $M) = preg_split('/[- :]/', $dbdate);

        if (strlen($y) > 0 && strlen($m) > 0 && strlen($d) > 0) {
            if ($time) {
                $str = sprintf('%04d/%02d/%02d %02d:%02d', $y, $m, $d, $H, $M);
            } else {
                $str = sprintf('%04d/%02d/%02d', $y, $m, $d, $H, $M);
            }
        } else {
            $str = '';
        }

        return $str;
    }

    /**
     * 配列をキー名ごとの配列に変更する
     *
     * @param array $array
     * @param bool $isColumnName
     * @return array
     */
    public static function sfSwapArray($array, $isColumnName = true)
    {
        $arrRet = array();
        foreach ($array as $key1 => $arr1) {
            if (!is_array($arr1)) continue 1;
            $index = 0;
            foreach ($arr1 as $key2 => $val) {
                if ($isColumnName) {
                    $arrRet[$key2][$key1] = $val;
                } else {
                    $arrRet[$index++][$key1] = $val;
                }
            }
        }

        return $arrRet;
    }

    /**
     * 連想配列から新たな配列を生成して返す.
     *
     * $requires が指定された場合, $requires に含まれるキーの値のみを返す.
     *
     * @param array $hash 連想配列
     * @param array $requires 必須キーの配列
     * @return array 連想配列の値のみの配列
     */
    public static function getHash2Array($hash, $requires = array())
    {
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
    public function sfMultiply($num1, $num2)
    {
        return $num1 * $num2;
    }

    /**
     * 加算ポイントの計算
     *
     * ・店舗基本情報に基づいた計算は \Eccube\Helper\Application::alias('eccube.helper.db')->getAddPoint() を使用する
     *
     * @param  integer $totalpoint
     * @param  integer $use_point
     * @param  integer $point_rate
     * @return integer 加算ポイント
     */
    public static function getAddPoint($totalpoint, $use_point, $point_rate)
    {
        // 購入商品の合計ポイントから利用したポイントのポイント換算価値を引く方式
        $add_point = $totalpoint - intval($use_point * ($point_rate / 100));

        if ($add_point < 0) {
            $add_point = '0';
        }

        return $add_point;
    }

    /* 一意かつ予測されにくいID */
    public static function sfGetUniqRandomId($head = '')
    {
        // 予測されないようにランダム文字列を付与する。
        $random = GcUtils::gfMakePassword(8);
        // 同一ホスト内で一意なIDを生成
        $id = uniqid($head);

        return $id . $random;
    }

    /**
     * 二回以上繰り返されているスラッシュ[/]を一つに変換する。
     *
     * @param string $istr
     * @return string
     */
    public static function sfRmDupSlash($istr)
    {
        if (preg_match('|^http://|', $istr)) {
            $str = substr($istr, 7);
            $head = 'http://';
        } elseif (preg_match('|^https://|', $istr)) {
            $str = substr($istr, 8);
            $head = 'https://';
        } else {
            $str = $istr;
            $head = '';
        }
        $str = preg_replace('|[/]+|', '/', $str);
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
     * @param  string $filepath 変換するテキストファイルのパス
     * @param  string $enc_type 変換後のファイルエンコーディングの種類を表す文字列
     * @param  string $out_dir  変換後のファイルを出力するディレクトリを表す文字列
     * @return string 変換後のテキストファイルのパス
     */
    public static function sfEncodeFile($filepath, $enc_type, $out_dir)
    {
        $ifp = fopen($filepath, 'r');

        // 正常にファイルオープンした場合
        if ($ifp !== false) {
            $basename = basename($filepath);
            $outpath = $out_dir . 'enc_' . $basename;

            $ofp = fopen($outpath, 'w+');

            while (!feof($ifp)) {
                $line = fgets($ifp);
                $line = mb_convert_encoding($line, $enc_type, 'auto');
                fwrite($ofp, $line);
            }

            fclose($ofp);
            fclose($ifp);
        // ファイルが開けなかった場合はエラーページを表示
        } else {
            static::sfDispError('');
            exit;
        }

        return $outpath;
    }

    public function sfCutString($str, $len, $byte = true, $commadisp = true)
    {
        if ($byte) {
            if (strlen($str) > ($len + 2)) {
                $ret =substr($str, 0, $len);
                $cut = substr($str, $len);
            } else {
                $ret = $str;
                $commadisp = false;
            }
        } else {
            if (mb_strlen($str) > ($len + 1)) {
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

        if ($commadisp) {
            $ret = $ret . '...';
        }

        return $ret;
    }

    // 年、月、締め日から、先月の締め日+1、今月の締め日を求める。
    public static function sfTermMonth($year, $month, $close_day)
    {
        $end_year = $year;
        $end_month = $month;

        // 該当月の末日を求める。
        $end_last_day = date('d', mktime(0, 0, 0, $month + 1, 0, $year));

        // 月の末日が締め日より少ない場合
        if ($end_last_day < $close_day) {
            // 締め日を月末日に合わせる
            $end_day = $end_last_day;
        } else {
            $end_day = $close_day;
        }

        // 前月の取得
        $tmp_year = date('Y', mktime(0, 0, 0, $month, 0, $year));
        $tmp_month = date('m', mktime(0, 0, 0, $month, 0, $year));
        // 前月の末日を求める。
        $start_last_day = date('d', mktime(0, 0, 0, $month, 0, $year));

        // 前月の末日が締め日より少ない場合
        if ($start_last_day < $close_day) {
            // 月末日に合わせる
            $tmp_day = $start_last_day;
        } else {
            $tmp_day = $close_day;
        }

        // 先月の末日の翌日を取得する
        $start_year = date('Y', mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
        $start_month = date('m', mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));
        $start_day = date('d', mktime(0, 0, 0, $tmp_month, $tmp_day + 1, $tmp_year));

        // 日付の作成
        $start_date = sprintf('%d/%d/%d', $start_year, $start_month, $start_day);
        $end_date = sprintf('%d/%d/%d 23:59:59', $end_year, $end_month, $end_day);

        return array($start_date, $end_date);
    }

    // 再帰的に多段配列を検索して一次元配列(Hidden引渡し用配列)に変換する。
    public static function sfMakeHiddenArray($arrSrc, $arrDst = array(), $parent_key = '')
    {
        if (is_array($arrSrc)) {
            foreach ($arrSrc as $key => $val) {
                if ($parent_key != '') {
                    $keyname = $parent_key . '['. $key . ']';
                } else {
                    $keyname = $key;
                }
                if (is_array($val)) {
                    $arrDst = static::sfMakeHiddenArray($val, $arrDst, $keyname);
                } else {
                    $arrDst[$keyname] = $val;
                }
            }
        }

        return $arrDst;
    }

    // DB取得日時をタイムに変換
    public static function sfDBDatetoTime($db_date)
    {
        $date = preg_replace("|\..*$|", '', $db_date);
        $time = strtotime($date);

        return $time;
    }

    /**
     * PHPのmb_convert_encoding関数をSmartyでも使えるようにする
     *
     * XXX この関数を使っている箇所は、ほぼ設計誤りと思われる。変数にフェッチするか、出力時のエンコーディングで対応すべきと見受ける。
     */
    public function sfMbConvertEncoding($str, $encode = CHAR_CODE)
    {
        return mb_convert_encoding($str, $encode);
    }

    // 2つの配列を用いて連想配列を作成する
    public function sfArrCombine($arrKeys, $arrValues)
    {
        if (count($arrKeys) <= 0 and count($arrValues) <= 0) return array();

        $keys = array_values($arrKeys);
        $vals = array_values($arrValues);

        $max = max(count($keys), count($vals));
        $combine_ary = array();
        for ($i=0; $i<$max; $i++) {
            $combine_ary[$keys[$i]] = $vals[$i];
        }
        if (is_array($combine_ary)) return $combine_ary;
        return false;
    }

    /* 階層構造のテーブルから与えられたIDの直属の子を取得する */

    /**
     * @param string $pid_name
     * @param string $id_name
     */
    public static function sfGetUnderChildrenArray($arrData, $pid_name, $id_name, $parent)
    {
        $max = count($arrData);

        $arrChildren = array();
        // 子IDを検索する
        for ($i = 0; $i < $max; $i++) {
            if ($arrData[$i][$pid_name] == $parent) {
                $arrChildren[] = $arrData[$i][$id_name];
            }
        }

        return $arrChildren;
    }

    /**
     * SQLシングルクォート対応
     * @deprecated Query::quote() を使用すること
     */
    public static function sfQuoteSmart($in)
    {
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
    public static function sfMakeDir($path)
    {
        static $count = 0;
        $count++;  // 無限ループ回避
        $dir = dirname($path);
        if (preg_match("|^[/]$|", $dir) || preg_match("|^[A-Z]:[\\]$|", $dir) || $count > 256) {
            // ルートディレクトリで終了
            return;
        } else {
            if (is_writable(dirname($dir))) {
                if (!file_exists($dir)) {
                    mkdir($dir);
                    GcUtils::gfPrintLog("mkdir $dir");
                }
            } else {
                static::sfMakeDir($dir);
                if (is_writable(dirname($dir))) {
                    if (!file_exists($dir)) {
                        mkdir($dir);
                        GcUtils::gfPrintLog("mkdir $dir");
                    }
                }
            }
        }

        return;
    }

    // ディレクトリ以下のファイルを再帰的にコピー

    /**
     * @param string $src
     * @param string $des
     */
    public static function sfCopyDir($src, $des, $mess = '', $override = false)
    {
        if (!is_dir($src)) {
            return false;
        }

        $oldmask = umask(0);
        $mod= stat($src);

        // ディレクトリがなければ作成する
        if (!file_exists($des)) {
            if (!mkdir($des, $mod[2])) {
                echo 'path:' . $des;
            }
        }

        $fileArray=glob($src.'*');
        if (is_array($fileArray)) {
            foreach ($fileArray as $data_) {
                // CVS管理ファイルはコピーしない
                if (strpos($data_, '/CVS/Entries') !== false) {
                    break;
                }
                if (strpos($data_, '/CVS/Repository') !== false) {
                    break;
                }
                if (strpos($data_, '/CVS/Root') !== false) {
                    break;
                }

                $matches = array();
                mb_ereg("^(.*[\/])(.*)", $data_, $matches);
                $data=$matches[2];
                if (is_dir($data_)) {
                    $mess = static::sfCopyDir($data_.'/', $des.$data.'/', $mess);
                } else {
                    if (!$override && file_exists($des.$data)) {
                        $mess.= $des.$data . "：ファイルが存在します\n";
                    } else {
                        if (@copy($data_, $des.$data)) {
                            $mess.= $des.$data . "：コピー成功\n";
                        } else {
                            $mess.= $des.$data . "：コピー失敗\n";
                        }
                    }
                    $mod=stat($data_);
                }
            }
        }
        umask($oldmask);

        return $mess;
    }

    /**
     * ブラウザに強制的に送出する
     *
     * @param  boolean|string $output 半角スペース256文字+改行を出力するか。または、送信する文字列を指定。
     * @param int $sleep
     * @return void
     */
    public static function sfFlush($output = false, $sleep = 0)
    {
        // 出力をバッファリングしない(==日本語自動変換もしない)
        while (@ob_end_flush());

        if ($output === true) {
            // IEのために半角スペース256文字+改行を出力
            //echo str_repeat(' ', 256) . "\n";
            echo str_pad('', 256) . "\n";
        } elseif ($output !== false) {
            echo $output;
        }

        // 出力をフラッシュする
        flush();

        ob_start();

        // 時間のかかる処理
        sleep($sleep);
    }

    /**
     * @versionの記載があるファイルからバージョンを取得する。
     *
     * 使用されていない
     */
    public function sfGetFileVersion($path)
    {
        if (file_exists($path)) {
            $src_fp = fopen($path, 'rb');
            if ($src_fp) {
                while (!feof($src_fp)) {
                    $line = fgets($src_fp);
                    if (strpos($line, '@version') !== false) {
                        $arrLine = explode(' ', $line);
                        $version = $arrLine[5];
                    }
                }
                fclose($src_fp);
            }
        }

        return $version;
    }

    /**
     * $array の要素を $arrConvList で指定した方式で mb_convert_kana を適用する.
     *
     * 使用されていない
     *
     * @param  array $array       変換する文字列の配列
     * @param  array $arrConvList mb_convert_kana の適用ルール
     * @return array 変換後の配列
     * @see mb_convert_kana
     */
    public function mbConvertKanaWithArray($array, $arrConvList)
    {
        foreach ($arrConvList as $key => $val) {
            if (isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key], $val);
            }
        }

        return $array;
    }

    /**
     * 配列の添字が未定義の場合は空文字を代入して定義する.
     *
     * @param  array $array         添字をチェックする配列
     * @param  string[] $defineIndexes チェックする添字
     * @return array 添字を定義した配列
     */
    public static function arrayDefineIndexes($array, $defineIndexes)
    {
        foreach ($defineIndexes as $key) {
            if (!isset($array[$key])) $array[$key] = '';
        }

        return $array;
    }

    /**
     * $arrSrc のうち、キーが $arrKey に含まれるものを返す
     *
     * $arrSrc に含まない要素は返されない。
     *
     * @param  array $arrSrc
     * @param  array $arrKey
     * @return array
     */
    public static function sfArrayIntersectKeys($arrSrc, $arrKey)
    {
        $arrRet = array();
        foreach ($arrKey as $key) {
            if (isset($arrSrc[$key])) $arrRet[$key] = $arrSrc[$key];
        }

        return $arrRet;
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::printXMLDeclaration を使用すること
     */
    public function printXMLDeclaration()
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        GcUtils::printXMLDeclaration();
    }

    /**
     * 配列をテーブルタグで出力する。
     *
     * 使用されていない
     *
     * @param array $array
     * @return string
     */
    public function getTableTag($array)
    {
        $html = '<table>';
        $html.= '<tr>';
        foreach ($array[0] as $key => $val) {
            $html.="<th>$key</th>";
        }
        $html.= '</tr>';

        $cnt = count($array);

        for ($i = 0; $i < $cnt; $i++) {
            $html.= '<tr>';
            foreach ($array[$i] as $val) {
                $html.="<td>$val</td>";
            }
            $html.= '</tr>';
        }

        return $html;
    }

    /**
     * 指定の画像のパスを返す
     *
     * @param $filename
     * @return string $file 画像のパス、画像が存在しない場合、NO_IMAGE_REALFILEを返す
     */
    public static function getSaveImagePath($filename)
    {
        $file = NO_IMAGE_REALFILE;

        // ファイル名が与えられており、ファイルが存在する場合だけ、$fileを設定
        if (!static::isBlank($filename) && file_exists(IMAGE_SAVE_REALDIR . $filename)) {
            $file = IMAGE_SAVE_REALDIR . $filename;
        }

        return $file;
    }

    /**
     * 一覧-メイン画像のファイル指定がない場合、専用の画像ファイルに書き換える。
     *
     * @param string &$filename ファイル名
     * @return string
     */
    public function sfNoImageMainList($filename = '')
    {
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
    public static function sfNoImageMain($filename = '')
    {
        if (strlen($filename) == 0 || substr($filename, -1, 1) == '/') {
            $filename .= 'noimage_main.png';
        }

        return $filename;
    }

    /**
     * デバッグ用
     *
     * @param mixed $obj
     * @return void
     */
    public static function sfPrintR($obj)
    {
        echo '<div style="font-size: 12px;color: #00FF00;">' . "\n";
        echo '<strong>**デバッグ中**</strong><br />' . "\n";
        echo '<pre>' . "\n";
        var_dump($obj);
        echo '</pre>' . "\n";
        echo '<strong>**デバッグ中**</strong></div>' . "\n";
    }

    /**
     * ランダムな文字列を取得する
     *
     * @param  integer $length 文字数
     * @return string  ランダムな文字列
     */
    public static function sfGetRandomString($length = 1)
    {
        return \Text_Password::create($length);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::getUrl を使用すること
     */
    public function sfGetUrl()
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);

        return GcUtils::getUrl();
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::toStringBacktrace を使用すること
     */
    public function sfBacktraceToString($arrBacktrace)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);

        return GcUtils::toStringBacktrace($arrBacktrace);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::isAdminFunction を使用すること
     */
    public function sfIsAdminFunction()
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);

        return GcUtils::isAdminFunction();
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::isFrontFunction を使用すること
     */
    public function sfIsFrontFunction()
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);

        return GcUtils::isFrontFunction();
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GcUtils::isInstallFunction を使用すること
     */
    public function sfIsInstallFunction()
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);

        return GcUtils::isInstallFunction();
    }

    // 郵便番号から住所の取得

    /**
     * @param string $zipcode
     */
    public static function sfGetAddress($zipcode)
    {
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');

        $masterData = Application::alias('eccube.db.master_data');
        $arrPref = $masterData->getMasterData('mtb_pref');
        // インデックスと値を反転させる。
        $arrREV_PREF = array_flip($arrPref);

        // 郵便番号検索文作成
        $zipcode = mb_convert_kana($zipcode, 'n');
        $sqlse = 'SELECT state, city, town FROM mtb_zip WHERE zipcode = ?';

        $data_list = $objQuery->getAll($sqlse, array($zipcode));
        if (empty($data_list)) return array();

        // $zip_cntが1より大きければtownを消す
        //（複数行HITしているので、どれに該当するか不明の為）
        $zip_cnt = count($data_list);
        if ($zip_cnt > 1) {
            $data_list[0]['town'] = '';
        }
        unset($zip_cnt);

        /*
         * 総務省からダウンロードしたデータをそのままインポートすると
         * 以下のような文字列が入っているので 対策する。
         * ・（１・１９丁目）
         * ・以下に掲載がない場合
         * ・●●の次に番地が来る場合
         */
        $town =  $data_list[0]['town'];
        $town = preg_replace("/（.*）$/", '', $town);
        $town = preg_replace('/以下に掲載がない場合/', '', $town);
        $town = preg_replace('/(.*?)の次に番地がくる場合/', '', $town);
        $data_list[0]['town'] = $town;
        $data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

        return $data_list;
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 microtime(true) を使用する。
     */
    public function sfMicrotimeFloat()
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);

        return microtime(true);
    }

    /**
     * 変数が空白かどうかをチェックする.
     *
     * 引数 $val が空白かどうかをチェックする. 空白の場合は true.
     * 以下の文字は空白と判断する.
     * - ' ' (ASCII 32 (0x20)), 通常の空白
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
     * @param  mixed   $val    チェック対象の変数
     * @param  boolean $greedy '貧欲'にチェックを行う場合 true
     * @return boolean $val が空白と判断された場合 true
     */
    public static function isBlank($val, $greedy = true)
    {
        if (is_array($val)) {
            if ($greedy) {
                if (empty($val)) {
                    return true;
                }
                $array_result = true;
                foreach ($val as $in) {
                    /*
                     * Utils への再帰は無限ループやメモリリークの懸念
                     * 自クラスへ再帰する.
                     */
                    $array_result = static::isBlank($in, $greedy);
                    if (!$array_result) {
                        return false;
                    }
                }

                return $array_result;
            } else {
                return empty($val);
            }
        }

        if ($greedy) {
            $val = preg_replace('/　/', '', $val);
        }

        $val = trim($val);
        if (strlen($val) > 0) {
            return false;
        }

        return true;
    }

    /**
     * 指定されたURLのドメインが一致するかを返す
     *
     * 戻り値：一致(true) 不一致(false)
     *
     * @param  string  $url
     * @return boolean
     */
    public static function sfIsInternalDomain($url)
    {
        /* @var $netURL Net_URL */
        $netURL = Application::alias('pear.net.url', HTTP_URL);
        $host = $netURL->host;
        if (!$host) {
            return false;
        }
        $host = preg_quote($host, '#');
        if (!preg_match("#^(http|https)://{$host}#i", $url)) {
            return false;
        }

        return true;
    }

    /**
     * パスワードのハッシュ化
     *
     * @param  string $str  暗号化したい文言
     * @param  string $salt salt
     * @return string ハッシュ暗号化された文字列
     */
    public static function sfGetHashString($str, $salt)
    {
        if ($salt == '') {
            $salt = AUTH_MAGIC;
        }
        if (AUTH_TYPE == 'PLAIN') {
            $res = $str;
        } else {
            $res = hash_hmac(PASSWORD_HASH_ALGOS, $str . ':' . AUTH_MAGIC, $salt);
        }

        return $res;
    }

    /**
     * パスワード文字列のハッシュ一致判定
     *
     * @param  string  $pass     確認したいパスワード文字列
     * @param  string  $hashpass 確認したいパスワードハッシュ文字列
     * @param  string  $salt     salt
     * @return boolean 一致判定
     */
    public static function sfIsMatchHashPassword($pass, $hashpass, $salt)
    {
        $res = false;
        if ($hashpass != '') {
            if (AUTH_TYPE == 'PLAIN') {
                if ($pass === $hashpass) {
                    $res = true;
                }
            } else {
                if (empty($salt)) {
                    // 旧バージョン(2.11未満)からの移行を考慮
                    $hash = sha1($pass . ':' . AUTH_MAGIC);
                } else {
                    $hash = static::sfGetHashString($pass, $salt);
                }
                if ($hash === $hashpass) {
                    $res = true;
                }
            }
        }

        return $res;
    }

    /**
     * 検索結果の1ページあたりの最大表示件数を取得する
     *
     * フォームの入力値から最大表示件数を取得する
     * 取得できなかった場合は, 定数 SEARCH_PMAX の値を返す
     *
     * @param  string  $search_page_max 表示件数の選択値
     * @return integer 1ページあたりの最大表示件数
     */
    public static function sfGetSearchPageMax($search_page_max)
    {
        if (static::sfIsInt($search_page_max) && $search_page_max > 0) {
            $page_max = intval($search_page_max);
        } else {
            $page_max = SEARCH_PMAX;
        }

        return $page_max;
    }

    /**
     * 値を JSON 形式にして返す.
     *
     * この関数は, json_encode() 又は Services_JSON::encode() のラッパーです.
     * json_encode() 関数が使用可能な場合は json_encode() 関数を使用する.
     * 使用できない場合は, Services_JSON::encode() 関数を使用する.
     *
     * @param  mixed  $value JSON 形式にエンコードする値
     * @return string JSON 形式にした文字列
     * @see json_encode()
     * @see Services_JSON::encode()
     */
    public static function jsonEncode($value)
    {
        if (function_exists('json_encode')) {
            return json_encode($value);
        } else {
            GcUtils::gfPrintLog(' *use Services_JSON::encode(). faster than using the json_encode!');
            /* @var $objJson Services_JSON */
            $objJson = Application::alias('pear.services.json');

            return $objJson->encode($value);
        }
    }

    /**
     * JSON 文字列をデコードする.
     *
     * この関数は, json_decode() 又は Services_JSON::decode() のラッパーです.
     * json_decode() 関数が使用可能な場合は json_decode() 関数を使用する.
     * 使用できない場合は, Services_JSON::decode() 関数を使用する.
     *
     * @param  string $json JSON 形式にエンコードされた文字列
     * @return mixed  デコードされた PHP の型
     * @see json_decode()
     * @see Services_JSON::decode()
     */
    public static function jsonDecode($json)
    {
        if (function_exists('json_decode')) {
            return json_decode($json);
        } else {
            GcUtils::gfPrintLog(' *use Services_JSON::decode(). faster than using the json_decode!');
            /* @var $objJson Services_JSON */
            $objJson = Application::alias('pear.services.json');

            return $objJson->decode($json);
        }
    }

    /**
     * パスが絶対パスかどうかをチェックする.
     *
     * 引数のパスが絶対パスの場合は true を返す.
     * この関数は, パスの存在チェックを行なわないため注意すること.
     *
     * use File_Util::isAbsolute
     * http://pear.php.net/package/File_Util/
     *
     * @param string $path チェック対象のパス
     * @return boolean 絶対パスの場合 true
     */
    public static function isAbsoluteRealPath($path)
    {
        if (preg_match('/(?:\/|\\\)\.\.(?=\/|$)/', $path)) {
            return false;
        }
        if (DIRECTORY_SEPARATOR == '\\') {
            return (($path{0} == '/') ||  preg_match('/^[a-zA-Z]:(\\\|\/)/', $path));
        }
        return ($path{0} == '/') || ($path{0} == '~');
    }

    /**
     * ディレクトリを再帰的に作成する.
     *
     * @param  integer $mode     作成するディレクトリのパーミッション
     * @param string $path
     * @return boolean 作成に成功した場合 true; 失敗した場合 false
     * @see http://jp.php.net/mkdir
     */
    public static function recursiveMkdir($path, $mode = 0777)
    {
        // Windows環境ではmodeは効かない
        return mkdir($path, $mode, true);
    }

    public static function isAppInnerUrl($url)
    {
        $pattern = '/^(' . preg_quote(HTTP_URL, '/') . '|' . preg_quote(HTTPS_URL, '/') . ')/';

        return preg_match($pattern, $url) >= 1;
    }

    /**
     * PHP のタイムアウトを延長する
     *
     * ループの中で呼び出すことを意図している。
     * 暴走スレッドが残留する確率を軽減するため、set_time_limit(0) とはしていない。
     * @param  integer $seconds 最大実行時間を延長する秒数。
     * @return boolean 成功=true, 失敗=false
     */
    public static function extendTimeOut($seconds = null)
    {
        $safe_mode = (boolean) ini_get('safe_mode');
        if ($safe_mode) return false;

        if (is_null($seconds)) {
            $seconds
                = is_numeric(ini_get('max_execution_time'))
                ? intval(ini_get('max_execution_time'))
                : intval(get_cfg_var('max_execution_time'));
        }

        // タイムアウトをリセット
        set_time_limit($seconds);

        return true;
    }

    /**
     * コンパイルファイルを削除します.
     * @return void
     */
    public static function clearCompliedTemplate()
    {
        // コンパイルファイルの削除処理
        Application::alias('eccube.helper.file_manager')->deleteFile(COMPILE_REALDIR, false);
        Application::alias('eccube.helper.file_manager')->deleteFile(COMPILE_ADMIN_REALDIR, false);
        Application::alias('eccube.helper.file_manager')->deleteFile(SMARTPHONE_COMPILE_REALDIR, false);
        Application::alias('eccube.helper.file_manager')->deleteFile(MOBILE_COMPILE_REALDIR, false);
    }

    /**
     * 指定されたパスの配下を再帰的にコピーします.
     * @param string $source_path コピー元ディレクトリのパス
     * @param string $dest_path コピー先ディレクトリのパス
     * @return void
     */
    public static function copyDirectory($source_path, $dest_path)
    {
        $handle=opendir($source_path);
        while ($filename = readdir($handle)) {
            if ($filename === '.' || $filename === '..') continue;
            $cur_path = $source_path . $filename;
            $dest_file_path = $dest_path . $filename;
            if (is_dir($cur_path)) {
                // ディレクトリの場合
                // コピー先に無いディレクトリの場合、ディレクトリ作成.
                if (!empty($filename) && !file_exists($dest_file_path)) mkdir($dest_file_path);
                static::copyDirectory($cur_path . '/', $dest_file_path . '/');
            } else {
                if (file_exists($dest_file_path)) unlink($dest_file_path);
                copy($cur_path, $dest_file_path);
            }
        }
    }

    /**
     * 文字列を区切り文字を挟み反復する
     * @param  string $input      繰り返す文字列。
     * @param  string $multiplier input を繰り返す回数。
     * @param  string $separator  区切り文字
     * @return string
     */
    public static function repeatStrWithSeparator($input, $multiplier, $separator = ',')
    {
        return implode($separator, array_fill(0, $multiplier, $input));
    }

    /**
     * RFC3986に準拠したURIエンコード
     * MEMO: PHP5.3.0未満では、~のエンコードをしてしまうための処理
     *
     * @param  string $str 文字列
     * @return string RFC3986エンコード文字列
     */
    public static function encodeRFC3986($str)
    {
        return str_replace('%7E', '~', rawurlencode($str));
    }

    /**
     * マルチバイト対応の trim
     *
     * @param  string $str      入力文字列
     * @param  string $charlist 削除する文字を指定
     * @return string 変更後の文字列
     */
    public static function trim($str, $charlist = null)
    {
        $re = static::getTrimPregPattern($charlist);

        return preg_replace('/(^' . $re . ')|(' . $re . '$)/us', '', $str);
    }

    /**
     * マルチバイト対応の ltrim
     *
     * @param  string $str      入力文字列
     * @param  string $charlist 削除する文字を指定
     * @return string 変更後の文字列
     */
    public static function ltrim($str, $charlist = null)
    {
        $re = static::getTrimPregPattern($charlist);

        return preg_replace('/^' . $re . '/us', '', $str);
    }

    /**
     * マルチバイト対応の rtrim
     *
     * @param  string $str      入力文字列
     * @param  string $charlist 削除する文字を指定
     * @return string 変更後の文字列
     */
    public static function rtrim($str, $charlist = null)
    {
        $re = static::getTrimPregPattern($charlist);

        return preg_replace('/' . $re . '$/us', '', $str);
    }

    /**
     * 文字列のトリム処理で使用する PCRE のパターン
     *
     * @param  string $charlist 削除する文字を指定
     * @return string パターン
     */
    public static function getTrimPregPattern($charlist = null)
    {
        if (is_null($charlist)) {
            return '\s+';
        } else {
            return '[' . preg_quote($charlist, '/') . ']+';
        }
    }

    /**
     * データ量の単位を付与する
     *
     * @param  int    $data
     * @return string
     */
    public static function getUnitDataSize($data)
    {
        if ($data < 1000) {
            $return = $data . "KB";
        } elseif ($data < 1000000) {
            $return = $data/1000 . "MB";
        } else {
            $return = $data/1000000 . "GB";
        }

        return $return;
    }

    /**
     * カテゴリーツリー状の配列を作成.
     *
     * @param  string  $primary_key
     * @param  string  $glue_key
     * @param  integer $max_depth
     * @param  array   $correction
     * @param  integer $root_id
     * @return array   ツリーの配列
     */
    public static function buildTree($primary_key, $glue_key, $max_depth, $correction = array(), $root_id = 0)
    {
        $children = array();
        foreach ($correction as $child) {
            $children[$child[$glue_key]][] = $child;
        }
        $arrTree = $children[$root_id];
        foreach ($arrTree as &$child) {
            static::addChild($child, $primary_key, 1, $max_depth, $children);
        }

        return $arrTree;
    }

    /**
     * ツリーの親子をつなげるルーチン.
     *
     * @param  array   $target      親
     * @param  string  $primary_key 主キーの識別子
     * @param  integer $level       親の階層
     * @param  integer $max_depth   階層の深さの最大値
     * @param  array   $children    子の配列（キーが親ID）
     * @return void
     */
    public static function addChild(&$target, $primary_key, $level, $max_depth, &$children = array())
    {
        if (isset($children[$target[$primary_key]])) {
            $target['children'] = $children[$target[$primary_key]];
            if ($level + 1 < $max_depth) {
                foreach ($target['children'] as &$child) {
                    static::addChild($child, $primary_key, $level+1, $max_depth, $children);
                }
            }
        }
    }

    /**
     * 配列のキーをIDにした配列を作成.
     *
     * @param  string $ID_name    IDが格納されているキー名
     * @param  array  $correction 元の配列
     * @return array
     */
    public static function makeArrayIDToKey($ID_name, $correction = array())
    {
        $arrTmp = array();
        foreach ($correction as $item) {
            $arrTmp[$item[$ID_name]] = $item;
        }
        $return =& $arrTmp;
        unset($arrTmp);

        return $return;
    }

    /**
     * 階層情報が含まれている配列から親ID配列を取得する.
     *
     * @param  integer $start_id    取得起点
     * @param  string  $primary_key 主キー名
     * @param  string  $glue_key    親IDキー名
     * @param  array   $correction  階層構造が含まれている配列
     * @param  boolean $cid_is_key  キーがIDの配列の場合はtrue
     * @param  integer $root_id     ルートID
     * @param  boolean $id_only     IDだけの配列を返す場合はtrue
     * @return array   親ID配列
     */
    public static function getTreeTrail($start_id, $primary_key, $glue_key, $correction = array(), $cid_is_key = FALSE, $root_id = 0, $id_only = TRUE)
    {
        if ($cid_is_key) {
            $arrIDToKay = $correction;
        } else {
            $arrIDToKay = static::makeArrayIDToKey($primary_key, $correction);
        }
        $id = $start_id;
        $arrTrail = array();
        while ($id != $root_id && !static::isBlank($id)) {
            if ($id_only) {
                $arrTrail[] = $id;
            } else {
                $arrTrail[] = $arrIDToKay[$id];
            }
            if (isset($arrIDToKay[$id][$glue_key])) {
                $id = $arrIDToKay[$id][$glue_key];
            } else {
                $id = $root_id;
            }
        }

        return array_reverse($arrTrail);
    }

    /**
     * ベースとなるパスと相対パスを比較してファイルが存在する事をチェックする
     *
     * @param  string  $file
     * @param  string  $base_path
     * @return bool true = exists / false does not exist
     */
    public static function checkFileExistsWithInBasePath($file, $base_path)
    {
        $arrPath = explode('/', str_replace('\\', '/', $file));
        $arrBasePath = explode('/', str_replace('\\', '/', $base_path));
        $path_diff = implode("/", array_diff_assoc($arrPath, $arrBasePath));
        return file_exists(realpath(str_replace('..', '', $base_path . $path_diff))) ? true : false;
    }

    /**
     * マイクロ秒付きの時間文字列を取得する.
     *
     * @return string
     */
    public static function getFormattedDateWithMicroSecond()
    {
        $micro = explode(" ", microtime());
        $micro_string = explode('.', $micro[0]);
        return date('Y-m-d H:i:s') . "." . substr($micro_string[1], 0, 5);
    }
}
