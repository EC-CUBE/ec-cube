<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * 規格分類 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Products_ClassCategory.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Admin_Products_ClassCategory extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/classcategory.tpl';
        $this->tpl_subno = 'class';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '規格管理＞分類登録';
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

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $class_id = $objFormParam->getValue('class_id');
        $classcategory_id = $objFormParam->getValue('classcategory_id');

        switch ($this->getMode()) {
            // 登録ボタン押下
            // 新規作成 or 編集
            case 'edit':
                // パラメーター値の取得
                $this->arrForm = $objFormParam->getHashArray();
                // 入力パラメーターチェック
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    //新規規格追加かどうかを判定する
                    $is_insert = $this->lfCheckInsert($classcategory_id);
                    if ($is_insert) {
                        //新規追加
                        $this->lfInsertClass($this->arrForm);
                    } else {
                        //更新
                        $this->lfUpdateClass($this->arrForm);
                    }

                    // 再表示
                    SC_Response_Ex::reload();
                }
                break;
                // 削除
            case 'delete':
                // ランク付きレコードの削除
                $this->lfDeleteClassCat($class_id, $classcategory_id);

                SC_Response_Ex::reload();
                break;
                // 編集前処理
            case 'pre_edit':
                // 規格名を取得する。
                $classcategory_name = $this->lfGetClassCatName($classcategory_id);
                // 入力項目にカテゴリ名を入力する。
                $this->arrForm['name'] = $classcategory_name;
                break;
            case 'down':
                //並び順を下げる
                $this->lfDownRank($class_id, $classcategory_id);

                SC_Response_Ex::reload();
                break;
            case 'up':
                //並び順を上げる
                $this->lfUpRank($class_id, $classcategory_id);

                SC_Response_Ex::reload();
                break;
            default:
                break;
        }
        //規格分類名の取得
        $this->tpl_class_name = $this->lfGetClassName($class_id);
        //規格分類情報の取得
        $this->arrClassCat = $this->lfGetClassCat($class_id);
        // POSTデータを引き継ぐ
        $this->tpl_classcategory_id = $classcategory_id;

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
     * パラメーターの初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('規格ID', 'class_id', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('規格分類名', 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK' ,'SPTAB_CHECK' ,'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('規格分類ID', 'classcategory_id', INT_LEN, 'n', array('NUM_CHECK'));
    }

    /**
     * 有効な規格分類情報の取得
     *
     * @param integer $class_id 規格ID
     * @return array 規格分類情報
     */
    function lfGetClassCat($class_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $where = 'del_flg <> 1 AND class_id = ?';
        $objQuery->setOrder('rank DESC'); // XXX 降順
        $arrClassCat = $objQuery->select('name, classcategory_id', 'dtb_classcategory', $where, array($class_id));
        return $arrClassCat;
    }

    /**
     * 規格名の取得
     *
     * @param integer $class_id 規格ID
     * @return string 規格名
     */
    function lfGetClassName($class_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $where = 'class_id = ?';
        $name = $objQuery->get('name', 'dtb_class', $where, array($class_id));
        return $name;
    }

    /**
     * 規格分類名を取得する
     *
     * @param integer $classcategory_id 規格分類ID
     * @return string 規格分類名
     */
    function lfGetClassCatName($classcategory_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'classcategory_id = ?';
        $name = $objQuery->get('name', 'dtb_classcategory', $where, array($classcategory_id));
        return $name;
    }

    /**
     * 規格分類情報を新規登録
     *
     * @param array $arrForm フォームパラメータークラス
     * @return integer 更新件数
     */
    function lfInsertClass($arrForm) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        // 親規格IDの存在チェック
        $where = 'del_flg <> 1 AND class_id = ?';
        $class_id = $objQuery->get('class_id', 'dtb_class', $where, array($arrForm['class_id']));
        if (!SC_Utils_Ex::isBlank($class_id)) {
            // INSERTする値を作成する。
            $sqlval['name'] = $arrForm['name'];
            $sqlval['class_id'] = $arrForm['class_id'];
            $sqlval['creator_id'] = $_SESSION['member_id'];
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_classcategory', $where, array($arrForm['class_id'])) + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
            // INSERTの実行
            $sqlval['classcategory_id'] = $objQuery->nextVal('dtb_classcategory_classcategory_id');
            $ret = $objQuery->insert('dtb_classcategory', $sqlval);
        }
        $objQuery->commit();
        return $ret;
    }

    /**
     * 規格分類情報を更新
     *
     * @param array $arrForm フォームパラメータークラス
     * @return integer 更新件数
     */
    function lfUpdateClass($arrForm) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEする値を作成する。
        $sqlval['name'] = $arrForm['name'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'classcategory_id = ?';
        // UPDATEの実行
        $ret = $objQuery->update('dtb_classcategory', $sqlval, $where, array($arrForm['classcategory_id']));
        return $ret;
    }

    /**
     * エラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrForm = $objFormParam->getHashArray();
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        if (!SC_Utils_Ex::isBlank($arrErr)) {
            return $arrErr;
        } else {
            $arrForm = $objFormParam->getHashArray();
        }

        $where = 'class_id = ? AND name = ?';
        $arrRet = $objQuery->select('classcategory_id, name', 'dtb_classcategory', $where, array($arrForm['class_id'], $arrForm['name']));
        // 編集中のレコード以外に同じ名称が存在する場合
        if ($arrRet[0]['classcategory_id'] != $arrForm['classcategory_id'] && $arrRet[0]['name'] == $arrForm['name']) {
            $arrErr['name'] = '※ 既に同じ内容の登録が存在します。<br>';
        }
        return $arrErr;
    }

    /**
     * 新規規格分類追加かどうかを判定する.
     *
     * @param integer $classcategory_id 規格分類ID
     * @return boolean 新規商品追加の場合 true
     */
    function lfCheckInsert($classcategory_id) {
        //classcategory_id のあるなしで新規規格分類化かどうかを判定
        if (empty($classcategory_id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 規格分類情報を削除する
     *
     * @param integer $class_id 規格ID
     * @param integer $classcategory_id 規格分類ID
     * @return void
     */
    function lfDeleteClassCat($class_id, $classcategory_id) {
        $objDb = new SC_Helper_DB_Ex();
        $where = 'class_id = ' . SC_Utils_Ex::sfQuoteSmart($class_id);
        $objDb->sfDeleteRankRecord('dtb_classcategory', 'classcategory_id', $classcategory_id, $where, true);
    }
    /**
     * 並び順を上げる
     *
     * @param integer $class_id 規格ID
     * @param integer $classcategory_id 規格分類ID
     * @return void
     */
    function lfUpRank($class_id, $classcategory_id) {
        $objDb = new SC_Helper_DB_Ex();
        $where = 'class_id = ' . SC_Utils_Ex::sfQuoteSmart($class_id);
        $objDb->sfRankUp('dtb_classcategory', 'classcategory_id', $classcategory_id, $where);
    }
    /**
     * 並び順を下げる
     *
     * @param integer $class_id 規格ID
     * @param integer $classcategory_id 規格分類ID
     * @return void
     */
    function lfDownRank($class_id, $classcategory_id) {
        $objDb = new SC_Helper_DB_Ex();
        $where = 'class_id = ' . SC_Utils_Ex::sfQuoteSmart($class_id);
        $objDb->sfRankDown('dtb_classcategory', 'classcategory_id', $classcategory_id, $where);
    }
}
