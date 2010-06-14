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
require_once CLASS_PATH . "pages/LC_Page.php";
require_once DATA_PATH . 'module/Services/JSON.php';
require_once DATA_PATH . 'module/Request.php';

/**
 * 管理画面ホーム のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Home extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'home.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // DBバージョンの取得
        $objDb = new SC_Helper_DB_Ex();
        $this->db_version = $objDb->sfGetDBVersion();

        // PHPバージョンの取得
        $this->php_version = "PHP " . phpversion();

        // 現在の会員数
        $this->customer_cnt = $this->lfGetCustomerCnt($conn);

        // 昨日の売上高
        $this->order_yesterday_amount = $this->lfGetOrderYesterday($conn, "SUM");

        // 昨日の売上件数
        $this->order_yesterday_cnt = $this->lfGetOrderYesterday($conn, "COUNT");

        // 今月の売上高
        $this->order_month_amount = $this->lfGetOrderMonth($conn, "SUM");

        // 今月の売上件数
        $this->order_month_cnt = $this->lfGetOrderMonth($conn, "COUNT");

        // 顧客の累計ポイント
        $this->customer_point = $this->lfGetTotalCustomerPoint();

        //昨日のレビュー書き込み数
        $this->review_yesterday_cnt = $this->lfGetReviewYesterday($conn);

        //レビュー書き込み非表示数
        $this->review_nondisp_cnt = $this->lfGetReviewNonDisp($conn);

        // 品切れ商品
        $this->arrSoldout = $this->lfGetSoldOut();

        // 新規受付一覧
        $arrNewOrder = $this->lfGetNewOrder();

        foreach ($arrNewOrder as $key => $val){
            $arrNewOrder[$key]['create_date'] = str_replace("-", "/", substr($val['create_date'], 0,19));

        }
        $this->arrNewOrder = $arrNewOrder;

        // お知らせ一覧の取得
        $this->arrInfo = $this->lfGetInfo();

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // 会員数
    function lfGetCustomerCnt($conn){

        $sql = "SELECT COUNT(customer_id) FROM dtb_customer WHERE del_flg = 0 AND status = 2";
        $return = $conn->getOne($sql);
        return $return;
    }

    // 昨日の売上高・売上件数
    function lfGetOrderYesterday($conn, $method){
        if ( $method == 'SUM' or $method == 'COUNT'){
            // postgresql と mysql とでSQLをわける
            if (DB_TYPE == "pgsql") {
                $sql = "SELECT ".$method."(total) FROM dtb_order
                         WHERE del_flg = 0 AND to_char(create_date,'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD') AND status <> " . ORDER_CANCEL;
            }else if (DB_TYPE == "mysql") {
                $sql = "SELECT ".$method."(total) FROM dtb_order
                         WHERE del_flg = 0 AND cast(create_date as date) = DATE_ADD(current_date, interval -1 day) AND status <> " . ORDER_CANCEL;
            }
            $return = $conn->getOne($sql);
        }
        return $return;
    }

    function lfGetOrderMonth($conn, $method){

        $month = date("Y/m", mktime());

        if ( $method == 'SUM' or $method == 'COUNT'){
        // postgresql と mysql とでSQLをわける
        if (DB_TYPE == "pgsql") {
            $sql = "SELECT ".$method."(total) FROM dtb_order
                     WHERE del_flg = 0 AND to_char(create_date,'YYYY/MM') = ?
                     AND to_char(create_date,'YYYY/MM/DD') <> to_char(now(),'YYYY/MM/DD') AND status <> " . ORDER_CANCEL;
        }else if (DB_TYPE == "mysql") {
            $sql = "SELECT ".$method."(total) FROM dtb_order
                     WHERE del_flg = 0 AND date_format(create_date, '%Y/%m') = ?
                     AND date_format(create_date, '%Y/%m/%d') <> date_format(now(), '%Y/%m/%d') AND status <> " . ORDER_CANCEL;
        }
            $return = $conn->getOne($sql, array($month));
        }
        return $return;
    }

    function lfGetTotalCustomerPoint() {
        $objQuery = new SC_Query();
        $col = "SUM(point)";
        $where = "del_flg = 0";
        $from = "dtb_customer";
        $ret = $objQuery->get($from, $col, $where);
        return $ret;
    }

    function lfGetReviewYesterday($conn){
        // postgresql と mysql とでSQLをわける
        if (DB_TYPE == "pgsql") {
            $sql = "SELECT COUNT(*) FROM dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id
                     WHERE A.del_flg=0 AND B.del_flg = 0 AND to_char(A.create_date, 'YYYY/MM/DD') = to_char(now() - interval '1 days','YYYY/MM/DD')
                     AND to_char(A.create_date,'YYYY/MM/DD') != to_char(now(),'YYYY/MM/DD')";
        }else if (DB_TYPE == "mysql") {
            $sql = "SELECT COUNT(*) FROM dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id
                     WHERE A.del_flg = 0 AND B.del_flg = 0 AND cast(A.create_date as date) = DATE_ADD(current_date, interval -1 day)
                     AND cast(A.create_date as date) != current_date";
        }
        $return = $conn->getOne($sql);
        return $return;
    }

    function lfGetReviewNonDisp($conn){
        $sql = "SELECT COUNT(*) FROM dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id WHERE A.del_flg=0 AND A.status=2 AND B.del_flg=0";
        $return = $conn->getOne($sql);
        return $return;
    }

    // 品切れ商品番号の取得
    function lfGetSoldOut() {
        $objQuery = new SC_Query();
        $where = "product_id IN (SELECT product_id FROM dtb_products_class WHERE stock_unlimited IS NULL AND stock <= 0)";
        $arrRet = $objQuery->select("product_id, name", "dtb_products", $where);
        return $arrRet;
    }

    // 新規受付一覧
    function lfGetNewOrder() {
        $objQuery = new SC_Query();
        $sql = "SELECT
                    ord.order_id,
                    ord.customer_id,
                    ord.order_name01 AS name01,
                    ord.order_name02 AS name02,
                    ord.total,
                    ord.create_date,
                    (SELECT
                        det.product_name
                    FROM
                        dtb_order_detail AS det
                    WHERE
                        ord.order_id = det.order_id LIMIT 1
                    ) AS product_name,
                    (SELECT
                        pay.payment_method
                    FROM
                        dtb_payment AS pay
                    WHERE
                        ord.payment_id = pay.payment_id
                    ) AS payment_method
                FROM (
                    SELECT
                        order_id,
                        customer_id,
                        order_name01,
                        order_name02,
                        total,
                        create_date,
                        payment_id
                    FROM
                        dtb_order AS ord
                    WHERE
                        del_flg = 0 AND status <> " . ORDER_CANCEL . "
                    ORDER BY
                        create_date DESC LIMIT 10 OFFSET 0
                ) AS ord";
        $arrRet = $objQuery->getAll($sql);
        return $arrRet;
    }

    /**
     * リリース情報を取得する.
     *
     * @return unknown
     */
    function lfGetInfo() {
        $query = '';
        // TODO サイト情報の送信可否設定を行う
        if (true) {
            $query = '?site_url=' . SITE_URL . '&eccube_version=' . ECCUBE_VERSION;
        }

        $url = UPDATE_HTTP . $query;
        $jsonStr = @file_get_contents($url);

        $objJson = new Services_JSON;
        $arrTmpData = is_string($jsonStr) ? $objJson->decode($jsonStr) : null;

        if (empty($arrTmpData)) {
            SC_Utils_Ex::sfErrorHeader(">> 更新情報の取得に失敗しました。");
            return array();
        }

        $arrInfo = array();
        foreach ($arrTmpData as $objData) {
            $arrInfo[] = get_object_vars($objData);
        }

        return $arrInfo;
    }
}
?>
