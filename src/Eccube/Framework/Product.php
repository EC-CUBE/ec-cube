<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\DB\DBFactory;

/**
 * 商品を扱うサービスクラス.
 *
 * @author LOCKON CO.,LTD.
 * @author Kentaro Ohkouchi
 */
class Product
{
    /** 規格名一覧 */
    public $arrClassName;
    /** 規格分類名一覧 */
    public $arrClassCatName;
    /** このプロパティが保持する price01 及び price02 は、税金付与した金額である。 */
    public $classCategories = array();
    public $stock_find;
    /** 規格1クラス名 */
    public $className1 = '';
    /** 規格2クラス名 */
    public $className2 = '';
    /** 規格1が設定されている */
    public $classCat1_find;
    /** 規格2が設定されている */
    public $classCat2_find;
    public $classCats1;
    /** 検索用並び替え条件配列 */
    public $arrOrderData;

    /**
     * 商品検索結果の並び順を指定する。
     *
     * ただし指定できるテーブルはproduct_idを持っているテーブルであることが必要.
     *
     * @param  string $col   並び替えの基準とするフィールド
     * @param  string $table 並び替えの基準とするフィールドがあるテーブル
     * @param  string $order 並び替えの順序 ASC / DESC
     * @return void
     */
    public function setProductsOrder($col, $table = 'dtb_products', $order = 'ASC')
    {
        $this->arrOrderData = array('col' => $col, 'table' => $table, 'order' => $order);
    }

    /**
     * Queryインスタンスに設定された検索条件を元に並び替え済みの検索結果商品IDの配列を取得する。
     *
     * 検索条件は, Query::setWhere() 関数で設定しておく必要があります.
     *
     * @param  Query $objQuery Query インスタンス
     * @param  array    $arrVal   検索パラメーターの配列
     * @return array    商品IDの配列
     */
    public function findProductIdsOrder(Query &$objQuery, $arrVal = array())
    {
        $table = 'dtb_products AS alldtl';

        if (is_array($this->arrOrderData) and $objQuery->order == '') {
            $o_col = $this->arrOrderData['col'];
            $o_table = $this->arrOrderData['table'];
            $o_order = $this->arrOrderData['order'];
            $objQuery->setOrder("T2.$o_col $o_order");
            $sub_sql = $objQuery->getSql($o_col, "$o_table AS T2", 'T2.product_id = alldtl.product_id');
            $sub_sql = $objQuery->dbFactory->addLimitOffset($sub_sql, 1);

            $objQuery->setOrder("($sub_sql) $o_order, product_id");
        }
        $arrReturn = $objQuery->getCol('alldtl.product_id', $table, '', $arrVal);

        return $arrReturn;
    }

    /**
     * Queryインスタンスに設定された検索条件をもとに対象商品数を取得する.
     *
     * 検索条件は, Query::setWhere() 関数で設定しておく必要があります.
     *
     * @param  Query $objQuery Query インスタンス
     * @param  array    $arrVal   検索パラメーターの配列
     * @return integer    対象商品ID数
     */
    public function findProductCount(Query &$objQuery, $arrVal = array())
    {
        $table = 'dtb_products AS alldtl';

        return $objQuery->count($table, '', $arrVal);
    }

    /**
     * Queryインスタンスに設定された検索条件をもとに商品一覧の配列を取得する.
     *
     * 主に Application::alias('eccube.product')->findProductIds() で取得した商品IDを検索条件にし,
     * Query::setOrder() や Query::setLimitOffset() を設定して, 商品一覧
     * の配列を取得する.
     *
     * @param  Query $objQuery Query インスタンス
     * @return array    商品一覧の配列
     */
    public function lists(Query &$objQuery)
    {
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
        $res = $objQuery->select($col, $this->alldtlSQL());

        return $res;
    }

