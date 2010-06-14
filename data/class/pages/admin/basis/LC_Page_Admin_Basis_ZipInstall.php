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

/** CSV ファイルの最大行数 */
define("ZIP_CSV_LINE_MAX", 8192);

/** 画像の表示個数 */
define("IMAGE_MAX", 680);

/** 郵便番号CSV ファイルのパス */
define("ZIP_CSV_FILE_PATH", DATA_PATH . "downloads/KEN_ALL.CSV");

/**
 * 郵便番号DB登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Basis_ZipInstall.php 16741 2007-11-08 00:43:24Z adachi $
 */
class LC_Page_Admin_Basis_ZipInstall extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objQuery = new SC_Query();
        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $fp = fopen(ZIP_CSV_FILE_PATH, "r");
        $img_path = USER_URL . "packages/" . TEMPLATE_NAME . "/img/";

        // 一部のIEは256バイト以上受け取ってから表示を開始する。
        for($i = 0; $i < 256; $i++) {
            print(" ");
        }
        print("\n");
        $this->myFlush();

        if(!$fp) {
            SC_Utils_Ex::sfErrorHeader(">> " . ZIP_CSV_FILE_PATH . "の取得に失敗しました。");
        } else {
            print("<img src='". $img_path . "install/main_w.jpg'><br>");
            $this->myFlush();

            // CSVの件数を数える
            $line = 0;
            while(!feof($fp)) {
                fgets($fp, ZIP_CSV_LINE_MAX);
                $line++;
            }

            print("<img src='". $img_path ."install/space_w.gif'>");
            $this->myFlush();

            // ファイルポインタを戻す
            fseek($fp, 0);

            // 画像を一個表示する件数を求める。
            $disp_line = intval($line / IMAGE_MAX);

            // 既に書き込まれたデータを数える
            $end_cnt = $objQuery->count("mtb_zip");
            $cnt = 1;
            $img_cnt = 0;
            while (!feof($fp)) {
                $arrCSV = fgetcsv($fp, ZIP_CSV_LINE_MAX);

                // すでに書き込まれたデータを飛ばす。
                if($cnt > $end_cnt) {
                    $sqlval['code'] = $arrCSV[0];
                    $sqlval['old_zipcode'] = $arrCSV[1];
                    $sqlval['zipcode'] = $arrCSV[2];
                    $sqlval['state_kana'] = $arrCSV[3];
                    $sqlval['city_kana'] = $arrCSV[4];
                    $sqlval['town_kana'] = $arrCSV[5];
                    $sqlval['state'] = $arrCSV[6];
                    $sqlval['city'] = $arrCSV[7];
                    $sqlval['town'] = $arrCSV[8];
                    $sqlval['flg1'] = $arrCSV[9];
                    $sqlval['flg2'] = $arrCSV[10];
                    $sqlval['flg3'] = $arrCSV[11];
                    $sqlval['flg4'] = $arrCSV[12];
                    $sqlval['flg5'] = $arrCSV[13];
                    $sqlval['flg6'] = $arrCSV[14];
                    $objQuery->insert("mtb_zip", $sqlval);
                }

                $cnt++;
                // $disp_line件ごとに進捗表示する
                if($cnt % $disp_line == 0 && $img_cnt < IMAGE_MAX) {
                    print("<img src='". $img_path ."install/graph_1_w.gif'>");
                    $this->myFlush();
                    $img_cnt++;
                }
            }
            fclose($fp);

            print("<img src='". $img_path ."install/space_w.gif'><br>\n");
            print("<table width='700' height='50' border='0' cellpadding='0' cellspacing='0' bgcolor='#494E5F'>\n");
            print("<tr>\n");
            print("<td align='center'><a href='javascript:window.close()'><img src='". $img_path ."install/close.gif' alt='CLOSE' width='85' height='22' border='0' /></a></td>\n");
            print("</tr>\n");
            print("</table>\n");
        }
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
     * 出力バッファをフラッシュし, バッファリングを開始する.
     *
     * @return void
     */
    function myFlush() {
        flush();
        ob_end_flush();
        ob_start();
    }
}
?>
