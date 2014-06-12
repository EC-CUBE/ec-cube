<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * レビュー管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Review extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/review.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'review';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'レビュー管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData('mtb_page_max');
        $this->arrRECOMMEND = $masterData->getMasterData('mtb_recommend');
        $this->arrSex = $masterData->getMasterData('mtb_sex');

        $objDate = new SC_Date_Ex();
        // 登録・更新検索開始年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrStartYear = $objDate->getYear();
        $this->arrStartMonth = $objDate->getMonth();
        $this->arrStartDay = $objDate->getDay();
        // 登録・更新検索終了年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrEndYear = $objDate->getYear();
        $this->arrEndMonth = $objDate->getMonth();
        $this->arrEndDay = $objDate->getDay();
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
        $objReview = new SC_Helper_Review_Ex();
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // URLを小文字に変換
        $objFormParam->toLower('search_reviewer_url');

        $this->arrForm = $objFormParam->getHashArray();
        $this->arrHidden = $this->lfSetHidden($this->arrForm);

        // 入力パラメーターチェック
        $this->arrErr = $this->lfCheckError($objFormParam);
        if (!SC_Utils_Ex::isBlank($this->arrErr)) {
            return;
        }

        switch ($this->getMode()) {
            case 'delete':
                $objReview->delete($this->arrForm['review_id']);
            case 'search':
            case 'csv':

                // 検索結果を取得
                $this->arrReview = $this->lfGetReview($objReview);

                //CSVダウンロード
                if ($this->getMode() == 'csv') {
                    // 検索条件を取得
                    list($where, $arrWhereVal) = $this->lfGetWhere($this->arrForm);
                    $this->lfDoOutputCsv($where, $arrWhereVal);

                    SC_Response_Ex::actionExit();
                }

                break;
            default:
                break;
        }

    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function lfCheckError(&$objFormParam)
    {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        switch ($this->getMode()) {
            case 'search':
                $objErr->doFunc(array('開始日', 'search_startyear', 'search_startmonth', 'search_startday'), array('CHECK_DATE'));
                $objErr->doFunc(array('終了日', 'search_endyear', 'search_endmonth', 'search_endday'), array('CHECK_DATE'));
                $objErr->doFunc(array('開始日', '終了日', 'search_startyear', 'search_startmonth', 'search_startday', 'search_endyear', 'search_endmonth', 'search_endday'), array('CHECK_SET_TERM'));
                break;

            case 'complete':
                $objErr->doFunc(array('おすすめレベル', 'recommend_level'), array('SELECT_CHECK'));
                $objErr->doFunc(array('タイトル', 'title', STEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
                $objErr->doFunc(array('コメント', 'comment', LTEXT_LEN), array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
                break;
            default:
                break;
        }

        return $objErr->arrErr;
    }

    /**
     * hidden情報の作成
     *
     * @param  array $arrForm フォームデータ
     * @return array hidden情報
     */
    public function lfSetHidden($arrForm)
    {
        $arrHidden = array();
        foreach ($arrForm AS $key=>$val) {
            if (preg_match('/^search_/', $key)) {
                switch ($key) {
                    case 'search_sex':
                        $arrHidden[$key] = SC_Utils_Ex::sfMergeParamCheckBoxes($val);
                        if (!is_array($val)) {
                            $arrForm[$key] = explode('-', $val);
                        }
                        break;
                    default:
                        $arrHidden[$key] = $val;
                        break;
                }
            }
        }

        return $arrHidden;
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('投稿者名', 'search_reviewer_name', STEXT_LEN, 'KVas', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('投稿者URL', 'search_reviewer_url', STEXT_LEN, 'KVas', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('商品名', 'search_name', STEXT_LEN, 'KVas', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('商品コード', 'search_product_code', STEXT_LEN, 'KVas', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('性別', 'search_sex', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('おすすめレベル', 'search_recommend_level', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('投稿年', 'search_startyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $objFormParam->addParam('投稿月', 'search_startmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $objFormParam->addParam('投稿日', 'search_startday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $objFormParam->addParam('投稿年', 'search_endyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $objFormParam->addParam('投稿月', 'search_endmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $objFormParam->addParam('投稿日', 'search_endday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '', false);
        $objFormParam->addParam('最大表示件数', 'search_page_max', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('ページ番号件数', 'search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('レビューID', 'review_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
    }

    /**
     * CSV ファイル出力実行
     *
     * @param  string $where       WHERE文
     * @param  array  $arrWhereVal WHERE文の判定値
     * @return void
     */
    public function lfDoOutputCsv($where, $arrWhereVal)
    {
        $objCSV = new SC_Helper_CSV_Ex();
        $objCSV->sfDownloadCsv('4', $where, $arrWhereVal, '', true);
    }

    /**
     * WHERE文の作成
     *
     * @param  array $arrForm フォームデータ
     * @return array WHERE文、判定値
     */
    public function lfGetWhere($arrForm)
    {
        //削除されていない商品を検索
        $where = 'A.del_flg = 0 AND B.del_flg = 0';
        $arrWhereVal = array();

        foreach ($arrForm AS $key=>$val) {
            if (empty($val)) continue;

            switch ($key) {
                case 'search_reviewer_name':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND reviewer_name LIKE ? ';
                    $arrWhereVal[] = "%$val%";
                    break;

                case 'search_reviewer_url':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND reviewer_url LIKE ? ';
                    $arrWhereVal[] = "%$val%";
                    break;

                case 'search_name':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND name LIKE ? ';
                    $arrWhereVal[] = "%$val%";
                    break;

                case 'search_product_code':
                    $val = preg_replace('/ /', '%', $val);
                    $where.= ' AND A.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ?)';
                    $arrWhereVal[] = "%$val%";
                    break;

                case 'search_sex':
                    $tmp_where = '';
                    //$val=配列の中身,$element=各キーの値(1,2)
                    if (is_array($val)) {
                        foreach ($val as $element) {
                            if ($element != '') {
                                if ($tmp_where == '') {
                                    $tmp_where .= ' AND (sex = ?';
                                } else {
                                    $tmp_where .= ' OR sex = ?';
                                }
                                $arrWhereVal[] = $element;
                            }
                        }
                        if ($tmp_where != '') {
                            $tmp_where .= ')';
                            $where .= " $tmp_where ";
                        }
                    }

                    break;

                case 'search_recommend_level':
                    $where.= ' AND recommend_level = ? ';
                    $arrWhereVal[] = $val;
                    break;

                case 'search_startyear':
                    if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])) {
                        $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
                        $where.= ' AND A.create_date >= ? ';
                        $arrWhereVal[] = $date;
                    }
                    break;

                case 'search_endyear':
                    if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])) {
                        $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
                        $end_date = date('Y/m/d', strtotime('1 day', strtotime($date)));
                        $where.= " AND A.create_date <= cast('$end_date' as date) ";
                    }
                    break;

                default:
                    break;
            }

        }

        return array($where, $arrWhereVal);
    }

    /**
     * レビュー検索結果の取得
     *
     * @param  SC_Helper_Review $objReview
     * @return array  レビュー一覧
     */
    public function lfGetReview(SC_Helper_Review $objReview)
    {
        $arrForm = $this->arrForm;

        $query = $this->makeQuery($arrForm);
        $linemax = $objReview->count($query);

        $this->tpl_linemax = $linemax;
        $this->tpl_pageno = isset($arrForm['search_pageno']) ? $arrForm['search_pageno'] : '';

        // ページ送りの取得
        $page_max = SC_Utils_Ex::sfGetSearchPageMax($arrForm['search_page_max']);
        $objNavi = new SC_PageNavi_Ex($this->tpl_pageno, $linemax, $page_max,
                                      'eccube.moveNaviPage', NAVI_PMAX);
        $this->arrPagenavi = $objNavi->arrPagenavi;
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $params = array(
            'query' => $query,
            'limit' => $page_max,
            'offset' => $startno
        );
        $arrReview = $objReview->find($params);

        return $arrReview;
    }

    /**
     * SC_Helper_Reviewインスタンスへ渡す検索条件の配列を作成.
     *
     * @param array $data
     * @return array
     */
    private function makeQuery($data = array()) {
        $query = array();

        foreach ($data AS $key => $val) {
            if (empty($val)) continue;

            switch ($key) {
                case 'search_reviewer_name':
                    $query['reviewer_name'] = $val;
                    break;

                case 'search_reviewer_url':
                    $query['reviewer_url'] = $val;
                    break;

                case 'search_name':
                    $query['product_name'] = $val;
                    break;

                case 'search_product_code':
                    $query['product_code'] = $val;
                    break;

                case 'search_sex':
                    $query['reviewer_sex'] = $val;
                    break;

                case 'search_recommend_level':
                    $query['recommend_level'] = $val;
                    break;

                case 'search_startyear':
                    if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])) {
                        $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
                        $query['date_from'] = $date;
                    }
                    break;

                case 'search_endyear':
                    if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])) {
                        $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
                        $end_date = date('Y/m/d', strtotime('1 day', strtotime($date)));
                        $query['date_to'] = $end_date;
                    }
                    break;

                default:
                    break;
            }

        }

        return $query;
    }
}
