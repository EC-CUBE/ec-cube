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

namespace Eccube\Page\Admin\Total;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Graph\BarGraph;
use Eccube\Framework\Graph\LineGraph;
use Eccube\Framework\Graph\PieGraph;
use Eccube\Framework\Util\Utils;

/**
 * 売上集計 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        // GDライブラリのインストール判定
        $this->install_GD = function_exists('gd_info') ? true : false;
        $this->tpl_mainpage         = 'total/index.tpl';
        $this->tpl_graphsubtitle    = 'total/subtitle.tpl';
        $this->tpl_titleimage       = ROOT_URLPATH.'img/title/title_sale.jpg';
        $this->tpl_maintitle = '売上集計';
        $this->tpl_mainno           = 'total';

        $masterData                 = Application::alias('eccube.db.master_data');
        $this->arrWDAY              = $masterData->getMasterData('mtb_wday');
        $this->arrSex               = $masterData->getMasterData('mtb_sex');
        $this->arrJob               = $masterData->getMasterData('mtb_job');

        // 登録・更新日検索用
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date');
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrYear              = $objDate->getYear();
        $this->arrMonth             = $objDate->getMonth();
        $this->arrDay               = $objDate->getDay();

        // ページタイトル todo あとでなおす
        $this->arrTitle['']         = '期間別集計';
        $this->arrTitle['term']     = '期間別集計';
        $this->arrTitle['products'] = '商品別集計';
        $this->arrTitle['age']      = '年代別集計';
        $this->arrTitle['job']      = '職業別集計';
        $this->arrTitle['member']   = '会員別集計';

        // 月度集計のkey名
        $this->arrSearchForm1       = array('search_startyear_m', 'search_startmonth_m');

        // 期間別集計のkey名
        $this->arrSearchForm2 = array(
            'search_startyear',
            'search_startmonth',
            'search_startday',
            'search_endyear',
            'search_endmonth',
            'search_endday',
        );
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
        if (isset($_GET['draw_image']) && $_GET['draw_image'] != '') {
            define('DRAW_IMAGE', true);
        } else {
            define('DRAW_IMAGE', false);
        }

        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);

        // 検索ワードの引き継ぎ
        $this->arrHidden = $objFormParam->getSearchArray();

        switch ($this->getMode()) {
            case 'csv':
            case 'search':

                $this->arrErr = $this->lfCheckError($objFormParam);
                if (empty($this->arrErr)) {
                    // 日付
                    list($sdate, $edate) = $this->lfSetStartEndDate($objFormParam);

                    // ページ
                    $page = ($objFormParam->getValue('page')) ? $objFormParam->getValue('page') : 'term';

                    // 集計種類
                    $type = ($objFormParam->getValue('type')) ? $objFormParam->getValue('type'): 'all';

                    $this->tpl_page_type = 'total/page_'. $page .'.tpl';
                    // FIXME 可読性が低いので call_user_func_array を使わない (またはメソッド名を1つの定数値とする) 実装に。
                    list($this->arrResults, $this->tpl_image) = call_user_func_array(array($this, 'lfGetOrder'.$page),
                                                                                     array($type, $sdate, $edate));
                    if ($this->getMode() == 'csv') {
                        // CSV出力タイトル行の取得
                        list($arrTitleCol, $arrDataCol) = $this->lfGetCSVColum($page);
                        $head = Utils::sfGetCSVList($arrTitleCol);
                        $data = $this->lfGetDataColCSV($this->arrResults, $arrDataCol);

                        // CSVを送信する。
                        list($fime_name, $data) = Utils::sfGetCSVData($head.$data);

                        $this->sendResponseCSV($fime_name, $data);
                        Application::alias('eccube.response')->actionExit();
                    }
                }
                break;
            default:
                break;
        }

        // 画面宣しても日付が保存される
        $_SESSION           = $this->lfSaveDateSession($_SESSION, $this->arrHidden);
        $objFormParam->setParam($_SESSION['total']);
        // 入力値の取得
        $this->arrForm      = $objFormParam->getFormParamList();
        $this->tpl_subtitle = $this->arrTitle[$objFormParam->getValue('page')];
    }

    /* デフォルト値の取得 */
    public function lfGetDateDefault()
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $list = isset($_SESSION['total']) ? $_SESSION['total'] : '';

        // セッション情報に開始月度が保存されていない。
        if (empty($_SESSION['total']['startyear_m'])) {
            $list['startyear_m'] = $year;
            $list['startmonth_m'] = $month;
        }

        // セッション情報に開始日付、終了日付が保存されていない。
        if (empty($_SESSION['total']['startyear']) && empty($_SESSION['total']['endyear'])) {
            $list['startyear'] = $year;
            $list['startmonth'] = $month;
            $list['startday'] = $day;
            $list['endyear'] = $year;
            $list['endmonth'] = $month;
            $list['endday'] = $day;
        }

        return $list;
    }

    /* パラメーター情報の初期化 */

    /**
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        // デフォルト値の取得
        $arrList = $this->lfGetDateDefault();

        // 月度集計
        $objFormParam->addParam('月度(年)', 'search_startyear_m', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['startyear_m']);
        $objFormParam->addParam('月度(月)', 'search_startmonth_m', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['startmonth_m']);
        // 期間集計
        $objFormParam->addParam('期間(開始日)', 'search_startyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['startyear']);
        $objFormParam->addParam('期間(開始日)', 'search_startmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['startmonth']);
        $objFormParam->addParam('期間(開始日)', 'search_startday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['startday']);
        $objFormParam->addParam('期間(終了日)', 'search_endyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['endyear']);
        $objFormParam->addParam('期間(終了日)', 'search_endmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['endmonth']);
        $objFormParam->addParam('期間(終了日)', 'search_endday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), $arrList['endday']);

        // hiddenデータの取得用
        $objFormParam->addParam('', 'page');
        $objFormParam->addParam('', 'type');
        $objFormParam->addParam('', 'mode');
        $objFormParam->addParam('', 'search_form');
    }

    /* 入力内容のチェック */

    /**
     * @param FormParam $objFormParam
     */
    public function lfCheckError(&$objFormParam)
    {
        $objFormParam->convParam();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        // 特殊項目チェック

        // 月度集計
        if ($objFormParam->getValue('search_form') == 1) {
            $objErr->doFunc(array('月度', 'search_startyear_m', 'search_startmonth_m'), array('FULL_EXIST_CHECK'));
        }

        // 期間集計
        if ($objFormParam->getValue('search_form') == 2) {
            $objErr->doFunc(array('期間(開始日)', 'search_startyear', 'search_startmonth', 'search_startday'), array('FULL_EXIST_CHECK'));
            $objErr->doFunc(array('期間(終了日)', 'search_endyear', 'search_endmonth', 'search_endday'), array('FULL_EXIST_CHECK'));
            $objErr->doFunc(array('期間(開始日)', 'search_startyear', 'search_startmonth', 'search_startday'), array('CHECK_DATE'));
            $objErr->doFunc(array('期間(終了日)', 'search_endyear', 'search_endmonth', 'search_endday'), array('CHECK_DATE'));
            $objErr->doFunc(array('期間(開始日)', '期間(終了日)', 'search_startyear', 'search_startmonth', 'search_startday', 'search_endyear', 'search_endmonth', 'search_endday'), array('CHECK_SET_TERM'));
        }

        return $objErr->arrErr;
    }

    /* サブナビを移動しても日付が残るようにセッションに入力期間を記録する */
    public function lfSaveDateSession($session, $arrForm)
    {
        // session の初期化をする
        if (!isset($session['total'])) {
            $session['total'] = $this->lfGetDateInit();
        }

        if (!empty($arrForm)) {
            $session['total'] = array_merge($session['total'], $arrForm);
        }

        return $session;
    }

    /* 日付の初期値 */
    public function lfGetDateInit()
    {
        $search_startyear_m     = $search_startyear  = $search_endyear  = date('Y');
        $search_startmonth_m    = $search_startmonth = $search_endmonth = date('m');
        $search_startday        = $search_endday     = date('d');

        return compact($this->arrSearchForm1, $this->arrSearchForm2);
    }

    /* フォームで入力された日付を適切な形にする */

    /**
     * @param FormParam $objFormParam
     */
    public function lfSetStartEndDate(&$objFormParam)
    {
        $arrRet = $objFormParam->getHashArray();

        // 月度集計
        if ($arrRet['search_form'] == 1) {
            list($sdate, $edate) = Utils::sfTermMonth($arrRet['search_startyear_m'],
                                                            $arrRet['search_startmonth_m'],
                                                            CLOSE_DAY);
        // 期間集計
        } elseif ($arrRet['search_form'] == 2) {
            $sdate = $arrRet['search_startyear'] . '/' . $arrRet['search_startmonth'] . '/' . $arrRet['search_startday'];
            $edate = $arrRet['search_endyear'] . '/' . $arrRet['search_endmonth'] . '/' . $arrRet['search_endday'];
        }

        return array($sdate, $edate);
    }

    /* 折れ線グラフの作成 */

    /**
     * @param string $keyname
     * @param string $type
     * @param string $xtitle
     * @param string $ytitle
     * @param boolean $xincline
     */
    public function lfGetGraphLine($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate, $xincline)
    {
        $ret_path = '';

        // 結果が0行以上ある場合のみグラフを生成する。
        if (count($arrResults) > 0 && $this->install_GD) {
            // グラフの生成
            $arrList = Utils::sfArrKeyValue($arrResults, $keyname, 'total');

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);

            $path = GRAPH_REALDIR . $pngname;

            // ラベル表示インターバルを求める
            $interval = intval(count($arrList) / 20);
            if ($interval < 1) {
                $interval = 1;
            }

            /* @var $objGraphLine LineGraph */
            $objGraphLine = Application::alias('eccube.graph.line');;

            // 値のセット
            $objGraphLine->setData($arrList);
            $objGraphLine->setXLabel(array_keys($arrList));

            // ラベル回転(日本語不可)
            if ($xincline == true) {
                $objGraphLine->setXLabelAngle(45);
            }

            // タイトルセット
            $objGraphLine->setXTitle($xtitle);
            $objGraphLine->setYTitle($ytitle);

            // メインタイトル作成
            list($sy, $sm, $sd) = preg_split('|[/ ]|', $sdate);
            list($ey, $em, $ed) = preg_split('|[/ ]|', $edate);
            $start_date = $sy . '年' . $sm . '月' . $sd . '日';
            $end_date = $ey . '年' . $em . '月' . $ed . '日';
            $objGraphLine->drawTitle('集計期間：' . $start_date . ' - ' . $end_date);

            // グラフ描画
            $objGraphLine->drawGraph();

            // グラフの出力
            if (DRAW_IMAGE) {
                $objGraphLine->outputGraph();
                Application::alias('eccube.response')->actionExit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URLPATH . $pngname;
        }

        return $ret_path;
    }

    // 円グラフの作成

    /**
     * @param string $keyname
     * @param string $type
     */
    public function lfGetGraphPie($arrResults, $keyname, $type, $title = '', $sdate = '', $edate = '')
    {
        $ret_path = '';
        // 結果が0行以上ある場合のみグラフを生成する。
        if (count($arrResults) > 0 && $this->install_GD) {
            // グラフの生成
            $arrList = Utils::sfArrKeyValue($arrResults, $keyname,
                                                  'total', GRAPH_PIE_MAX,
                                                  GRAPH_LABEL_MAX);

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);
            $path = GRAPH_REALDIR . $pngname;

            /* @var $objGraphPie PieGraph */
            $objGraphPie = Application::alias('eccube.graph.pie');

            // データをセットする
            $objGraphPie->setData($arrList);
            // 凡例をセットする
            $objGraphPie->setLegend(array_keys($arrList));

            // メインタイトル作成
            list($sy, $sm, $sd) = preg_split('|[/ ]|', $sdate);
            list($ey, $em, $ed) = preg_split('|[/ ]|', $edate);
            $start_date = $sy . '年' . $sm . '月' . $sd . '日';
            $end_date = $ey . '年' . $em . '月' . $ed . '日';
            $objGraphPie->drawTitle('集計期間：' . $start_date . ' - ' . $end_date);

            // 円グラフ描画
            $objGraphPie->drawGraph();

            // グラフの出力
            if (DRAW_IMAGE) {
                $objGraphPie->outputGraph();
                Application::alias('eccube.response')->actionExit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URLPATH . $pngname;
        }

        return $ret_path;
    }

    // 棒グラフの作成

    /**
     * @param string $keyname
     * @param string $type
     * @param string $xtitle
     * @param string $ytitle
     */
    public function lfGetGraphBar($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate)
    {
        $ret_path = '';

        // 結果が0行以上ある場合のみグラフを生成する。
        if (count($arrResults) > 0 && $this->install_GD) {
            // グラフの生成
            $arrList = Utils::sfArrKeyValue($arrResults, $keyname, 'total', GRAPH_PIE_MAX, GRAPH_LABEL_MAX);

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);
            $path = GRAPH_REALDIR . $pngname;

            /* @var $objGraphBar BarGraph */
            $objGraphBar = Application::alias('eccube.graph.bar');

            foreach ($arrList as $key => $value) {
                $arrKey[] = preg_replace('/～/u', '-', $key);
            }

            // グラフ描画
            $objGraphBar->setXLabel($arrKey);
            $objGraphBar->setXTitle($xtitle);
            $objGraphBar->setYTitle($ytitle);
            $objGraphBar->setData($arrList);

            // メインタイトル作成
            $arrKey = array_keys($arrList);
            list($sy, $sm, $sd) = preg_split('|[/ ]|', $sdate);
            list($ey, $em, $ed) = preg_split('|[/ ]|', $edate);
            $start_date = $sy . '年' . $sm . '月' . $sd . '日';
            $end_date = $ey . '年' . $em . '月' . $ed . '日';
            $objGraphBar->drawTitle('集計期間：' . $start_date . ' - ' . $end_date);

            $objGraphBar->drawGraph();

            if (DRAW_IMAGE) {
                $objGraphBar->outputGraph();
                Application::alias('eccube.response')->actionExit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URLPATH . $pngname;
        }

        return $ret_path;
    }

    // グラフ用のPNGファイル名

    /**
     * @param string $keyname
     */
    public function lfGetGraphPng($keyname)
    {
        if ($_POST['search_startyear_m'] != '') {
            $pngname = sprintf('%s_%02d%02d.png', $keyname, substr($_POST['search_startyear_m'], 2), $_POST['search_startmonth_m']);
        } else {
            $pngname = sprintf('%s_%02d%02d%02d_%02d%02d%02d.png', $keyname, substr($_POST['search_startyear'], 2), $_POST['search_startmonth'], $_POST['search_startday'], substr($_POST['search_endyear'], 2), $_POST['search_endmonth'], $_POST['search_endday']);
        }

        return $pngname;
    }

    // 会員、非会員集計のWHERE分の作成

    /**
     * @param string $col_date
     */
    public function lfGetWhereMember($col_date, $sdate, $edate, $type, $col_member = 'customer_id')
    {
        $where = '';
        // 取得日付の指定
        if ($sdate != '') {
            if ($where != '') {
                $where.= ' AND ';
            }
            $where.= " $col_date >= '". $sdate ."'";
        }

        if ($edate != '') {
            if ($where != '') {
                $where.= ' AND ';
            }
            $edate = date('Y/m/d', strtotime('1 day', strtotime($edate)));
            $where.= " $col_date < date('" . $edate ."')";
        }

        // 会員、非会員の判定
        switch ($type) {
            // 全体
            case 'all':
                break;
            case 'member':
                if ($where != '') {
                    $where.= ' AND ';
                }
                $where.= " $col_member <> 0";
                break;
            case 'nonmember':
                if ($where != '') {
                    $where.= ' AND ';
                }
                $where.= " $col_member = 0";
                break;
            default:
                break;
        }

        return array($where, array());
    }

    /** 会員別集計 **/
    public function lfGetOrderMember($type, $sdate, $edate)
    {
        $objQuery = Application::alias('eccube.query');

        list($where, $arrWhereVal) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);
        $where .= ' AND del_flg = 0 AND status <> ?';
        $arrWhereVal[] = ORDER_CANCEL;

        // 会員集計の取得
        $col = <<< __EOS__
            COUNT(order_id) AS order_count,
            SUM(total) AS total,
            AVG(total) AS total_average,
            CASE
                WHEN customer_id <> 0 THEN 1
                ELSE 0
            END AS member,
            order_sex