    /**
     * 商品IDを指定し、商品一覧を取得する
     *
     * Query::setOrder() や Query::setLimitOffset() を設定して, 商品一覧
     * の配列を取得する.
     * FIXME: 呼び出し元で設定した、Query::setWhere() も有効に扱いたい。
     *
     * @param  Query $objQuery     Query インスタンス
     * @param  array    $arrProductId 商品ID
     * @return array    商品一覧の配列 (キー: 商品ID)
     */
    public function getListByProductIds(Query &$objQuery, $arrProductId = array())
    {
        if (empty($arrProductId)) {
            return array();
        }

        $where = 'alldtl.product_id IN (' . Utils::repeatStrWithSeparator('?', count($arrProductId)) . ')';
        $where .= ' AND alldtl.del_flg = 0';

        $objQuery->setWhere($where, $arrProductId);
        $arrProducts = $this->lists($objQuery);

        // 配列のキーを商品IDに
        $arrProducts = Utils::makeArrayIDToKey('product_id', $arrProducts);

        // Query::setOrder() の指定がない場合、$arrProductId で指定された商品IDの順に配列要素を並び替え
        if (strlen($objQuery->order) === 0) {
            $arrTmp = array();
            foreach ($arrProductId as $product_id) {
                $arrTmp[$product_id] = $arrProducts[$product_id];
            }
            $arrProducts =& $arrTmp;
            unset($arrTmp);
        }

        // 税込金額を設定する
        $this->setIncTaxToProducts($arrProducts);

        return $arrProducts;
    }

    /**
     * 商品詳細を取得する.
     *
     * @param  integer $product_id 商品ID
     * @return array   商品詳細情報の配列
     */
    public function getDetail($product_id)
    {
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');

        $from = $this->alldtlSQL();
        $where = 'product_id = ?';
        $arrWhereVal = array($product_id);
        $arrProduct = (array)$objQuery->getRow('*', $from, $where, $arrWhereVal);

        // 税込金額を設定する
        $this->setIncTaxToProduct($arrProduct);

        return $arrProduct;
    }

