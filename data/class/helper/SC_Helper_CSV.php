<?php
  /*
   * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
   *
   * http://www.lockon.co.jp/
   */

  /**
   * CSV 関連 のヘルパークラス.
   *
   * @package Page
   * @author LOCKON CO.,LTD.
   * @version $Id$
   */
class SC_Helper_CSV 
{

    // {{{ properties

    /** 項目英名 */
    var $arrSubnavi;

    /** 項目名 */
    var $arrSubnaviName;

    // }}}
    // {{{ constructor

    /**
     * デフォルトコンストラクタ.
     */
    function __construct()
    {
        $this->init();
    }

    // }}}
    // {{{ functions

    /**
     * 項目情報を初期化する.
     *
     * @access private
     * @return void
     */
    function init()
    {
        $this->arrSubnavi = array(
            1 => 'product',
            2 => 'customer',
            3 => 'order',
            4 => 'review',
            5 => 'category',
        );

        $this->arrSubnaviName = array(
            1 => '商品管理',
            2 => '会員管理',
            3 => '受注管理',
            4 => 'レビュー',
            5 => 'カテゴリ',
        );
    }

    /**
     * CSVファイルを送信する
     *
     * @param integer $csv_id CSVフォーマットID
     * @param string $where WHERE条件文
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。
     * @param string $order ORDER文
     * @param boolean $is_download true:ダウンロード用出力までさせる false:CSVの内容を返す(旧方式、メモリを食います。）
     * @return mixed $is_download = true時 成功失敗フラグ(boolean) 、$is_downalod = false時 string
     */
    function sfDownloadCsv($csv_id, $where = '', $arrVal = array(), $order = '', $is_download = false)
    {
        // CSV出力タイトル行の作成
        $arrOutput = SC_Utils_Ex::sfSwapArray($this->sfGetCsvOutput($csv_id, 'status = ' . CSV_COLUMN_STATUS_FLG_ENABLE));
        if (count($arrOutput) <= 0) return false; // 失敗終了
        $arrOutputCols = $arrOutput['col'];

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder($order);
        $cols = SC_Utils_Ex::sfGetCommaList($arrOutputCols, true);

        // TODO: 固有処理 なんかエレガントな処理にしたい
        // 商品の場合
        if ($csv_id == 1) {
            $objProduct = new SC_Product_Ex();
            // このWhereを足さないと無効な規格も出力される。現行仕様と合わせる為追加。
            $inner_where = 'dtb_products_class.del_flg = 0';
            $from = $objProduct->prdclsSQL($inner_where);
            $sql = $objQuery->getSql($cols, $from, $where);
        }
        // 商品以外の場合
        else {
            // 会員の場合
            if ($csv_id == 2) {
                $from = 'dtb_customer';
            }
            // 注文の場合
            else if ($csv_id == 3) {
                $from = 'dtb_order';
            }
            // レビューの場合
            else if ($csv_id == 4) {
                $from = 'dtb_review AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id';
            }
            // カテゴリの場合
            else if ($csv_id == 5) {
                $from = 'dtb_category';
            }

            $sql = "SELECT $cols FROM $from $where";
            // 「getSqlを使っているcsv_id=1」以外で「order指定」がある場合は末尾に付与
            // 全csv_idでgetSqlを使用して生成するよう統一する場合は当メソッドの呼び元も要修正
            if ($csv_id != 1 && strlen($order) >= 1) {
                $sql .= " ORDER BY $order";
            }
        }
        // 固有処理ここまで

        return $this->sfDownloadCsvFromSql($sql, $arrVal, $this->arrSubnavi[$csv_id], $arrOutput['disp_name'], $is_download);
    }

    /**
     * CSV 項目を出力する.
     *
     * @param integer $csv_id CSV ID
     * @param string $where SQL の WHERE 句
     * @param array $arrVal WHERE 句の要素
     * @param array $order SQL の ORDER BY 句
     * @return array CSV 項目の配列
     */
    function sfGetCsvOutput($csv_id = '', $where = '', $arrVal = array(), $order = 'rank, no')
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $cols = 'no, csv_id, col, disp_name, rank, status, rw_flg, mb_convert_kana_option, size_const_type, error_check_types';
        $table = 'dtb_csv';

        if (SC_Utils_Ex::sfIsInt($csv_id)) {
            if ($where == '') {
                $where = 'csv_id = ?';
            } else {
                $where = "$where AND csv_id = ?";
            }
            $arrVal[] = $csv_id;
        }
        $objQuery->setOrder($order);

