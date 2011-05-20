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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once DATA_REALDIR . 'module/Request.php';

/** CSV ファイルの最大行数 */
define("ZIP_CSV_LINE_MAX", 8192);

/** 画像の表示数量 */
define("IMAGE_MAX", 680);

/** 郵便番号CSV ファイルのパス */
define("ZIP_CSV_REALFILE", DATA_REALDIR . "downloads/KEN_ALL.CSV");

/** UTF-8 変換済みの郵便番号CSV ファイルのパス */
define("ZIP_CSV_UTF8_REALFILE", DATA_REALDIR . "downloads/KEN_ALL_utf-8.CSV");

/**
 * 郵便番号DB登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Basis_ZipInstall.php 16741 2007-11-08 00:43:24Z adachi $
 */
class LC_Page_Admin_Basis_ZipInstall extends LC_Page_Admin_Ex {

    /** CSVの行数 */
    var $tpl_line = 0;
    var $tpl_mode;
    var $exec;
    var $tpl_count_mtb_zip;

    /** CSV の更新日時 */
    var $tpl_csv_datetime;

    /** ZIP アーカイブファイルの取得元 */
    var $zip_download_url = 'http://www.post.japanpost.jp/zipcode/dl/kogaki/zip/ken_all.zip';

    /** 日本郵便から取得した ZIP アーカイブファイルの保管パス */
    var $zip_csv_temp_realfile;

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
        $this->tpl_subno = 'zip_install';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '郵便番号DB登録';
        $this->tpl_mainno = 'basis';

        $this->tpl_mode = $this->getMode();
        $this->exec = (boolean)$_GET['exec'];
        $this->zip_csv_temp_realfile = DATA_REALDIR . 'downloads/tmp/ken_all.zip';
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
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメータ情報の初期化
        $this->lfInitParam($this->tpl_mode, $objFormParam);
        $objFormParam->setParam($_GET);
        $this->arrErr = $objFormParam->checkError();
        $this->arrForm = $objFormParam->getHashArray();

        if ($this->exec) {
            if (!empty($this->arrErr)) {
                SC_Utils_Ex::sfDispException();
            }
            switch ($this->tpl_mode) {
                // 自動登録
                case 'auto':
                    $this->lfAutoCommitZip();
                    break;
                // DB手動登録
                case 'manual':
                    $this->insertMtbZip($this->arrForm['startRowNum']);
                    break;
            }
            exit;
        }

        switch ($this->tpl_mode) {
            // 削除
            case 'delete':
                $this->lfDeleteZip();

                // 進捗・完了画面を表示しない
                $this->tpl_mode = null;
                break;

            // 郵便番号CSV更新
            case 'update_csv';
                $this->lfDownloadZipFileFromJp();
                $this->lfExtractZipFile();

                // 進捗・完了画面を表示しない
                $this->tpl_mode = null;
                break;
        }

        $this->tpl_line = $this->countZipCsv();
        $this->tpl_count_mtb_zip = $this->countMtbZip();
        $this->tpl_csv_datetime = $this->lfGetCsvDatetime();
        // XXX PHP4 を切捨てたら、ダウンロードの必要性チェックなども行いたい
        // $arrHeader = get_headers($this->zip_download_url, 1);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfAutoCommitZip() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $this->lfDownloadZipFileFromJp();
        $this->lfExtractZipFile();

