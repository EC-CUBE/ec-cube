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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Rank extends LC_Page_Admin {
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        // チェック後のデータを格納
        $arrClean = array();

        // $_GET['move'] が想定値かどうかチェック
        switch($_GET['move']) {
            case 'up':
            case 'down':
                $arrClean['move'] = $_GET['move'];
                break;
            default:
                $arrClean['move'] = "";
                break;
        }


        // 正当な数値であればOK
        if (SC_Utils::sfIsInt($_GET['id'])) {
            $arrClean['id'] = $_GET['id'];

            switch($arrClean['move']) {
                case 'up':
                    $this->lfRunkUp($arrClean['id']);
                    break;

                case 'down':
                    $this->lfRunkDown($arrClean['id']);
                    break;

                default:
                    break;
            }
        }
        
        // エラー処理
        else {
            GC_Utils::gfPrintLog("error id=".$_GET['id']);
        }

        // ページの表示
        SC_Response_Ex::sendRedirect(ADMIN_SYSTEM_URLPATH);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // ランキングを上げる。
    function lfRunkUp($id) {
        $objQuery =& SC_Query::getSingletonInstance();

        // 自身のランクを取得する。
        $rank = $objQuery->getOne("SELECT rank FROM dtb_member WHERE member_id = ?", array($id));

        // ランクの最大値を取得する。
        $maxno = $objQuery->getOne("SELECT max(rank) FROM dtb_member");
        // ランクが最大値よりも小さい場合に実行する。
        if($rank < $maxno) {
            // ランクがひとつ上のIDを取得する。
            $sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
            $up_id = $objQuery->getOne($sqlse, $rank + 1);

            // Updateする値を作成する.
            $sqlVal1 = array();
            $sqlVal2 = array();
            $sqlVal1['rank'] = $rank + 1;
            $sqlVal2['rank'] = $rank;
            $where = "member_id = ?";

            // ランク入れ替えの実行
            $objQuery->begin();
            $objQuery->update("dtb_member", $sqlVal1, $where, array($id));
            $objQuery->update("dtb_member", $sqlVal2, $where, array($up_id));
            $objQuery->commit();
        }
    }

    // ランキングを下げる。
    function lfRunkDown($id) {
        $objQuery =& SC_Query::getSingletonInstance();

        // 自身のランクを取得する。
        $rank = $objQuery->getOne("SELECT rank FROM dtb_member WHERE member_id = ?", array($id));
        // ランクの最小値を取得する。
        $minno = $objQuery->getOne("SELECT min(rank) FROM dtb_member");
        // ランクが最大値よりも大きい場合に実行する。
        if($rank > $minno) {
            // ランクがひとつ下のIDを取得する。
            $sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
            $down_id = $objQuery->getOne($sqlse, $rank - 1);

            // Updateする値を作成する.
            $sqlVal1 = array();
            $sqlVal2 = array();
            $sqlVal1['rank'] = $rank - 1;
            $sqlVal2['rank'] = $rank;
            $where = "member_id = ?";

            // ランク入れ替えの実行
            $objQuery->begin();
            $objQuery->update("dtb_member", $sqlVal1, $where, array($id));
            $objQuery->update("dtb_member", $sqlVal2, $where, array($down_id));
            $objQuery->commit();
        }
    }
}
?>
