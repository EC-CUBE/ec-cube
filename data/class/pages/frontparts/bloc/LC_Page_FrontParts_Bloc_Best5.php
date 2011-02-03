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
require_once(CLASS_REALDIR . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * Best5 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Best5 extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->setTplMainpage('best5.tpl');
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

        // 基本情報を渡す
        $objSiteInfo = new SC_SiteInfo();
        $this->arrInfo = $objSiteInfo->data;

        //おすすめ商品表示
        $this->arrBestProducts = $this->lfGetRanking();
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
     * おすすめ商品検索.
     *
     * @return array $arrBestProducts 検索結果配列
     */
    function lfGetRanking(){
        $objQuery = SC_Query::getSingletonInstance();
        // FIXME SC_Product クラスを使用した実装
        $col = 'DISTINCT A.*, name, price02_min, price01_min, main_list_image ';
        $from = 'dtb_best_products AS A INNER JOIN vw_products_allclass AS allcls using(product_id)';
        $where = 'allcls.del_flg = 0 AND allcls.status = 1';
        
        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $where .= ' AND (allcls.stock_max >= 1 OR allcls.stock_unlimited_max = 1)';
        }
        
        $order = 'rank';
        $objQuery->setOrder($order);
        $objQuery->setLimit(RECOMMEND_NUM);

        $arrBestProducts = $objQuery->select($col, $from, $where);

        return $arrBestProducts;
    }
}
?>
