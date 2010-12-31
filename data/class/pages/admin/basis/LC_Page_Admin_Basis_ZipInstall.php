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
require_once(CLASS_FILE_PATH . "pages/admin/LC_Page_Admin.php");

/** CSV ファイルの最大行数 */
define("ZIP_CSV_LINE_MAX", 8192);

/** 画像の表示数量 */
define("IMAGE_MAX", 680);

/** 郵便番号CSV ファイルのパス */
define("ZIP_CSV_FILE_PATH", DATA_FILE_PATH . "downloads/KEN_ALL.CSV");

/** UTF-8 変換済みの郵便番号CSV ファイルのパス */
define("ZIP_CSV_UTF8_FILE_PATH", DATA_FILE_PATH . "downloads/KEN_ALL_utf-8.CSV");

/**
 * 郵便番号DB登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Basis_ZipInstall.php 16741 2007-11-08 00:43:24Z adachi $
 */
class LC_Page_Admin_Basis_ZipInstall extends LC_Page_Admin {

    /** CSVの行数 */
    var $tpl_line = 0;
    var $tpl_mode;
    var $exec;
    var $tpl_count_mtb_zip;
    /** フォームパラメータの配列 */
    var $objFormParam;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/zip_install.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'zip_install';
        $this->tpl_subtitle = '郵便番号DB登録';
        $this->tpl_mainno = 'basis';

        $this->tpl_mode = $_GET['mode'];
        $this->exec = (boolean)$_GET['exec'];
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objQuery = new SC_Query();