    /**
     * 商品詳細情報と商品規格を取得する.
     *
     * @param  integer $productClassId 商品規格ID
     * @return array   商品詳細情報と商品規格の配列
     */
    public function getDetailAndProductsClass($productClassId)
    {
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
     * @param  array   $arrProductId 商品ID の配列
     * @param  boolean $has_deleted  削除された商品規格も含む場合 true; 初期値 false
     * @return void
     */
    public function setProductsClassByProductIds($arrProductId, $has_deleted = false)
    {
        foreach ($arrProductId as $productId) {
            $arrProductClasses = $this->getProductsClassFullByProductId($productId, $has_deleted);

            $classCats1 = array();
            $classCats1['__unselected'] = '選択してください';

            // 規格1クラス名
            $this->className1[$productId] =
                isset($arrProductClasses[0]['class_name1'])
                ? $arrProductClasses[0]['class_name1']
                : '';

            // 規格2クラス名
            $this->className2[$productId] =
                isset($arrProductClasses[0]['class_name2'])
                ? $arrProductClasses[0]['class_name2']
                : '';

            // 規格1が設定されている
            $this->classCat1_find[$productId] = $arrProductClasses[0]['classcategory_id1'] > 0; // 要変更ただし、他にも改修が必要となる
            // 規格2が設定されている
            $this->classCat2_find[$productId] = $arrProductClasses[0]['classcategory_id2'] > 0; // 要変更ただし、他にも改修が必要となる

            $this->stock_find[$productId] = false;
            $classCategories = array();
            $classCategories['__unselected']['__unselected']['name'] = '選択してください';
            $classCategories['__unselected']['__unselected']['product_class_id'] = $arrProductClasses[0]['product_class_id'];
            // 商品種別
            $classCategories['__unselected']['__unselected']['product_type'] = $arrProductClasses[0]['product_type_id'];
            $this->product_class_id[$productId] = $arrProductClasses[0]['product_class_id'];
            // 商品種別
            $this->product_type[$productId] = $arrProductClasses[0]['product_type_id'];
            foreach ($arrProductClasses as $arrProductsClass) {
                $arrClassCats2 = array();
                $classcategory_id1 = $arrProductsClass['classcategory_id1'];
                $classcategory_id2 = $arrProductsClass['classcategory_id2'];
                // 在庫
                $stock_find_class = ($arrProductsClass['stock_unlimited'] || $arrProductsClass['stock'] > 0);

                $arrClassCats2['classcategory_id2'] = $classcategory_id2;
                $arrClassCats2['name'] = $arrProductsClass['classcategory_name2'] . ($stock_find_class ? '' : ' (品切れ中)');

                $arrClassCats2['stock_find'] = $stock_find_class;

                if ($stock_find_class) {
                    $this->stock_find[$productId] = true;
                }

                if (!in_array($classcategory_id1, $classCats1)) {
                    $classCats1[$classcategory_id1] = $arrProductsClass['classcategory_name1']
                        . ($classcategory_id2 == 0 && !$stock_find_class ? ' (品切れ中)' : '');
                }

                // 価格
                // TODO: ここでprice01,price02を税込みにしてよいのか？ _inctax を付けるべき？要検証
                $arrClassCats2['price01']
                    = strlen($arrProductsClass['price01'])
                    ? number_format(TaxRuleHelper::sfCalcIncTax($arrProductsClass['price01'], $productId, $arrProductsClass['product_class_id']))
                    : '';

                $arrClassCats2['price02']
                    = strlen($arrProductsClass['price02'])
                    ? number_format(TaxRuleHelper::sfCalcIncTax($arrProductsClass['price02'], $productId, $arrProductsClass['product_class_id']))
                    : '';

                // ポイント
                $arrClassCats2['point']
                    = number_format(Utils::sfPrePoint($arrProductsClass['price02'], $arrProductsClass['point_rate']));

                // 商品コード
                $arrClassCats2['product_code'] = $arrProductsClass['product_code'];
                // 商品規格ID
                $arrClassCats2['product_class_id'] = $arrProductsClass['product_class_id'];
                // 商品種別
                $arrClassCats2['product_type'] = $arrProductsClass['product_type_id'];

                // #929(GC8 規格のプルダウン順序表示不具合)対応のため、2次キーは「#」を前置
                if (!$this->classCat1_find[$productId]) {
                    $classcategory_id1 = '__unselected2';
                }
                $classCategories[$classcategory_id1]['#'] = array(
                    'classcategory_id2' => '',
                    'name' => '選択してください',
                );
                $classCategories[$classcategory_id1]['#' . $classcategory_id2] = $arrClassCats2;
            }

            $this->classCategories[$productId] = $classCategories;

            // 規格1
            $this->classCats1[$productId] = $classCats1;
        }
    }

    /**
     * Query インスタンスに設定された検索条件を使用して商品規格を取得する.
     *
     * @param  Query $objQuery Queryインスタンス
     * @param  array    $params   検索パラメーターの配列
     * @return array    商品規格の配列
     */
    public function getProductsClassByQuery(Query &$objQuery, $params)
    {
        // 末端の規格を取得
        $col = <<< __EOS__
            T1.product_id,
            T1.stock,
            T1.stock_unlimited,
            T1.sale_limit,
            T1.price01,
            T1.price02,
            T1.point_rate,
            T1.product_code,
            T1.product_class_id,
            T1.del_flg,
            T1.product_type_id,
            T1.down_filename,
            T1.down_realfilename,
            T3.name AS classcategory_name1,
            T3.rank AS rank1,
            T4.name AS class_name1,
            T4.class_id AS class_id1,
            T1.classcategory_id1,
            T1.classcategory_id2,
            dtb_classcategory2.name AS classcategory_name2,
            dtb_classcategory2.rank AS rank2,
            dtb_class2.name AS class_name2,
            dtb_class2.class_id AS class_id2
__EOS__;
        $table = <<< __EOS__
            dtb_products_class T1
            LEFT JOIN dtb_classcategory T3
                ON T1.classcategory_id1 = T3.classcategory_id
            LEFT JOIN dtb_class T4
                ON T3.class_id = T4.class_id
            LEFT JOIN dtb_classcategory dtb_classcategory2
                ON T1.classcategory_id2 = dtb_classcategory2.classcategory_id
            LEFT JOIN dtb_class dtb_class2
                ON dtb_classcategory2.class_id = dtb_class2.class_id
__EOS__;

        $objQuery->andWhere(' T3.classcategory_id is not null AND dtb_classcategory2.classcategory_id is not null ');
        $objQuery->setOrder('T3.rank DESC, dtb_classcategory2.rank DESC'); // XXX
        $arrRet = $objQuery->select($col, $table, '', $params);

        return $arrRet;
    }

    /**
     * 商品規格IDから商品規格を取得する.
     *
     * 削除された商品規格は取得しない.
     *
     * @param  integer $productClassId 商品規格ID
     * @return array   商品規格の配列
     */
    public function getProductsClass($productClassId)
    {
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $objQuery->setWhere('product_class_id = ? AND T1.del_flg = 0');
        $arrRes = $this->getProductsClassByQuery($objQuery, $productClassId);

        $arrProduct = (array) $arrRes[0];

        // 税込計算
        if (!Utils::isBlank($arrProduct['price01'])) {
            $arrProduct['price01_inctax'] = TaxRuleHelper::sfCalcIncTax($arrProduct['price01'], $arrProduct['product_id'], $productClassId);        
        }
        if (!Utils::isBlank($arrProduct['price02'])) {
            $arrProduct['price02_inctax'] = TaxRuleHelper::sfCalcIncTax($arrProduct['price02'], $arrProduct['product_id'], $productClassId);        
        }

        return $arrProduct;
    }

    /**
     * 複数の商品IDに紐づいた, 商品規格を取得する.
     *
     * @param  array   $productIds  商品IDの配列
     * @param  boolean $has_deleted 削除された商品規格も含む場合 true; 初期値 false
     * @return array   商品規格の配列
     */
    public function getProductsClassByProductIds($productIds = array(), $has_deleted = false)
    {
        if (empty($productIds)) {
            return array();
        }
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $where = 'product_id IN (' . Utils::repeatStrWithSeparator('?', count($productIds)) . ')';
        if (!$has_deleted) {
            $where .= ' AND T1.del_flg = 0';
        }
        $objQuery->setWhere($where);

        return $this->getProductsClassByQuery($objQuery, $productIds);
    }

    /**
     * 商品IDに紐づいた, 商品規格を全ての組み合わせごとに取得する.
     *
     * @param  array   $productId   商品ID
     * @param  boolean $has_deleted 削除された商品規格も含む場合 true; 初期値 false
     * @return array   全ての組み合わせの商品規格の配列
     */
    public function getProductsClassFullByProductId($productId, $has_deleted = false)
    {
        $arrRet = $this->getProductsClassByProductIds(array($productId), $has_deleted);

        return $arrRet;
    }

    /**
     * 商品IDをキーにした, 商品ステータスIDの配列を取得する.
     *
     * @param array 商品ID の配列
     * @return array 商品IDをキーにした商品ステータスIDの配列
     */
    public function getProductStatus($productIds)
    {
        if (empty($productIds)) {
            return array();
        }
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $cols = 'product_id, product_status_id';
        $from = 'dtb_product_status';
        $where = 'del_flg = 0 AND product_id IN (' . Utils::repeatStrWithSeparator('?', count($productIds)) . ')';
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
     * @param integer $productId        商品ID
     * @param array   $productStatusIds ON にする商品ステータスIDの配列
     */
    public function setProductStatus($productId, $productStatusIds)
    {
        $val['product_id'] = $productId;
        $val['creator_id'] = $_SESSION['member_id'];
        $val['create_date'] = 'CURRENT_TIMESTAMP';
        $val['update_date'] = 'CURRENT_TIMESTAMP';
        $val['del_flg'] = '0';

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $objQuery->delete('dtb_product_status', 'product_id = ?', array($productId));
        foreach ($productStatusIds as $productStatusId) {
            if ($productStatusId == '') continue;
            $val['product_status_id'] = $productStatusId;
            $objQuery->insert('dtb_product_status', $val);
        }
    }

    /**
     * 商品詳細の結果から, 販売制限数を取得する.
     *
     * getDetailAndProductsClass() の結果から, 販売制限数を取得する.
     *
     * @param  array   $p 商品詳細の検索結果の配列
     * @return integer 商品詳細の結果から求めた販売制限数.
     * @see getDetailAndProductsClass()
     */
    public function getBuyLimit($p)
    {
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
     * @param  integer $productClassId 商品規格ID
     * @param  integer $quantity       減少させる在庫数
     * @return boolean 在庫の減少に成功した場合 true; 失敗した場合 false
     */
    public function reduceStock($productClassId, $quantity)
    {
        if ($quantity == 0) {
            return false;
        }

        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');
        $objQuery->update('dtb_products_class', array(),
                          'product_class_id = ?', array($productClassId),
                          array('stock' => 'stock - ?'), array($quantity));
        // TODO エラーハンドリング

        $productsClass = $this->getDetailAndProductsClass($productClassId);
        if ($productsClass['stock_unlimited'] != '1' && $productsClass['stock'] < 0) {
            return false;
        }

        return true;
    }

    /**
     * 商品情報の配列に, 税込金額を設定して返す.
     *
     * この関数は, 主にスマートフォンで使用します.
     *
     * @param  array $arrProducts 商品情報の配列
     * @return array 旧バージョン互換用のデータ
     */
    public function setPriceTaxTo(&$arrProducts)
    {
        foreach ($arrProducts as &$arrProduct) {
            $arrProduct['price01_min_format'] = number_format($arrProduct['price01_min']);
            $arrProduct['price01_max_format'] = number_format($arrProduct['price01_max']);
            $arrProduct['price02_min_format'] = number_format($arrProduct['price02_min']);
            $arrProduct['price02_max_format'] = number_format($arrProduct['price02_max']);

            $this->setIncTaxToProduct($arrProduct);

            $arrProduct['price01_min_inctax_format'] = number_format($arrProduct['price01_min_inctax']);
            $arrProduct['price01_max_inctax_format'] = number_format($arrProduct['price01_max_inctax']);
            $arrProduct['price02_min_inctax_format'] = number_format($arrProduct['price02_min_inctax']);
            $arrProduct['price02_max_inctax_format'] = number_format($arrProduct['price02_max_inctax']);

            // @deprecated 2.12.4
            // 旧バージョン互換用
            // 本来は、税額の代入で使用すべきキー名。
            $arrProduct['price01_min_tax_format'] =& $arrProduct['price01_min_inctax_format'];
            $arrProduct['price01_max_tax_format'] =& $arrProduct['price01_max_inctax_format'];
            $arrProduct['price02_min_tax_format'] =& $arrProduct['price02_min_inctax_format'];
            $arrProduct['price02_max_tax_format'] =& $arrProduct['price02_max_inctax_format'];
        }
        // @deprecated 2.12.4
        // 旧バージョン互換用
        // 現在は参照渡しで戻せる
        return $arrProducts;
    }

    /**
     * 商品情報の配列に税込金額を設定する
     *
     * @param  array $arrProducts 商品情報の配列
     * @return void
     */
    public function setIncTaxToProducts(&$arrProducts)
    {
        foreach ($arrProducts as &$arrProduct) {
            $this->setIncTaxToProduct($arrProduct);
        }
    }

    /**
     * 商品情報の配列に税込金額を設定する
     *
     * @param  array $arrProduct 商品情報の配列
     * @return void
     */
    public function setIncTaxToProduct(&$arrProduct)
    {
        $arrProduct['price01_min_inctax'] = isset($arrProduct['price01_min']) ? TaxRuleHelper::sfCalcIncTax($arrProduct['price01_min'], $arrProduct['product_id']) : null;
        $arrProduct['price01_max_inctax'] = isset($arrProduct['price01_max']) ? TaxRuleHelper::sfCalcIncTax($arrProduct['price01_max'], $arrProduct['product_id']) : null;
        $arrProduct['price02_min_inctax'] = isset($arrProduct['price02_min']) ? TaxRuleHelper::sfCalcIncTax($arrProduct['price02_min'], $arrProduct['product_id']) : null;
        $arrProduct['price02_max_inctax'] = isset($arrProduct['price02_max']) ? TaxRuleHelper::sfCalcIncTax($arrProduct['price02_max'], $arrProduct['product_id']) : null;
    }

    /**
     * 商品詳細の SQL を取得する.
     *
     * @param  string $where_products_class 商品規格情報の WHERE 句
     * @return string 商品詳細の SQL
     */
    public function alldtlSQL($where_products_class = '')
    {
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');

        return $dbFactory->alldtlSQL($where_products_class);
    }

    /**
     * 商品規格詳細の SQL を取得する.
     *
     * MEMO: 2.4系 vw_product_classに相当(?)するイメージ
     *
     * @param  string $where 商品詳細の WHERE 句
     * @return string 商品規格詳細の SQL
     */
    public function prdclsSQL($where = '')
    {
        $where_clause = '';
        if (!Utils::isBlank($where)) {
            $where_clause = ' WHERE ' . $where;
        }
        $sql = <<< __EOS__
        (
            SELECT dtb_products.*,
                dtb_products_class.product_class_id,
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
                dtb_products_class.classcategory_id1 AS classcategory_id, /* 削除 */
                dtb_products_class.classcategory_id1,
                dtb_products_class.classcategory_id2 AS parent_classcategory_id, /* 削除 */
                dtb_products_class.classcategory_id2,
                Tcc1.class_id as class_id,
                Tcc1.name as classcategory_name,
                Tcc2.class_id as parent_class_id,
                Tcc2.name as parent_classcategory_name
            FROM dtb_products
                LEFT JOIN dtb_products_class
                    ON dtb_products.product_id = dtb_products_class.product_id
                LEFT JOIN dtb_classcategory as Tcc1
                    ON dtb_products_class.classcategory_id1 = Tcc1.classcategory_id
                LEFT JOIN dtb_classcategory as Tcc2
                    ON dtb_products_class.classcategory_id2 = Tcc2.classcategory_id
            $where_clause
        ) as prdcls
__EOS__;

        return $sql;
    }

    /**
     * @param string $tablename
     */
    public function getProductDispConditions($tablename = null)
    {
        $tablename = ($tablename) ? $tablename . '.' : null;

        return $tablename . 'del_flg = 0 AND ' . $tablename . 'status = 1 ';
    }

    /**
     * 商品が属しているカテゴリーIDを取得する.
     *
     * @param int $product_id
     * @param bool $include_hidden
     * @return array
     */
    public function getCategoryIds($product_id, $include_hidden = false) {
        if ($this->isValidProductId($product_id, $include_hidden)) {
            /* @var $objQuery Query */
            $objQuery = Application::alias('eccube.query');
            $category_id = $objQuery->getCol('category_id', 'dtb_product_categories', 'product_id = ?', array($product_id));
        } else {
            // 不正な場合は、空の配列を返す。
            $category_id = array();
        }

        return $category_id;
    }

    /**
     * 有効な商品IDかチェックする.
     *
     * @param int $product_id
     * @param bool $include_hidden
     * @param bool $include_deleted
     * @return bool
     */
    public function isValidProductId($product_id, $include_hidden = false, $include_deleted = false) {
        $where = '';
        if (!$include_hidden) {
            $where .= 'status = 1';
        }
        if (!$include_deleted) {
            if ($where != '') {
                $where .= ' AND ';
            }
            $where .= 'del_flg = 0';
        }
        if (
            Utils::sfIsInt($product_id)
            && !Utils::sfIsZeroFilling($product_id)
            && Application::alias('eccube.helper.db')->isRecord('dtb_products', 'product_id', array($product_id), $where)
        ) {
            return true;
        }
        return false;
    }
}
