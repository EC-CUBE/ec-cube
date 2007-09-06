<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once("../../require.php");
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(CLASS_PATH . "batch_extends/SC_Batch_Daily_Ex.php");
require_once(CLASS_PATH . "graph/SC_GraphPie.php");
require_once(CLASS_PATH . "graph/SC_GraphLine.php");
require_once(CLASS_PATH . "graph/SC_GraphBar.php");

/**
 * 売上集計 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Total extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        // GDライブラリのインストール判定
        $this->install_GD = function_exists("gd_info") ? true : false;
        $this->tpl_mainpage = 'total/index.tpl';
        $this->tpl_subnavi = 'total/subnavi.tpl';
        $this->tpl_graphsubtitle = 'total/subtitle.tpl';
        $this->tpl_titleimage = URL_DIR.'img/title/title_sale.jpg';
        $this->tpl_mainno = 'total';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrWDAY = $masterData->getMasterData("mtb_wday");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        // ページタイトル
        $this->arrTitle[''] = "期間別集計";
        $this->arrTitle['term'] = "期間別集計";
        $this->arrTitle['products'] = "商品別集計";
        $this->arrTitle['age'] = "年代別集計";
        $this->arrTitle['job'] = "職業別集計";
        $this->arrTitle['member'] = "会員別集計";

        // キャッシュ回避のために日付を渡す
        $this->cashtime = time();
        $this->objBatch = new SC_Batch_Daily_Ex();

        // TODO エレガントじゃない...
        if (!isset($_POST['search_startyear'])) $_POST['search_startyear'] = "";
        if (!isset($_POST['search_startmonth'])) $_POST['search_startmonth'] = "";
        if (!isset($_POST['search_startday'])) $_POST['search_startday'] = "";
        if (!isset($_POST['search_endyear'])) $_POST['search_endyear'] = "";
        if (!isset($_POST['search_endmonth'])) $_POST['search_endmonth'] = "";
        if (!isset($_POST['search_endday'])) $_POST['search_endday'] = "";

        if (!isset($_POST['search_startyear_m'])) $_POST['search_startyear_m'] = "";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // 入力期間をセッションに記録する
        $this->lfSaveDateSession();

        if(isset($_GET['draw_image']) && $_GET['draw_image'] != ""){
            define('DRAW_IMAGE' , true);
        }else{
            define('DRAW_IMAGE' , false);
        }

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);
        $this->objFormParam->setParam($_GET);

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                $this->arrHidden[$key] = $val;
            }
        }

        $mode = $this->objFormParam->getValue('mode');
        switch($mode) {
        case 'pdf':
        case 'csv':
        case 'search':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            $arrRet = $this->objFormParam->getHashArray();
            // 入力エラーなし
            if (empty($this->arrErr)) {
                foreach ($arrRet as $key => $val) {
                    if($val == "") {
                        continue;
                    }
                    switch ($key) {
                    case 'search_startyear':
                        $sdate = $this->objFormParam->getValue('search_startyear') . "/" . $this->objFormParam->getValue('search_startmonth') . "/" . $this->objFormParam->getValue('search_startday');
                        break;
                    case 'search_endyear':
                        $edate = $this->objFormParam->getValue('search_endyear') . "/" . $this->objFormParam->getValue('search_endmonth') . "/" . $this->objFormParam->getValue('search_endday');
                        break;
                    case 'search_startyear_m':
                        list($sdate, $edate) = SC_Utils_Ex::sfTermMonth($this->objFormParam->getValue('search_startyear_m'), $this->objFormParam->getValue('search_startmonth_m'), CLOSE_DAY);
                        break;
                    default:
                        break;
                    }
                }
                if($this->objFormParam->getValue('type') != "") {
                    $type = $this->objFormParam->getValue('type');
                } else {
                    $type = "";
                }

                $page = $this->objFormParam->getValue('page');

                switch($page) {
                    // 商品別集計
                case 'products':
                    if($type == "") {
                        $type = 'all';
                    }
                    $this->tpl_page_type = "total/page_products.tpl";
                    // 未集計データの集計を行う
                    if(!DAILY_BATCH_MODE) {
                        $this->objBatch->lfRealTimeDailyTotal($sdate, $edate);
                    }
                    // 検索結果の取得
                    $this->lfGetOrderProducts($type, $sdate, $edate, $this, $this->install_GD, $mode);
                    break;
                    // 職業別集計
                case 'job':
                    if($type == "") {
                        $type = 'all';
                    }
                    $this->tpl_page_type = "total/page_job.tpl";
                    // 未集計データの集計を行う
                    if(!DAILY_BATCH_MODE) {
                        $this->objBatch->lfRealTimeDailyTotal($sdate, $edate);
                    }
                    // 検索結果の取得
                    $this->lfGetOrderJob($type, $sdate, $edate, $this, $this->install_GD);
                    break;
                    // 会員別集計
                case 'member':
                    if($type == "") {
                        $type = 'all';
                    }
                    $this->tpl_page_type = "total/page_member.tpl";
                    // 未集計データの集計を行う
                    if(!DAILY_BATCH_MODE) {
                        $this->objBatch->lfRealTimeDailyTotal($sdate, $edate);
                    }
                    // 検索結果の取得
                    $this->lfGetOrderMember($type, $sdate, $edate, $this, $this->install_GD);
                    break;
                    // 年代別集計
                case 'age':
                    if($type == "") {
                        $type = 'all';
                    }

                    $this->tpl_page_type = "total/page_age.tpl";
                    // 未集計データの集計を行う
                    if(!DAILY_BATCH_MODE) {
                        $this->objBatch->lfRealTimeDailyTotal($sdate, $edate);
                    }
                    // 検索結果の取得
                    $this->lfGetOrderAge($type, $sdate, $edate, $this, $this->install_GD);
                    break;
                    // 期間別集計
                default:
                    if (!isset($type)) $type = "";
                    if($type == "") {
                        $type = 'day';
                    }
                    $this->tpl_page_type = "total/page_term.tpl";
                    // 未集計データの集計を行う
                    if(!DAILY_BATCH_MODE) {
                        $this->objBatch->lfRealTimeDailyTotal($sdate, $edate);
                    }
                    // 検索結果の取得
                    $this->lfGetOrderTerm($type, $sdate, $edate, $this, $this->install_GD);

                    break;
                }

                if($mode == 'csv') {
                    // CSV出力タイトル行の取得
                    list($arrTitleCol, $arrDataCol) = $this->lfGetCSVColum($page, $this->keyname);
                    $head = SC_Utils_Ex::sfGetCSVList($arrTitleCol);
                    $data = $this->lfGetDataColCSV($this->arrResults, $arrDataCol);
                    // CSVを送信する。
                    SC_Utils_Ex::sfCSVDownload($head.$data, $page."_".$type);
                    exit;
                }

                if($mode == 'pdf') {
                    // CSV出力タイトル行の取得
                    list($arrTitleCol, $arrDataCol, $arrColSize, $arrAlign, $title) = $this->lfGetPDFColum($page, $type, $this->keyname);
                    $head = sfGetPDFList($arrTitleCol);
                    $data = lfGetDataColPDF($this->arrResults, $arrDataCol, 40);
                    // PDF出力用
                    $graph_name = basename($this->tpl_image);
                    $this->lfPDFDownload($graph_name, $head . $data, $arrColSize, $arrAlign, $sdate, $edate, $title, $page);
                    exit;
                }
            }
            break;
        default:
            if(count($_GET) == 0) {
                // バッチモードの場合のみ実行する（当日の集計を行うため）
                if(DAILY_BATCH_MODE) {
                    // 3日前までの集計
                    $this->objBatch->lfStartDailyTotal(3,0);
                }
            }
            break;
        }

        // 登録・更新日検索用
        $objDate = new SC_Date();
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();

        $this->tpl_subtitle = $this->arrTitle[$this->objFormParam->getValue('page')];
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* PDF出力 */
    function lfPDFDownload($image, $table, $arrColSize, $arrAlign, $sdate, $edate, $title, $page = "") {

        $objPdf = new SC_Pdf();
        $objPdf->setTableColor("CCCCCC", "F0F0F0", "D1DEFE");

        // 土台となるPDFファイルの指定
        $objPdf->setTemplate(PDF_DIR . "total.pdf");

        $disp_sdate = sfDispDBDate($sdate, false);
        $disp_edate = sfDispDBDate($edate, false);

        $arrText['title_block'] = $title;
        $arrText['date_block'] = "$disp_sdate-$disp_edate";
        $arrImage['graph_block'] = GRAPH_DIR . $image;

        // 文末の\nを削除する
        $table = ereg_replace("\n$", "", $table);
        $arrRet = split("\n", $table);
        $page_max = intval((count($arrRet) / 35) + 1);

        for($page = 1; $page <= $page_max; $page++) {
            if($page > 1) {
                // 2ページ以降
                $start_no = 35 * ($page - 1) + 1;
            } else {
                // 開始ページ
                $start_no = 1;
            }

            $arrText['page_block'] = $page . " / " . $page_max;
            $objPdf->setTextBlock($arrText);
            $objPdf->setImageBlock($arrImage);
            // ブロック値の入力
            $objPdf->writeBlock();
            // 最終ページのみ、商品別集計は合計がないので最終行の色を変更しない。
            if($page == $page_max && $page != 'products') {
                $last_color_flg = true;
            } else {
                $last_color_flg = false;
            }
            $objPdf->writeTableCenter($table, 500, $arrColSize, $arrAlign, 35, $start_no, $last_color_flg);
            $objPdf->closePage();
        }

        // PDFの出力
        $objPdf->output();
    }

    /* セッションに入力期間を記録する */
    function lfSaveDateSession() {
        if (!isset($_POST['form'])) $_POST['form'] = "";

        if($_POST['form'] == 1) {
            $_SESSION['total']['startyear_m'] = $_POST['search_startyear_m'];
            $_SESSION['total']['startmonth_m'] = $_POST['search_startmonth_m'];
        }

        if($_POST['form'] == 2) {
            $_SESSION['total']['startyear'] = $_POST['search_startyear'];
            $_SESSION['total']['startmonth'] = $_POST['search_startmonth'];
            $_SESSION['total']['startday'] = $_POST['search_startday'];
            $_SESSION['total']['endyear'] = $_POST['search_endyear'];
            $_SESSION['total']['endmonth'] = $_POST['search_endmonth'];
            $_SESSION['total']['endday'] = $_POST['search_endday'];
        }
    }

    /* デフォルト値の取得 */
    function lfGetDateDefault() {
        $year = date("Y");
        $month = date("m");
        $day = date("d");

        $list = isset($_SESSION['total']) ? $_SESSION['total'] : "";

        // セッション情報に開始月度が保存されていない。
        if(empty($_SESSION['total']['startyear_m'])) {
            $list['startyear_m'] = $year;
            $list['startmonth_m'] = $month;
        }

        // セッション情報に開始日付、終了日付が保存されていない。
        if(empty($_SESSION['total']['startyear']) && empty($_SESSION['total']['endyear'])) {
            $list['startyear'] = $year;
            $list['startmonth'] = $month;
            $list['startday'] = $day;
            $list['endyear'] = $year;
            $list['endmonth'] = $month;
            $list['endday'] = $day;
        }

        return $list;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        // デフォルト値の取得
        $arrList = $this->lfGetDateDefault();

        // 月度集計
        $this->objFormParam->addParam("月度", "search_startyear_m", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear_m']);
        $this->objFormParam->addParam("月度", "search_startmonth_m", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth_m']);
        // 期間集計
        $this->objFormParam->addParam("開始日", "search_startyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear']);
        $this->objFormParam->addParam("開始日", "search_startmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth']);
        $this->objFormParam->addParam("開始日", "search_startday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startday']);
        $this->objFormParam->addParam("終了日", "search_endyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endyear']);
        $this->objFormParam->addParam("終了日", "search_endmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endmonth']);
        $this->objFormParam->addParam("終了日", "search_endday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endday']);

        // hiddenデータの取得用
        $this->objFormParam->addParam("", "page");
        $this->objFormParam->addParam("", "type");
        $this->objFormParam->addParam("", "mode");

    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 特殊項目チェック
        if($_POST['form'] == 1) {
            $objErr->doFunc(array("月度", "search_startyear_m"), array("ONE_EXIST_CHECK"));
        }

        if($_POST['form'] == 2) {
            $objErr->doFunc(array("期間", "search_startyear", "search_endyear"), array("ONE_EXIST_CHECK"));
        }

        $objErr->doFunc(array("月度", "search_startyear_m", "search_startmonth_m"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
        return $objErr->arrErr;
    }

    /* 折れ線グラフの作成 */
    function lfGetGraphLine($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {

        $ret_path = "";

        // 結果が0行以上ある場合のみグラフを生成する。
        if(count($arrResults) > 0) {

            // グラフの生成
            $arrList = SC_Utils_Ex::sfArrKeyValue($arrResults, $keyname, "total");

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);

            $path = GRAPH_DIR . $pngname;

            // ラベル表示インターバルを求める
            $interval = intval(count($arrList) / 20);
            if($interval < 1) {
                $interval = 1;
            }
            $objGraphPie = new SC_GraphPie();
            $objGraphLine = new SC_GraphLine();

            // 値のセット
            $objGraphLine->setData($arrList);
            $objGraphLine->setXLabel(array_keys($arrList));

            // ラベル回転(日本語不可)
            if($keyname == "key_day"){
                $objGraphLine->setXLabelAngle(45);
            }

            // タイトルセット
            $objGraphLine->setXTitle($xtitle);
            $objGraphLine->setYTitle($ytitle);

            // メインタイトル作成
            list($sy, $sm, $sd) = split("[/ ]" , $sdate);
            list($ey, $em, $ed) = split("[/ ]" , $edate);
            $start_date = $sy . "年" . $sm . "月" . $sd . "日";
            $end_date = $ey . "年" . $em . "月" . $ed . "日";
            $objGraphLine->drawTitle("集計期間：" . $start_date . " - " . $end_date);

            // グラフ描画
            $objGraphLine->drawGraph();

            // グラフの出力
            if(DRAW_IMAGE){
                $objGraphLine->outputGraph();
                exit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URL . $pngname;
        }
        return $ret_path;
    }

    // 円グラフの作成
    function lfGetGraphPie($arrResults, $keyname, $type, $title = "", $sdate = "", $edate = "") {

        $ret_path = "";
        // 結果が0行以上ある場合のみグラフを生成する。
        if(count($arrResults) > 0) {
            // グラフの生成
            $arrList = SC_Utils_Ex::sfArrKeyValue($arrResults, $keyname,
                                                  "total", GRAPH_PIE_MAX,
                                                  GRAPH_LABEL_MAX);

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);
            $path = GRAPH_DIR . $pngname;

            $objGraphPie = new SC_GraphPie();

            /* デバッグ表示用 by naka
             foreach($arrList as $key => $val) {
             $objGraphPie->debugPrint("key:$key val:$val");
             }
            */

            // データをセットする
            $objGraphPie->setData($arrList);
            // 凡例をセットする
            $objGraphPie->setLegend(array_keys($arrList));

            // メインタイトル作成
            list($sy, $sm, $sd) = split("[/ ]" , $sdate);
            list($ey, $em, $ed) = split("[/ ]" , $edate);
            $start_date = $sy . "年" . $sm . "月" . $sd . "日";
            $end_date = $ey . "年" . $em . "月" . $ed . "日";
            $objGraphPie->drawTitle("集計期間：" . $start_date . " - " . $end_date);

            // 円グラフ描画
            $objGraphPie->drawGraph();

            // グラフの出力
            if(DRAW_IMAGE){
                $objGraphPie->outputGraph();
                exit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URL . $pngname;
        }
        return $ret_path;
    }

    // 棒グラフの作成
    function lfGetGraphBar($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {
        $ret_path = "";

        // 結果が0行以上ある場合のみグラフを生成する。
        if(count($arrResults) > 0) {
            // グラフの生成
            $arrList = SC_Utils_Ex::sfArrKeyValue($arrResults, $keyname, "total", GRAPH_PIE_MAX, GRAPH_LABEL_MAX);

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);
            $path = GRAPH_DIR . $pngname;

            $objGraphBar = new SC_GraphBar();

            foreach(array_keys($arrList) as $val) {
                $arrKey[] = ereg_replace("〜", "-", $val);
            }

            // グラフ描画
            $objGraphBar->setXLabel($arrKey);
            $objGraphBar->setXTitle($xtitle);
            $objGraphBar->setYTitle($ytitle);
            $objGraphBar->setData($arrList);

            // メインタイトル作成
            $arrKey = array_keys($arrList);
            list($sy, $sm, $sd) = split("[/ ]" , $sdate);
            list($ey, $em, $ed) = split("[/ ]" , $edate);
            $start_date = $sy . "年" . $sm . "月" . $sd . "日";
            $end_date = $ey . "年" . $em . "月" . $ed . "日";
            $objGraphBar->drawTitle("集計期間：" . $start_date . " - " . $end_date);

            $objGraphBar->drawGraph();

            if(DRAW_IMAGE){
                $objGraphBar->outputGraph();
                exit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URL . $pngname;
        }
        return $ret_path;
    }

    // グラフ用のPNGファイル名
    function lfGetGraphPng($keyname) {

        if($_POST['search_startyear_m'] != "") {
            $pngname = sprintf("%s_%02d%02d.png", $keyname, substr($_POST['search_startyear_m'],2), $_POST['search_startmonth_m']);
        } else {
            $pngname = sprintf("%s_%02d%02d%02d_%02d%02d%02d.png", $keyname, substr($_POST['search_startyear'], 2), $_POST['search_startmonth'], $_POST['search_startday'], substr($_POST['search_endyear'],2), $_POST['search_endmonth'], $_POST['search_endday']);
        }
        return $pngname;
    }

    // 会員、非会員集計のWHERE分の作成
    function lfGetWhereMember($col_date, $sdate, $edate, $type, $col_member = "customer_id") {
        $where = "";
        // 取得日付の指定
        if($sdate != "") {
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " $col_date >= '". $sdate ."'";
        }

        if($edate != "") {
            if ($where != "") {
                $where.= " AND ";
            }
            $edate = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));
            $where.= " $col_date < date('" . $edate ."')";
        }

        // 会員、非会員の判定
        switch($type) {
            // 全体
        case 'all':
            break;
        case 'member':
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " $col_member <> 0";
            break;
        case 'nonmember':
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " $col_member = 0";
            break;
        default:
            break;
        }

        return array($where, array());
    }

    /** 会員別集計 **/
    function lfGetOrderMember($type, $sdate, $edate, &$objPage, $graph = true) {
        list($where, $arrval) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);

        // 会員集計の取得
        $col = "COUNT(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average, order_sex";
        $from = "dtb_order";
        $objQuery = new SC_Query();
        $objQuery->setGroupBy("order_sex");

        $tmp_where = $where . " AND customer_id <> 0 AND del_flg = 0 ";
        $arrRet = $objQuery->select($col, $from, $tmp_where, $arrval);

        // 会員購入であることを記録する。
        $max = count($arrRet);
        for($i = 0; $i < $max; $i++) {
            $arrRet[$i]['member_name'] = '会員'.$this->arrSex[$arrRet[$i]['order_sex']];
        }
        $objPage->arrResults = $arrRet;

        // 非会員集計の取得
        $tmp_where = $where . " AND customer_id = 0 AND del_flg = 0 ";
        $arrRet = $objQuery->select($col, $from, $tmp_where, $arrval);
        // 非会員購入であることを記録する。
        $max = count($arrRet);
        for($i = 0; $i < $max; $i++) {
            $arrRet[$i]['member_name'] = '非会員'.$this->arrSex[$arrRet[$i]['order_sex']];
        }

        $objPage->arrResults = array_merge($objPage->arrResults, $arrRet);

        // 円グラフの生成
        if($graph) {
            $image_key = "member";
            $objPage->tpl_image = $this->lfGetGraphPie($objPage->arrResults, "member_name", $image_key, "(売上比率)", $sdate, $edate);
        }
    }

    /** 商品別集計 **/
    function lfGetOrderProducts($type, $sdate, $edate, &$objPage, $graph = true, $mode = "") {
        list($where, $arrval) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);

        $where .= " and del_flg=0 and status <> " . ORDER_CANCEL;

        $sql = "SELECT T1.product_id, T1.product_code, T1.product_name as name, T1.products_count, T1.order_count, T1.price, T1.total ";
        $sql.= "FROM ( ";
        $sql.= "SELECT product_id, product_name, product_code, price, ";
        $sql.= "COUNT(*) AS order_count, ";
        $sql.= "SUM(quantity) AS products_count, ";
        $sql.= "(price * sum(quantity)) AS total ";
        $sql.= "FROM dtb_order_detail WHERE order_id IN (SELECT order_id FROM dtb_order WHERE $where ) ";
        $sql.= "GROUP BY product_id, product_name, product_code, price ";
        $sql.= ") AS T1 ";
        $sql.= "ORDER BY T1.total DESC ";

        if($mode != "csv") {
            $sql.= "LIMIT " . PRODUCTS_TOTAL_MAX;
        }

        $objQuery = new SC_Query();
        $objPage->arrResults = $objQuery->getall($sql, $arrval);

        // 円グラフの生成
        if($graph) {
            $image_key = "products_" . $type;
            $objPage->tpl_image = $this->lfGetGraphPie($objPage->arrResults, "name", $image_key, "(売上比率)", $sdate, $edate);
        }
    }

    /** 職業別集計 **/
    function lfGetOrderJob($type, $sdate, $edate, &$objPage, $graph = true) {
        global $arrJob;

        list($where, $arrval) = $this->lfGetWhereMember('T2.create_date', $sdate, $edate, $type);

        $sql = "SELECT job, count(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average ";
        $sql.= "FROM dtb_customer AS T1 LEFT JOIN dtb_order AS T2 USING ( customer_id ) WHERE $where AND T2.del_flg = 0 and T2.status <> " . ORDER_CANCEL;
        $sql.= " GROUP BY job ORDER BY total DESC";

        $objQuery = new SC_Query();
        $objPage->arrResults = $objQuery->getall($sql, $arrval);

        $max = count($objPage->arrResults);
        for($i = 0; $i < $max; $i++) {
            $job_key = $objPage->arrResults[$i]['job'];
            if($job_key != "") {
                $objPage->arrResults[$i]['job_name'] = $arrJob[$job_key];
            } else {
                $objPage->arrResults[$i]['job_name'] = "未回答";
            }
        }

        // 円グラフの生成
        if($graph) {
            $image_key = "job_" . $type;
            $objPage->tpl_image = $this->lfGetGraphPie($objPage->arrResults, "job_name", $image_key, "(売上比率)", $sdate, $edate);
        }
    }

    /** 年代別集計 **/
    function lfGetOrderAge($type, $sdate, $edate, &$objPage, $graph = true) {

        list($where, $arrval) = $this->lfGetWhereMember('order_date', $sdate, $edate, $type, "member");

        $sql = "SELECT SUM(order_count) AS order_count, SUM(total) AS total, start_age, end_age ";
        $sql.= "FROM dtb_bat_order_daily_age WHERE $where ";
        $sql.= "GROUP BY start_age, end_age ORDER BY start_age, end_age";

        $objQuery = new SC_Query();
        $objPage->arrResults = $objQuery->getall($sql, $arrval);

        $max = count($objPage->arrResults);
        for($i = 0; $i < $max; $i++) {
            if($objPage->arrResults[$i]['order_count'] > 0) {
                $objPage->arrResults[$i]['total_average'] = intval($objPage->arrResults[$i]['total'] / $objPage->arrResults[$i]['order_count']);
            }
            $start_age = $objPage->arrResults[$i]['start_age'];
            $end_age = $objPage->arrResults[$i]['end_age'];
            if($start_age != "" || $end_age != "") {
                if($end_age != 999) {
                    $objPage->arrResults[$i]['age_name'] = $start_age . "〜" . $end_age . "歳";
                } else {
                    $objPage->arrResults[$i]['age_name'] = $start_age . "歳〜";
                }
            } else {
                $objPage->arrResults[$i]['age_name'] = "未回答";
            }
        }

        // 棒グラフの生成
        if($graph) {
            $image_key = "age_" . $type;
            $xtitle = "(年齢)";
            $ytitle = "(売上合計)";
            $objPage->tpl_image = $this->lfGetGraphBar($objPage->arrResults, "age_name", $image_key, $xtitle, $ytitle, $sdate, $edate);
        }
    }

    /** 期間別集計 **/
    function lfGetOrderTerm($type, $sdate, $edate, &$objPage, $graph = true) {

        $tmp_col = "sum(total_order) as total_order, sum(men) as men, sum(women) as women,";
        $tmp_col.= "sum(men_member) as men_member, sum(men_nonmember) as men_nonmember,";
        $tmp_col.= "sum(women_member) as women_member, sum(women_nonmember) as women_nonmember,";
        $tmp_col.= "sum(total) as total, (avg(total_average)) as total_average";
        $objQuery = new SC_Query();

        switch($type) {
            // 月別
        case 'month':
            $col = $tmp_col . ",key_month";
            $objQuery->setgroupby("key_month");
            $objQuery->setOrder("key_month");
            $objPage->keyname = "key_month";
            $objPage->tpl_tail = "月";
            $from = "dtb_bat_order_daily";
            $xtitle = "(月別)";
            $ytitle = "(売上合計)";
            break;
            // 年別
        case 'year':
            $col = $tmp_col . ",key_year";
            $objQuery->setgroupby("key_year");
            $objQuery->setOrder("key_year");
            $objPage->keyname = "key_year";
            $objPage->tpl_tail = "年";
            $from = "dtb_bat_order_daily";
            $xtitle = "(年別)";
            $ytitle = "(売上合計)";
            break;
            // 曜日別
        case 'wday':
            $col = $tmp_col . ",key_wday, wday";
            $objQuery->setgroupby("key_wday, wday");
            $objQuery->setOrder("wday");
            $objPage->keyname = "key_wday";
            $objPage->tpl_tail = "曜日";
            $from = "dtb_bat_order_daily";
            $xtitle = "(曜日別)";
            $ytitle = "(売上合計)";
            break;
            // 時間別
        case 'hour':
            $col = $tmp_col . ",hour";
            $objQuery->setgroupby("hour");
            $objQuery->setOrder("hour");
            $objPage->keyname = "hour";
            $objPage->tpl_tail = "時";
            $from = "dtb_bat_order_daily_hour";
            $xtitle = "(時間別)";
            $ytitle = "(売上合計)";
            break;
        default:
            $col = "*";
            $objQuery->setOrder("key_day");
            $objPage->keyname = "key_day";
            $from = "dtb_bat_order_daily";
            $xtitle = "(日別)";
            $ytitle = "(売上合計)";
            break;
        }

        if (!isset($where)) $where = "";

        // 取得日付の指定
        if($sdate != "") {
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " order_date >= '". $sdate ."'";
        }

        if($edate != "") {
            if ($where != "") {
                $where.= " AND ";
            }
            $edate_next = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));
            $where.= " order_date < date('" . $edate_next ."')";
        }

        if (!isset($arrval)) $arrval = array();

        // 検索結果の取得
        $objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);

        // 折れ線グラフの生成
        if($graph) {
            $image_key = "term_" . $type;
            $objPage->tpl_image = $this->lfGetGraphLine($objPage->arrResults, $objPage->keyname, $image_key, $xtitle, $ytitle, $sdate, $edate);
        }

        // 検索結果が0でない場合
        if(count($objPage->arrResults) > 0) {
            // 最終集計行取得する
            $col = $tmp_col;
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select($col, $from, $where, $arrval);
            $arrRet[0][$objPage->keyname] = "合計";
            $objPage->arrResults[] = $arrRet[0];
        }

        // 平均値の計算
        $max = count($objPage->arrResults);
        for($i = 0; $i < $max; $i++) {
            if($objPage->arrResults[$i]['total_order'] > 0) {
                $objPage->arrResults[$i]['total_average'] = intval($objPage->arrResults[$i]['total'] / $objPage->arrResults[$i]['total_order']);
            }
        }
    }
}
?>
