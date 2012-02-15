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
 * メーカー登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Maker extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/maker.tpl';
        $this->tpl_subno = 'maker';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'メーカー登録';
        $this->tpl_mainno = 'products';
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
        $objFormParam = new SC_FormParam_Ex();

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // POST値をセット
        $objFormParam->setParam($_POST);

        // POST値の入力文字変換
        $objFormParam->convParam();

        //maker_idを変数にセット
        $maker_id = $objFormParam->getValue('maker_id');

        // 変換後のPOST値を取得
        $this->arrForm  = $objFormParam->getHashArray();

        // モードによる処理切り替え
        switch ($this->getMode()) {

        // 編集処理
        case 'edit':
        // 入力文字の変換

            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            if (count($this->arrErr) <= 0) {
                if ($this->arrForm['maker_id'] == '') {
                    // メーカー情報新規登録
                    $this->lfInsert($this->arrForm);
                } else {
                    // メーカー情報編集
                    $this->lfUpdate($this->arrForm);
                }
                // 再表示
                $this->objDisplay->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_maker_id = $this->arrForm['maker_id'];
            }
            break;

        // 編集前処理
        case 'pre_edit':
            $this->arrForm = $this->lfPreEdit($this->arrForm, $this->arrForm['maker_id']);
            $this->tpl_maker_id = $this->arrForm['maker_id'];
            break;

        // メーカー順変更
        case 'up':
        case 'down':
            $this->lfRankChange($this->arrForm['maker_id'], $this->getMode());
            // リロード
            SC_Response_Ex::reload();
            break;

        // 削除
        case 'delete':
            $this->lfDelete($this->arrForm['maker_id']);
            // リロード
            SC_Response_Ex::reload();
            break;

        default:
            break;
        }

        // メーカー情報読み込み
        $this->arrMaker = $this->lfDisp();
        // POSTデータを引き継ぐ
        $this->tpl_maker_id = $maker_id;

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
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('メーカーID', 'maker_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メーカー名', 'name', SMTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
    }

    /**
     * メーカー情報表示.
     *
     * @return array $arrMaker メーカー情報
     */
    function lfDisp() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 削除されていないメーカー情報を表示する
        $where = 'del_flg = 0';
        $objQuery->setOrder('rank DESC');
        $arrMaker = array();
        $arrMaker = $objQuery->select('maker_id, name', 'dtb_maker', $where);
        return $arrMaker;
    }

    /**
     * メーカー情報新規登録.
     *
     * @param array $arrForm メーカー情報
     * @return void
     */
    function lfInsert(&$arrForm) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // INSERTする値を作成する
        $sqlval['name'] = $arrForm['name'];
        $sqlval['rank'] = $objQuery->max('rank', 'dtb_maker') + 1;
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['maker_id'] = $objQuery->nextVal('dtb_maker_maker_id');

        // INSERTの実行
        $objQuery->insert('dtb_maker', $sqlval);
    }

    /**
     * メーカー情報更新.
     *
     * @param array $arrForm メーカー情報
     * @return void
     */
    function lfUpdate(&$arrForm) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // UPDATEする値を作成する
        $sqlval['name'] = $arrForm['name'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'maker_id = ?';

        // UPDATEの実行
        $objQuery->update('dtb_maker', $sqlval, $where, array($arrForm['maker_id']));
    }

    /**
     * メーカー情報削除.
     *
     * @param integer $maker_id メーカーID
     * @return void
     */
    function lfDelete($maker_id) {
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfDeleteRankRecord('dtb_maker', 'maker_id', $maker_id, '', true);
    }

    /**
     * メーカー情報順番変更.
     *
     * @param  integer $maker_id メーカーID
     * @param  string  $mode up か down のモードを示す文字列
     * @return void
     */
    function lfRankChange($maker_id, $mode) {
        $objDb = new SC_Helper_DB_Ex();

        switch ($mode) {
        case 'up':
            $objDb->sfRankUp('dtb_maker', 'maker_id', $maker_id);
            break;

        case 'down':
            $objDb->sfRankDown('dtb_maker', 'maker_id', $maker_id);
            break;

        default:
            break;
        }
    }

    /**
     * メーカー情報編集前処理.
     *
     * @param array   $arrForm メーカー情報
     * @param integer $maker_id メーカーID
     * @return array  $arrForm メーカー名を追加
     */
    function lfPreEdit(&$arrForm, $maker_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 編集項目を取得する
        $where = 'maker_id = ?';
        $arrMaker = array();
        $arrMaker = $objQuery->select('name', 'dtb_maker', $where, array($maker_id));
        $arrForm['name'] = $arrMaker[0]['name'];

        return $arrForm;
    }

    /**
     * 入力エラーチェック.
     *
     * @param  array $arrForm メーカー情報
     * @return array $objErr->arrErr エラー内容
     */
    function lfErrorCheck(&$arrForm) {
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('メーカー名', 'name', SMTEXT_LEN), array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));

        // maker_id の正当性チェック
        if (!empty($arrForm['maker_id'])) {
            $objDb = new SC_Helper_DB_Ex();
            if(!SC_Utils_Ex::sfIsInt($arrForm['maker_id']) 
              || SC_Utils_Ex::sfIsZeroFilling($arrForm['maker_id'])
              || !$objDb->sfIsRecord('dtb_maker', 'maker_id', array($arrForm['maker_id']))) {

              // maker_idが指定されていて、且つその値が不正と思われる場合はエラー
              $objErr->arrErr['maker_id'] = '※ メーカーIDが不正です<br />';
            }
        }
        if (!isset($objErr->arrErr['name'])) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $arrMaker = array();
            $arrMaker = $objQuery->select('maker_id, name', 'dtb_maker', 'del_flg = 0 AND name = ?', array($arrForm['name']));

            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrMaker[0]['maker_id'] != $arrForm['maker_id'] && $arrMaker[0]['name'] == $arrForm['name']) {
                $objErr->arrErr['name'] = '※ 既に同じ内容の登録が存在します。<br />';
            }
        }

        return $objErr->arrErr;
    }
}
