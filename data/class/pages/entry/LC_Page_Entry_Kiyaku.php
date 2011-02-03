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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * ご利用規約 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Entry_Kiyaku extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "ご利用規約";
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

        $arrKiyaku = $this->lfGetKiyakuData();
        $this->max = count($arrKiyaku);

        $offset    = '';
        // mobile時はGETでページ指定
        if ( Net_UserAgent_Mobile::isMobile() === true ){
            $this->offset = $this->lfSetOffset($_GET['offset']);
        }

        $this->tpl_kiyaku_text = $this->lfMakeKiyakuText($arrKiyaku, $this->max, $this->offset);
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
     * lfMakeKiyakuText
     *
     * 規約文の作成
     *
     * @param mixed $arrKiyaku
     * @param mixed $max
     * @param mixed $offset
     * @access public
     * @return void
     */
    function lfMakeKiyakuText($arrKiyaku, $max, $offset) {
        $this->tpl_kiyaku_text = "";
        for ($i = 0; $i < $max; $i++) {
        	if ($offset !== null && ($offset - 1) <> $i) continue;
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_title'] . "\n\n";
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_text'] . "\n\n";
        }
        return $tpl_kiyaku_text;
    }

    /**
     * lfGetKiyakuData
     *
     * 規約内容の取得
     *
     * @access public
     * @return void
     */
    function lfGetKiyakuData() {

        $objQuery = new SC_Query();
        $objQuery->setOrder("rank DESC");
        $arrRet = $objQuery->select("kiyaku_title, kiyaku_text", "dtb_kiyaku", "del_flg <> 1");

        return $arrRet;
    }

    /**
     * lfSetOffset
     *
     * @param mixed $offset
     * @access public
     * @return void
     */
    function lfSetOffset($offset) {
       return is_numeric($offset) === true ? intval($offset) : 1;
    }

}
?>
