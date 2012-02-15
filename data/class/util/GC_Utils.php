<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
 * @package Util
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class GC_Utils {

    /*----------------------------------------------------------------------
     * [名称] gfDownloadCsv
     * [概要] 引数データをCSVとして、クライアントにダウンロードさせる
     * [引数] 1:ヘッダ文字列 2:CSVデータ
     * [戻値] -
     * [依存] -
     * [注釈] 引数は１，２ともカンマ区切りになっていること
     *----------------------------------------------------------------------*/
    function gfDownloadCsv($header, $contents) {

        $fiest_name = date('YmdHis') .'.csv';

        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename=${fiest_name}");
        Header("Content-type: application/octet-stream; name=${fiest_name}");

        $return = $header.$contents;
        if (mb_detect_encoding($return) == CHAR_CODE) {
            // 文字コード変換
            $return = mb_convert_encoding($return,'SJIS',CHAR_CODE);
            // 改行方法の統一
            $return = str_replace( array("\r\n", "\r"), "\n", $return);
        }
        echo $return;
    }

    /*----------------------------------------------------------------------
     * [名称] gfSetCsv
     * [概要] 引数の配列をCSV形式に変換する
     * [引数] 1:CSVにする配列 2:引数1が連想配列時の添え字を指定した配列
     * [戻値] CSVデータ
     * [依存] -
     * [注釈] -
     *----------------------------------------------------------------------*/
    function gfSetCsv($array, $arrayIndex = '') {
        //引数$arrayIndexは、$arrayが連想配列のときに添え字を指定してやるために使用する

        $return = '';
        for ($i=0; $i<count($array); $i++) {

            for ($j=0; $j<count($array[$i]); $j++) {
                if ($j > 0) $return .= ',';
                $return .= "\"";
                if ($arrayIndex) {
                    $return .= mb_ereg_replace('<','＜',mb_ereg_replace("\"","\"\"",$array[$i][$arrayIndex[$j]])) ."\"";
                } else {
                    $return .= mb_ereg_replace('<','＜',mb_ereg_replace("\"","\"\"",$array[$i][$j])) ."\"";
                }
            }
            $return .= "\n";
        }

        return $return;
    }

    /*----------------------------------------------------------------------
     * [名称] gfGetAge
     * [概要] 日付より年齢を計算する。
     * [引数] 1:日付文字列(yyyy/mm/dd、yyyy-mm-dd hh:mm:ss等)
     * [戻値] 年齢の数値
     * [依存] -
     * [注釈] -
     *----------------------------------------------------------------------*/
    function gfGetAge($dbdate) {
        $ty = date('Y');
        $tm = date('m');
        $td = date('d');
        list($by, $bm, $bd) = preg_split('/[-/ ]/', $dbdate);
        $age = $ty - $by;
        if($tm * 100 + $td < $bm * 100 + $bd) $age--;
        return $age;
    }

    /**
     * ログファイルに変数の詳細を出力
     *
     * @param mixed $obj
     * @return void
     */
    function gfDebugLog($obj) {
        if (DEBUG_MODE === true) {
            GC_Utils_Ex::gfPrintLog(
                "*** start Debug ***\n" .
                print_r($obj, true) .
                '*** end Debug ***'
            );
        }
    }

    /**
     * 呼び出し元関数名を返します
     *
     * @param int $forLogInfo ログ出力用に利用するかどうか(1:ログ出力用に利用する)
     * @return string 呼び出し元クラス、関数名、行数の文字列表現
     */
    function gfGetCallerInfo($forLogInfo=true) {
        // バックトレースを取得する
        $traces = debug_backtrace(false);
        $bklv = 1;
        if ($forLogInfo === true) {
            $bklv = 3;
            if( ($traces[3]['class'] === 'LC_Page'
                || $traces[3]['class'] === 'LC_Page_Admin')
                && $traces[3]['function'] === 'log')
            {
                $bklv = 4;
            }
        }
        $str = $traces[$bklv]['class'] . '::' . $traces[$bklv]['function'] . '(' . $traces[$bklv-1]['line'] . ') ';
        return $str;
    }

    /**
     * ログメッセージに、呼び出し元関数名等の情報を付加して返します
     *
     * @param string $mess ログメッセージ
     * @param string $log_level ログレベル('Info' or 'Debug')
     * @return string ログメッセージに呼び出し元関数名等の情報を付加した文字列
     */
    function gfGetLogStr($mess, $log_level='Info') {
        // メッセージの前に、ログ出力元関数名とログ出力関数呼び出し部分の行数を付与
        $mess = GC_Utils::gfGetCallerInfo(true) . $mess;

        // ログレベル=Debugの場合は、[Debug]を先頭に付与する
        if ($log_level === 'Debug') {
            $mess = '[Debug]' . $mess;
        }

        return $mess;
    }

    /**
     * 管理画面用ログ出力
     *
     * 管理画面用ログ出力を行ないます
     * @param string $mess ログメッセージ
     * @param string $log_level ログレベル('Info' or 'Debug')
     * @return void
     */
    function gfAdminLog($mess, $log_level='Info') {
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug'&& DEBUG_MODE === false) {
            return;
        }

        // ログメッセージに、呼び出し元関数名等の情報を付加する
        $mess = GC_Utils::gfGetLogStr($mess, $log_level);

        // ログ出力
        // ※現在は管理画面用・フロント用のログ出力とも、同じファイル(site.log)に出力します。
        // 　分けたい場合は、以下の関数呼び出しの第２引数にファイルパスを指定してください
        GC_Utils_Ex::gfPrintLog($mess);
    }

    /**
     * フロント用ログ出力
     *
     * フロント用ログ出力を行ないます
     * @param string $mess ログメッセージ
     * @param string $log_level ログレベル('Info' or 'Debug')
     * @return void
     */
    function gfFrontLog($mess, $log_level='Info') {
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug'&& DEBUG_MODE === false) {
            return;
        }

        // ログメッセージに、呼び出し元関数名等の情報を付加する
        $mess = GC_Utils::gfGetLogStr($mess, $log_level);

        // ログ出力
        // ※現在は管理画面用・フロント用のログ出力とも、同じファイル(site.log)に出力します。
        // 　分けたい場合は、以下の関数呼び出しの第２引数にファイルパスを指定してください
        GC_Utils_Ex::gfPrintLog($mess);
    }

    /*----------------------------------------------------------------------
     * [名称] gfPrintLog
     * [概要] ログファイルに日時、処理ファイル名、メッセージを出力
     * [引数] 表示したいメッセージ
     * [戻値] なし
     * [依存] なし
     * [注釈] -
     *----------------------------------------------------------------------*/
    function gfPrintLog($mess, $path = '') {
        // 日付の取得
        $today = date('Y/m/d H:i:s');
        // 出力パスの作成
        if ($path == '') {
            $path = LOG_REALFILE;
        }

        // エスケープされている文字をもとに戻す
        $trans_tbl = get_html_translation_table (HTML_ENTITIES);
        $trans_tbl = array_flip ($trans_tbl);
        $mess = strtr($mess, $trans_tbl);

        $fp = fopen($path, 'a+');
        if ($fp) {
            $string = "$today [{$_SERVER['PHP_SELF']}] $mess from {$_SERVER['REMOTE_ADDR']}\n";
            fwrite($fp, $string);
            fclose($fp);
        }

        // ログテーション
        GC_Utils_Ex::gfLogRotation(MAX_LOG_QUANTITY, MAX_LOG_SIZE, $path);
    }

    /**
     * ログローテーション機能
     *
     * XXX この類のローテーションは通常 0 開始だが、本実装は 1 開始である。
     * この中でログ出力は行なわないこと。(無限ループの懸念あり)
     * @param integer $max_log 最大ファイル数
     * @param integer $max_size 最大サイズ
     * @param string  $path ファイルパス
     * @return void
     */
    function gfLogRotation($max_log, $max_size, $path) {

        // ファイルが存在しない場合、終了
        if (!file_exists($path)) return;

        // ファイルが最大サイズを超えていない場合、終了
        if (filesize($path) <= $max_size) return;

        // Windows 版 PHP への対策として明示的に事前削除
        $path_max = "$path.$max_log";
        if (file_exists($path_max)) {
            $res = unlink($path_max);
            // 削除に失敗時した場合、ログローテーションは見送り
            if (!$res) return;
        }

        // アーカイブのインクリメント
        for ($i = $max_log; $i >= 2; $i--) {
            $path_old = "$path." . ($i - 1);
            $path_new = "$path.$i";
            if (file_exists($path_old)) {
                rename($path_old, $path_new);
            }
        }

        // 現在ファイルのアーカイブ
        rename($path, "$path.1");
    }

    /*----------------------------------------------------------------------
     * [名称] gfMakePassword
     * [概要] ランダムパスワード生成（英数字）
     * [引数] パスワードの桁数
     * [戻値] ランダム生成されたパスワード
     * [依存] なし
     * [注釈] -
     *----------------------------------------------------------------------*/
    function gfMakePassword($pwLength) {

        // 乱数表のシードを決定
        srand((double)microtime() * 54234853);

        // パスワード文字列の配列を作成
        $character = 'abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679';
        $pw = preg_split('//', $character, 0, PREG_SPLIT_NO_EMPTY);

        $password = '';
        for ($i = 0; $i<$pwLength; $i++) {
            $password .= $pw[array_rand($pw, 1)];
        }

        return $password;
    }

    /*----------------------------------------------------------------------------------------------------------------------
     * [名称] gfMailHeaderAddr
     * [概要] 入力されたメールアドレスをメール関数用の宛先に変換
     * [引数] 「メールアドレス」または「名前<メールアドレス>」、複数アドレス指定時はカンマ区切りで指定する。
     * [戻値] 「メールアドレス」または「JIS_MIMEにコード変換した名前 <メールアドレス>」、複数アドレス指定時はカンマ区切りで返却する。
     * [依存] なし
     * [注釈] -
     *----------------------------------------------------------------------------------------------------------------------*/

    function gfMailHeaderAddr($str) {
        $addrs = explode(',', $str); //アドレスを配列に入れる
        foreach ($addrs as $addr) {
            if (preg_match("/^(.+)<(.+)>$/", $addr, $matches)) {
                //引数が「名前<メールアドレス>」の場合
                $mailaddrs[] = mb_encode_mimeheader(trim($matches[1])).' <'.trim($matches[2]).'>';
            } else {
                //メールアドレスのみの場合
                $mailaddrs[] =  trim($addr);
            }
        }
        return implode(', ', $mailaddrs); //複数アドレスはカンマ区切りにする
    }
}
