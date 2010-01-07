<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
 * 画像詳細 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Products_DetailImage extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/detail_image.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCartSess = new SC_CartSession();
        $objDb = new SC_Helper_DB_Ex();

        // 管理ページからの確認の場合は、非公開の商品も表示する。
        if(isset($_GET['admim']) && $_GET['admin'] == 'on') {
            $where = "del_flg = 0";
        } else {
            $where = "del_flg = 0 AND status = 1";
        }

        // 値の正当性チェック
        if(!SC_Utils_Ex::sfIsInt($_GET['product_id']) || !$objDb->sfIsRecord("dtb_products", "product_id", $_GET['product_id'], $where)) {
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        }


        $image_key = $_GET['image'];

        $objQuery = new SC_Query();
         // カラムが存在していなければエラー画面を表示
        if(!$objDb->sfColumnExists("dtb_products",$image_key)){
            SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
        }
        $col = "name, $image_key";
        
        $arrRet = $objQuery->select($col, "dtb_products", "product_id = ?", array($_GET['product_id']));
		$image_path = IMAGE_SAVE_DIR . $arrRet[0][$image_key];
		
		if(file_exists($image_path)) {
	        list($width, $height) = getimagesize($image_path);
		} else {
			$width = 0;
			$height = 0;
		}
	    
		$this->tpl_width = $width;
	    $this->tpl_height = $height;
        $this->tpl_table_width = $this->tpl_width + 20;
        $this->tpl_table_height = $this->tpl_height + 20;

        $this->tpl_image = $arrRet[0][$image_key];
        $this->tpl_name = $arrRet[0]['name'];

        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
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
