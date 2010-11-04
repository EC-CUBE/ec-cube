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
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_System_Rank.php 16582 2007-11-28 15:02:29Z satou $
 */
class LC_Page_Admin_System_Rank extends LC_Page {
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
        $objQuery = new SC_Query();

        // ログインチェック
        SC_Utils::sfIsSuccess(new SC_Session());

        // ランキングの変更
        if($_GET['move'] == 'up') {
            // 正当な数値であった場合
            if(SC_Utils::sfIsInt($_GET['id'])){
                $this->lfRunkUp($objQuery, $_GET['id']);
            // エラー処理
            } else {
                GC_Utils::gfPrintLog("error id=".$_GET['id']);
            }
        } else if($_GET['move'] == 'down') {
            if(SC_Utils::sfIsInt($_GET['id'])){
                $this->lfRunkDown($objQuery, $_GET['id']);
            // エラー処理
            } else {
                GC_Utils::gfPrintLog("error id=".$_GET['id']);
            }
        }
        
        // ページの表示
        $this->sendRedirect($this->getLocation(URL_SYSTEM_TOP));
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
    function lfRunkUp($objQuery, $id) {
        // 自身のランクを取得する。
        $rank = $objQuery->getOne("SELECT rank FROM dtb_member WHERE member_id = ".$id);
        // ランクの最大値を取得する。
        $maxno = $objQuery->getOne("SELECT max(rank) FROM dtb_member");
        // ランクが最大値よりも小さい場合に実行する。
        if($rank < $maxno) {
            // ランクがひとつ上のIDを取得する。
            $sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
            $up_id = $objQuery->getOne($sqlse, $rank + 1);
            // ランク入れ替えの実行
            $objQuery->begin();
            $sqlup = "UPDATE dtb_member SET rank = ? WHERE member_id = ?";
            $objQuery->query($sqlup, array($rank + 1, $id));
            $objQuery->query($sqlup, array($rank, $up_id));
            $objQuery->commit();
        }
    }

    // ランキングを下げる。
    function lfRunkDown($objQuery, $id) {
        // 自身のランクを取得する。
        $rank = $objQuery->getOne("SELECT rank FROM dtb_member WHERE member_id = ".$id);
        // ランクの最小値を取得する。
        $minno = $objQuery->getOne("SELECT min(rank) FROM dtb_member");
        // ランクが最大値よりも大きい場合に実行する。
        if($rank > $minno) {
            // ランクがひとつ下のIDを取得する。
            $sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
            $down_id = $objQuery->getOne($sqlse, $rank - 1);
            // ランク入れ替えの実行
            $objQuery->begin();
            $sqlup = "UPDATE dtb_member SET rank = ? WHERE member_id = ?";
            $objQuery->query($sqlup, array($rank - 1, $id));
            $objQuery->query($sqlup, array($rank, $down_id));
            $objQuery->query("COMMIT");
        }
    }
}
?>
