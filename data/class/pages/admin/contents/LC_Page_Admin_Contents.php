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

/**
 * コンテンツ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/index.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'contents';
        $this->arrForm = array(
            'year' => date('Y'),
            'month' => date('n'),
            'day' => date('j'),
        );
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = '新着情報管理';
        //---- 日付プルダウン設定
        $objDate = new SC_Date_Ex(ADMIN_NEWS_STARTYEAR);
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
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
        // フックポイント.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance();
        $objPlugin->doAction('lc_page_admin_contents_action_start', array($this));

        $objDb = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $news_id = $objFormParam->getValue('news_id');

        //---- 新規登録/編集登録
        switch ($this->getMode()) {
            case 'regist':
                $arrPost = $objFormParam->getHashArray();
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // ニュースIDの値がPOSTされて来た場合は既存データの編集とみなし、
                    // 更新メソッドを呼び出す。
                    // ニュースIDが存在しない場合は新規登録を行う。
                    $arrPost['link_method'] = $this->checkLinkMethod($arrPost['link_method']);
                    $arrPost['news_date'] = $this->getRegistDate($arrPost);
                    $member_id = $_SESSION['member_id'];
                    if (strlen($news_id) > 0 && is_numeric($news_id)) {
                        $this->lfNewsUpdate($arrPost,$member_id);
                    } else {
                        $this->lfNewsInsert($arrPost,$member_id);
                    }
                    $news_id = '';
                    $this->tpl_onload = "window.alert('編集が完了しました');";
                } else {
                    $this->arrForm = $arrPost;
                }
                break;
            case 'search':
                if (is_numeric($news_id)) {
                    list($this->arrForm) = $this->getNews($news_id);
                    list($this->arrForm['year'],$this->arrForm['month'],$this->arrForm['day']) = $this->splitNewsDate($this->arrForm['cast_news_date']);
                    $this->edit_mode = 'on';
                }
                break;
            case 'delete':
            //----　データ削除
                if (is_numeric($news_id)) {
                    $pre_rank = $this->getRankByNewsId($news_id);
                    $this->computeRankForDelete($news_id,$pre_rank);

                    // フックポイント.
                    $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance();
                    $objPlugin->doAction('lc_page_admin_contents_action_delete', array($this));

                    SC_Response_Ex::reload();             //自分にリダイレクト（再読込による誤動作防止）
                }
                break;
            case 'move':
            //----　表示順位移動
                if (strlen($news_id) > 0 && is_numeric($news_id) == true) {
                    $term = $objFormParam->getValue('term');
                    if ($term == 'up') {
                        $objDb->sfRankUp('dtb_news', 'news_id', $news_id);
                    } else if ($term == 'down') {
                        $objDb->sfRankDown('dtb_news', 'news_id', $news_id);
                    }
                    // フックポイント.
                    $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance();
                    $objPlugin->doAction('lc_page_admin_contents_action_move', array($this));

                    $this->objDisplay->reload();
                }
                break;
            case 'moveRankSet':
            //----　指定表示順位移動
                $input_pos = $this->getPostRank($news_id);
                if (SC_Utils_Ex::sfIsInt($input_pos)) {
                    $objDb->sfMoveRank('dtb_news', 'news_id', $news_id, $input_pos);

                    // フックポイント.
                    $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance();
                    $objPlugin->doAction('lc_page_admin_contents_action_moveRankSet', array($this));

                    $this->objDisplay->reload();
                }
                break;
            default:
                break;
        }

        $this->arrNews = $this->getNews();
        $this->tpl_news_id = $news_id;
        $this->line_max = count($this->arrNews);
        $this->max_rank = $this->getRankMax();

        // フックポイント.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance();
        $objPlugin->doAction('lc_page_admin_contents_action_end', array($this));
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
     * 入力されたパラメーターのエラーチェックを行う。
     * @param Object $objFormParam
     * @return Array エラー内容
     */
    function lfCheckError(&$objFormParam) {
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();
        $objErr->doFunc(array('日付', 'year', 'month', 'day'), array('CHECK_DATE'));
        return $objErr->arrErr;
    }

    /**
     * パラメーターの初期化を行う
     * @param Object $objFormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('news_id', 'news_id');
        $objFormParam->addParam('日付(年)', 'year', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('日付(月)', 'month', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('日付(日)', 'day', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('タイトル', 'news_title', MTEXT_LEN, 'KVa', array('EXIST_CHECK','MAX_LENGTH_CHECK','SPTAB_CHECK'));
        $objFormParam->addParam('URL', 'news_url', URL_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('本文', 'news_comment', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('別ウィンドウで開く', 'link_method', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ランク移動', 'term', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 新着記事のデータの登録を行う
     * @param Array $arrPost POSTデータの配列
     * @param Integer $member_id 登録した管理者のID
     */
    function lfNewsInsert($arrPost,$member_id) {
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();

        // rankの最大+1を取得する
        $rank_max = $this->getRankMax();
        $rank_max = $rank_max + 1;

        $table = 'dtb_news';
        $sqlval = array();
        $news_id = $objQuery->nextVal('dtb_news_news_id');
        $sqlval['news_id'] = $news_id;
        $sqlval['news_date'] = $arrPost['news_date'];
        $sqlval['news_title'] = $arrPost['news_title'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['news_url'] = $arrPost['news_url'];
        $sqlval['link_method'] = $arrPost['link_method'];
        $sqlval['news_comment'] = $arrPost['news_comment'];
        $sqlval['rank'] = $rank_max;
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->insert($table, $sqlval);
    }

    function lfNewsUpdate($arrPost,$member_id) {
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();

        $table = 'dtb_news';
        $sqlval = array();
        $sqlval['news_date'] = $arrPost['news_date'];
        $sqlval['news_title'] = $arrPost['news_title'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['news_url'] = $arrPost['news_url'];
        $sqlval['news_comment'] = $arrPost['news_comment'];
        $sqlval['link_method'] = $arrPost['link_method'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'news_id = ?';
        $arrValIn = array($arrPost['news_id']);
        $objQuery->update($table, $sqlval, $where, $arrValIn);
    }

    /**
     * データの登録日を返す。
     * @param Array $arrPost POSTのグローバル変数
     * @return string 登録日を示す文字列
     */
    function getRegistDate($arrPost) {
        $registDate = $arrPost['year'] .'/'. $arrPost['month'] .'/'. $arrPost['day'];
        return $registDate;
    }

    /**
     * チェックボックスの値が空の時は無効な値として1を格納する
     * @param int $link_method
     * @return int
     */
    function checkLinkMethod($link_method) {
        if (strlen($link_method) == 0) {
            $link_method = 1;
        }
        return $link_method;
    }

    /**
     * ニュース記事を取得する。
     * @param Integer news_id ニュースID
     */
    function getNews($news_id = '') {
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*, cast(news_date as date) as cast_news_date';
        $table = 'dtb_news';
        $order = 'rank DESC';
        if (strlen($news_id) == 0) {
            $where = 'del_flg = 0';
            $arrWhereVal = array();
        } else {
            $where = 'del_flg = 0 AND news_id = ?';
            $arrWhereVal = array($news_id);
        }
        $objQuery->setOrder($order);
        return $objQuery->select($col, $table, $where, $arrWhereVal);
    }

    /**
     * 指定されたニュースのランクの値を取得する。
     * @param Integer $news_id
     */
    function getRankByNewsId($news_id) {
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'rank';
        $table = 'dtb_news';
        $where = 'del_flg = 0 AND news_id = ?';
        $arrWhereVal = array($news_id);
        list($rank) = $objQuery->select($col, $table, $where, $arrWhereVal);
        return $rank['rank'];
    }

    /**
     * 削除する新着情報以降のrankを1つ繰り上げる。
     * @param Integer $news_id
     * @param Integer $rank
     */
    function computeRankForDelete($news_id,$rank) {
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $table = 'dtb_news';
        $sqlval = array();
        $sqlval['rank'] = $rank;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'del_flg = 0 AND rank > ?';
        $arrValIn = array($rank);
        $objQuery->update($table, $sqlval, $where, $arrValIn);

        $sqlval = array();
        $sqlval['rank'] = '0';
        $sqlval['del_flg'] = '1';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'news_id = ?';
        $arrValIn = array($news_id);
        $objQuery->update($table, $sqlval, $where, $arrValIn);
        $objQuery->commit();
    }

    /**
     * ニュースの日付の値をフロントでの表示形式に合わせるために分割
     * @param String $news_date
     */
    function splitNewsDate($news_date) {
        return explode('-', $news_date);
    }

    /**
     * ランクの最大値の値を返す。
     * @return Intger $max ランクの最大値の値
     */
    function getRankMax() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'MAX(rank) as max';
        $table = 'dtb_news';
        $where = 'del_flg = 0';
        list($result) = $objQuery->select($col, $table, $where);
        return $result['max'];
    }

    /**
     * POSTされたランクの値を取得する
     * @param Object $objFormParam
     * @param Integer $news_id
     */
    function getPostRank($news_id) {
        if (strlen($news_id) > 0 && is_numeric($news_id) == true) {
            $key = 'pos-' . $news_id;
            $input_pos = $_POST[$key];
            return $input_pos;
        }
    }

}
