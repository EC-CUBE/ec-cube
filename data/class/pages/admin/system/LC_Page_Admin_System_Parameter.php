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
 * パラメーター設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Parameter extends LC_Page_Admin_Ex {

    // {{{ properties

    /** 定数キーとなる配列 */
    var $arrKeys;

    /** 定数コメントとなる配列 */
    var $arrComments;

    /** 定数値となる配列 */
    var $arrValues;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/parameter.tpl';
        $this->tpl_subno = 'parameter';
        $this->tpl_mainno = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'パラメーター設定';
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
        $masterData = new SC_DB_MasterData_Ex();

        // キーの配列を生成
        $this->arrKeys = $this->getParamKeys($masterData);

        switch ($this->getMode()) {
        case 'update':
            // データの引き継ぎ
            $this->arrForm = $_POST;

            // エラーチェック
            $this->arrErr = $this->errorCheck($this->arrKeys, $this->arrForm);
            // エラーの無い場合は update
            if (empty($this->arrErr)) {
                $this->update($this->arrKeys, $this->arrForm);
                $this->tpl_onload = "window.alert('パラメーターの設定が完了しました。');";
            } else {
                $this->arrValues = SC_Utils_Ex::getHash2Array($this->arrForm,
                                                              $this->arrKeys);
                $this->tpl_onload = "window.alert('エラーが発生しました。入力内容をご確認下さい。');";
            }
            break;
        default:
            break;
        }

        if (empty($this->arrErr)) {
            $this->arrValues = SC_Utils_Ex::getHash2Array(
                                       $masterData->getDBMasterData("mtb_constants"));
        }

        // コメント, 値の配列を生成
        $this->arrComments = SC_Utils_Ex::getHash2Array(
                                     $masterData->getDBMasterData("mtb_constants",
                                             array('id', 'remarks', 'rank')));

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
     * パラメーター情報を更新する.
     *
     * 画面の設定値で mtb_constants テーブルの値とキャッシュを更新する.
     *
     * @access private
     * @return void
     */
    function update(&$arrKeys, &$arrForm) {
        $data = array();
        $masterData = new SC_DB_MasterData_Ex();
        foreach ($arrKeys as $key) {
            $data[$key] = $arrForm[$key];
        }

        // DBのデータを更新
        $masterData->updateMasterData('mtb_constants', array(), $data);

        // キャッシュを生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
    }

    /**
     * エラーチェックを行う.
     *
     * @access private
     * @param array $arrForm $_POST 値
     * @return void
     */
    function errorCheck(&$arrKeys, &$arrForm) {
        $objErr = new SC_CheckError_Ex($arrForm);
        for ($i = 0; $i < count($arrKeys); $i++) {
            $objErr->doFunc(array($arrKeys[$i],
                                  $arrForm[$arrKeys[$i]]),
                            array("EXIST_CHECK_REVERSE", "EVAL_CHECK"));
        }
        return $objErr->arrErr;
    }

    /**
     * パラメーターのキーを配列で返す.
     *
     * @access private
     * @return array パラメーターのキーの配列
     */
    function getParamKeys(&$masterData) {
        $keys = array();
        $i = 0;
        foreach ($masterData->getDBMasterData("mtb_constants") as $key => $val) {
            $keys[$i] = $key;
            $i++;
        }
        return $keys;
    }
}
?>
