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
        // おすすめ商品取得
        $objQuery = SC_Query::getSingletonInstance();
        $sql = '';
        $sql .= ' SELECT';
        $sql .= ' DISTINCT';
        $sql .= '    T1.best_id,';
        $sql .= '    T1.category_id,';
        $sql .= '    T1.rank,';
        $sql .= '    T1.product_id,';
        $sql .= '    T1.title,';
        $sql .= '    T1.comment,';
        $sql .= '    T1.create_date,';
        $sql .= '    T1.update_date';
        $sql .= ' FROM';
        $sql .= '   dtb_best_products AS T1';
        $sql .= ' WHERE';
        $sql .= '   del_flg = 0';
        $objQuery->setOrder('rank');
        $objQuery->setLimit(RECOMMEND_NUM);
        $arrBestProducts = $objQuery->getAll($sql);
        // 各商品の詳細情報を取得
        $objQuery = SC_Query::getSingletonInstance();
        $arrProduct = array();
        $objProduct = new SC_Product();
        foreach( $arrBestProducts as $key => $val ) {
            $where = 'product_id = ' . $val['product_id'];
            $objQuery->setWhere($where);
            $arrProductLsit = $objProduct->lists($objQuery);
            if ( !empty($arrProductLsit) ) {
                $arrProduct[$key] = array_merge($val, $arrProductLsit[0]);
            }
        }
        return $arrProduct;
    }
}
?>
