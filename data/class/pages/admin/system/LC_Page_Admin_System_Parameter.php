<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * パラメータ設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Parameter extends LC_Page_Admin {

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
        $this->tpl_subnavi = 'system/subnavi.tpl';
        $this->tpl_subno = 'parameter';
        $this->tpl_mainno = 'system';
        $this->tpl_subtitle = 'パラメータ設定';
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

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess(new SC_Session());

        // キーの配列を生成
        $this->arrKeys = $this->getParamKeys($masterData);

        if (isset($_POST["mode"]) && $_POST["mode"] == "update") {

            // データの引き継ぎ
            $this->arrForm = $_POST;

            // エラーチェック
            $this->arrErr = $this->errorCheck();
            // エラーの無い場合は update
            if (empty($this->arrErr)) {
                $this->update();
                $this->tpl_onload = "window.alert('パラメータの設定が完了しました。');";
            } else {
                $this->arrValues = SC_Utils_Ex::getHash2Array($this->arrForm,
                                                              $this->arrKeys);
                $this->tpl_onload = "window.alert('エラーが発生しました。入力内容をご確認下さい。');";
            }

        }

        if (empty($this->arrErr)) {
            $this->arrValues = SC_Utils_Ex::getHash2Array(
                                       $masterData->getDBMasterData("mtb_constants"));
        }

        // コメント, 値の配列を生成
        $this->arrComments = SC_Utils_Ex::getHash2Array(
                                     $masterData->getDBMasterData("mtb_constants",
                                             array("id", "remarks", "rank")));

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
     * パラメータ情報を更新する.
     *
     * 画面の設定値で mtb_constants テーブルの値とキャッシュを更新する.
     *
     * @access private
     * @return void
     */
    function update() {
        $data = array();
        $masterData = new SC_DB_MasterData_Ex();
        foreach ($this->arrKeys as $key) {
            $data[$key] = $_POST[$key];
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
     * @return void
     */
    function errorCheck() {
        $objErr = new SC_CheckError($this->arrForm);
        for ($i = 0; $i < count($this->arrKeys); $i++) {
            $objErr->doFunc(array($this->arrKeys[$i],
                                  $this->arrForm[$this->arrKeys[$i]]),
                            array("EXIST_CHECK_REVERSE", "EVAL_CHECK"));
        }
        return $objErr->arrErr;
    }

    /**
     * パラメータのキーを配列で返す.
     *
     * @access private
     * @return array パラメータのキーの配列
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