__EOS__;

        $from       = 'dtb_order';

        $objQuery->setGroupBy('member, order_sex');

        $arrTotalResults = $objQuery->select($col, $from, $where, $arrWhereVal);

        foreach ($arrTotalResults as $key => $value) {
            $arrResult =& $arrTotalResults[$key];
            $member_key = $arrResult['order_sex'];
            if ($member_key != '') {
                $arrResult['member_name'] = (($arrResult['member']) ? '会員' : '非会員') . $this->arrSex[$member_key];
            } else {
                $arrResult['member_name'] = '未回答';
            }
        }

        $tpl_image = $this->lfGetGraphPie($arrTotalResults, 'member_name', 'member', '(売上比率)', $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 商品別集計 **/
    public function lfGetOrderProducts($type, $sdate, $edate)
    {
        $objQuery = Application::alias('eccube.query');

        list($where, $arrWhereVal) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);

        $where .= ' AND dtb_order.del_flg = 0 AND dtb_order.status <> ?';
        $arrWhereVal[] = ORDER_CANCEL;

        $col = <<< __EOS__
                product_id,
                product_code,
                product_name,
                SUM(quantity) AS products_count,
                COUNT(dtb_order_detail.order_id) AS order_count,
                price,
                (price * SUM(quantity)) AS total
__EOS__;

        $from = 'dtb_order_detail JOIN dtb_order ON dtb_order_detail.order_id = dtb_order.order_id';

        // FIXME グループを副問い合わせにして無駄な処理を減らす
        $objQuery->setGroupBy('product_id, product_name, product_code, price');
        $objQuery->setOrder('total DESC');
        $arrTotalResults = $objQuery->select($col, $from, $where, $arrWhereVal);

        $tpl_image  = $this->lfGetGraphPie($arrTotalResults, 'product_name', 'products_' . $type, '(売上比率)', $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 職業別集計 **/
    public function lfGetOrderJob($type, $sdate, $edate)
    {
        $objQuery = Application::alias('eccube.query');
        list($where, $arrWhereVal) = $this->lfGetWhereMember('dtb_order.create_date', $sdate, $edate, $type);

        $col = <<< __EOS__
            job,
            COUNT(order_id) AS order_count,
            SUM(total) AS total,
            AVG(total) AS total_average
__EOS__;

        $from   = 'dtb_order JOIN dtb_customer ON dtb_order.customer_id = dtb_customer.customer_id';

        $where .= ' AND dtb_order.del_flg = 0 AND dtb_order.status <> ?';
        $arrWhereVal[] = ORDER_CANCEL;

        $objQuery->setGroupBy('job');
        $objQuery->setOrder('total DESC');
        $arrTotalResults = $objQuery->select($col, $from, $where, $arrWhereVal);

        foreach ($arrTotalResults as $key => $value) {
            $arrResult =& $arrTotalResults[$key];
            $job_key = $arrResult['job'];
            if ($job_key != '') {
                $arrResult['job_name'] = $this->arrJob[$job_key];
            } else {
                $arrResult['job_name'] = '未回答';
            }

        }
        $tpl_image     = $this->lfGetGraphPie($arrTotalResults, 'job_name', 'job_' . $type, '(売上比率)', $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 年代別集計 **/
    public function lfGetOrderAge($type, $sdate, $edate)
    {
        $objQuery = Application::alias('eccube.query');

        list($where, $arrWhereVal) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);

        $dbFactory = Application::alias('eccube.db.factory');
        $col = $dbFactory->getOrderTotalAgeColSql() . ' AS age';
        $col .= ',COUNT(order_id) AS order_count';
        $col .= ',SUM(total) AS total';
        $col .= ',AVG(total) AS total_average';

        $from   = 'dtb_order';

        $where .= ' AND del_flg = 0 AND status <> ?';
        $arrWhereVal[] = ORDER_CANCEL;

        $objQuery->setGroupBy('age');
        $objQuery->setOrder('age DESC');
        $arrTotalResults = $objQuery->select($col, $from, $where, $arrWhereVal);

        foreach ($arrTotalResults as $key => $value) {
            $arrResult =& $arrTotalResults[$key];
            $age_key = $arrResult['age'];
            if ($age_key != '') {
                $arrResult['age_name'] = $arrResult['age'] . '代';
            } else {
                $arrResult['age_name'] = '未回答';
            }

        }
        $tpl_image = $this->lfGetGraphBar($arrTotalResults, 'age_name', 'age_' . $type, '(年齢)', '(売上合計)', $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 期間別集計 **/
    // todo あいだの日付埋める
    public function lfGetOrderTerm($type, $sdate, $edate)
    {
        $objQuery   = Application::alias('eccube.query');

        list($where, $arrWhereVal) = $this->lfGetWhereMember('create_date', $sdate, $edate);
        $where .= ' AND del_flg = 0 AND status <> ?';
        $arrWhereVal[] = ORDER_CANCEL;

        switch ($type) {
            case 'month':
                $xtitle = '(月別)';
                $ytitle = '(売上合計)';
                $format = '%Y-%m';
                break;
            case 'year':
                $xtitle = '(年別)';
                $ytitle = '(売上合計)';
                $format = '%Y';
                break;
            case 'wday':
                $xtitle = '(曜日別)';
                $ytitle = '(売上合計)';
                $format = '%a';
                break;
            case 'hour':
                $xtitle = '(時間別)';
                $ytitle = '(売上合計)';
                $format = '%H';
                break;
            default:
                $xtitle = '(日別)';
                $ytitle = '(売上合計)';
                $format = '%Y-%m-%d';
                $xincline = true;
                break;
        }

        $dbFactory = Application::alias('eccube.db.factory');
        // todo postgres
        $col = $dbFactory->getOrderTotalDaysWhereSql($type);

        $objQuery->setGroupBy('str_date');
        $objQuery->setOrder('str_date');
        // 検索結果の取得
        $arrTotalResults = $objQuery->select($col, 'dtb_order', $where, $arrWhereVal);

        $arrTotalResults = $this->lfAddBlankLine($arrTotalResults, $type, $sdate, $edate);
        // todo GDない場合の処理
        $tpl_image       = $this->lfGetGraphLine($arrTotalResults, 'str_date', 'term_' . $type, $xtitle, $ytitle, $sdate, $edate, $xincline);
        $arrTotalResults = $this->lfAddTotalLine($arrTotalResults);

        return array($arrTotalResults, $tpl_image);
    }

    /*
     * 期間中の日付を埋める
     */
    public function lfAddBlankLine($arrResults, $type, $st, $ed)
    {
        $arrDateList = $this->lfDateTimeArray($type, $st, $ed);

        foreach ($arrResults as $arrResult) {
            $strdate                = $arrResult['str_date'];
            $arrDateResults[$strdate] = $arrResult;
        }

        foreach ($arrDateList as $date) {
            if (array_key_exists($date, $arrDateResults)) {
                $arrRet[] = $arrDateResults[$date];
            } else {
                $arrRet[]['str_date'] = $date;
            }
        }

        return $arrRet;
    }

    /*
     * 日付の配列を作成する
     *
     */
    public function lfDateTimeArray($type, $st, $ed)
    {
        switch ($type) {
            case 'month':
                $format        = 'Y-m';
                break;
            case 'year':
                $format        = 'Y';
                break;
            case 'wday':
                $format        = 'D';
                break;
            case 'hour':
                $format        = 'H';
                break;
            default:
                $format        = 'Y-m-d';
                break;
        }

        if ($type == 'hour') {
            $arrDateList = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16',  '17', '18', '19', '20', '21', '22', '23');
        } else {
            $arrDateList = array();
            $tmp    = strtotime($st);
            $nAday  = 60*60*24;
            $edx    = strtotime($ed);
            while ($tmp <= $edx) {
                $sDate = date($format, $tmp);
                if (!in_array($sDate, $arrDateList)) {
                    $arrDateList[] = $sDate;
                }
                $tmp += $nAday;
            }
        }

        return $arrDateList;
    }

    /*
     * 合計を付与する
     */
    public function lfAddTotalLine($arrResults)
    {
        // 検索結果が0でない場合
        if (count($arrResults) > 0) {
            // 合計の計算
            foreach ($arrResults as $arrResult) {
                foreach ($arrResult as $key => $value) {
                    $arrTotal[$key] += $arrResult[$key];
                }
            }
            // 平均値の計算
            $arrTotal['total_average'] = $arrTotal['total'] / $arrTotal['total_order'];
            $arrResults[] = $arrTotal;
        }

        return $arrResults;
    }

    // 必要なカラムのみ抽出する(CSVデータで取得する)

    /**
     * @param string[] $arrDataCol
     */
    public function lfGetDataColCSV($arrData, $arrDataCol)
    {
        $max = count($arrData);
        $csv_data = '';
        for ($i = 0; $i < $max; $i++) {
            foreach ($arrDataCol as $val) {
                $arrRet[$i][$val] = ($arrData[$i][$val]) ? $arrData[$i][$val] : "0";
            }
            // 期間別集計の合計行の「期間」項目に不要な値が表示されてしまわない様、'合計'と表示する
            if (($i === $max -1) && isset($arrRet[$i]['str_date'])) {
                $arrRet[$i]['str_date'] = '合計';
            }
            $csv_data.= Utils::sfGetCSVList($arrRet[$i]);
        }

        return $csv_data;
    }

    public function lfGetCSVColum($page)
    {
        switch ($page) {
            // 商品別集計
            case 'products':
                $arrTitleCol = array(
                    '商品コード',
                    '商品名',
                    '購入件数',
                    '数量',
                    '単価',
                    '金額',
                );
                $arrDataCol = array(
                    'product_code',
                    'product_name',
                    'order_count',
                    'products_count',
                    'price',
                    'total',
                );
                break;
            // 職業別集計
            case 'job':
                $arrTitleCol = array(
                    '職業',
                    '購入件数',
                    '購入合計',
                    '購入平均',
                );
                $arrDataCol = array(
                    'job_name',
                    'order_count',
                    'total',
                    'total_average',
                );
                break;
            // 会員別集計
            case 'member':
                $arrTitleCol = array(
                    '会員',
                    '購入件数',
                    '購入合計',
                    '購入平均',
                );
                $arrDataCol = array(
                    'member_name',
                    'order_count',
                    'total',
                    'total_average',
                );
                break;
            // 年代別集計
            case 'age':
                $arrTitleCol = array(
                    '年齢',
                    '購入件数',
                    '購入合計',
                    '購入平均',
                );
                $arrDataCol = array(
                    'age_name',
                    'order_count',
                    'total',
                    'total_average',
                );
                break;
            // 期間別集計
            default:
                $arrTitleCol = array(
                    '期間',
                    '購入件数',
                    '男性',
                    '女性',
                    '男性(会員)',
                    '男性(非会員)',
                    '女性(会員)',
                    '女性(非会員)',
                    '購入合計',
                    '購入平均',
                );
                $arrDataCol = array(
                    'str_date',
                    'total_order',
                    'men',
                    'women',
                    'men_member',
                    'men_nonmember',
                    'women_member',
                    'women_nonmember',
                    'total',
                    'total_average',
                );
                break;
        }

        return array($arrTitleCol, $arrDataCol);
    }
}
