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

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに商品IDの配列を取得する.
     *
     * 検索条件は, SC_Query::getWhere() 関数で設定しておく必要があります.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param array $arrVal 検索パラメータの配列
     * @return array 商品IDの配列
     */
    function findProductIds(&$objQuery, $arrVal = array()) {
        $table = <<< __EOS__
                 dtb_products AS alldtl
            JOIN dtb_product_categories AS T2
              ON alldtl.product_id = T2.product_id
            JOIN dtb_category
              ON T2.category_id = dtb_category.category_id
__EOS__;
        // SC_Query::getCol() ではパフォーマンスが出ない
        $results = $objQuery->select('alldtl.product_id', $table, "", $arrVal,
                                     MDB2_FETCHMODE_ORDERED);
        foreach ($results as $val) {
            $resultValues[] = $val[0];
        }
        return array_unique($resultValues);
    }

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに商品一覧の配列を取得する.
     *
     * 主に SC_Product::findProductIds() で取得した商品IDを検索条件にし,
     * SC_Query::setOrder() や SC_Query::setLimitOffset() を設定して, 商品一覧
     * の配列を取得する.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param array $arrVal 検索パラメータ(ソート条件)の配列
     * @return array 商品一覧の配列
     */
    function lists(&$objQuery, $arrVal = array()) {
        $col = <<< __EOS__
             product_id
            ,product_code_min
            ,product_code_max
            ,name
            ,comment1
            ,comment2
            ,comment3
            ,main_list_comment
            ,main_image
            ,main_list_image
            ,price01_min
            ,price01_max
            ,price02_min
            ,price02_max
            ,stock_min
            ,stock_max
            ,stock_unlimited_min
            ,stock_unlimited_max
            ,deliv_date_id
            ,status
            ,del_flg
            ,update_date
__EOS__;
        return $objQuery->select($col, $this->alldtlSQL($objQuery->where),
                                 "", $arrVal);
    }

    /**
     * 商品詳細を取得する.
     *
     * @param integer $productId 商品ID
     * @return array 商品詳細情報の配列
     */
    function getDetail($productId) {
        $objQuery =& SC_Query::getSingletonInstance();
        $result = $objQuery->select("*", $this->alldtlSQL("product_id = ?"),
                                    "product_id = ?",
                                    array($productId, $productId));
        return $result[0];
    }

    /**
     * 商品IDに紐づく商品規格を自分自身に設定する.
     *
     * 引数の商品IDの配列に紐づく商品規格を取得し, 自分自身のフィールドに
     * 設定する.
     *
     * @param array $arrProductId 商品ID の配列
     * @return void
     */
    function setProductsClassByProductIds($arrProductId) {

        foreach ($arrProductId as $productId) {
            $rows[$productId] = $this->getProductsClassFullByProductId($productId);
        }

        $arrProductsClass = array();
        foreach ($rows as $productId => $arrProductClass) {
            $classCats1 = array();
            $classCats1[''] = '選択してください';

            // 規格1クラス名
            $this->className1[$productId] =
                isset($arrProductClass[0]['class_name1'])
                ? $arrProductClass[0]['class_name1']
                : '';

            // 規格2クラス名
            $this->className2[$productId] =
                isset($arrProductClass[0]['class_name2'])
                ? $arrProductClass[0]['class_name2']
                : '';

            // 規格1が設定されている
            $this->classCat1_find[$productId] = (!SC_Utils_Ex::isBlank($arrProductClass[0]['classcategory_id1']));
            // 規格2が設定されている
            $this->classCat2_find[$productId] = (!SC_Utils_Ex::isBlank($arrProductClass[0]['classcategory_id2']));

            $this->stock_find[$productId] = false;
            $classCategories = array();
            $classCategories['']['']['name'] = '選択してください';
            foreach ($arrProductClass as $productsClass) {
                $productsClass1 = $productsClass['classcategory_id1'];
                $productsClass2 = $productsClass['classcategory_id2'];
                $classCategories[$productsClass1]['']['name'] = '選択してください';
                // 在庫
                $stock_find_class = ($productsClass['stock_unlimited'] || $productsClass['stock'] > 0);

                $classCategories[$productsClass1][$productsClass2]['name'] = $productsClass['name2'] . ($stock_find_class ? '' : ' (品切れ中)');

                $classCategories[$productsClass1][$productsClass2]['stock_find'] = $stock_find_class;

                if ($stock_find_class) {
                    $this->stock_find[$productId] = true;
                }

                if (!in_array($classcat_id1, $classCats1)) {
                    $classCats1[$productsClass1] = $productsClass['name1']
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

    /**
     * 複数の商品IDに紐づいた, 商品規格を取得する.
     *
     * @param array $productIds 商品IDの配列
     * @return array 商品規格の配列
     */
    function getProductsClassByProductIds($productIds = array()) {
        if (empty($productIds)) {
            return array();
        }
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere('product_id IN (' . implode(', ', array_pad(array(), count($productIds), '?')) . ')');
        $objQuery->setOrder("T2.level DESC");
        // 末端の規格を取得
        $col = <<< __EOS__
            T1.product_id,
            T1.stock,
            T1.stock_unlimited,
            T1.price01,
            T1.price02,
            T1.point_rate,
            T1.product_code,
            T1.product_class_id,
            T1.del_flg,
            T1.down,
            T1.down_filename,
            T1.down_realfilename,
            T2.class_combination_id,
            T2.parent_class_combination_id,
            T2.classcategory_id,
            T2.level,
            T3.name,
            T4.name AS class_name,
            T4.class_id
__EOS__;
        $table = <<< __EOS__
                      dtb_products_class T1
            LEFT JOIN dtb_class_combination T2
                   ON T1.class_combination_id = T2.class_combination_id
            LEFT JOIN dtb_classcategory T3
                   ON T2.classcategory_id = T3.classcategory_id
            LEFT JOIN dtb_class T4
                   ON T3.class_id = T4.class_id
__EOS__;
        $arrRet = $objQuery->select($col, $table, "", $productIds);
        $levels = array();
        $parents = array();
        foreach ($arrRet as $rows) {
            $levels[] = $rows['level'];
            $parents[] = $rows['parent_class_combination_id'];
        }
        $level = max($levels);
        $parentsClass = array();
        // 階層分の親を取得
        for ($i = 0; $i < $level -1; $i++) {
            $objQuery =& SC_Query::getSingletonInstance();
            $objQuery->setWhere('T1.class_combination_id IN (' . implode(', ', array_pad(array(), count($parents), '?')) . ')');

            $col = <<< __EOS__
                T1.class_combination_id,
                T1.classcategory_id,
                T1.parent_class_combination_id,
                T1.level,
                T2.name,
                T3.name AS class_name,
                T3.class_id
__EOS__;
            $table = <<< __EOS__
                          dtb_class_combination T1
                LEFT JOIN dtb_classcategory T2
                       ON T1.classcategory_id = T2.classcategory_id
                LEFT JOIN dtb_class T3
                       ON T2.class_id = T3.class_id
__EOS__;

            $arrParents = $objQuery->select($col, $table, "", $parents);

            unset($parents);
            foreach ($arrParents as $rows) {
                $parents[] = $rows['parent_class_combination_id'];

                foreach ($arrRet as $child) {
                    if ($child['parent_class_combination_id']
                        == $rows['class_combination_id']) {
                        $rows['product_id'] = $child['product_id'];
                    }
                }
                $tmpParents[] = $rows;
            }
            $parentsClass = array_merge($parentsClass, $tmpParents);
        }

        // 末端から枝を作成
        $tmpClass = array_merge($arrRet, $parentsClass);

        foreach ($tmpClass as $val) {
            $val['class_id' . $val['level']] = $val['class_id'];
            $val['class_name' . $val['level']] = $val['class_name'];
            $val['name' . $val['level']] = $val['name'];
            $val['classcategory_id' . $val['level']] = $val['classcategory_id'];
            $arrProductsClass[] = $val;
        }

        return $arrProductsClass;
    }

    /**
     * 商品IDに紐づいた, 商品規格を階層ごとに取得する.
     *
     * @param array $productId 商品IDの配列
     * @return array 階層ごとの商品規格の配列
     */
    function getProductsClassLevelByProductId($productId) {
        $results = $this->getProductsClassByProductIds(array($productId));
        foreach ($results as $row) {
            $productsClassLevel["level" . $row['level']][] = $row;
        }
        return $productsClassLevel;
    }

    /**
     * 商品IDに紐づいた, 商品規格をすべての組み合わせごとに取得する.
     *
     * @param array $productId 商品IDの配列
     * @return array すべての組み合わせの商品規格の配列
     */
    function getProductsClassFullByProductId($productId) {
        $results = $this->getProductsClassLevelByProductId($productId);
        $productsClass = array();
        if (SC_Utils_Ex::isBlank($results["level1"]) && SC_Utils_Ex::isBlank($results["level2"])) {
            return $results["level"];
        }

        foreach ($results["level1"] as $level1) {
            foreach ($results["level2"] as $level2) {
                if ($level2['parent_class_combination_id'] == $level1['class_combination_id']) {
                    $level1 = array_merge($level1, $level2);
                }
            }
            $productsClass[] = $level1;
        }
        return $productsClass;
    }

    /**
     * 商品詳細の SQL を取得する.
     *
     * @param string $where 商品詳細の WHERE 句
     * @return string 商品詳細の SQL
     */
    function alldtlSQL($where = "") {
        $whereCause = "";
        if (!SC_Utils_Ex::isBlank($where)) {
            $whereCause = " WHERE " . $where;
        }
        /*
         * point_rate は商品規格(dtb_products_class)ごとに保持しているが,
         * 商品(dtb_products)ごとの設定なので MAX のみを取得する.
         */
        $sql = <<< __EOS__
            (
             SELECT dtb_products.product_id,
                    dtb_products.name,
                    dtb_products.maker_id,
                    dtb_products.rank,
                    dtb_products.status,
                    dtb_products.comment1,
                    dtb_products.comment2,
                    dtb_products.comment3,
                    dtb_products.comment4,
                    dtb_products.comment5,
                    dtb_products.comment6,
                    dtb_products.note,
                    dtb_products.file1,
                    dtb_products.file2,
                    dtb_products.file3,
                    dtb_products.file4,
                    dtb_products.file5,
                    dtb_products.file6,
                    dtb_products.main_list_comment,
                    dtb_products.main_list_image,
                    dtb_products.main_comment,
                    dtb_products.main_image,
                    dtb_products.main_large_image,
                    dtb_products.sub_title1,
                    dtb_products.sub_comment1,
                    dtb_products.sub_image1,
                    dtb_products.sub_large_image1,
                    dtb_products.sub_title2,
                    dtb_products.sub_comment2,
                    dtb_products.sub_image2,
                    dtb_products.sub_large_image2,
                    dtb_products.sub_title3,
                    dtb_products.sub_comment3,
                    dtb_products.sub_image3,
                    dtb_products.sub_large_image3,
                    dtb_products.sub_title4,
                    dtb_products.sub_comment4,
                    dtb_products.sub_image4,
                    dtb_products.sub_large_image4,
                    dtb_products.sub_title5,
                    dtb_products.sub_comment5,
                    dtb_products.sub_image5,
                    dtb_products.sub_large_image5,
                    dtb_products.sub_title6,
                    dtb_products.sub_comment6,
                    dtb_products.sub_image6,
                    dtb_products.sub_large_image6,
                    dtb_products.del_flg,
                    dtb_products.creator_id,
                    dtb_products.create_date,
                    dtb_products.update_date,
                    dtb_products.deliv_date_id,
                    T4.product_code_min,
                    T4.product_code_max,
                    T4.price01_min,
                    T4.price01_max,
                    T4.price02_min,
                    T4.price02_max,
                    T4.stock_min,
                    T4.stock_max,
                    T4.stock_unlimited_min,
                    T4.stock_unlimited_max,
                    T4.point_rate,
                    T4.class_count
               FROM dtb_products
               JOIN (
                       SELECT product_id,
                              MIN(product_code) AS product_code_min,
                              MAX(product_code) AS product_code_max,
                              MIN(price01) AS price01_min,
                              MAX(price01) AS price01_max,
                              MIN(price02) AS price02_min,
                              MAX(price02) AS price02_max,
                              MIN(stock) AS stock_min,
                              MAX(stock) AS stock_max,
                              MIN(stock_unlimited) AS stock_unlimited_min,
                              MAX(stock_unlimited) AS stock_unlimited_max,
                              MAX(point_rate) AS point_rate,
                              COUNT(*) as class_count
                         FROM dtb_products_class
                       $whereCause
                     GROUP BY product_id
                     ) AS T4
                 ON dtb_products.product_id = T4.product_id
        ) AS alldtl
__EOS__;
        return $sql;
    }
}
?>
