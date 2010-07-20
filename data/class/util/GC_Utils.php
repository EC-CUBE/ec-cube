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
    function gfDownloadCsv($header, $contents){

        $fiest_name = date("YmdHis") .".csv";

        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename=${fiest_name}");
        Header("Content-type: application/octet-stream; name=${fiest_name}");

        $return = $header.$contents;
        if (mb_detect_encoding($return) == CHAR_CODE){						//文字コード変換
            $return = mb_convert_encoding($return,'SJIS',CHAR_CODE);
            $return = str_replace( array( "\r\n", "\r" ), "\n", $return);	// 改行方法の統一
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
    function gfSetCsv( $array, $arrayIndex = "" ){
        //引数$arrayIndexは、$arrayが連想配列のときに添え字を指定してやるために使用する

        $return = "";
        for ($i=0; $i<count($array); $i++){

            for ($j=0; $j<count($array[$i]); $j++ ){
                if ( $j > 0 ) $return .= ",";
                $return .= "\"";
                if ( $arrayIndex ){
                    $return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";
                } else {
                    $return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
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
    function gfGetAge($dbdate)
    {
        $ty = date("Y");
        $tm = date("m");
        $td = date("d");
        list($by, $bm, $bd) = split("[-/ ]", $dbdate);
        $age = $ty - $by;
        if($tm * 100 + $td < $bm * 100 + $bd) $age--;
        return $age;
    }

    /*----------------------------------------------------------------------
     * [名称] gfDebugLog
     * [概要] ログファイルに変数の詳細を出力する。
     * [引数] 対象となる変数
     * [戻値] なし
     * [依存] gfPrintLog
     * [注釈] -
     *----------------------------------------------------------------------*/
    function gfDebugLog($obj){
        if(DEBUG_MODE === true) {
            GC_Utils::gfPrintLog("*** start Debug ***");
            ob_start();
            print_r($obj);
            $buffer = ob_get_contents();
            ob_end_clean();
            $fp = fopen(LOG_PATH, "a+");
            fwrite( $fp, $buffer."\n" );
            fclose( $fp );
            GC_Utils::gfPrintLog("*** end Debug ***");
            // ログテーション
            GC_Utils::gfLogRotation(MAX_LOG_QUANTITY, MAX_LOG_SIZE, LOG_PATH);
		}
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
        $today = date("Y/m/d H:i:s");
        // 出力パスの作成
        if ($path == "") {
            $path = LOG_PATH;
        }

        // エスケープされている文字をもとに戻す
        $trans_tbl = get_html_translation_table (HTML_ENTITIES);
        $trans_tbl = array_flip ($trans_tbl);
        $mess = strtr($mess, $trans_tbl);

        $fp = fopen($path, "a+");
        if($fp) {
            fwrite( $fp, $today." [".$_SERVER['PHP_SELF']."] ".$mess." from ". $_SERVER['REMOTE_ADDR']. "\n" );
            fclose( $fp );
        }

        // ログテーション
        GC_Utils::gfLogRotation(MAX_LOG_QUANTITY, MAX_LOG_SIZE, $path);
    }

    /**
     * ログローテーション機能
     * XXX この類のローテーションは通常 0 開始だが、本実装は 1 開始である。
     * @param integer $max_log 最大ファイル数
     * @param integer $max_size 最大サイズ
     * @param string  $path ファイルパス
     * @return void なし
     */
    function gfLogRotation($max_log, $max_size, $path) {

        // ファイルが最大サイズを超えていない場合、終了
        if (filesize($path) <= $max_size) return;

        // アーカイブのインクリメント(削除を兼ねる)
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
        $character = "abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679";
        $pw = preg_split("//", $character, 0, PREG_SPLIT_NO_EMPTY);

        $password = "";
        for($i = 0; $i<$pwLength; $i++ ) {
            $password .= $pw[array_rand($pw, 1)];
        }

        return $password;
    }

    /*----------------------------------------------------------------------
     * [名称] sf_explodeExt
     * [概要] ファイルの拡張子取得
     * [引数] ファイル名
     * [戻値] 拡張子
     * [依存] なし
     * [注釈] -
     *----------------------------------------------------------------------*/
    function gf_explodeExt($fileName) {
        $ext1 = explode(".", $fileName);
        $ext2 = $ext1[count($ext1) - 1];
        $ext2 = strtolower($ext2);
        return $ext2;
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
        $addrs = explode(",", $str); //アドレスを配列に入れる
        foreach ($addrs as $addr) {
            if (preg_match("/^(.+)<(.+)>$/", $addr, $matches)) {
                //引数が「名前<メールアドレス>」の場合
                $mailaddrs[] = mb_encode_mimeheader(trim($matches[1]))." <".trim($matches[2]).">";
            } else {
                //メールアドレスのみの場合
                $mailaddrs[] =  trim($addr);
            }
        }
        return implode(", ", $mailaddrs); //複数アドレスはカンマ区切りにする
    }
}
?>
