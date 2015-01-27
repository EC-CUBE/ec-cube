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

namespace Eccube\Page\Admin\Basis;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Util\Utils;

/** CSV ファイルの最大行数 */
define('ZIP_CSV_LINE_MAX', 8192);

/** 画像の表示数量 */
define('IMAGE_MAX', 680);

/** 郵便番号CSV ファイルのパス */
define('ZIP_CSV_REALFILE', DATA_REALDIR . 'downloads/KEN_ALL.CSV');

/** UTF-8 変換済みの郵便番号CSV ファイルのパス */
define('ZIP_CSV_UTF8_REALFILE', DATA_REALDIR . 'downloads/KEN_ALL_utf-8.CSV');

/**
 * 郵便番号DB登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ZipInstall extends AbstractAdminPage
{
    /** CSVの行数 */
    public $tpl_line = 0;
    public $tpl_mode;
    public $exec;
    public $tpl_count_mtb_zip;

    /** CSV の更新日時 */
    public $tpl_csv_datetime;

    /** 日本郵便から取得した ZIP アーカイブファイルの保管パス */
    public $zip_csv_temp_realfile;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/zip_install.tpl';
        $this->tpl_subno = 'zip_install';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '郵便番号DB登録';
        $this->tpl_mainno = 'basis';

        $this->tpl_mode = $this->getMode();
        $this->exec = (boolean) $_GET['exec'];
        $this->zip_csv_temp_realfile = DATA_REALDIR . 'downloads/tmp/ken_all.zip';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($this->tpl_mode, $objFormParam);
        $objFormParam->setParam($_GET);
        $this->arrErr = $objFormParam->checkError();
        $this->arrForm = $objFormParam->getHashArray();
        $this->tpl_zip_download_url_empty = !defined('ZIP_DOWNLOAD_URL') || strlen(ZIP_DOWNLOAD_URL) === 0 || ZIP_DOWNLOAD_URL === false;
        $this->tpl_zip_function_not_exists = !function_exists('zip_open');
        $this->tpl_skip_update_csv = $this->tpl_zip_download_url_empty || $this->tpl_zip_function_not_exists;

        if ($this->exec) {
            if (!empty($this->arrErr)) {
                trigger_error('', E_USER_ERROR);
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
            Application::alias('eccube.response')->actionExit();
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

            // 自動登録時の郵便番号CSV更新
            // XXX iframe内にエラー表示しない様、ここでlfDownloadZipFileFromJp()を呼ぶ。
            case 'auto';
                if (!$this->tpl_skip_update_csv) {
                    $this->lfDownloadZipFileFromJp();
                    $this->lfExtractZipFile();
                }
                break;
        }

        $this->tpl_line = $this->countZipCsv();
        $this->tpl_count_mtb_zip = $this->countMtbZip();
        $this->tpl_csv_datetime = $this->lfGetCsvDatetime();
        // XXX PHP4 を切捨てたら、ダウンロードの必要性チェックなども行いたい
        // $arrHeader = get_headers(ZIP_DOWNLOAD_URL, 1);
    }

    public function lfAutoCommitZip()
    {
        $objQuery = Application::alias('eccube.query');

        // DB更新
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
    public function lfDeleteZip()
    {
        $objQuery = Application::alias('eccube.query');

        // DB
        $objQuery->delete('mtb_zip');

        // UTF-8 変換済みの郵便番号 CSV
        unlink(ZIP_CSV_UTF8_REALFILE);
    }

    /**
     * パラメーター情報の初期化
     *
     * @param FormParam $objFormParam
     * @return void
     */
    public function lfInitParam($tpl_mode, &$objFormParam)
    {
        if ($tpl_mode == 'manual') {
            $objFormParam->addParam('開始行', 'startRowNum', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        }
    }

    /**
     * DB登録
     *
     * @return void
     */
    public function insertMtbZip($start = 1)
    {
        $objQuery = Application::alias('eccube.query');

        $img_path = USER_URL . USER_PACKAGE_DIR . 'admin/img/basis/'; // 画像パスは admin 固定

        ?>
        <html xmlns='http://www.w3.org/1999/xhtml' lang='ja' xml:lang='ja'>
        <head>
            <meta http-equiv='Content-Type' content='text/html; charset=<?php echo CHAR_CODE ?>' />
        </head>
        <body>
        <p>DB 登録進捗状況</p>
        <div style='background-color: #494E5F;'>
        <?php
        // 一部のIEは256バイト以上受け取ってから表示を開始する。
        Utils::sfFlush(true);

        echo '<img src="' . $img_path . 'zip_install_progress.gif"><br />';
        echo '<img src="' . $img_path . 'space_w.gif">';
        Utils::sfFlush();

        // 画像を一個表示する件数を求める。
        $line_all = $this->countZipCsv();
        $disp_line = intval($line_all / IMAGE_MAX);

        /** 現在行(CSV形式。空行は除く。) */
        $cntCurrentLine = 0;
        /** 挿入した行数 */
        $cntInsert = 0;
        $img_cnt = 0;

        $fp = $this->openZipCsv();
        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, ZIP_CSV_LINE_MAX);
            if (empty($arrCSV)) continue;
            $cntCurrentLine++;
            if ($cntCurrentLine >= $start) {
                $sqlval = array();
                $sqlval['zip_id'] = $cntCurrentLine;
                $sqlval['zipcode'] = $arrCSV[2];
                $sqlval['state'] = $arrCSV[6];
                $sqlval['city'] = $arrCSV[7];
                $sqlval['town'] = $arrCSV[8];
                $objQuery->insert('mtb_zip', $sqlval);
                $cntInsert++;
            }

            // $disp_line件ごとに進捗表示する
            if ($cntCurrentLine % $disp_line == 0 && $img_cnt < IMAGE_MAX) {
                echo '<img src="' . $img_path . 'graph_1_w.gif">';
                Utils::sfFlush();
                $img_cnt++;
            }
            Utils::extendTimeOut();
        }
        fclose($fp);

        echo '<img src="' . $img_path . 'space_w.gif">';

        ?>
        </div>
        <script type='text/javascript' language='javascript'>
            <!--
                // 完了画面
                function complete()
                {
                    document.open('text/html','replace');
                    document.clear();
                    document.write('<p>完了しました。<br />');
                    document.write("<?php echo $cntInsert ?> 件を追加しました。</p>");
                    document.write("<p><a href='?' target='_top'>戻る</a></p>");
                    document.close();
                }
                // コンテンツを削除するため、タイムアウトで呼び出し。
                setTimeout('complete()', 0);
            // -->
        </script>
        </body>
        </html>
        <?php
    }

    public function openZipCsv()
    {
        $this->convertZipCsv();
        $fp = fopen(ZIP_CSV_UTF8_REALFILE, 'r');
        if (!$fp) {
            trigger_error(ZIP_CSV_UTF8_REALFILE . ' の読み込みに失敗しました。', E_USER_ERROR);
        }

        return $fp;
    }

    public function convertZipCsv()
    {
        if (file_exists(ZIP_CSV_UTF8_REALFILE)) return;

        $fpr = fopen(ZIP_CSV_REALFILE, 'r');
        if (!$fpr) {
            trigger_error(ZIP_CSV_REALFILE . ' の読み込みに失敗しました。', E_USER_ERROR);
        }

        $fpw = fopen(ZIP_CSV_UTF8_REALFILE, 'w');
        if (!$fpw) {
            trigger_error(ZIP_CSV_UTF8_REALFILE . ' を開けません。', E_USER_ERROR);
        }

        while (!feof($fpr)) {
            fwrite($fpw, mb_convert_encoding(fgets($fpr, ZIP_CSV_LINE_MAX), CHAR_CODE, 'sjis-win'));
        }

        fclose($fpw);
        fclose($fpr);
    }

    public function countMtbZip()
    {
        $objQuery = Application::alias('eccube.query');

        return $objQuery->count('mtb_zip');
    }

    public function countZipCsv()
    {
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
    public function lfDownloadZipFileFromJp()
    {
        // Proxy経由を可能とする。
        // TODO Proxyの設定は「data/module/HTTP/Request.php」内の「function HTTP_Request」へ記述する。いずれは、外部設定としたい。
        $req = new HTTP_Request();

        $req->setURL(ZIP_DOWNLOAD_URL);

        // 郵便番号CSVをdownloadする。
        $res = $req->sendRequest();
        if (!$res || strlen($res) > 1) {
            trigger_error(ZIP_DOWNLOAD_URL . ' の取得に失敗しました。', E_USER_ERROR);
        }

        // 郵便番号CSV(zip file)を保存する。
        $fp = fopen($this->zip_csv_temp_realfile, 'w');
        if (!$fp) {
            trigger_error($this->zip_csv_temp_realfile . ' を開けません。', E_USER_ERROR);
        }
        $res = fwrite($fp, $req->getResponseBody());
        if (!$res) {
            trigger_error($this->zip_csv_temp_realfile . ' への書き込みに失敗しました。', E_USER_ERROR);
        }
    }

    /**
     * ZIP アーカイブファイルを展開して、郵便番号 CSV を上書き
     *
     * @return void
     */
    public function lfExtractZipFile()
    {
        $zip = zip_open($this->zip_csv_temp_realfile);
        if (!is_resource($zip)) {
            trigger_error($this->zip_csv_temp_realfile . ' をオープンできません。', E_USER_ERROR);
        }

        do {
            $entry = zip_read($zip);
        } while ($entry && zip_entry_name($entry) != 'KEN_ALL.CSV');

        if (!$entry) {
            trigger_error($this->zip_csv_temp_realfile . ' に対象ファイルが見つかりません。', E_USER_ERROR);
        }

        // 展開時の破損を考慮し、別名で一旦展開する。
        $tmp_csv_realfile = ZIP_CSV_REALFILE . '.tmp';

        $res = zip_entry_open($zip, $entry, 'rb');
        if (!$res) {
            trigger_error($this->zip_csv_temp_realfile . ' の展開に失敗しました。', E_USER_ERROR);
        }

        $fp = fopen($tmp_csv_realfile, 'w');
        if (!$fp) {
            trigger_error($tmp_csv_realfile . ' を開けません。', E_USER_ERROR);
        }

        $res = fwrite($fp, zip_entry_read($entry, zip_entry_filesize($entry)));
        if ($res === FALSE) {
            trigger_error($tmp_csv_realfile . ' の書き込みに失敗しました。', E_USER_ERROR);
        }

        fclose($fp);
        zip_close($zip);

        // CSV 削除
        $res = unlink(ZIP_CSV_REALFILE);
        if (!$res) {
            trigger_error(ZIP_CSV_REALFILE . ' を削除できません。', E_USER_ERROR);
        }

        // CSV ファイル名変更
        $res = rename($tmp_csv_realfile, ZIP_CSV_REALFILE);
        if (!$res) {
            trigger_error('ファイル名を変更できません。: ' . $tmp_csv_realfile . ' -> ' . ZIP_CSV_REALFILE, E_USER_ERROR);
        }
    }

    /**
     * CSV の更新日時を取得
     *
     * @return string CSV の更新日時 (整形済みテキスト)
     */
    public function lfGetCsvDatetime()
    {
        return date('Y/m/d H:i:s', filemtime(ZIP_CSV_REALFILE));
    }
}
