<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * DB関連のヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_Helper_DB.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_Helper_DB {

    // {{{ properties

    /** ルートカテゴリ取得フラグ */
    var $g_root_on;

    /** ルートカテゴリID */
    var $g_root_id;

    /** 選択中カテゴリ取得フラグ */
    var $g_category_on;

    /** 選択中カテゴリID */
    var $g_category_id;

    // }}}
    // {{{ functions

    /**
     * データベースのバージョンを所得する.
     *
     * @param string $dsn データソース名
     * @return string データベースのバージョン
     */
    function sfGetDBVersion($dsn = "") {
        $dbFactory = SC_DB_DBFactory::getInstance();
        return $dbFactory->sfGetDBVersion($dsn);
    }

    /**
     * テーブルの存在をチェックする.
     *
     * @param string $table_name チェック対象のテーブル名
     * @param string $dsn データソース名
     * @return テーブルが存在する場合 true
     */
    function sfTabaleExists($table_name, $dsn = "") {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $dsn = $dbFactory->getDSN($dsn);

        $objQuery = new SC_Query($dsn, true, true);
        // 正常に接続されている場合
        if(!$objQuery->isError()) {
            list($db_type) = split(":", $dsn);
            $sql = $dbFactory->getTableExistsSql();
            $arrRet = $objQuery->getAll($sql, array($table_name));
            if(count($arrRet) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * カラムの存在チェックと作成を行う.
     *
     * チェック対象のテーブルに, 該当のカラムが存在するかチェックする.
     * 引数 $add が true の場合, 該当のカラムが存在しない場合は, カラムの生成を行う.
     * カラムの生成も行う場合は, $col_type も必須となる.
     *
     * @param string $table_name テーブル名
     * @param string $column_name カラム名
     * @param string $col_type カラムのデータ型
     * @param string $dsn データソース名
     * @param bool $add カラムの作成も行う場合 true
     * @return bool カラムが存在する場合とカラムの生成に成功した場合 true,
     * 			     テーブルが存在しない場合 false,
     * 				 引数 $add == false でカラムが存在しない場合 false
     */
    function sfColumnExists($table_name, $col_name, $col_type = "", $dsn = "", $add = false) {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $dsn = $dbFactory->getDSN($dsn);

        // テーブルが無ければエラー
        if(!$this->sfTabaleExists($table_name, $dsn)) return false;

        $objQuery = new SC_Query($dsn, true, true);
        // 正常に接続されている場合
        if(!$objQuery->isError()) {
            list($db_type) = split(":", $dsn);

            // カラムリストを取得
            $arrRet = $dbFactory->sfGetColumnList($table_name);
            if(count($arrRet) > 0) {
                if(in_array($col_name, $arrRet)){
                    return true;
                }
            }
        }

        // カラムを追加する
        if($add){
            $objQuery->query("ALTER TABLE $table_name ADD $col_name $col_type ");
            return true;
        }
        return false;
    }

    /**
     * インデックスの存在チェックと作成を行う.
     *
     * チェック対象のテーブルに, 該当のインデックスが存在するかチェックする.
     * 引数 $add が true の場合, 該当のインデックスが存在しない場合は, インデックスの生成を行う.
     * インデックスの生成も行う場合で, DB_TYPE が mysql の場合は, $length も必須となる.
     *
     * @param string $table_name テーブル名
     * @param string $column_name カラム名
     * @param string $index_name インデックス名
     * @param integer|string $length インデックスを作成するデータ長
     * @param string $dsn データソース名
     * @param bool $add インデックスの生成もする場合 true
     * @return bool インデックスが存在する場合とインデックスの生成に成功した場合 true,
     * 			     テーブルが存在しない場合 false,
     * 				 引数 $add == false でインデックスが存在しない場合 false
     */
    function sfIndexExists($table_name, $col_name, $index_name, $length = "", $dsn = "", $add = false) {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $dsn = $dbFactory->getDSN($dsn);

        // テーブルが無ければエラー
        if (!$this->sfTabaleExists($table_name, $dsn)) return false;

        $objQuery = new SC_Query($dsn, true, true);
        $arrRet = $dbFactory->getTableIndex($index_name, $table_name);

        // すでにインデックスが存在する場合
        if(count($arrRet) > 0) {
            return true;
        }

        // インデックスを作成する
        if($add){
            $dbFactory->createTableIndex($index_name, $table_name, $col_name, $length());
            return true;
        }
        return false;
    }

    /**
     * データの存在チェックを行う.
     *
     * @param string $table_name テーブル名
     * @param string $where データを検索する WHERE 句
     * @param string $dsn データソース名
     * @param string $sql データの追加を行う場合の SQL文
     * @param bool $add データの追加も行う場合 true
     * @return bool データが存在する場合 true, データの追加に成功した場合 true,
     *               $add == false で, データが存在しない場合 false
     */
    function sfDataExists($table_name, $where, $arrval, $dsn = "", $sql = "", $add = false) {
        $dbFactory = SC_DB_DBFactory::getInstance();
        $dsn = $dbFactory->getDSN($dsn);

        $objQuery = new SC_Query($dsn, true, true);
        $count = $objQuery->count($table_name, $where, $arrval);

        if($count > 0) {
            $ret = true;
        } else {
            $ret = false;
        }
        // データを追加する
        if(!$ret && $add) {
            $objQuery->exec($sql);
        }
        return $ret;
    }

    /**
     * 店舗基本情報を取得する.
     *
     * @return array 店舗基本情報の配列
     */
    function sf_getBasisData() {
        //DBから設定情報を取得
        $objConn = new SC_DbConn();
        $result = $objConn->getAll("SELECT * FROM dtb_baseinfo");
        if(is_array($result[0])) {
            foreach ( $result[0] as $key=>$value ){
                $CONF["$key"] = $value;
            }
        }
        return $CONF;
    }

    /* 選択中のアイテムのルートカテゴリIDを取得する */
    function sfGetRootId() {

        if(!$this->g_root_on)	{
            $this->g_root_on = true;
            $objQuery = new SC_Query();

            if (!isset($_GET['product_id'])) $_GET['product_id'] = "";
            if (!isset($_GET['category_id'])) $_GET['category_id'] = "";

            if(!empty($_GET['product_id']) || !empty($_GET['category_id'])) {
                // 選択中のカテゴリIDを判定する
                $category_id = $this->sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
                // ROOTカテゴリIDの取得
                $arrRet = $this->sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $category_id);
                $root_id = $arrRet[0];
            } else {
                // ROOTカテゴリIDをなしに設定する
                $root_id = "";
            }
            $this->g_root_id = $root_id;
        }
        return $this->g_root_id;
    }

    /**
     * 商品規格情報を取得する.
     *
     * @param array $arrID 規格ID
     * @return array 規格情報の配列
     */
    function sfGetProductsClass($arrID) {
        list($product_id, $classcategory_id1, $classcategory_id2) = $arrID;

        if($classcategory_id1 == "") {
            $classcategory_id1 = '0';
        }
        if($classcategory_id2 == "") {
            $classcategory_id2 = '0';
        }

        // 商品規格取得
        $objQuery = new SC_Query();
        $col = "product_id, deliv_fee, name, product_code, main_list_image, main_image, price01, price02, point_rate, product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited, sale_limit, sale_unlimited";
        $table = "vw_product_class AS prdcls";
        $where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
        $objQuery->setorder("rank1 DESC, rank2 DESC");
        $arrRet = $objQuery->select($col, $table, $where, array($product_id, $classcategory_id1, $classcategory_id2));
        return $arrRet[0];
    }

    /**
     * 支払い方法を取得する.
     *
     * @return void
     */
    function sfGetPayment() {
        $objQuery = new SC_Query();
        // 購入金額が条件額以下の項目を取得
        $where = "del_flg = 0";
        $objQuery->setorder("fix, rank DESC");
        $arrRet = $objQuery->select("payment_id, payment_method, rule", "dtb_payment", $where);
        return $arrRet;
    }

    /**
     * カート内商品の集計処理を行う.
     *
     * @param LC_Page $objPage ページクラスのインスタンス
     * @param SC_CartSession $objCartSess カートセッションのインスタンス
     * @param array $arrInfo 商品情報の配列
     * @return LC_Page 集計処理後のページクラスインスタンス
     */
    function sfTotalCart(&$objPage, $objCartSess, $arrInfo) {
        $objDb = new SC_Helper_DB_Ex();
        // 規格名一覧
        $arrClassName = $objDb->sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");

        $objPage->tpl_total_pretax = 0;		// 費用合計(税込み)
        $objPage->tpl_total_tax = 0;		// 消費税合計
        $objPage->tpl_total_point = 0;		// ポイント合計

        // カート内情報の取得
        $arrCart = $objCartSess->getCartList();
        $max = count($arrCart);
        $cnt = 0;

        for ($i = 0; $i < $max; $i++) {
            // 商品規格情報の取得
            $arrData = $this->sfGetProductsClass($arrCart[$i]['id']);
            $limit = "";
            // DBに存在する商品
            if (count($arrData) > 0) {

                // 購入制限数を求める。
                if ($arrData['stock_unlimited'] != '1' && $arrData['sale_unlimited'] != '1') {
                    if($arrData['sale_limit'] < $arrData['stock']) {
                        $limit = $arrData['sale_limit'];
                    } else {
                        $limit = $arrData['stock'];
                    }
                } else {
                    if ($arrData['sale_unlimited'] != '1') {
                        $limit = $arrData['sale_limit'];
                    }
                    if ($arrData['stock_unlimited'] != '1') {
                        $limit = $arrData['stock'];
                    }
                }

                if($limit != "" && $limit < $arrCart[$i]['quantity']) {
                    // カート内商品数を制限に合わせる
                    $objCartSess->setProductValue($arrCart[$i]['id'], 'quantity', $limit);
                    $quantity = $limit;
                    $objPage->tpl_message = "※「" . $arrData['name'] . "」は販売制限しております、一度にこれ以上の購入はできません。";
                } else {
                    $quantity = $arrCart[$i]['quantity'];
                }

                $objPage->arrProductsClass[$cnt] = $arrData;
                $objPage->arrProductsClass[$cnt]['quantity'] = $quantity;
                $objPage->arrProductsClass[$cnt]['cart_no'] = $arrCart[$i]['cart_no'];
                $objPage->arrProductsClass[$cnt]['class_name1'] =
                    isset($arrClassName[$arrData['class_id1']])
                        ? $arrClassName[$arrData['class_id1']] : "";

                $objPage->arrProductsClass[$cnt]['class_name2'] =
                    isset($arrClassName[$arrData['class_id2']])
                        ? $arrClassName[$arrData['class_id2']] : "";

                $objPage->arrProductsClass[$cnt]['classcategory_name1'] =
                    $arrClassCatName[$arrData['classcategory_id1']];

                $objPage->arrProductsClass[$cnt]['classcategory_name2'] =
                    $arrClassCatName[$arrData['classcategory_id2']];

                // 画像サイズ
                list($image_width, $image_height) = getimagesize(IMAGE_SAVE_DIR . basename($objPage->arrProductsClass[$cnt]["main_image"]));
                $objPage->arrProductsClass[$cnt]["tpl_image_width"] = $image_width + 60;
                $objPage->arrProductsClass[$cnt]["tpl_image_height"] = $image_height + 80;

                // 価格の登録
                if ($arrData['price02'] != "") {
                    $objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price02']);
                    $objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price02'];
                } else {
                    $objCartSess->setProductValue($arrCart[$i]['id'], 'price', $arrData['price01']);
                    $objPage->arrProductsClass[$cnt]['uniq_price'] = $arrData['price01'];
                }
                // ポイント付与率の登録
                $objCartSess->setProductValue($arrCart[$i]['id'], 'point_rate', $arrData['point_rate']);
                // 商品ごとの合計金額
                $objPage->arrProductsClass[$cnt]['total_pretax'] = $objCartSess->getProductTotal($arrInfo, $arrCart[$i]['id']);
                // 送料の合計を計算する
                $objPage->tpl_total_deliv_fee+= ($arrData['deliv_fee'] * $arrCart[$i]['quantity']);
                $cnt++;
            } else {
                // DBに商品が見つからない場合はカート商品の削除
                $objCartSess->delProductKey('id', $arrCart[$i]['id']);
            }
        }

        // 全商品合計金額(税込み)
        $objPage->tpl_total_pretax = $objCartSess->getAllProductsTotal($arrInfo);
        // 全商品合計消費税
        $objPage->tpl_total_tax = $objCartSess->getAllProductsTax($arrInfo);
        // 全商品合計ポイント
        $objPage->tpl_total_point = $objCartSess->getAllProductsPoint();

        return $objPage;
    }

    /**
     * 受注一時テーブルへの書き込み処理を行う.
     *
     * @param string $uniqid ユニークID
     * @param array $sqlval SQLの値の配列
     * @return void
     */
    function sfRegistTempOrder($uniqid, $sqlval) {
        if($uniqid != "") {
            // 既存データのチェック
            $objQuery = new SC_Query();
            $where = "order_temp_id = ?";
            $cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
            // 既存データがない場合
            if ($cnt == 0) {
                // 初回書き込み時に会員の登録済み情報を取り込む
                $sqlval = $this->sfGetCustomerSqlVal($uniqid, $sqlval);
                $sqlval['create_date'] = "now()";
                $objQuery->insert("dtb_order_temp", $sqlval);
            } else {
                $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
            }
        }
    }

    /**
     * 会員情報から SQL文の値を生成する.
     *
     * @param string $uniqid ユニークID
     * @param array $sqlval SQL の値の配列
     * @return array 会員情報を含んだ SQL の値の配列
     */
    function sfGetCustomerSqlVal($uniqid, $sqlval) {
        $objCustomer = new SC_Customer();
        // 会員情報登録処理
        if ($objCustomer->isLoginSuccess()) {
            // 登録データの作成
            $sqlval['order_temp_id'] = $uniqid;
            $sqlval['update_date'] = 'Now()';
            $sqlval['customer_id'] = $objCustomer->getValue('customer_id');
            $sqlval['order_name01'] = $objCustomer->getValue('name01');
            $sqlval['order_name02'] = $objCustomer->getValue('name02');
            $sqlval['order_kana01'] = $objCustomer->getValue('kana01');
            $sqlval['order_kana02'] = $objCustomer->getValue('kana02');
            $sqlval['order_sex'] = $objCustomer->getValue('sex');
            $sqlval['order_zip01'] = $objCustomer->getValue('zip01');
            $sqlval['order_zip02'] = $objCustomer->getValue('zip02');
            $sqlval['order_pref'] = $objCustomer->getValue('pref');
            $sqlval['order_addr01'] = $objCustomer->getValue('addr01');
            $sqlval['order_addr02'] = $objCustomer->getValue('addr02');
            $sqlval['order_tel01'] = $objCustomer->getValue('tel01');
            $sqlval['order_tel02'] = $objCustomer->getValue('tel02');
            $sqlval['order_tel03'] = $objCustomer->getValue('tel03');
            if (defined('MOBILE_SITE')) {
                $sqlval['order_email'] = $objCustomer->getValue('email_mobile');
            } else {
                $sqlval['order_email'] = $objCustomer->getValue('email');
            }
            $sqlval['order_job'] = $objCustomer->getValue('job');
            $sqlval['order_birth'] = $objCustomer->getValue('birth');
        }
        return $sqlval;
    }

    /**
     * 会員編集登録処理を行う.
     *
     * @param array $array パラメータの配列
     * @param array $arrRegistColumn 登録するカラムの配列
     * @return void
     */
    function sfEditCustomerData($array, $arrRegistColumn) {
        $objQuery = new SC_Query();

        foreach ($arrRegistColumn as $data) {
            if ($data["column"] != "password") {
                if($array[ $data['column'] ] != "") {
                    $arrRegist[ $data["column"] ] = $array[ $data["column"] ];
                } else {
                    $arrRegist[ $data['column'] ] = NULL;
                }
            }
        }
        if (strlen($array["year"]) > 0 && strlen($array["month"]) > 0 && strlen($array["day"]) > 0) {
            $arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
        } else {
            $arrRegist["birth"] = NULL;
        }

        //-- パスワードの更新がある場合は暗号化。（更新がない場合はUPDATE文を構成しない）
        if ($array["password"] != DEFAULT_PASSWORD) $arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC);
        $arrRegist["update_date"] = "NOW()";

        //-- 編集登録実行
        if (defined('MOBILE_SITE')) {
            $arrRegist['email_mobile'] = $arrRegist['email'];
            unset($arrRegist['email']);
        }
        $objQuery->begin();
        $objQuery->update("dtb_customer", $arrRegist, "customer_id = ? ", array($array['customer_id']));
        $objQuery->commit();
    }

    /**
     * 受注番号、利用ポイント、加算ポイントから最終ポイントを取得する.
     *
     * @param integer $order_id 受注番号
     * @param integer $use_point 利用ポイント
     * @param integer $add_point 加算ポイント
     * @return array 最終ポイントの配列
     */
    function sfGetCustomerPoint($order_id, $use_point, $add_point) {
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select("customer_id", "dtb_order", "order_id = ?", array($order_id));
        $customer_id = $arrRet[0]['customer_id'];
        if($customer_id != "" && $customer_id >= 1) {
            $arrRet = $objQuery->select("point", "dtb_customer", "customer_id = ?", array($customer_id));
            $point = $arrRet[0]['point'];
            $total_point = $arrRet[0]['point'] - $use_point + $add_point;
        } else {
            $total_point = "";
            $point = "";
        }
        return array($point, $total_point);
    }

    /**
     * カテゴリツリーの取得を行う.
     *
     * @param integer $parent_category_id 親カテゴリID
     * @param bool $count_check 登録商品数のチェックを行う場合 true
     * @return array カテゴリツリーの配列
     */
    function sfGetCatTree($parent_category_id, $count_check = false) {
        $objQuery = new SC_Query();
        $col = "";
        $col .= " cat.category_id,";
        $col .= " cat.category_name,";
        $col .= " cat.parent_category_id,";
        $col .= " cat.level,";
        $col .= " cat.rank,";
        $col .= " cat.creator_id,";
        $col .= " cat.create_date,";
        $col .= " cat.update_date,";
        $col .= " cat.del_flg, ";
        $col .= " ttl.product_count";
        $from = "dtb_category as cat left join dtb_category_total_count as ttl on ttl.category_id = cat.category_id";
        // 登録商品数のチェック
        if($count_check) {
            $where = "del_flg = 0 AND product_count > 0";
        } else {
            $where = "del_flg = 0";
        }
        $objQuery->setoption("ORDER BY rank DESC");
        $arrRet = $objQuery->select($col, $from, $where);

        $arrParentID = $this->sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $parent_category_id);

        foreach($arrRet as $key => $array) {
            foreach($arrParentID as $val) {
                if($array['category_id'] == $val) {
                    $arrRet[$key]['display'] = 1;
                    break;
                }
            }
        }

        return $arrRet;
    }

    /**
     * 親カテゴリーを連結した文字列を取得する.
     *
     * @param integer $category_id カテゴリID
     * @return string 親カテゴリーを連結した文字列
     */
    function sfGetCatCombName($category_id){
        // 商品が属するカテゴリIDを縦に取得
        $objQuery = new SC_Query();
        $arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
        $ConbName = "";

        // カテゴリー名称を取得する
        foreach($arrCatID as $key => $val){
            $sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
            $arrVal = array($val);
            $CatName = $objQuery->getOne($sql,$arrVal);
            $ConbName .= $CatName . ' | ';
        }
        // 最後の ｜ をカットする
        $ConbName = substr_replace($ConbName, "", strlen($ConbName) - 2, 2);

        return $ConbName;
    }

    /**
     * 指定したカテゴリーIDの大カテゴリーを取得する.
     *
     * @param integer $category_id カテゴリID
     * @return array 指定したカテゴリーIDの大カテゴリー
     */
    function sfGetFirstCat($category_id){
        // 商品が属するカテゴリIDを縦に取得
        $objQuery = new SC_Query();
        $arrRet = array();
        $arrCatID = $this->sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
        $arrRet['id'] = $arrCatID[0];

        // カテゴリー名称を取得する
        $sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
        $arrVal = array($arrRet['id']);
        $arrRet['name'] = $objQuery->getOne($sql,$arrVal);

        return $arrRet;
    }

    /**
     * カテゴリツリーの取得を行う.
     *
     * $products_check:true商品登録済みのものだけ取得する
     *
     * @param string $addwhere 追加する WHERE 句
     * @param bool $products_check 商品の存在するカテゴリのみ取得する場合 true
     * @param string $head カテゴリ名のプレフィックス文字列
     * @return array カテゴリツリーの配列
     */
    function sfGetCategoryList($addwhere = "", $products_check = false, $head = CATEGORY_HEAD) {
        $objQuery = new SC_Query();
        $where = "del_flg = 0";

        if($addwhere != "") {
            $where.= " AND $addwhere";
        }

        $objQuery->setoption("ORDER BY rank DESC");

        if($products_check) {
            $col = "T1.category_id, category_name, level";
            $from = "dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2 ON T1.category_id = T2.category_id";
            $where .= " AND product_count > 0";
        } else {
            $col = "category_id, category_name, level";
            $from = "dtb_category";
        }

        $arrRet = $objQuery->select($col, $from, $where);

        $max = count($arrRet);
        for($cnt = 0; $cnt < $max; $cnt++) {
            $id = $arrRet[$cnt]['category_id'];
            $name = $arrRet[$cnt]['category_name'];
            $arrList[$id] = "";
            /*
            for($n = 1; $n < $arrRet[$cnt]['level']; $n++) {
                $arrList[$id].= "　";
            }
            */
            for($cat_cnt = 0; $cat_cnt < $arrRet[$cnt]['level']; $cat_cnt++) {
                $arrList[$id].= $head;
            }
            $arrList[$id].= $name;
        }
        return $arrList;
    }

    /**
     * カテゴリーツリーの取得を行う.
     *
     * 親カテゴリの Value=0 を対象とする
     *
     * @param bool $parent_zero 親カテゴリの Value=0 の場合 true
     * @return array カテゴリツリーの配列
     */
    function sfGetLevelCatList($parent_zero = true) {
        $objQuery = new SC_Query();
        $col = "category_id, category_name, level";
        $where = "del_flg = 0";
        $objQuery->setoption("ORDER BY rank DESC");
        $arrRet = $objQuery->select($col, "dtb_category", $where);
        $max = count($arrRet);

        for($cnt = 0; $cnt < $max; $cnt++) {
            if($parent_zero) {
                if($arrRet[$cnt]['level'] == LEVEL_MAX) {
                    $arrValue[$cnt] = $arrRet[$cnt]['category_id'];
                } else {
                    $arrValue[$cnt] = "";
                }
            } else {
                $arrValue[$cnt] = $arrRet[$cnt]['category_id'];
            }

            $arrOutput[$cnt] = "";
            /*
            for($n = 1; $n < $arrRet[$cnt]['level']; $n++) {
                $arrOutput[$cnt].= "　";
            }
            */
            for($cat_cnt = 0; $cat_cnt < $arrRet[$cnt]['level']; $cat_cnt++) {
                $arrOutput[$cnt].= CATEGORY_HEAD;
            }
            $arrOutput[$cnt].= $arrRet[$cnt]['category_name'];
        }
        return array($arrValue, $arrOutput);
    }

    /**
     * 選択中のカテゴリを取得する.
     *
     * @param integer $product_id プロダクトID
     * @param integer $category_id カテゴリID
     * @return integer 選択中のカテゴリID
     *
     */
    function sfGetCategoryId($product_id, $category_id) {

        if(!$this->g_category_on)	{
            $this->g_category_on = true;
            $category_id = (int) $category_id;
            $product_id = (int) $product_id;
            if(SC_Utils_Ex::sfIsInt($category_id) && $this->sfIsRecord("dtb_category","category_id", $category_id)) {
                $this->g_category_id = $category_id;
            } else if (SC_Utils_Ex::sfIsInt($product_id) && $this->sfIsRecord("dtb_products","product_id", $product_id, "status = 1")) {
                $objQuery = new SC_Query();
                $where = "product_id = ?";
                $category_id = $objQuery->get("dtb_products", "category_id", $where, array($product_id));
                $this->g_category_id = $category_id;
            } else {
                // 不正な場合は、0を返す。
                $this->g_category_id = 0;
            }
        }
        return $this->g_category_id;
    }

    /**
     * カテゴリ数の登録を行う.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @return void
     */
    function sfCategory_Count($objQuery){
        $sql = "";

        //テーブル内容の削除
        $objQuery->query("DELETE FROM dtb_category_count");
        $objQuery->query("DELETE FROM dtb_category_total_count");

        //各カテゴリ内の商品数を数えて格納
        $sql = " INSERT INTO dtb_category_count(category_id, product_count, create_date) ";
        $sql .= " SELECT T1.category_id, count(T2.category_id), now() FROM dtb_category AS T1 LEFT JOIN dtb_products AS T2 ";
        $sql .= " ON T1.category_id = T2.category_id  ";
        $sql .= " WHERE T2.del_flg = 0 AND T2.status = 1 ";
        $sql .= " GROUP BY T1.category_id, T2.category_id ";
        $objQuery->query($sql);

        //子カテゴリ内の商品数を集計する
        $arrCat = $objQuery->getAll("SELECT * FROM dtb_category");

        $sql = "";
        foreach($arrCat as $key => $val){

            // 子ID一覧を取得
            $arrRet = $this->sfGetChildrenArray('dtb_category', 'parent_category_id', 'category_id', $val['category_id']);
            $line = SC_Utils_Ex::sfGetCommaList($arrRet);

            $sql = " INSERT INTO dtb_category_total_count(category_id, product_count, create_date) ";
            $sql .= " SELECT ?, SUM(product_count), now() FROM dtb_category_count ";
            $sql .= " WHERE category_id IN (" . $line . ")";

            $objQuery->query($sql, array($val['category_id']));
        }
    }

    /**
     * 子IDの配列を返す.
     *
     * @param string $table テーブル名
     * @param string $pid_name 親ID名
     * @param string $id_name ID名
     * @param integer $id ID
     * @param array 子ID の配列
     */
    function sfGetChildsID($table, $pid_name, $id_name, $id) {
        $arrRet = $this->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        return $arrRet;
    }

    /**
     * 階層構造のテーブルから子ID配列を取得する.
     *
     * @param string $table テーブル名
     * @param string $pid_name 親ID名
     * @param string $id_name ID名
     * @param integer $id ID番号
     * @return array 子IDの配列
     */
    function sfGetChildrenArray($table, $pid_name, $id_name, $id) {
        $objQuery = new SC_Query();
        $col = $pid_name . "," . $id_name;
         $arrData = $objQuery->select($col, $table);

        $arrPID = array();
        $arrPID[] = $id;
        $arrChildren = array();
        $arrChildren[] = $id;

        $arrRet = $this->sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrPID);

        while(count($arrRet) > 0) {
            $arrChildren = array_merge($arrChildren, $arrRet);
            $arrRet = $this->sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrRet);
        }

        return $arrChildren;
    }

    /**
     * 親ID直下の子IDをすべて取得する.
     *
     * @param array $arrData 親カテゴリの配列
     * @param string $pid_name 親ID名
     * @param string $id_name ID名
     * @param array $arrPID 親IDの配列
     * @return array 子IDの配列
     */
    function sfGetChildrenArraySub($arrData, $pid_name, $id_name, $arrPID) {
        $arrChildren = array();
        $max = count($arrData);

        for($i = 0; $i < $max; $i++) {
            foreach($arrPID as $val) {
                if($arrData[$i][$pid_name] == $val) {
                    $arrChildren[] = $arrData[$i][$id_name];
                }
            }
        }
        return $arrChildren;
    }

    /**
     * 所属するすべての階層の親IDを配列で返す.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param string $table テーブル名
     * @param string $pid_name 親ID名
     * @param string $id_name ID名
     * @param integer $id ID
     * @return array 親IDの配列
     */
    function sfGetParents($objQuery, $table, $pid_name, $id_name, $id) {
        $arrRet = $this->sfGetParentsArray($table, $pid_name, $id_name, $id);
        // 配列の先頭1つを削除する。
        array_shift($arrRet);
        return $arrRet;
    }

    /**
     * 階層構造のテーブルから親ID配列を取得する.
     *
     * @param string $table テーブル名
     * @param string $pid_name 親ID名
     * @param string $id_name ID名
     * @param integer $id ID
     * @return array 親IDの配列
     */
    function sfGetParentsArray($table, $pid_name, $id_name, $id) {
        $objQuery = new SC_Query();
        $col = $pid_name . "," . $id_name;
         $arrData = $objQuery->select($col, $table);

        $arrParents = array();
        $arrParents[] = $id;
        $child = $id;

        $ret = SC_Utils::sfGetParentsArraySub($arrData, $pid_name, $id_name, $child);

        while($ret != "") {
            $arrParents[] = $ret;
            $ret = SC_Utils::sfGetParentsArraySub($arrData, $pid_name, $id_name, $ret);
        }

        $arrParents = array_reverse($arrParents);

        return $arrParents;
    }

    /**
     * カテゴリから商品を検索する場合のWHERE文と値を返す.
     *
     * @param integer $category_id カテゴリID
     * @return array 商品を検索する場合の配列
     */
    function sfGetCatWhere($category_id) {
        // 子カテゴリIDの取得
        $arrRet = $this->sfGetChildsID("dtb_category", "parent_category_id", "category_id", $category_id);
        $tmp_where = "";
        foreach ($arrRet as $val) {
            if($tmp_where == "") {
                $tmp_where.= " category_id IN ( ?";
            } else {
                $tmp_where.= ",? ";
            }
            $arrval[] = $val;
        }
        $tmp_where.= " ) ";
        return array($tmp_where, $arrval);
    }

    /**
     * 受注一時テーブルから情報を取得する.
     *
     * @param integer $order_temp_id 受注一時ID
     * @return array 受注一時情報の配列
     */
    function sfGetOrderTemp($order_temp_id) {
        $objQuery = new SC_Query();
        $where = "order_temp_id = ?";
        $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($order_temp_id));
        return $arrRet[0];
    }

    /**
     * SELECTボックス用リストを作成する.
     *
     * @param string $table テーブル名
     * @param string $keyname プライマリーキーのカラム名
     * @param string $valname データ内容のカラム名
     * @return array SELECT ボックス用リストの配列
     */
    function sfGetIDValueList($table, $keyname, $valname) {
        $objQuery = new SC_Query();
        $col = "$keyname, $valname";
        $objQuery->setwhere("del_flg = 0");
        $objQuery->setorder("rank DESC");
        $arrList = $objQuery->select($col, $table);
        $count = count($arrList);
        for($cnt = 0; $cnt < $count; $cnt++) {
            $key = $arrList[$cnt][$keyname];
            $val = $arrList[$cnt][$valname];
            $arrRet[$key] = $val;
        }
        return $arrRet;
    }

    /**
     * ランキングを上げる.
     *
     * @param string $table テーブル名
     * @param string $colname カラム名
     * @param string|integer $id テーブルのキー
     * @param string $andwhere SQL の AND 条件である WHERE 句
     * @return void
     */
    function sfRankUp($table, $colname, $id, $andwhere = "") {
        $objQuery = new SC_Query();
        $objQuery->begin();
        $where = "$colname = ?";
        if($andwhere != "") {
            $where.= " AND $andwhere";
        }
        // 対象項目のランクを取得
        $rank = $objQuery->get($table, "rank", $where, array($id));
        // ランクの最大値を取得
        $maxrank = $objQuery->max($table, "rank", $andwhere);
        // ランクが最大値よりも小さい場合に実行する。
        if($rank < $maxrank) {
            // ランクが一つ上のIDを取得する。
            $where = "rank = ?";
            if($andwhere != "") {
                $where.= " AND $andwhere";
            }
            $uprank = $rank + 1;
            $up_id = $objQuery->get($table, $colname, $where, array($uprank));
            // ランク入れ替えの実行
            $sqlup = "UPDATE $table SET rank = ?, update_date = Now() WHERE $colname = ?";
            $objQuery->exec($sqlup, array($rank + 1, $id));
            $objQuery->exec($sqlup, array($rank, $up_id));
        }
        $objQuery->commit();
    }

    /**
     * ランキングを下げる.
     *
     * @param string $table テーブル名
     * @param string $colname カラム名
     * @param string|integer $id テーブルのキー
     * @param string $andwhere SQL の AND 条件である WHERE 句
     * @return void
     */
    function sfRankDown($table, $colname, $id, $andwhere = "") {
        $objQuery = new SC_Query();
        $objQuery->begin();
        $where = "$colname = ?";
        if($andwhere != "") {
            $where.= " AND $andwhere";
        }
        // 対象項目のランクを取得
        $rank = $objQuery->get($table, "rank", $where, array($id));

        // ランクが1(最小値)よりも大きい場合に実行する。
        if($rank > 1) {
            // ランクが一つ下のIDを取得する。
            $where = "rank = ?";
            if($andwhere != "") {
                $where.= " AND $andwhere";
            }
            $downrank = $rank - 1;
            $down_id = $objQuery->get($table, $colname, $where, array($downrank));
            // ランク入れ替えの実行
            $sqlup = "UPDATE $table SET rank = ?, update_date = Now() WHERE $colname = ?";
            $objQuery->exec($sqlup, array($rank - 1, $id));
            $objQuery->exec($sqlup, array($rank, $down_id));
        }
        $objQuery->commit();
    }

    /**
     * 指定順位へ移動する.
     *
     * @param string $tableName テーブル名
     * @param string $keyIdColumn キーを保持するカラム名
     * @param string|integer $keyId キーの値
     * @param integer $pos 指定順位
     * @param string $where SQL の AND 条件である WHERE 句
     * @return void
     */
    function sfMoveRank($tableName, $keyIdColumn, $keyId, $pos, $where = "") {
        $objQuery = new SC_Query();
        $objQuery->begin();

        // 自身のランクを取得する
        $rank = $objQuery->get($tableName, "rank", "$keyIdColumn = ?", array($keyId));
        $max = $objQuery->max($tableName, "rank", $where);

        // 値の調整（逆順）
        if($pos > $max) {
            $position = 1;
        } else if($pos < 1) {
            $position = $max;
        } else {
            $position = $max - $pos + 1;
        }

        if( $position > $rank ) $term = "rank - 1";	//入れ替え先の順位が入れ換え元の順位より大きい場合
        if( $position < $rank ) $term = "rank + 1";	//入れ替え先の順位が入れ換え元の順位より小さい場合

        // 指定した順位の商品から移動させる商品までのrankを１つずらす
        $sql = "UPDATE $tableName SET rank = $term, update_date = NOW() WHERE rank BETWEEN ? AND ? AND del_flg = 0";
        if($where != "") {
            $sql.= " AND $where";
        }

        if( $position > $rank ) $objQuery->exec( $sql, array( $rank + 1, $position ));
        if( $position < $rank ) $objQuery->exec( $sql, array( $position, $rank - 1 ));

        // 指定した順位へrankを書き換える。
        $sql  = "UPDATE $tableName SET rank = ?, update_date = NOW() WHERE $keyIdColumn = ? AND del_flg = 0 ";
        if($where != "") {
            $sql.= " AND $where";
        }

        $objQuery->exec( $sql, array( $position, $keyId ) );
        $objQuery->commit();
    }

    /**
     * ランクを含むレコードを削除する.
     *
     * レコードごと削除する場合は、$deleteをtrueにする
     *
     * @param string $table テーブル名
     * @param string $colname カラム名
     * @param string|integer $id テーブルのキー
     * @param string $andwhere SQL の AND 条件である WHERE 句
     * @param bool $delete レコードごと削除する場合 true,
     *                     レコードごと削除しない場合 false
     * @return void
     */
    function sfDeleteRankRecord($table, $colname, $id, $andwhere = "",
                                $delete = false) {
        $objQuery = new SC_Query();
        $objQuery->begin();
        // 削除レコードのランクを取得する。
        $where = "$colname = ?";
        if($andwhere != "") {
            $where.= " AND $andwhere";
        }
        $rank = $objQuery->get($table, "rank", $where, array($id));

        if(!$delete) {
            // ランクを最下位にする、DELフラグON
            $sqlup = "UPDATE $table SET rank = 0, del_flg = 1, update_date = Now() ";
            $sqlup.= "WHERE $colname = ?";
            // UPDATEの実行
            $objQuery->exec($sqlup, array($id));
        } else {
            $objQuery->delete($table, "$colname = ?", array($id));
        }

        // 追加レコードのランクより上のレコードを一つずらす。
        $where = "rank > ?";
        if($andwhere != "") {
            $where.= " AND $andwhere";
        }
        $sqlup = "UPDATE $table SET rank = (rank - 1) WHERE $where";
        $objQuery->exec($sqlup, array($rank));
        $objQuery->commit();
    }

    /**
     * 親IDの配列を元に特定のカラムを取得する.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param string $table テーブル名
     * @param string $id_name ID名
     * @param string $col_name カラム名
     * @param array $arrId IDの配列
     * @return array 特定のカラムの配列
     */
    function sfGetParentsCol($objQuery, $table, $id_name, $col_name, $arrId ) {
        $col = $col_name;
        $len = count($arrId);
        $where = "";

        for($cnt = 0; $cnt < $len; $cnt++) {
            if($where == "") {
                $where = "$id_name = ?";
            } else {
                $where.= " OR $id_name = ?";
            }
        }

        $objQuery->setorder("level");
        $arrRet = $objQuery->select($col, $table, $where, $arrId);
        return $arrRet;
    }

    /**
     * カテゴリ変更時の移動処理を行う.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param string $table テーブル名
     * @param string $id_name ID名
     * @param string $cat_name カテゴリ名
     * @param integer $old_catid 旧カテゴリID
     * @param integer $new_catid 新カテゴリID
     * @param integer $id ID
     * @return void
     */
    function sfMoveCatRank($objQuery, $table, $id_name, $cat_name, $old_catid, $new_catid, $id) {
        if ($old_catid == $new_catid) {
            return;
        }
        // 旧カテゴリでのランク削除処理
        // 移動レコードのランクを取得する。
        $where = "$id_name = ?";
        $rank = $objQuery->get($table, "rank", $where, array($id));
        // 削除レコードのランクより上のレコードを一つ下にずらす。
        $where = "rank > ? AND $cat_name = ?";
        $sqlup = "UPDATE $table SET rank = (rank - 1) WHERE $where";
        $objQuery->exec($sqlup, array($rank, $old_catid));
        // 新カテゴリでの登録処理
        // 新カテゴリの最大ランクを取得する。
        $max_rank = $objQuery->max($table, "rank", "$cat_name = ?", array($new_catid)) + 1;
        $where = "$id_name = ?";
        $sqlup = "UPDATE $table SET rank = ? WHERE $where";
        $objQuery->exec($sqlup, array($max_rank, $id));
    }

    /**
     * 配送時間を取得する.
     *
     * @param integer $payment_id 支払い方法ID
     * @return array 配送時間の配列
     */
    function sfGetDelivTime($payment_id = "") {
        $objQuery = new SC_Query();

        $deliv_id = "";

        if($payment_id != "") {
            $where = "del_flg = 0 AND payment_id = ?";
            $arrRet = $objQuery->select("deliv_id", "dtb_payment", $where, array($payment_id));
            $deliv_id = $arrRet[0]['deliv_id'];
        }

        if($deliv_id != "") {
            $objQuery->setorder("time_id");
            $where = "deliv_id = ?";
            $arrRet= $objQuery->select("time_id, deliv_time", "dtb_delivtime", $where, array($deliv_id));
        }

        return $arrRet;
    }

    /**
     * 都道府県、支払い方法から配送料金を取得する.
     *
     * @param integer $pref 都道府県ID
     * @param integer $payment_id 支払い方法ID
     * @return string 指定の都道府県, 支払い方法の配送料金
     */
    function sfGetDelivFee($pref, $payment_id = "") {
        $objQuery = new SC_Query();

        $deliv_id = "";

        // 支払い方法が指定されている場合は、対応した配送業者を取得する
        if($payment_id != "") {
            $where = "del_flg = 0 AND payment_id = ?";
            $arrRet = $objQuery->select("deliv_id", "dtb_payment", $where, array($payment_id));
            $deliv_id = $arrRet[0]['deliv_id'];
        // 支払い方法が指定されていない場合は、先頭の配送業者を取得する
        } else {
            $where = "del_flg = 0";
            $objQuery->setOrder("rank DESC");
            $objQuery->setLimitOffset(1);
            $arrRet = $objQuery->select("deliv_id", "dtb_deliv", $where);
            $deliv_id = $arrRet[0]['deliv_id'];
        }

        // 配送業者から配送料を取得
        if($deliv_id != "") {

            // 都道府県が指定されていない場合は、東京都の番号を指定しておく
            if($pref == "") {
                $pref = 13;
            }

            $objQuery = new SC_Query();
            $where = "deliv_id = ? AND pref = ?";
            $arrRet= $objQuery->select("fee", "dtb_delivfee", $where, array($deliv_id, $pref));
        }
        return $arrRet[0]['fee'];
    }

    /**
     * 集計情報を元に最終計算を行う.
     *
     * @param array $arrData 各種情報
     * @param LC_Page $objPage LC_Page インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @param array $arrInfo 店舗情報の配列
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @return array 最終計算後の配列
     */
    function sfTotalConfirm($arrData, &$objPage, &$objCartSess, $arrInfo, &$objCustomer = "") {
        // 未定義変数を定義
        if (!isset($arrData['deliv_pref'])) $arrData['deliv_pref'] = "";
        if (!isset($arrData['payment_id'])) $arrData['payment_id'] = "";
        if (!isset($arrData['charge'])) $arrData['charge'] = "";
        if (!isset($arrData['use_point'])) $arrData['use_point'] = "";

        // 商品の合計個数
        $total_quantity = $objCartSess->getTotalQuantity(true);

        // 税金の取得
        $arrData['tax'] = $objPage->tpl_total_tax;
        // 小計の取得
        $arrData['subtotal'] = $objPage->tpl_total_pretax;

        // 合計送料の取得
        $arrData['deliv_fee'] = 0;

        // 商品ごとの送料が有効の場合
        if (OPTION_PRODUCT_DELIV_FEE == 1) {
            $arrData['deliv_fee']+= $objCartSess->getAllProductsDelivFee();
        }

        // 配送業者の送料が有効の場合
        if (OPTION_DELIV_FEE == 1) {
            // 送料の合計を計算する
            $arrData['deliv_fee']
                += $this->sfGetDelivFee($arrData['deliv_pref'],
                                           $arrData['payment_id']);

        }

        // 送料無料の購入数が設定されている場合
        if(DELIV_FREE_AMOUNT > 0) {
            if($total_quantity >= DELIV_FREE_AMOUNT) {
                $arrData['deliv_fee'] = 0;
            }
        }

        // 送料無料条件が設定されている場合
        if($arrInfo['free_rule'] > 0) {
            // 小計が無料条件を超えている場合
            if($arrData['subtotal'] >= $arrInfo['free_rule']) {
                $arrData['deliv_fee'] = 0;
            }
        }

        // 合計の計算
        $arrData['total'] = $objPage->tpl_total_pretax;	// 商品合計
        $arrData['total']+= $arrData['deliv_fee'];		// 送料
        $arrData['total']+= $arrData['charge'];			// 手数料
        // お支払い合計
        $arrData['payment_total'] = $arrData['total'] - ($arrData['use_point'] * POINT_VALUE);
        // 加算ポイントの計算
        $arrData['add_point'] = SC_Utils::sfGetAddPoint($objPage->tpl_total_point, $arrData['use_point'], $arrInfo);

        if($objCustomer != "") {
            // 誕生日月であった場合
            if($objCustomer->isBirthMonth()) {
                $arrData['birth_point'] = BIRTH_MONTH_POINT;
                $arrData['add_point'] += $arrData['birth_point'];
            }
        }

        if($arrData['add_point'] < 0) {
            $arrData['add_point'] = 0;
        }
        return $arrData;
    }

    /**
     * レコードの存在チェックを行う.
     *
     * @param string $table テーブル名
     * @param string $col カラム名
     * @param array $arrval 要素の配列
     * @param array $addwhere SQL の AND 条件である WHERE 句
     * @return bool レコードが存在する場合 true
     */
    function sfIsRecord($table, $col, $arrval, $addwhere = "") {
        $objQuery = new SC_Query();
        $arrCol = split("[, ]", $col);

        $where = "del_flg = 0";

        if($addwhere != "") {
            $where.= " AND $addwhere";
        }

        foreach($arrCol as $val) {
            if($val != "") {
                if($where == "") {
                    $where = "$val = ?";
                } else {
                    $where.= " AND $val = ?";
                }
            }
        }
        $ret = $objQuery->get($table, $col, $where, $arrval);

        if($ret != "") {
            return true;
        }
        return false;
    }

}
?>
