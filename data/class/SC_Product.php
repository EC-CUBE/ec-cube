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

/**
 * 商品を扱うサービスクラス.
 *
 * @author LOCKON CO.,LTD.
 * @author Kentaro Ohkouchi
 * @version $Id$
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
    /** 検索用並び替え条件配列 */
    var $arrOrderData;

    /**
     * 商品検索結果の並び順を指定する。
     *
     * ただし指定できるテーブルはproduct_idを持っているテーブルであることが必要.
     *
     * @param string $col 並び替えの基準とするフィールド
     * @param string $table 並び替えの基準とするフィールドがあるテーブル
     * @param string $order 並び替えの順序 ASC / DESC
     * @return void
     */
    function setProductsOrder($col, $table = 'dtb_products', $order = 'ASC') {
        $this->arrOrderData = array('col' => $col, 'table' => $table, 'order' => $order);
    }

    /**
     * SC_Queryインスタンスに設定された検索条件を元に並び替え済みの検索結果商品IDの配列を取得する。
     *
     * 検索条件は, SC_Query::getWhere() 関数で設定しておく必要があります.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param array $arrVal 検索パラメータの配列
     * @return array 商品IDの配列
     */
    function findProductIdsOrder(&$objQuery, $arrVal = array(), $where) {
        $table = <<< __EOS__
                 dtb_products AS alldtl
            JOIN dtb_products_class AS T1
              ON alldtl.product_id = T1.product_id
            JOIN dtb_product_categories AS T2
              ON alldtl.product_id = T2.product_id
            JOIN dtb_category
              ON T2.category_id = dtb_category.category_id
__EOS__;
        $objQuery->setGroupBy('alldtl.product_id');
        if(is_array($this->arrOrderData) and $objQuery->order == ""){
            $o_col = $this->arrOrderData['col'];
            $o_table = $this->arrOrderData['table'];
            $o_order = $this->arrOrderData['order'];
            $order = <<< __EOS__
                    (
                        SELECT $o_col
                        FROM
                            $o_table as T2
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T2.$o_col $o_order
                        LIMIT 1
                    ) $o_order, product_id
__EOS__;
            $objQuery->setOrder($order);
        }
        $results = $objQuery->select('alldtl.product_id', $table, "", $arrVal,
                                     MDB2_FETCHMODE_ORDERED);
        foreach ($results as $val) {
            $resultValues[] = $val[0];
        }
        return $resultValues;
    }

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに対象商品数を取得する.
     *
     * 検索条件は, SC_Query::getWhere() 関数で設定しておく必要があります.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param array $arrVal 検索パラメータの配列
     * @return array 対象商品ID数
     */
    function findProductCount(&$objQuery, $arrVal = array()) {
        $table = <<< __EOS__
                 dtb_products AS alldtl
            JOIN dtb_product_categories AS T2
              ON alldtl.product_id = T2.product_id
            JOIN dtb_category
              ON T2.category_id = dtb_category.category_id
__EOS__;
        $objQuery->setGroupBy('alldtl.product_id');
        $sql_base = $objQuery->getSql('alldtl.product_id',$table);
        return $objQuery->getOne( "SELECT count(*) FROM ( $sql_base ) as t" , $arrVal);
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
        $res = $objQuery->select($col, $this->alldtlSQL($objQuery->where),
                                 "", $arrVal);
        return $res;
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
     * 商品詳細情報と商品規格を取得する.
     *
     * @param integer $productClassId 商品規格ID
     * @return array 商品詳細情報と商品規格の配列
     */
    function getDetailAndProductsClass($productClassId) {
        $result = $this->getProductsClass($productClassId);
        $result = array_merge($result, $this->getDetail($result['product_id']));
        return $result;
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
            $classCategories['']['']['product_class_id'] = $arrProductClass[0]['product_class_id'];
            // 商品種別
            $classCategories['']['']['product_type'] = $arrProductClass[0]['product_type_id'];
            $this->product_class_id[$productId] = $arrProductClass[0]['product_class_id'];
            // 商品種別
            $this->product_type[$productId] = $arrProductClass[0]['product_type_id'];
            foreach ($arrProductClass as $productsClass) {
                $productsClass1 = $productsClass['classcategory_id1'];
                $productsClass2 = $productsClass['classcategory_id2'];
                $classCategories[$productsClass1]['']['name'] = '選択してください';
                // 在庫
                $stock_find_class = ($productsClass['stock_unlimited'] || $productsClass['stock'] > 0);

                $classCategories[$productsClass1][$productsClass2]['name'] = $productsClass['classcategory_name2'] . ($stock_find_class ? '' : ' (品切れ中)');

                $classCategories[$productsClass1][$productsClass2]['stock_find'] = $stock_find_class;

                if ($stock_find_class) {
                    $this->stock_find[$productId] = true;
                }

                if (!in_array($classcat_id1, $classCats1)) {
                    $classCats1[$productsClass1] = $productsClass['classcategory_name1']
                        . ($productsClass2 == 0 && !$stock_find_class ? ' (品切れ中)' : '');
                }

                // 価格
                $classCategories[$productsClass1][$productsClass2]['price01']
                    = strlen($productsClass['price01'])
                    ? number_format(SC_Helper_DB_Ex::sfCalcIncTax($productsClass['price01']))
                    : '';

                $classCategories[$productsClass1][$productsClass2]['price02']
                    = strlen($productsClass['price02'])
                    ? number_format(SC_Helper_DB_Ex::sfCalcIncTax($productsClass['price02']))
                    : '';

                // ポイント
                // XXX sfPrePoint() の第4パラメータは、処理にバグがあるため現状省略している。(http://xoops.ec-cube.net/modules/newbb/viewtopic.php?topic_id=3540&forum=1&post_id=13853#forumpost13853)
                $classCategories[$productsClass1][$productsClass2]['point']
                    = SC_Utils_Ex::sfPrePoint($productsClass['price02'], $productsClass['point_rate']);

                // 商品コード
                $classCategories[$productsClass1][$productsClass2]['product_code'] = $productsClass['product_code'];
                // 商品規格ID
                $classCategories[$productsClass1][$productsClass2]['product_class_id'] = $productsClass['product_class_id'];
                // 商品種別
                $classCategories[$productsClass1][$productsClass2]['product_type'] = $productsClass['product_type_id'];
            }

            $this->classCategories[$productId] = $classCategories;

            // 規格1
            $this->classCats1[$productId] = $classCats1;
        }
    }

    /**
     * SC_Query インスタンスに設定された検索条件を使用して商品規格を取得する.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $params 検索パラメータの配列
     * @return array 商品規格の配列
     */
    function getProductsClassByQuery(&$objQuery, $params) {
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
            T1.product_type_id,
            T1.down_filename,
            T1.down_realfilename,
            T2.class_combination_id,
            T2.parent_class_combination_id,
            T2.classcategory_id,
            T2.level,
            T3.name AS classcategory_name,
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
        $arrRet = $objQuery->select($col, $table, "", $params);
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
                T2.name AS classcategory_name,
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
            $val['classcategory_name' . $val['level']] = $val['classcategory_name'];
            $val['classcategory_id' . $val['level']] = $val['classcategory_id'];
            $arrProductsClass[] = $val;
        }

        return $arrProductsClass;
    }

    /**
     * 商品規格IDから商品規格を取得する.
     */
    function getProductsClass($productClassId) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setWhere('product_class_id = ?');
        $objQuery->setOrder("T2.level DESC");
        $results = $this->getProductsClassByQuery($objQuery, $productClassId);
        $productsClass = $this->getProductsClassFull($results);
        return $productsClass[0];
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
        return $this->getProductsClassByQuery($objQuery, $productIds);
    }

    /**
     * 商品IDに紐づいた, 商品規格を階層ごとに取得する.
     *
     * @param array $productId 商品ID
     * @return array 階層ごとの商品規格の配列
     */
    function getProductsClassLevelByProductId($productId) {
        $results = $this->getProductsClassByProductIds(array($productId));
        return $this->getProductsClassLevel($results);
    }

    /**
     * 商品IDに紐づいた, 商品規格をすべての組み合わせごとに取得する.
     *
     * @param array $productId 商品ID
     * @return array すべての組み合わせの商品規格の配列
     */
    function getProductsClassFullByProductId($productId) {
        $results = $this->getProductsClassByProductIds(array($productId));
        return $this->getProductsClassFull($results);
    }

    /**
     * 商品規格の配列から, 商品規格を階層ごとに取得する.
     *
     * @access private
     * @param array $productsClassResults 商品規格の結果の配列
     * @return array 階層ごとの商品規格の配列
     */
    function getProductsClassLevel($productsClassResults) {
        foreach ($productsClassResults as $row) {
            $productsClassLevel["level" . $row['level']][] = $row;
        }
        return $productsClassLevel;
    }

    /**
     * 商品規格の配列から, 商品規格のすべての組み合わせを取得する.
     *
     * @access private
     * @param array $productsClassResults 商品規格の結果の配列
     * @ array 階層ごとの商品規格の配列
     */
    function getProductsClassFull($productsClassResults) {
        $results = $this->getProductsClassLevel($productsClassResults);
        $productsClass = array();
        if (SC_Utils_Ex::isBlank($results["level1"])
            && SC_Utils_Ex::isBlank($results["level2"])) {
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
     * 商品IDをキーにした, 商品ステータスIDの配列を取得する.
     *
     * @param array 商品ID の配列
     * @return array 商品IDをキーにした商品ステータスIDの配列
     */
    function getProductStatus($productIds) {
        if (empty($productIds)) {
            return array();
        }
        $objQuery =& SC_Query::getSingletonInstance();
        $cols = 'product_id, product_status_id';
        $from = 'dtb_product_status';
        $where = 'del_flg = 0 AND product_id IN (' . implode(', ', array_pad(array(), count($productIds), '?')) . ')';
        $productStatus = $objQuery->select($cols, $from, $where, $productIds);
        $results = array();
        foreach ($productStatus as $status) {
            $results[$status['product_id']][] = $status['product_status_id'];
        }
        return $results;
    }

    /**
     * 商品ステータスを設定する.
     *
     * TODO 現在は DELETE/INSERT だが, UPDATE を検討する.
     *
     * @param integer $productId 商品ID
     * @param array $productStatusIds ON にする商品ステータスIDの配列
     */
    function setProductStatus($productId, $productStatusIds) {

        $val['product_id'] = $productId;
        $val['creator_id'] = $_SESSION['member_id'];
        $val['create_date'] = 'Now()';
        $val['update_date'] = 'Now()';
        $val['del_flg'] = '0';

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->delete('dtb_product_status', 'product_id = ?', array($productId));
        foreach ($productStatusIds as $productStatusId) {
            $val['product_status_id'] = $productStatusId;
            $objQuery->insert('dtb_product_status', $val);
        }
    }

    /**
     * 商品詳細の結果から, 購入制限数を取得する.
     *
     * getDetailAndProductsClass() の結果から, 購入制限数を取得する.
     *
     * @param array $p 商品詳細の検索結果の配列
     * @return integer 商品詳細の結果から求めた購入制限数.
     * @see getDetailAndProductsClass()
     */
    function getBuyLimit($p) {
        $limit = null;
        if ($p['stock_unlimited'] != '1' && is_numeric($p['sale_limit'])) {
            $limit = min($p['sale_limit'], $p['stock']);
        } elseif (is_numeric($p['sale_limit'])) {
            $limit = $p['sale_limit'];
        } elseif ($p['stock_unlimited'] != '1') {
            $limit = $p['stock'];
        }
        return $limit;
    }

    /**
     * 在庫を減少させる.
     *
     * 指定の在庫数まで, 在庫を減少させる.
     * 減少させた結果, 在庫数が 0 未満になった場合, 引数 $quantity が 0 の場合は,
     * 在庫の減少を中止し, false を返す.
     * 在庫の減少に成功した場合は true を返す.
     *
     * @param integer $productClassId 商品規格ID
     * @param integer $quantity 減少させる在庫数
     * @return boolean 在庫の減少に成功した場合 true; 失敗した場合 false
     */
    function reduceStock($productClassId, $quantity) {

        $productsClass = $this->getDetailAndProductsClass($productClassId);
        if (($productsClass['stock_unlimited'] != '1'
             && $productsClass['stock'] < $quantity)
            || $quantity == 0) {
            return false;
        }

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->update('dtb_products_class', array(),
                          "product_class_id = ?", array($productClassId),
                          array('stock' => 'stock - ?'), array($quantity));
        // TODO エラーハンドリング
        return true;
    }

    /**
     * 引数の商品規格IDで有効な支払方法IDの配列を取得する.
     *
     * @param array $productClassIds 商品規格IDの配列
     * @return array 支払方法IDの配列
     */
    function getEnablePaymentIds($productClassIds) {
        $size = count($productClassIds);
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->groupby = 'GROUP BY payment_id HAVING COUNT(payment_id) = ?';
        $paymentIds = $objQuery->getCol('payment_id', 'dtb_payment_options',
                                        'product_class_id IN (' . implode(', ', array_pad(array(), $size, '?')) . ')',
                                        array_merge($productClassIds, array($size)),
                                        MDB2_FETCHMODE_ORDERED);
        return $paymentIds;
    }

    /**
     * 商品規格に支払方法を設定する.
     *
     * TODO 現在は DELETE/INSERT だが, UPDATE を検討する.
     *
     * @param integer $productClassId 商品規格ID
     * @param array 設定する支払方法IDの配列
     * @return void
     */
    function setPaymentOptions($productClassId, $paymentIds) {
        $val['product_class_id'] = $productClassId;

        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->delete('dtb_payment_options', 'product_class_id = ?', array($productClassId));
        $rank = 1;
        foreach ($paymentIds as $paymentId) {
            $val['payment_id'] = $paymentId;
            $val['rank'] = $rank;
            $objQuery->insert('dtb_payment_options', $val);
            $rank++;
        }
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
                    dtb_products.status,
                    dtb_products.comment1,
                    dtb_products.comment2,
                    dtb_products.comment3,
                    dtb_products.comment4,
                    dtb_products.comment5,
                    dtb_products.comment6,
                    dtb_products.note,
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

    /**
     * 商品規格詳細の SQL を取得する.
     *
     * MEMO: 2.4系 vw_product_classに相当(?)するイメージ
     *
     * @param string $where 商品詳細の WHERE 句
     * @return string 商品規格詳細の SQL
     */
    function prdclsSQL($where = "") {
        $whereCause = "";
        if (!SC_Utils_Ex::isBlank($where)) {
            $whereCause = " WHERE " . $where;
        }
        $sql = <<< __EOS__
        (
             SELECT dtb_products.*,
                    dtb_products_class.product_class_id,
                    dtb_products_class.class_combination_id,
                    dtb_products_class.product_type_id,
                    dtb_products_class.product_code,
                    dtb_products_class.stock,
                    dtb_products_class.stock_unlimited,
                    dtb_products_class.sale_limit,
                    dtb_products_class.price01,
                    dtb_products_class.price02,
                    dtb_products_class.deliv_fee,
                    dtb_products_class.point_rate,
                    dtb_products_class.down_filename,
                    dtb_products_class.down_realfilename,
                    dtb_class_combination.parent_class_combination_id,
                    dtb_class_combination.classcategory_id,
                    dtb_class_combination.level as classlevel,
                    Tpcm.classcategory_id as parent_classcategory_id,
                    Tpcm.level as parent_classlevel,
                    Tcc1.class_id as class_id,
                    Tcc1.name as classcategory_name,
                    Tcc2.class_id as parent_class_id,
                    Tcc2.name as parent_classcategory_name
             FROM dtb_products
                 LEFT JOIN dtb_products_class
                     ON dtb_products.product_id = dtb_products_class.product_id
                 LEFT JOIN dtb_class_combination
                     ON dtb_products_class.class_combination_id = dtb_class_combination.class_combination_id
                 LEFT JOIN dtb_class_combination as Tpcm
                     ON dtb_class_combination.parent_class_combination_id = Tpcm.class_combination_id
                 LEFT JOIN dtb_classcategory as Tcc1
                     ON dtb_class_combination.classcategory_id = Tcc1.classcategory_id
                 LEFT JOIN dtb_classcategory as Tcc2
                     ON Tpcm.classcategory_id = Tcc2.classcategory_id
             $whereCause
        ) as prdcls
__EOS__;
        return $sql;
    }
    
    /**
     * 商品規格ID1、2に紐づいた,product_class_idを取得する.
     *
     * @param int $productId 商品ID
     * @param int $classcategory_id1 商品規格ID1
     * @param int $classcategory_id2 商品規格ID2
     * @return string product_class_id
     */
    function getClasscategoryIdsByProductClassId($productId, $classcategory_id1, $classcategory_id2) {
        $objQuery = new SC_Query();
        $col = "T1.product_id AS product_id,T1.product_class_id AS product_class_id,T1.classcategory_id1 AS classcategory_id1,T1.classcategory_id2 AS classcategory_id2";
        $table = <<< __EOS__
            (SELECT
                pc.product_code AS product_code,
                pc.product_id AS product_id,
                pc.product_class_id AS product_class_id,
                pc.class_combination_id AS class_combination_id,
                COALESCE(cc2.classcategory_id,0) AS classcategory_id1,
                COALESCE(cc1.classcategory_id,0) AS classcategory_id2
            FROM
                dtb_products_class pc LEFT JOIN dtb_class_combination cc1 ON pc.class_combination_id = cc1.class_combination_id
                LEFT JOIN dtb_class_combination cc2 ON cc1.parent_class_combination_id = cc2.class_combination_id) T1
__EOS__;
        $where = "T1.product_id = ? AND T1.classcategory_id1 = ? AND T1.classcategory_id2 = ?";
        $arrRet = $objQuery->select($col, $table, $where,
                                    array($productId, $classcategory_id1, $classcategory_id2));
        return $arrRet[0]['product_class_id'];
    }

}
?>
