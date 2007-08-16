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
 * @version $Id$
 */
class SC_Helper_DB {

    // {{{ properties

    /** ルートカテゴリ取得フラグ */
    var $g_root_on;

    /** ルートカテゴリID */
    var $g_root_id;

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
                $category_id = SC_Utils_Ex::sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
                // ROOTカテゴリIDの取得
                $arrRet = SC_Utils_Ex::sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $category_id);
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
     * カート内商品の集計処理を行う.
     *
     * @param LC_Page $objPage ページクラスのインスタンス
     * @param SC_CartSession $objCartSess カートセッションのインスタンス
     * @param array $arrInfo 商品情報の配列
     * @return LC_Page 集計処理後のページクラスインスタンス
     */
    function sfTotalCart($objPage, $objCartSess, $arrInfo) {
        // 規格名一覧
        $arrClassName = SC_Utils_Ex::sfGetIDValueList("dtb_class", "class_id", "name");
        // 規格分類名一覧
        $arrClassCatName = SC_Utils_Ex::sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");

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
                $objPage->arrProductsClass[$cnt]['class_name1'] = $arrClassName[$arrData['class_id1']];
                $objPage->arrProductsClass[$cnt]['class_name2'] = $arrClassName[$arrData['class_id2']];
                $objPage->arrProductsClass[$cnt]['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
                $objPage->arrProductsClass[$cnt]['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];

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