        SC_Utils_Ex::sfIsSuccess(new SC_Session);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_GET);
        $this->arrErr = $this->objFormParam->checkError();
        $this->arrForm = $this->objFormParam->getHashArray();

        if ($this->exec) {
            if (!empty($this->arrErr)) {
                SC_Utils_Ex::sfDispException();
            }
            switch ($this->tpl_mode) {
                // 自動登録
                case 'auto':
                    $objQuery->begin();
                    $objQuery->delete('mtb_zip');
                    $this->insertMtbZip();
                    $objQuery->commit();
                    break;
                // 手動登録
                case 'manual':
                    $this->insertMtbZip($this->arrForm['startRowNum']);
                    break;
            }
            exit;
        }

        switch ($this->tpl_mode) {
            // 手動削除
            case 'delete':
                $objQuery->delete('mtb_zip');

                // 進捗・完了画面を表示しない
                $this->tpl_mode = null;

                break;
        }

        $this->tpl_line = $this->countZipCsv();
        $this->tpl_count_mtb_zip = $this->countMtbZip();
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
     * パラメータ情報の初期化
     *
     * @return void
     */
    function lfInitParam() {
        if ($this->tpl_mode == 'manual') {
            $this->objFormParam->addParam("開始行", "startRowNum", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }
    }

    /**
     * DB登録
     *
     * @return void
     */
    function insertMtbZip($start = 1) {
        $objQuery = new SC_Query();
        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $img_path = USER_URL . USER_PACKAGE_DIR . DEFAULT_TEMPLATE_NAME . "/img/";

        ?>
        <html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHAR_CODE ?>" />
        </head>
        <body>
        <p>進捗状況</p>
        <div style="background-color: #494E5F;">
        <?php
        // 一部のIEは256バイト以上受け取ってから表示を開始する。
        SC_Utils_Ex::sfFlush(true);

        echo "<img src='" . $img_path . ADMIN_DIR . "basis/zip_install_progress.gif'><br />";
        echo "<img src='" . $img_path . "install/space_w.gif'>";
        SC_Utils_Ex::sfFlush();

        // 画像を一個表示する件数を求める。
        $line_all = $this->countZipCsv();
        $disp_line = intval($line_all / IMAGE_MAX);

        /** 現在行(CSV形式。空行は除く。) */
        $cntCurrentLine = 0;
        /** 挿入した行数 */
        $cntInsert = 0;
        $img_cnt = 0;
        $safe_mode = (boolean)ini_get('safe_mode');
        $max_execution_time
            = is_numeric(ini_get('max_execution_time'))
            ? intval(ini_get('max_execution_time'))
            : intval(get_cfg_var('max_execution_time'))
        ;

        $fp = $this->openZipCsv();
        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, ZIP_CSV_LINE_MAX);
            if (empty($arrCSV)) continue;
            $cntCurrentLine++;
            if ($cntCurrentLine >= $start) {
                $sqlval = array();
                // $sqlval['code'] = $arrCSV[0];
                // $sqlval['old_zipcode'] = $arrCSV[1];
                $sqlval['zipcode'] = $arrCSV[2];
                // $sqlval['state_kana'] = $arrCSV[3];
                // $sqlval['city_kana'] = $arrCSV[4];
                // $sqlval['town_kana'] = $arrCSV[5];
                $sqlval['state'] = $arrCSV[6];
                $sqlval['city'] = $arrCSV[7];
                $sqlval['town'] = $arrCSV[8];
                // $sqlval['flg1'] = $arrCSV[9];
                // $sqlval['flg2'] = $arrCSV[10];
                // $sqlval['flg3'] = $arrCSV[11];
                // $sqlval['flg4'] = $arrCSV[12];
                // $sqlval['flg5'] = $arrCSV[13];
                // $sqlval['flg6'] = $arrCSV[14];
                $objQuery->insert("mtb_zip", $sqlval);
                $cntInsert++;
            }

            // $disp_line件ごとに進捗表示する
            if($cntCurrentLine % $disp_line == 0 && $img_cnt < IMAGE_MAX) {
                print("<img src='". $img_path ."install/graph_1_w.gif'>");
                SC_Utils_Ex::sfFlush();
                $img_cnt++;
            }
            // 暴走スレッドが残留する確率を軽減したタイムアウト防止のロジック
            // TODO 動作が安定していれば、SC_Utils 辺りに移動したい。
            if (!$safe_mode) {
                // タイムアウトをリセット
                set_time_limit($max_execution_time);
            }
        }
        fclose($fp);

        echo "<img src='". $img_path ."install/space_w.gif'>";
        echo "</div>\n";
        
        ?>
        <script type="text/javascript" language="javascript">
            <!--
                // 完了画面
                function complete() {
                    document.open("text/html","replace");
                    document.clear();
                    document.write("<p>完了しました。<br />");
                    document.write("<?php echo $cntInsert ?> 件を追加しました。</p>");
                    document.write("<p><a href='?' target='_top'>戻る</a></p>");
                    document.close();
                }
                // コンテンツを削除するため、タイムアウトで呼び出し。
                setTimeout("complete()", 0);
            // -->
        </script>
        </body>
        </html>
        <?php
    }

    function openZipCsv() {
        // http://www.post.japanpost.jp/zipcode/dl/kogaki/lzh/ken_all.lzh
        $this->convertZipCsv();
        $fp = fopen(ZIP_CSV_UTF8_FILE_PATH, "r");
        if (!$fp) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_UTF8_FILE_PATH . ' の読み込みに失敗しました。');
        }
        return $fp;
    }

    function convertZipCsv() {
        if (file_exists(ZIP_CSV_UTF8_FILE_PATH)) return;

        $fpr = fopen(ZIP_CSV_FILE_PATH, "r");
        if (!$fpr) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_FILE_PATH . ' の読み込みに失敗しました。');
        }

        $fpw = fopen(ZIP_CSV_UTF8_FILE_PATH, "w");
        if (!$fpw) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_UTF8_FILE_PATH . ' を開けません。');
        }

        while (!feof($fpr)) {
            fwrite($fpw, mb_convert_encoding(fgets($fpr, ZIP_CSV_LINE_MAX), CHAR_CODE, 'sjis-win'));
        }

        fclose($fpw);
        fclose($fpr);
    }

    function countMtbZip() {
        $objQuery = new SC_Query();
        return $objQuery->count('mtb_zip');
    }

    function countZipCsv() {
        $line = 0;
        $fp = $this->openZipCsv();

        // CSVの行数を数える
        while (!feof($fp)) {
            /*
            // 正確にカウントする
            $tmp = fgetcsv($fp, ZIP_CSV_LINE_MAX);
            */
            // 推測でカウントする
            $tmp = fgets($fp, ZIP_CSV_LINE_MAX);
            if (empty($tmp)) continue;
            $line++;
        }
        fclose($fp);

        return $line;
    }
}
?>
