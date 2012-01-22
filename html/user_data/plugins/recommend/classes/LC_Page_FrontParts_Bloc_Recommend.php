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

require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

/**
 * こんな商品も買っていますプラグインを制御するクラス
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Recommend extends LC_Page_FrontParts_Bloc {

    /** プラグイン情報配列 (呼び出し元でセットする) */
    var $arrPluginInfo;

    /** 取得する上限数 */
    var $max = 4;

    var $arrRecommendProducts = array();

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = $this->arrPluginInfo['fullpath'] . 'tpl/bloc.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSubView = new SC_SiteView_Ex(false);

        $this->arrRecommendProducts = $this->lfGetRecommendProducts($_REQUEST['product_id']);

        $objSubView->assignobj($this);
        $objSubView->display($this->tpl_mainpage);
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
     * デストラクタ.
     *
     * @return void
     */
    function lfGetRecommendProducts($product_id) {

        $objQuery = new SC_Query();
        $cols = '*, (SELECT COUNT(*) FROM dtb_order_detail WHERE product_id = alldtl.product_id) AS cnt';
        $from = 'vw_products_allclass_detail AS alldtl';
        $where = <<< __EOS__
            del_flg = 0
            AND status = 1
            AND product_id IN (
                SELECT product_id
                FROM
                    dtb_order_detail
                    INNER JOIN dtb_order
                        ON dtb_order_detail.order_id = dtb_order.order_id
                WHERE 0=0
                    AND dtb_order.del_flg = 0
                    AND dtb_order.order_id IN (
                        SELECT order_id
                        FROM dtb_order_detail
                        WHERE 0=0
                            AND product_id = ?
                    )
                    AND dtb_order_detail.product_id <> ?
            )
__EOS__;
        $objQuery->setorder('cnt DESC, RANDOM()');
        $objQuery->setlimit($this->max);
        $recommendProducts = $objQuery->select($cols, $from, $where, array($product_id, $product_id));

        return $recommendProducts;
    }
}