        $objQuery->begin();
        $this->lfDeleteZip();
        $this->insertMtbZip();
        $objQuery->commit();
    }

    /**
     * テーブルデータと UTF-8 変換済みの郵便番号 CSV を削除
     *
     * @return void
     */
    function lfDeleteZip() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // DB
        $objQuery->delete('mtb_zip');

        // UTF-8 変換済みの郵便番号 CSV
        unlink(ZIP_CSV_UTF8_REALFILE);
    }

    /**
     * パラメータ情報の初期化
     *
     * @return void
     */
    function lfInitParam($tpl_mode, &$objFormParam) {
        if ($tpl_mode == 'manual') {
            $objFormParam->addParam("開始行", 'startRowNum', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }
    }

    /**
     * DB登録
     *
     * @return void
     */
    function insertMtbZip($start = 1) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $img_path = USER_URL . USER_PACKAGE_DIR . "admin/img/basis/"; // 画像パスは admin 固定

        ?>
        <html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHAR_CODE ?>" />
        </head>
        <body>
        <p>DB 登録進捗状況</p>
        <div style="background-color: #494E5F;">
        <?php
        // 一部のIEは256バイト以上受け取ってから表示を開始する。
        SC_Utils_Ex::sfFlush(true);

        echo '<img src="' . $img_path . 'zip_install_progress.gif"><br />';
        echo '<img src="' . $img_path . 'space_w.gif">';
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
                echo '<img src="' . $img_path . 'graph_1_w.gif">';
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

        echo '<img src="' . $img_path . 'space_w.gif">';
        echo '</div>' . "\n";

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
        $this->convertZipCsv();
        $fp = fopen(ZIP_CSV_UTF8_REALFILE, 'r');
        if (!$fp) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_UTF8_REALFILE . ' の読み込みに失敗しました。');
        }
        return $fp;
    }

    function convertZipCsv() {
        if (file_exists(ZIP_CSV_UTF8_REALFILE)) return;

        $fpr = fopen(ZIP_CSV_REALFILE, 'r');
        if (!$fpr) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_REALFILE . ' の読み込みに失敗しました。');
        }

        $fpw = fopen(ZIP_CSV_UTF8_REALFILE, 'w');
        if (!$fpw) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_UTF8_REALFILE . ' を開けません。');
        }

        while (!feof($fpr)) {
            fwrite($fpw, mb_convert_encoding(fgets($fpr, ZIP_CSV_LINE_MAX), CHAR_CODE, 'sjis-win'));
        }

        fclose($fpw);
        fclose($fpr);
    }

    function countMtbZip() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
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

    /**
     * 日本郵便から郵便番号 CSV の ZIP アーカイブファイルを取得
     *
     * @return void
     */
    function lfDownloadZipFileFromJp() {
       // Proxy経由を可能とする。        
       // TODO Proxyの設定は「DATA_REALDIR . 'module/Request.php'」内の「function HTTP_Request」へ記述する。いずれは、外部設定としたい。
       $req = new HTTP_Request();
       $req->setURL($this->zip_download_url);
       
        // 郵便番号CSVをdownloadする。
       $res1 = $req->sendRequest();
       
       if ($res1) {
            // 郵便番号CSV(zip file)を保存する。
           $fp = fopen($this->zip_csv_temp_realfile, 'w');
           $res2 = fwrite($fp, $req->getResponseBody());
        }
       if (!$res1 or !$res2) {
            // 郵便番号CSVの「downloadに失敗」または「書き込みに失敗」
           SC_Utils_Ex::sfDispException($this->zip_download_url . ' の取得または ' . $this->zip_csv_temp_realfile . ' への書き込みに失敗しました。');
       }
    }

    /**
     * ZIP アーカイブファイルを展開して、郵便番号 CSV を上書き
     *
     * @return void
     */
    function lfExtractZipFile() {
        if (!function_exists('zip_open')) {
            SC_Utils_Ex::sfDispException('PHP 拡張モジュール「zip」を有効にしてください。');
        }

        $zip = zip_open($this->zip_csv_temp_realfile);
        if (!is_resource($zip)) {
            SC_Utils_Ex::sfDispException($this->zip_csv_temp_realfile . ' をオープンできません。');
        }

        do {
            $entry = zip_read($zip);
        } while ($entry && zip_entry_name($entry) != 'KEN_ALL.CSV');

        if (!$entry) {
            SC_Utils_Ex::sfDispException($this->zip_csv_temp_realfile . ' に対象ファイルが見つかりません。');
        }

        // 展開時の破損を考慮し、別名で一旦展開する。
        $tmp_csv_realfile = ZIP_CSV_REALFILE . '.tmp';

        $res = zip_entry_open($zip, $entry, 'rb');
        if (!$res) {
            SC_Utils_Ex::sfDispException($this->zip_csv_temp_realfile . ' の展開に失敗しました。');
        }

        $fp = fopen($tmp_csv_realfile, 'w');
        if (!$fp) {
            SC_Utils_Ex::sfDispException($tmp_csv_realfile . ' を開けません。');
        }

        $res = fwrite($fp, zip_entry_read($entry, zip_entry_filesize($entry)));
        if ($res === FALSE) {
            SC_Utils_Ex::sfDispException($tmp_csv_realfile . ' の書き込みに失敗しました。');
        }

        fclose($fp);
        zip_close($zip);

        // CSV 削除
        $res = unlink(ZIP_CSV_REALFILE);
        if (!$res) {
            SC_Utils_Ex::sfDispException(ZIP_CSV_REALFILE . ' を削除できません。');
        }

        // CSV ファイル名変更
        $res = rename($tmp_csv_realfile, ZIP_CSV_REALFILE);
        if (!$res) {
            SC_Utils_Ex::sfDispException('ファイル名を変更できません。: ' . $tmp_csv_realfile . ' -> ' . ZIP_CSV_REALFILE);
        }
    }

    /**
     * CSV の更新日時を取得
     *
     * @return string CSV の更新日時 (整形済みテキスト)
     */
    function lfGetCsvDatetime() {
        return date('Y/m/d H:i:s', filemtime(ZIP_CSV_REALFILE));
    }
}
?>
