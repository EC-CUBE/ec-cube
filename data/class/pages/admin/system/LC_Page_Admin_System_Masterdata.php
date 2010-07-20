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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * マスタデータ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Masterdata extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/masterdata.tpl';
        $this->tpl_subnavi = 'system/subnavi.tpl';
        $this->tpl_subno = 'masterdata';
        $this->tpl_mainno = 'system';
        $this->tpl_subtitle = 'マスタデータ管理';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        SC_Utils_Ex::sfIsSuccess(new SC_Session);

        $objView = new SC_AdminView();
        $this->arrMasterDataName = $this->getMasterDataNames(array("mtb_pref",
                                                                   "mtb_zip",
                                                                   "mtb_constants"));
        $masterData = new SC_DB_MasterData_Ex();

        if (!isset($_POST["mode"])) $_POST["mode"] = "";

        switch ($_POST["mode"]) {
        case "edit":
            // POST 文字列の妥当性チェック
            $this->checkMasterDataName();
            $this->errorMessage = $this->checkUniqueID();

            if (empty($this->errorMessage)) {
                // 取得したデータからマスタデータを生成
                $arrData = array();
                foreach ($_POST['id'] as $key => $val) {

                    // ID が空のデータは生成しない
                    if ($val != "") {
                        $arrData[$val] = $_POST['name'][$key];
                    }
                }

                // マスタデータを更新
                $masterData->objQuery = new SC_Query();
                $masterData->objQuery->begin();
                $masterData->deleteMasterData($this->masterDataName, false);
                // TODO カラム名はメタデータから取得した方が良い
                $masterData->registMasterData($this->masterDataName,
                                              array("id", "name", "rank"),
                                              $arrData, false);
                $masterData->objQuery->commit();
                $this->tpl_onload = "window.alert('マスタデータの設定が完了しました。');";
            }

        case "show":
            // POST 文字列の妥当性チェック
            $this->checkMasterDataName();

            // DB からマスタデータを取得
            $this->arrMasterData =
                $masterData->getDbMasterData($this->masterDataName);
            break;

        default:
        }

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
     * マスタデータ名チェックを行う
     *
     * @access private
     * @return void
     */
    function checkMasterDataName() {

        if (in_array($_POST['master_data_name'], $this->arrMasterDataName)) {
            $this->masterDataName = $_POST['master_data_name'];
            return true;
        } else {
            SC_Utils_Ex::sfDispeError("");
        }
    }

    /**
     * マスタデータ名を配列で取得する.
     *
     * @access private
     * @param array $ignores 取得しないマスタデータ名の配列
     * @return array マスタデータ名の配列
     */
    function getMasterDataNames($ignores = array()) {
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $arrMasterDataName = $dbFactory->findTableNames("mtb_");

        $i = 0;
        foreach ($arrMasterDataName as $val) {
            foreach ($ignores as $ignore) {
                if ($val == $ignore) {
                    unset($arrMasterDataName[$i]);
                }
            }
            $i++;
        }
        return $arrMasterDataName;
    }

    /**
     * ID の値がユニークかチェックする.
     *
     * 重複した値が存在する場合はエラーメッセージを表示する.
     *
     * @access private
     * @return void|string エラーが発生した場合はエラーメッセージを返す.
     */
    function checkUniqueID() {

        $arrId = $_POST['id'];
        for ($i = 0; $i < count($arrId); $i++) {

            $id = $arrId[$i];
            // 空の値は無視
            if ($arrId[$i] != "") {
                for ($j = $i + 1; $j < count($arrId); $j++) {
                    if ($id == $arrId[$j]) {
                        return $id . " が重複しているため登録できません.";
                    }
                }
            }
        }
    }
}
?>
