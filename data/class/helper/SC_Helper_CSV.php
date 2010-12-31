<?php
  /*
   * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
class SC_Helper_CSV {

    // {{{ properties

    /** 項目英名 */
    var $arrSubnavi;

    /** 項目名 */
    var $arrSubnaviName;

    /** レビュー管理項目 */
    var $arrREVIEW_CVSCOL;

    /** レビュータイトル */
    var $arrREVIEW_CVSTITLE;

    /** トラックバック項目 */
    var $arrTRACKBACK_CVSCOL;

    /** トラックバックタイトル */
    var $arrTRACKBACK_CVSTITLE;


    // }}}
    // {{{ constructor

    /**
     * デフォルトコンストラクタ.
     */
    function SC_Helper_CSV() {
        $this->init();

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrRECOMMEND = $masterData->getMasterData("mtb_recommend");
    }

    // }}}
    // {{{ functions

    /**
     * CSV 項目を出力する.
     *
     * @param integer $csv_id CSV ID
     * @param string $where SQL の WHERE 句
     * @param array $arrVal WHERE 句の要素
     * @param array $order SQL の ORDER BY 句
     * @return array CSV 項目の配列
     */
    function sfgetCsvOutput($csv_id = "", $where = '', $arrVal = array(), $order = 'rank, no'){
        $objQuery =& SC_Query::getSingletonInstance();
        
        $cols = 'no, csv_id, col, disp_name, rank, status, rw_flg, mb_convert_kana_option, size_const_type, error_check_types';
        $table = 'dtb_csv';
        
        if(SC_Utils_Ex::sfIsInt($csv_id)){
            if($where == "") {
                $where = "csv_id = ?";
            }else{
                $where = "$where AND csv_id = ?";
            }
            $arrVal[] = $csv_id;
        }
        $objQuery->setOrder($order);
        
        $arrRet = $objQuery->select($cols, $table, $where, $arrVal);
        return $arrRet;
    }

    // CSVを送信する。(共通。現状は受注のみ利用。)
    function sfDownloadCsv($csv_id, $where, $arrval, $order) {
        switch ($csv_id) {
            case 3: // 受注
                $from = 'dtb_order';
                break;
        }

        // CSV出力タイトル行の作成
        $arrCsvOutput = SC_Utils_Ex::sfSwapArray($this->sfgetCsvOutput($csv_id, 'status = 1'));

        if (count($arrCsvOutput) <= 0) break;

        $arrCsvOutputCols = $arrCsvOutput['col'];
        $arrCsvOutputConvs = $arrCsvOutput['conv'];
        $arrCsvOutputTitle = $arrCsvOutput['disp_name'];
        $head = SC_Utils_Ex::sfGetCSVList($arrCsvOutputTitle);
        $data = $objCSV->lfGetCSV("dtb_order", $where, $option, $arrval, $arrCsvOutputCols, $arrCsvOutputConvs);
    }
    
    /**
     * CSVが出力設定でインポート可能かのチェック
     *
     * @param array sfgetCsvOutputで取得した内容（またはそれと同等の配列)
     * @return boolean true:インポート可能、false:インポート不可
     */
    function sfIsImportCSVFrame(&$arrCSVFrame) {
        $result = true;
        foreach($arrCSVFrame as $key => $val) {
            if($val['status'] != "1"
                    and $val['rw_flg'] == "1"
                    and $val['error_check_types'] != ""
                    and stripos($val['error_check_types'], "EXIST_CHECK") !== FALSE) {
                //必須フィールド
                $result = false;
            }
        }
        return $result;
    }
    
    /**
     * CSVが出力設定で更新可能かのチェック
     *
     * @param array sfgetCsvOutputで取得した内容（またはそれと同等の配列)
     * @return boolean true:更新可能、false:新規追加のみ不可
     */
    function sfIsUpdateCSVFrame(&$arrCSVFrame) {
        $result = true;
        foreach($arrCSVFrame as $key => $val) {
            if($val['status'] != "1"
                    and $val['rw_flg'] == "3") {
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
    function sfGetCSVRecordCount($fp) {

        $count = 0;
        while(!feof($fp)) {
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

    //  CSV作成 コールバック関数
    function cbOutputProductCSV($data) {
        $line = $this->sfArrayToCSV($data);
        $line = mb_convert_encoding($line, 'SJIS-Win');
        $line .= "\r\n";
        fwrite($this->fpOutput, $line);
        return true;
    }

    // CSVを送信する。(商品)
    function sfDownloadProductsCsv($where, $arrval, $order, $is_download = false) {
        // 実行時間を制限しない
        @set_time_limit(0);

        // CSV出力タイトル行の作成
        $arrOutput = SC_Utils_Ex::sfSwapArray($this->sfgetCsvOutput(1, 'status = 1'));
        if (count($arrOutput) <= 0) return false; // 失敗終了
        $arrOutputCols = $arrOutput['col'];

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOrder($order);
        
        $objProduct = new SC_Product();
        $cols = SC_Utils_Ex::sfGetCommaList($arrOutputCols, true);
        // このWhereを足さないと無効な規格も出力される。現行仕様と合わせる為追加。
        $inner_where = 'dtb_products_class.del_flg = 0';
        $sql = $objQuery->getSql($cols, $objProduct->prdclsSQL($inner_where),$where);
        $header = $this->sfArrayToCSV($arrOutput['disp_name']);
        $header = mb_convert_encoding($header, 'SJIS-Win');
        $header .= "\r\n";
        
        //テンポラリファイル作成
        // TODO: パフォーマンス向上には、ストリームを使うようにすると良い
        //  環境要件がバージョン5.1以上になったら使うように変えても良いかと
        //  fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        $tmp_filename = tempnam(CSV_TEMP_REALDIR, 'product_csv');
        $this->fpOutput = fopen($tmp_filename, "w+");

        fwrite($this->fpOutput, $header);

        $objQuery->doCallbackAll(array(&$this, 'cbOutputProductCSV'), $sql, $arrval);

        fclose($this->fpOutput);

        if($is_download) {
            // CSVを送信する。
            $this->lfDownloadCSVFile($tmp_filename,"product_");
            $res = true;
        }else{
            $res = SC_Utils_Ex::sfReadFile($tmp_filename);
        }
        
        //テンポラリファイル削除
        unlink($tmp_filename);
        return $res;
    }

    // CSV出力データを作成する。(レビュー)
    function lfGetReviewCSV($where, $option, $arrval) {

        $from = "dtb_review AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
        $cols = SC_Utils_Ex::sfGetCommaList($this->arrREVIEW_CVSCOL);

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOption($option);

        $list_data = $objQuery->select($cols, $from, $where, $arrval);

        $max = count($list_data);
        if (!isset($data)) $data = "";
        for($i = 0; $i < $max; $i++) {
            // 各項目をCSV出力用に変換する。
            $data .= $this->lfMakeReviewCSV($list_data[$i]);
        }
        return $data;
    }

    // CSV出力データを作成する。(トラックバック)
    function lfGetTrackbackCSV($where, $option, $arrval) {
        $from = "dtb_trackback AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
        $cols = SC_Utils_Ex::sfGetCommaList($this->arrTRACKBACK_CVSCOL);

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOption($option);

        $list_data = $objQuery->select($cols, $from, $where, $arrval);

        $max = count($list_data);
        if (!isset($data)) $data = "";
        for($i = 0; $i < $max; $i++) {
            // 各項目をCSV出力用に変換する。
            $data .= $this->lfMakeTrackbackCSV($list_data[$i]);
        }
        return $data;
    }

    // CSVを送信する。(カテゴリ)
    function sfDownloadCategoryCsv() {

        // CSV出力タイトル行の作成
        $arrOutput = SC_Utils_Ex::sfSwapArray($this->sfgetCsvOutput(5, 'status = 1'));
        if (count($arrOutput) <= 0) return false; // 失敗終了
        $arrOutputCols = $arrOutput['col'];

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOrder('rank DESC');

        $dataRows = $objQuery->select(
             SC_Utils_Ex::sfGetCommaList($arrOutputCols)
            ,'dtb_category'
            ,'del_flg = 0'
        );
        
        $outputArray = array();
        
        // ヘッダ行
        $outputArray[] = $arrOutput['disp_name'];
        
        // データ行
        foreach ($dataRows as $row) {
            $outputArray[] = $row;
        }
        
        // CSVを送信する。
        $this->lfDownloadCsv($outputArray, 'category');
        
        // 成功終了
        return true;
    }

    // CSV出力データを作成する。
    function lfGetCSV($from, $where, $option, $arrval, $arrCsvOutputCols = "", $arrCsvOutputConverts = array()) {

        $cols = SC_Utils_Ex::sfGetCommaList($arrCsvOutputCols);

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOption($option);

        $list_data = $objQuery->select($cols, $from, $where, $arrval, MDB2_FETCHMODE_ORDERED);

        $csv = '';
        foreach ($list_data as $row) {
            $row = SC_Utils_Ex::mbConvertKanaWithArray($row, $arrCsvOutputConverts);
            // 各項目をCSV出力用に変換する。
            $line = $this->sfArrayToCsv($row);
            $csv .= "$line\r\n";
        }
        return $csv;
    }

    // 各項目をCSV出力用に変換する。
    function lfMakeCSV($list) {
        $line = "";
		
        foreach($list as $key => $val) {
            $tmp = "";
            switch($key) {
                case 'order_pref':
                case 'deliv_pref':
                    $tmp = $this->arrPref[$val];
                    break;
                default:
                    $tmp = $val;
                    break;
            }

            $tmp = preg_replace('/[",]/', " ", $tmp);
            $line .= "\"".$tmp."\",";
        }
        // 文末の","を変換
        $line = $this->replaceLineSuffix($line);
        return $line;
    }

    // 各項目をCSV出力用に変換する。(レビュー)
    function lfMakeReviewCSV($list) {
        $line = "";

        foreach($list as $key => $val) {
            $tmp = "";
            switch($key) {
            case 'sex':
                $tmp = isset($this->arrSex[$val]) ? $this->arrSex[$val] : "";
                break;
            case 'recommend_level':
                $tmp = isset($this->arrRECOMMEND[$val]) ? $this->arrRECOMMEND[$val]
                                                        : "";
                break;
            case 'status':
                $tmp = isset($this->arrDISP[$val]) ? $this->arrDISP[$val] : "";
                break;
            default:
                $tmp = $val;
                break;
            }

            $tmp = preg_replace('/[",]/', " ", $tmp);
            $line .= "\"".$tmp."\",";
        }
        // 文末の","を変換
        $line = $this->replaceLineSuffix($line);
        return $line;
    }

    // 各項目をCSV出力用に変換する。(トラックバック)
    function lfMakeTrackbackCSV($list) {

        $line = "";

        foreach($list as $key => $val) {
            $tmp = "";
            switch($key) {
            case 'status':
                $tmp = $this->arrTrackBackStatus[$val];
                break;
            default:
                $tmp = $val;
                break;
            }

            $tmp = preg_replace('/[",]/', " ", $tmp);
            $line .= "\"".$tmp."\",";
        }
        // 文末の","を変換
        $line = $this->replaceLineSuffix($line);
        return $line;
    }

    /**
     * 行末の ',' を CRLF へ変換する.
     *
     * @access private
     * @param string $line CSV出力用の1行分の文字列
     * @return string 行末の ',' を CRLF に変換した文字列
     */
    function replaceLineSuffix($line) {
//        return mb_ereg_replace(",$", "\r\n", $line);  
        return preg_replace('/,$/',"\r\n",$line);
    }

    /**
     * 項目情報を初期化する.
     *
     * @access private
     * @return void
     */
    function init() {
        $this->arrSubnavi = array(
                                  1 => 'product',
                                  2 => 'customer',
                                  3 => 'order',
                                  4 => 'campaign',
                                  5 => 'category'
                                  );

        $this->arrSubnaviName = array(
                                      1 => '商品管理',
                                      2 => '顧客管理',
                                      3 => '受注管理',
                                      4 => 'キャンペーン',
                                      5 => 'カテゴリ'
                                      );


        $this->arrREVIEW_CVSCOL = array(
                                        'B.name',
                                        'A.status',
                                        'A.create_date',
                                        'A.reviewer_name',
                                        'A.sex',
                                        'A.recommend_level',
                                        'A.title',
                                        'A.comment'
                                        );

        $this->arrREVIEW_CVSTITLE = array(
                                          '商品名',
                                          'レビュー表示',
                                          '投稿日',
                                          '投稿者名',
                                          '性別',
                                          'おすすめレベル',
                                          'タイトル',
                                          'コメント'
                                          );

        $this->arrTRACKBACK_CVSTITLE = array(
                                             '商品名',
                                             'ブログ名',
                                             'ブログ記事タイトル',
                                             'ブログ記事内容',
                                             '状態',
                                             '投稿日'
                                             );

        $this->arrTRACKBACK_CVSCOL = array(
                                           'B.name',
                                           'A.blog_name',
                                           'A.title',
                                           'A.excerpt',
                                           'A.status',
                                           'A.create_date'
                                           );
    }
    
    /**
     * 1次元配列を1行のCSVとして返す
     * 参考: http://jp.php.net/fputcsv
     */
    function sfArrayToCsv($fields, $delimiter = ',', $enclosure = '"', $arrayDelimiter = '|') {
        
        if( strlen($delimiter) != 1 ) {
            trigger_error('delimiter must be a single character', E_USER_WARNING);
            return "";
        }
        
        if( strlen($enclosure) < 1 ) {
            trigger_error('enclosure must be a single character', E_USER_WARNING);
            return "";
        }
        
        foreach (array_keys($fields) as $key) {
            $field =& $fields[$key];
            
            // 配列を「|」区切りの文字列に変換する
            if (is_array($field)) {
                $field = implode($arrayDelimiter, $field);
            }
            
            /* enclose a field that contains a delimiter, an enclosure character, or a newline */
            if (
                   is_string($field)
                && preg_match('/[' . preg_quote($delimiter) . preg_quote($enclosure) . '\\s]/', $field)
            ) {
                $field = $enclosure . preg_replace('/' . preg_quote($enclosure) . '/', $enclosure . $enclosure, $field) . $enclosure;
            }
        }
        
        return implode($delimiter, $fields);
    }
    
    /**
     * CSVを送信する。
     */
    function lfDownloadCsv($arrayData, $prefix = ""){

        if($prefix == "") {
            $dir_name = SC_Utils::sfUpDirName();
            $file_name = $dir_name . date("ymdHis") .".csv";
        } else {
            $file_name = $prefix . date("ymdHis") .".csv";
        }

        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename=${file_name}");
        Header("Content-type: application/octet-stream; name=${file_name}");
        Header("Cache-Control: ");
        Header("Pragma: ");

        /* データを出力 */
        foreach ($arrayData as $lineArray) {
            $lineString = $this->sfArrayToCsv($lineArray);
            $lineString = mb_convert_encoding($lineString, 'SJIS-Win');
            echo $lineString . "\r\n";
        }
    }
    
    /**
     * CSVファイルを送信する。
     */
    function lfDownloadCSVFile($filepath, $prefix = "") {
        $file_name = $prefix . date("YmdHis") . ".csv";
        
        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename=${file_name}");
        Header("Content-type: application/octet-stream; name=${file_name}");
        Header("Cache-Control: ");
        Header("Pragma: ");
        
        /* データを出力 */
        // file_get_contentsはメモリマッピングも自動的に使ってくれるので高速＆省メモリ
        echo file_get_contents($filepath);
    }

    /**
     * CSVデータを取得する。
     */
    function lfGetCsv2($arrayData, $prefix = "") {

        if($prefix == "") {
            $dir_name = SC_Utils::sfUpDirName();
            $file_name = $dir_name . date("ymdHis") .".csv";
        } else {
            $file_name = $prefix . date("ymdHis") .".csv";
        }

        /* データを出力 */
        foreach ($arrayData as $lineArray) {
            $lineString = $this->sfArrayToCsv($lineArray);
            $lineString = mb_convert_encoding($lineString, 'SJIS-Win');
            $lineString .= "\r\n";
        }
        return array($file_name, $lineString);
    }
}
?>
