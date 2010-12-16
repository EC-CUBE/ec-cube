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
 * XXX のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System_Delete extends LC_Page_Admin {

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
        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $this->initParam();

        // パラメータの検証
        if ($this->objForm->checkError()
            || !SC_Utils_ex::sfIsInt($id = $this->objForm->getValue('id'))) {

            GC_Utils_Ex::gfPrintLog("error id=$id");
            SC_Utils_Ex::sfDispError(INVALID_MOVE_ERRORR);
        }

        $id = $this->objForm->getValue('id');

        // レコードの削除
        $objQuery =& new SC_Query;
        $objQuery->begin();

        $this->renumberRank($objQuery, $id);
        $this->deleteRecode($objQuery, $id);

        $objQuery->commit();

        // リダイレクト
        $url = $this->getLocation(URL_SYSTEM_TOP)
             . '?pageno=' . $this->objForm->getValue('pageno');
        $this->objDisplay->redirect($url);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function initParam() {
        $objForm = new SC_FormParam;
        $objForm->addParam('pageno', 'pageno', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objForm->addParam('id', 'id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->setParam($_GET);

        $this->objForm = $objForm;
    }

    // ランキングの振り直し
    function renumberRank(&$objQuery, $id) {
        $where = "member_id = ?";
        // ランクの取得
        $rank = $objQuery->get("rank", "dtb_member", $where, array($id));

        // 削除したレコードより上のランキングを下げてRANKの空きを埋める。
        $sqlup =<<<END
        UPDATE
            dtb_member
        SET
            rank = (rank - 1)
        WHERE
            rank > ? AND del_flg <> 1
END;
        // UPDATEの実行
        return $objQuery->query($sqlup, array($rank));
    }

    // レコードの削除(削除フラグをONにする)
    function deleteRecode(&$objQuery, $id) {
        // ランクを最下位にする、DELフラグON
        $sqlup =<<<END
        UPDATE
            dtb_member
        SET
            rank = 0,
            del_flg = 1
        WHERE
            member_id = ?
END;
        // UPDATEの実行
        return $objQuery->query($sqlup, array($id));
    }


}
/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
