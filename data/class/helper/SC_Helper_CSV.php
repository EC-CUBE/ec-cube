<?php
  /*
   * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrRECOMMEND = $masterData->getMasterData("mtb_recommend");
    }

    // }}}
    // {{{ functions


    // CSV出力データを作成する。(商品)
    function lfGetProductsCSV($where, $option, $arrval, $arrOutputCols) {
        $objDb = new SC_Helper_DB_Ex();

        $from = "vw_product_class AS prdcls";
        $cols = SC_Utils_Ex::sfGetCommaList($arrOutputCols);

        $objQuery = new SC_Query();
        $objQuery->setoption($option);

        $list_data = $objQuery->select($cols, $from, $where, $arrval);
        $max = count($list_data);

        // 規格分類名一覧
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");

        for($i = 0; $i < $max; $i++) {
            // 関連商品情報の付与
            $list_data[$i]['classcategory_id1'] = $arrClassCatName[$list_data[$i]['classcategory_id1']];
            $list_data[$i]['classcategory_id2'] = $arrClassCatName[$list_data[$i]['classcategory_id2']];

            // 各項目をCSV出力用に変換する。
            if (!isset($data)) $data = "";
            $data .= $this->lfMakeProductsCSV($list_data[$i]);
        }
        return $data;
    }

    // CSV出力データを作成する。(レビュー)
    function lfGetReviewCSV($where, $option, $arrval) {

        $from = "dtb_review AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
        $cols = SC_Utils_Ex::sfGetCommaList($this->arrREVIEW_CVSCOL);

        $objQuery = new SC_Query();
        $objQuery->setoption($option);

        $list_data = $objQuery->select($cols, $from, $where, $arrval);

        $max = count($list_data);
        for($i = 0; $i < $max; $i++) {
            // 各項目をCSV出力用に変換する。
            if (!isset($data)) $data = "";
            $data .= $this->lfMakeReviewCSV($list_data[$i]);
        }
        return $data;
    }

    // CSV出力データを作成する。(トラックバック)
    function lfGetTrackbackCSV($where, $option, $arrval) {
        $from = "dtb_trackback AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
        $cols = SC_Utils_Ex::sfGetCommaList($this->arrTRACKBACK_CVSCOL);

        $objQuery = new SC_Query();
        $objQuery->setoption($option);

        $list_data = $objQuery->select($cols, $from, $where, $arrval);

        $max = count($list_data);
        for($i = 0; $i < $max; $i++) {
            // 各項目をCSV出力用に変換する。
            if (!isset($data)) $data = "";
            $data .= $this->lfMakeTrackbackCSV($list_data[$i]);
        }
        return $data;
    }

    // 各項目をCSV出力用に変換する。(商品)
    function lfMakeProductsCSV($list) {
        $line = "";
        if(is_array($list)) {
            foreach($list as $key => $val) {
                $tmp = "";
                switch($key) {
                case 'point_rate':
                    if($val == "") {
                        $tmp = '0';
                    } else {
                        $tmp = $val;
                    }
                    break;
                default:
                    $tmp = $val;
                    break;
                }

                $tmp = str_replace("\"", "\\\"", $tmp);
                $line .= "\"".$tmp."\",";
            }
            // 文末の","を変換
            $line = ereg_replace(",$", "\n", $line);
        }
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

            $tmp = ereg_replace("[\",]", " ", $tmp);
            $line .= "\"".$tmp."\",";
        }
        // 文末の","を変換
        $line = ereg_replace(",$", "\n", $line);
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

            $tmp = ereg_replace("[\",]", " ", $tmp);
            $line .= "\"".$tmp."\",";
        }
        // 文末の","を変換
        $line = ereg_replace(",$", "\n", $line);
        return $line;
    }

    /**
     * CSV出力項目を取得する.
     *
     * @param integer $csv_id CSV ID
     * @param string $where SQL の WHERE 句
     * @param array $arrVal WHERE 句の要素
     * @return array CSV 出力項目の配列
     */
    function sfgetCsvOutput($csv_id = "", $where = "", $arrVal = array()){
        $objQuery = new SC_Query();
        $arrData = array();
        $ret = array();

        $sql = "";
        $sql .= " SELECT ";
        $sql .= "     no, ";
        $sql .= "     csv_id, ";
        $sql .= "     col, ";
        $sql .= "     disp_name, ";
        $sql .= "     rank, ";
        $sql .= "     status, ";
        $sql .= "     create_date, ";
        $sql .= "     update_date ";
        $sql .= " FROM ";
        $sql .= "     dtb_csv ";

        if ($where != "") {
            $sql .= $where;
            $arrData = $arrVal;
        }elseif($csv_id != ""){
            $sql .= " WHERE csv_id = ? ";
            $arrData = array($csv_id);
        }

        $sql .= " ORDER BY ";
        $sql .= "     rank , no";
        $sql .= " ";

        $ret = $objQuery->getall($sql, $arrData);

        return $ret;
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
                                  4 => 'campaign'
                                  );

        $this->arrSubnaviName = array(
                                      1 => '商品管理',
                                      2 => '顧客管理',
                                      3 => '受注管理',
                                      4 => 'キャンペーン'
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
}
?>
