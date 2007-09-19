<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * パラメータ設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Parameter extends LC_Page {

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
        $this->tpl_mainpage = 'basis/parameter.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'parameter';
        $this->tpl_mainno = 'basis';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
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
            } else {
                $this->arrValues = SC_Utils_Ex::getHash2Array($this->arrForm,
                                                              $this->arrKeys);
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
        $masterData->updateMasterData("mtb_constants", array(), $data);

        // 更新したデータを取得
        $mtb_constants = $masterData->getDBMasterData("mtb_constants");

        // キャッシュを生成
        $masterData->clearCache("mtb_constants");
        $masterData->createCache("mtb_constants", $mtb_constants, true,
                                 array("id", "remarks", "rank"));
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
