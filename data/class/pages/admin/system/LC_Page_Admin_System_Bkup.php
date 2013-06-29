<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * バックアップ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Bkup extends LC_Page_Admin_Ex
{
    /** リストア中にエラーが発生したか */
    var $tpl_restore_err = false;

    /** 対象外とするシーケンス生成器 */
    var $arrExcludeSequence = array(
        'plsql_profiler_runid', // Postgres Plus Advanced Server 9.1
        'snapshot_num',         // Postgres Plus Advanced Server 9.1
    );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = 'system/bkup.tpl';
        $this->tpl_mainno = 'system';
        $this->tpl_subno = 'bkup';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'バックアップ管理';

        $this->bkup_dir = DATA_REALDIR . 'downloads/backup/';
        $this->bkup_ext = '.tar.gz';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {
        $objFormParam = new SC_FormParam_Ex;

        // パラメーターの初期化
        $this->initParam($objFormParam, $_POST);

        $arrErrTmp  = array();
        $arrForm = array();

        $this->mode = $this->getMode();
        switch ($this->mode) {
            // バックアップを作成する
            case 'bkup':

                // データ型エラーチェック
                $arrErrTmp[1] = $objFormParam->checkError();

                // データ型に問題がない場合
                if (SC_Utils_Ex::isBlank($arrErrTmp[1])) {
                    // データ型以外のエラーチェック
                    $arrErrTmp[2] = $this->lfCheckError($objFormParam->getHashArray(), $this->mode);
                }

                // エラーがなければバックアップ処理を行う
                if (SC_Utils_Ex::isBlank($arrErrTmp[1]) && SC_Utils_Ex::isBlank($arrErrTmp[2])) {
                    $arrData = $objFormParam->getHashArray();

                    $work_dir = $this->bkup_dir . $arrData['bkup_name'] . '/';
                    // バックアップデータの事前削除
                    SC_Helper_FileManager_Ex::deleteFile($work_dir);
                    // バックアップファイル作成
                    $res = $this->lfCreateBkupData($arrData['bkup_name'], $work_dir);
                    // バックアップデータの事後削除
                    SC_Helper_FileManager_Ex::deleteFile($work_dir);

                    $arrErrTmp[3] = array();
                    if ($res !== true) {
                        $arrErrTmp[3]['bkup_name'] = 'バックアップに失敗しました。(' . $res . ')';
                    }

                    // DBにデータ更新
                    if (SC_Utils_Ex::isBlank($arrErrTmp[3])) {
                        $this->lfUpdBkupData($arrData);
                    } else {
                        $arrForm = $arrData;
                        $arrErr = $arrErrTmp[3];
                    }

                    $this->tpl_onload = "alert('バックアップ完了しました');";
                } else {
                    $arrForm = $objFormParam->getHashArray();
                    $arrErr = array_merge((array)$arrErrTmp[1],(array)$arrErrTmp[2]);
                }
                break;

            // リストア
            case 'restore_config':
            case 'restore':
                // データベースに存在するかどうかチェック
                $arrErr = $this->lfCheckError($objFormParam->getHashArray(), $this->mode);

                // エラーがなければリストア処理を行う
                if (SC_Utils_Ex::isBlank($arrErr)) {
                    $arrData = $objFormParam->getHashArray();

                    $msg = '「' . $arrData['list_name'] . '」のリストアを開始します。';
                    GC_Utils_Ex::gfPrintLog($msg);

                    $success = $this->lfRestore($arrData['list_name'], $this->bkup_dir, $this->bkup_ext, $this->mode);

                    $msg = '「' . $arrData['list_name'] . '」の';
                    $msg .= $success ? 'リストアを終了しました。' : 'リストアに失敗しました。';

                    $this->tpl_restore_msg .= $msg . "\n";
                    GC_Utils_Ex::gfPrintLog($msg);
                }
                break;

            // 削除
            case 'delete':

                // データベースに存在するかどうかチェック
                $arrErr = $this->lfCheckError($objFormParam->getHashArray(), $this->mode);

                // エラーがなければリストア処理を行う
                if (SC_Utils_Ex::isBlank($arrErr)) {
                    $arrData = $objFormParam->getHashArray();

                    // DBとファイルを削除
                    $this->lfDeleteBackUp($arrData, $this->bkup_dir, $this->bkup_ext);
                }

                break;

                // ダウンロード
            case 'download' :

                // データベースに存在するかどうかチェック
                $arrErr = $this->lfCheckError($objFormParam->getHashArray(), $this->mode);

                // エラーがなければダウンロード処理を行う
                if (SC_Utils_Ex::isBlank($arrErr)) {
                    $arrData = $objFormParam->getHashArray();

                    $filename = $arrData['list_name'] . $this->bkup_ext;
                    $dl_file = $this->bkup_dir.$arrData['list_name'] . $this->bkup_ext;

                    // ダウンロード開始
                    Header("Content-disposition: attachment; filename=${filename}");
                    Header("Content-type: application/octet-stream; name=${filename}");
                    header('Content-Length: ' .filesize($dl_file));
                    readfile ($dl_file);
                    exit();
                    break;
                }

            default:
                break;
        }

        // 不要になった変数を解放
        unset($arrErrTmp);

        // バックアップリストを取得する
        $arrBkupList = $this->lfGetBkupData('ORDER BY create_date DESC');
        // テンプレートファイルに渡すデータをセット
        $this->arrErr = isset($arrErr) ? $arrErr : array();
        $this->arrForm = isset($arrForm) ? $arrForm : array();
        $this->arrBkupList = $arrBkupList;
    }

    /**
     * パラメーター初期化.
     *
     * @param object $objFormParam
     * @param array  $arrParams  $_POST値
     * @return void
     */
    function initParam(&$objFormParam, &$arrParams)
    {
        $objFormParam->addParam('バックアップ名', 'bkup_name', STEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB', 'FILE_NAME_CHECK_BY_NOUPLOAD'));
        $objFormParam->addParam('バックアップメモ', 'bkup_memo', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('バックアップ名(リスト)', 'list_name', STEXT_LEN, 'a', array('MAX_LENGTH_CHECK', 'NO_SPTAB', 'FILE_NAME_CHECK_BY_NOUPLOAD'));
        $objFormParam->setParam($arrParams);
        $objFormParam->convParam();
    }

    /**
     * データ型以外のエラーチェック.
     *
     * @param array  $arrForm
     * @param string $mode
     * @return $arrErr
     */
    function lfCheckError(&$arrForm, $mode)
    {
        switch ($mode) {
            case 'bkup':
                $name = $arrForm['bkup_name'];
                break;

            case 'restore_config':
            case 'restore':
            case 'download':
            case 'delete':
                $name = $arrForm['list_name'];
                break;

            default:
                trigger_error('不明な処理', E_USER_ERROR);
                break;
        }

        // 重複・存在チェック
        $ret = $this->lfGetBkupData('', $name);
        if (count($ret) > 0 && $mode == 'bkup') {
            $arrErr['bkup_name'] = 'バックアップ名が重複しています。別名を入力してください。';
        } elseif (count($ret) <= 0 && $mode != 'bkup') {
            $arrErr['list_name'] = '選択されたデータがみつかりませんでした。既に削除されている可能性があります。';
        }

        return $arrErr;
    }

    /**
     * バックアップファイル作成.
     *
     * @param string $bkup_name
     * @return boolean|int 結果。true:成功 int:失敗 FIXME 本来は int ではなく、エラーメッセージを戻すべき
     */
    function lfCreateBkupData($bkup_name, $work_dir)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $csv_autoinc = '';
        $arrData = array();

        $success = mkdir($work_dir, 0777, true);
        if (!$success) {
            return __LINE__;
        }

        // 全テーブル取得
        $arrTableList = $objQuery->listTables();

        // 各テーブル情報を取得する
        foreach ($arrTableList as $table) {
            if ($table == 'dtb_bkup' || $table == 'mtb_zip') {
                continue;
            }

            // dataをCSV出力
            $csv_file = $work_dir . $table . '.csv';
            $fp = fopen($csv_file, 'w');
            if (!$fp) {
                return __LINE__;
            }

            // 全データを取得
            $sql = 'SELECT * FROM ' . $objQuery->conn->quoteIdentifier($table);

            $this->fpOutput =& $fp;
            $this->first_line = true;
            $success = $objQuery->doCallbackAll(array(&$this, 'cbOutputCSV'), $sql);
            unset($this->fpOutput);

            if ($success === false) {
                return __LINE__;
            }

            fclose($fp);

            // タイムアウトを防ぐ
            SC_Utils_Ex::sfFlush();
        }

        // 自動採番型の構成を取得する
        $csv_autoinc = $this->lfGetAutoIncrement();

        $csv_autoinc_file = $work_dir . 'autoinc_data.csv';

        // CSV出力

        // 自動採番をCSV出力
        $fp = fopen($csv_autoinc_file,'w');
        if ($fp) {
            if ($csv_autoinc != '') {
                $success = fwrite($fp, $csv_autoinc);
                if (!$success) {
                    return __LINE__;
                }
            }
            fclose($fp);
        }

        //圧縮フラグTRUEはgzip圧縮をおこなう
        $tar = new Archive_Tar($this->bkup_dir . $bkup_name . $this->bkup_ext, TRUE);

        //bkupフォルダに移動する
        chdir($work_dir);

        //圧縮をおこなう
        $zip = $tar->create('./');

        return true;
    }

    /**
     * CSV作成 テンポラリファイル出力 コールバック関数
     *
     * @param mixed $data 出力データ
     * @return boolean true (true:固定 false:中断)
     */
    function cbOutputCSV($data)
    {
        $line = '';
        if ($this->first_line) {
            // カラム名
            $line .= SC_Helper_CSV_Ex::sfArrayToCsv(array_keys($data)) . "\n";
            $this->first_line = false;
        }
        $line .= SC_Helper_CSV_Ex::sfArrayToCsv($data);
        $line .= "\n";
        SC_Utils_Ex::extendTimeOut();

        return fwrite($this->fpOutput, $line);
    }

    /**
     * シーケンス一覧をCSV出力形式に変換する.
     *
     * シーケンス名,シーケンス値 の形式に出力する.
     *
     * @return string シーケンス一覧の文字列
     * @return string $ret
     */
    function lfGetAutoIncrement()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrSequences = $objQuery->listSequences();

        foreach ($arrSequences as $name) {
            if (in_array($name, $this->arrExcludeSequence, true)) {
                continue 1;
            }

            // XXX SC_Query::currVal は、PostgreSQL で nextval と等しい値を戻すケースがある。欠番を生じうるが、さして問題無いと推測している。
            $seq = $objQuery->currVal($name);

            // TODO CSV 生成の共通処理を使う
            $ret .= $name . ',';
            $ret .= is_null($seq) ? '0' : $seq;
            $ret .= "\r\n";
        }

        return $ret;
    }

    // バックアップテーブルにデータを更新する
    function lfUpdBkupData($data)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrVal = array();
        $arrVal['bkup_name'] = $data['bkup_name'];
        $arrVal['bkup_memo'] = $data['bkup_memo'];
        $arrVal['create_date'] = 'CURRENT_TIMESTAMP';

        $objQuery->insert('dtb_bkup', $arrVal);
    }

    /**
     * バックアップの一覧を取得する
     */
    function lfGetBkupData($sql_option = '', $filter_bkup_name = '')
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // テーブルから取得
        $arrVal = array();

        $sql = 'SELECT bkup_name, bkup_memo, create_date FROM dtb_bkup';
        if (strlen($filter_bkup_name) >= 1) {
            $sql .= ' WHERE bkup_name = ?';
            $arrVal[] = $filter_bkup_name;
        }
        if ($sql_option != '') {
            $sql .= ' ' . $sql_option;
        }

        $ret = $objQuery->getAll($sql, $arrVal);

        // ファイルのみのものを取得
        $glob = glob($this->bkup_dir . '*' . $this->bkup_ext);
        if (is_array($glob)) {
            foreach ($glob as $path) {
                $bkup_name = basename($path, $this->bkup_ext);
                if (strlen($filter_bkup_name) >= 1 && $bkup_name !== $filter_bkup_name) {
                    continue 1;
                }
                unset($row);
                foreach ($ret as $key => $value) {
                    if ($ret[$key]['bkup_name'] == $bkup_name) {
                        $row =& $ret[$key];
                    }
                }
                if (!isset($row)) {
                    $ret[] = array();
                    $row =& $ret[array_pop(array_keys($ret))];
                    $row['bkup_name'] = $bkup_name;
                    $row['bkup_memo'] = '(記録なし。バックアップファイルのみ。)';
                    $row['create_date'] = date('Y-m-d H:i:s', filemtime($path));
                }
            }
        }

        return $ret;
    }

    /**
     * バックアップファイルをリストアする
     *
     * @param string $bkup_name
     * @param string $bkup_dir
     * @param string $bkup_ext
     * @return void
     */
    function lfRestore($bkup_name, $bkup_dir, $bkup_ext, $mode)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $bkup_filepath = $bkup_dir . $bkup_name . $bkup_ext;
        $work_dir = $bkup_dir . $bkup_name . '/';

        //圧縮フラグTRUEはgzip解凍をおこなう
        $tar = new Archive_Tar($bkup_filepath, TRUE);

        //指定されたフォルダ内に解凍する
        $success = $tar->extract($work_dir);

        if (!$success) {
            $msg = 'バックアップファイルの展開に失敗しました。' . "\n";
            $msg .= '展開元: ' . $bkup_filepath . "\n";
            $msg .= '展開先: ' . $work_dir;
            trigger_error($msg, E_USER_ERROR);
        }

        // トランザクション開始
        $objQuery->begin();

        // INSERT実行
        $success = $this->lfExeInsertSQL($objQuery, $work_dir, $mode);

        // シーケンス生成器を復元する
        if ($success) $this->restoreSequence($objQuery, $work_dir . 'autoinc_data.csv');

        // リストア成功ならコミット失敗ならロールバック
        if ($success) {
            $objQuery->commit();
            $this->tpl_restore_err = true;
        } else {
            $objQuery->rollback();
            $this->tpl_restore_name = $bkup_name;
        }

        // FIXME この辺りで、バックアップ時と同等の一時ファイルの削除を実行すべきでは?

        SC_Utils_Ex::extendTimeOut();

        return $success;
    }

    /**
     * CSVファイルからインサート実行.
     *
     * @param object $objQuery
     * @param string $dir
     * @param string $mode
     * @return void
     */
    function lfExeInsertSQL(&$objQuery, $dir, $mode)
    {
        $tbl_flg = false;
        $col_flg = false;
        $ret = true;
        $pagelayout_flg = false;
        $arrVal = array();
        $arrCol = array();
        $arrAllTableList = $objQuery->listTables();

        $objDir = dir($dir);
        while (false !== ($file_name = $objDir->read())) {
            if (!preg_match('/^((dtb|mtb|plg)_(\w+))\.csv$/', $file_name, $matches)) {
                continue;
            }
            $file_path = $dir . $file_name;
            $table = $matches[1];

            // テーブル存在チェック
            if (!in_array($table, $arrAllTableList)) {
                if ($mode === 'restore_config') {
                    continue;
                }
                return false;
            }

            // csvファイルからデータの取得
            $fp = fopen($file_path, 'r');
            if ($fp === false) {
                trigger_error($file_name . ' のファイルオープンに失敗しました。', E_USER_ERROR);
            }

            GC_Utils_Ex::gfPrintLog('リストア実行: ' . $table);
            $objQuery->delete($table);

            $line = 0;
            $arrColName = array();
            while (!feof($fp)) {
                $line++;
                $arrCsvLine = fgetcsv($fp, 1024 * 1024);

                // 1行目: 列名
                if ($line === 1) {
                    $arrColName = $arrCsvLine;
                    continue;
                }

                // 空行を無視
                // false との比較は PHP 5.2.x Windows バグ対応
                // 参考: http://www.php.net/manual/ja/function.fgetcsv.php#98502
                if ($arrCsvLine === array(null) || $arrCsvLine === false) {
                    continue;
                }

                $arrVal = array_combine($arrColName, $arrCsvLine);
                $objQuery->insert($table, $arrVal);

                SC_Utils_Ex::extendTimeOut();
            }

            fclose($fp);
        }

        return $ret;
    }

    /**
     * シーケンス生成器を復元する
     */
    function restoreSequence(&$objQuery, $csv)
    {
        // csvファイルからデータの取得
        $arrCsvData = file($csv);

        foreach ($arrCsvData as $line) {
            list($name, $currval) = explode(',', trim($line));

            if (in_array($name, $this->arrExcludeSequence, true)) {
                continue 1;
            }

            // FIXME テーブルと同様に整合チェックを行う。また不整合時はスキップして続行する。

            // XXX +1 ではなく、nextVal を呼ぶべきかも。
            $objQuery->setVal($name, $currval + 1);
        }
    }

    // 選択したバックアップをDBから削除
    function lfDeleteBackUp(&$arrForm, $bkup_dir, $bkup_ext)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $del_file = $bkup_dir.$arrForm['list_name'] . $bkup_ext;
        // ファイルの削除
        if (is_file($del_file)) {
            $ret = unlink($del_file);
        }

        $objQuery->delete('dtb_bkup', 'bkup_name = ?', array($arrForm['list_name']));
    }
}
