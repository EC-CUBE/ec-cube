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

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * こんな商品も買っていますプラグインの管理画面を制御するクラス.
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id$
 */
class LC_Page_Admin_Plugin_Recommend extends LC_Page_Ex {

    /** プラグイン情報配列 (呼び出し元でセットする) */
    var $arrPluginInfo;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = $this->arrPluginInfo['fullpath'] . 'tpl/admin/index.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = $this->arrPluginInfo['path'];
        $this->tpl_subtitle = "プラグイン「{$this->arrPluginInfo['name']}」の設定";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess(new SC_Session());

        $objView = new SC_AdminView_Ex();
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
}
?>
