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

/*  [名称] SC_Product
 *  [概要] 商品クラス
 */
class SC_Product {

    /** 規格名一覧 */
    var $arrClassName;
    /** 規格分類名一覧 */
    var $arrClassCatName;
    var $classCategories = array();
    var $stock_find;
    /** 規格1クラス名 */
    var $className1 = '';
    /** 規格2クラス名 */
    var $className2 = '';
    /** 規格1が設定されている */
    var $classCat1_find;
    /** 規格2が設定されている */
    var $classCat2_find;
    var $classCats1;
    
    function SC_Product($arrProductId = null) {
        $objDb = new SC_Helper_DB_Ex();
        
        // 規格名一覧
        $this->arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $this->arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
        $this->arrClassCatName[''] = '選択してください';
        
        if (!is_null($arrProductId)) {
            $this->setProductId($arrProductId);
        }
    }

    function setProductId($arrProductId) {
        
        // 商品規格情報の取得
        $rows = $this->lfGetProductsClass($arrProductId);
        
        $arrProductsClass = array();
        foreach ($rows as $row) {
            $productId = $row['product_id'];
            $arrProductsClass[$productId][] = $row;
        }
        unset($rows);
        
        foreach ($arrProductsClass as $productId => $arrProductClass) {
            $classCats1 = array();
            $classCats1[''] = '選択してください';
            
            // 規格1クラス名
            $this->className1[$productId] =
                isset($this->arrClassName[$arrProductClass[0]['class_id1']])
                ? $this->arrClassName[$arrProductClass[0]['class_id1']]
                : '';

            // 規格2クラス名
            $this->className2[$productId] =
                isset($this->arrClassName[$arrProductClass[0]['class_id2']])
                ? $this->arrClassName[$arrProductClass[0]['class_id2']]
                : '';
            
            // 規格1が設定されている
            $this->classCat1_find[$productId] = ($arrProductClass[0]['classcategory_id1'] != '0');
            // 規格2が設定されている
            $this->classCat2_find[$productId] = ($arrProductClass[0]['classcategory_id2'] != '0');

            $this->stock_find[$productId] = false;
            $classCategories = array();
            $classCategories['']['']['name'] = '選択してください';
            foreach ($arrProductClass as $productsClass) {
                $productsClass1 = $productsClass['classcategory_id1'];
                $productsClass2 = $productsClass['classcategory_id2'];
                $classCategories[$productsClass1]['']['name'] = '選択してください';

                // 在庫
                $stock_find_class = ($productsClass['stock_unlimited'] || $productsClass['stock'] > 0);
                
                $classCategories[$productsClass1][$productsClass2]['name'] = $this->arrClassCatName[$productsClass2]
                    . ($stock_find_class ? '' : ' (品切れ中)');
                
                $classCategories[$productsClass1][$productsClass2]['stock_find'] = $stock_find_class;
                
                if ($stock_find_class) {
                    $this->stock_find[$productId] = true;
                }

                if (!in_array($classcat_id1, $classCats1)) {
                    $classCats1[$productsClass1] = $this->arrClassCatName[$productsClass1]
                        . ($productsClass2 == 0 && !$stock_find_class ? ' (品切れ中)' : '');
                }
                
                // 価格
                $classCategories[$productsClass1][$productsClass2]['price01']
                    = strlen($productsClass['price01'])
                    ? number_format(SC_Helper_DB_Ex::sfPreTax($productsClass['price01']))
                    : '';
                
                $classCategories[$productsClass1][$productsClass2]['price02']
                    = strlen($productsClass['price02'])
                    ? number_format(SC_Helper_DB_Ex::sfPreTax($productsClass['price02']))
                    : '';
                
                // ポイント
                // XXX sfPrePoint() の第4パラメータは、処理にバグがあるため現状省略している。(http://xoops.ec-cube.net/modules/newbb/viewtopic.php?topic_id=3540&forum=1&post_id=13853#forumpost13853)
                $classCategories[$productsClass1][$productsClass2]['point']
                    = SC_Utils_Ex::sfPrePoint($productsClass['price02'], $productsClass['point_rate']);
                
                // 商品コード
                $classCategories[$productsClass1][$productsClass2]['product_code'] = $productsClass['product_code'];
            }
            
            $this->classCategories[$productId] = $classCategories;
            
            // 規格1
            $this->classCats1[$productId] = $classCats1;
        }
    }

    /* 商品規格情報の取得 */
    function lfGetProductsClass($arrProductId) {
        $arrProductId = (Array) $arrProductId;
        
        if (empty($arrProductId)) {
            return array();
        }
        
        // 商品規格取得
        $objQuery = new SC_Query();
        $col = 'product_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited, price01, price02, point_rate, product_code';
        $table = 'vw_product_class AS prdcls';
        $where = 'product_id IN (' . implode(', ', array_pad(array(), count($arrProductId), '?')) . ')';
        $objQuery->setOrder("product_id, rank1 DESC, rank2 DESC");
        $arrRet = $objQuery->select($col, $table, $where, $arrProductId);
        
        return $arrRet;
    }
}
?>