        $arrRet = $objQuery->select($cols, $table, $where, $arrVal);
        return $arrRet;
    }

    /**
     * CSVが出力設定でインポート可能かのチェック
     *
     * @param array sfGetCsvOutputで取得した内容（またはそれと同等の配列)
     * @return boolean true:インポート可能、false:インポート不可
     */
    function sfIsImportCSVFrame(&$arrCSVFrame)
    {
        $result = true;
        foreach ($arrCSVFrame as $val) {
            if ($val['status'] != CSV_COLUMN_STATUS_FLG_ENABLE
                && $val['rw_flg'] == CSV_COLUMN_RW_FLG_READ_WRITE
                && $val['error_check_types'] != ''
                && strpos(strtoupper($val['error_check_types']), 'EXIST_CHECK') !== FALSE
            ) {
                //必須フィールド
                $result = false;
            }
        }
        return $result;
    }

    /**
     * CSVが出力設定で更新可能かのチェック
     *
     * @param array sfGetCsvOutputで取得した内容（またはそれと同等の配列)
     * @return boolean true:更新可能、false:新規追加のみ不可
     */
    function sfIsUpdateCSVFrame(&$arrCSVFrame)
    {
        $result = true;
        foreach ($arrCSVFrame as $val) {
            if ($val['status'] != CSV_COLUMN_STATUS_FLG_ENABLE
                && $val['rw_flg'] == CSV_COLUMN_RW_FLG_KEY_FIELD
            ) {
                //キーフィールド
                $result = false;
            }
        }
        return $result;
    }

    /**
     * CSVファイルのカウント数を得る.
     *
     * @param resource $fp fopenを使用して作成したファイルポインタ
     * @return integer CSV のカウント数
     */
    function sfGetCSVRecordCount($fp)
    {
        $count = 0;
        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
            $count++;
        }
        // ファイルポインタを戻す
        if (rewind($fp)) {
            return $count-1;
        } else {
            return FALSE;
        }
    }

    /**
     * CSV作成 テンポラリファイル出力 コールバック関数
     *
     * @param mixed $data 出力データ
     * @return boolean true (true:固定 false:中断)
     */
    function cbOutputCSV($data)
    {
        $line = $this->sfArrayToCSV($data);
        $line = mb_convert_encoding($line, 'SJIS-Win');
        $line .= "\r\n";
        fwrite($this->fpOutput, $line);
        SC_Utils_Ex::extendTimeOut();
        return true;
    }

    /**
     * SQL文からクエリ実行し CSVファイルを送信する
     *
     * @param integer $sql SQL文
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。
     * @param string $file_head ファイル名の頭に付ける文字列
     * @param array $arrHeader ヘッダ出力列配列
     * @param boolean $is_download true:ダウンロード用出力までさせる false:CSVの内容を返す(旧方式、メモリを食います。）
     * @return mixed $is_download = true時 成功失敗フラグ(boolean) 、$is_downalod = false時 string
     */
    function sfDownloadCsvFromSql($sql, $arrVal = array(), $file_head = 'csv', $arrHeader = array(), $is_download = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ヘッダ構築
        if (is_array($arrHeader)) {
            $header = $this->sfArrayToCSV($arrHeader);
            $header = mb_convert_encoding($header, 'SJIS-Win');
            $header .= "\r\n";
        }

        //テンポラリファイル作成
        // TODO: パフォーマンス向上には、ストリームを使うようにすると良い
        //  環境要件がPHPバージョン5.1以上になったら使うように変えても良いかと
        //  fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        $tmp_filename = tempnam(CSV_TEMP_REALDIR, $file_head . '_csv');
        $this->fpOutput = fopen($tmp_filename, 'w+');
        fwrite($this->fpOutput, $header);
        $objQuery->doCallbackAll(array(&$this, 'cbOutputCSV'), $sql, $arrVal);

        fclose($this->fpOutput);

        if ($is_download) {
            // CSVを送信する。
            $this->lfDownloadCSVFile($tmp_filename, $file_head . '_');
            $res = true;
        } else {
            $res = SC_Helper_FileManager_Ex::sfReadFile($tmp_filename);
        }

        //テンポラリファイル削除
        unlink($tmp_filename);
        return $res;
    }

    /**
     * 1次元配列を1行のCSVとして返す
     * 参考: http://jp.php.net/fputcsv
     *
     * @param array $fields データ1次元配列
     * @param string $delimiter
     * @param string $enclosure
     * @param string $arrayDelimiter
     * @return string 結果行
     */
    function sfArrayToCsv($fields, $delimiter = ',', $enclosure = '"', $arrayDelimiter = '|')
    {
        if (strlen($delimiter) != 1) {
            trigger_error('delimiter must be a single character', E_USER_WARNING);
            return '';
        }

        if (strlen($enclosure) < 1) {
            trigger_error('enclosure must be a single character', E_USER_WARNING);
            return '';
        }

        foreach ($fields as $key => $value) {
            $field =& $fields[$key];

            // 配列を「|」区切りの文字列に変換する
            if (is_array($field)) {
                $field = implode($arrayDelimiter, $field);
            }

            /* enclose a field that contains a delimiter, an enclosure character, or a newline */
            if (is_string($field)
                && preg_match('/[' . preg_quote($delimiter) . preg_quote($enclosure) . '\\s]/', $field)
            ) {
                $field = $enclosure . preg_replace('/' . preg_quote($enclosure) . '/', $enclosure . $enclosure, $field) . $enclosure;
            }
        }

        return implode($delimiter, $fields);
    }

    /**
     * 配列データのCSVを送信する。
     *
     * @param array $fields データ配列
     * @param string $prefix
     * @return void
     */
    function lfDownloadCsv($arrData, $prefix = '')
    {

        if ($prefix == '') {
            $dir_name = SC_Utils_Ex::sfUpDirName();
            $file_name = $dir_name . date('ymdHis') .'.csv';
        } else {
            $file_name = $prefix . date('ymdHis') .'.csv';
        }

        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename=${file_name}");
        Header("Content-type: application/octet-stream; name=${file_name}");
        Header('Cache-Control: ');
        Header('Pragma: ');

        /* データを出力 */
        foreach ($arrData as $lineArray) {
            $lineString = $this->sfArrayToCsv($lineArray);
            $lineString = mb_convert_encoding($lineString, 'SJIS-Win');
            echo $lineString . "\r\n";
        }
    }

    /**
     * CSVファイルを送信する。
     *
     * @param string $filepath 送信するファイルのフルパス
     * @param string $prefix
     * @return void
     */
    function lfDownloadCSVFile($filepath, $prefix = '')
    {
        $file_name = $prefix . date('YmdHis') . '.csv';

        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename={$file_name}");
        Header("Content-type: application/octet-stream; name={$file_name}");
        Header('Cache-Control: ');
        Header('Pragma: ');

        /* データを出力 */
        // file_get_contentsはメモリマッピングも自動的に使ってくれるので高速＆省メモリ
        echo file_get_contents($filepath);
    }
}
