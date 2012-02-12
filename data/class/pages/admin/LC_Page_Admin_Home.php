
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 管理画面ホーム のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Home extends LC_Page_Admin_Ex {

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
        $this->tpl_subtitle = 'ホーム';
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

        // DBバージョンの取得
        $this->db_version = $this->lfGetDBVersion();

        // PHPバージョンの取得
        $this->php_version = $this->lfGetPHPVersion();

        // 現在の会員数
        $this->customer_cnt = $this->lfGetCustomerCnt();

        // 昨日の売上高
        $this->order_yesterday_amount = $this->lfGetOrderYesterday('SUM');

        // 昨日の売上件数
        $this->order_yesterday_cnt = $this->lfGetOrderYesterday('COUNT');

        // 今月の売上高
        $this->order_month_amount = $this->lfGetOrderMonth('SUM');

        // 今月の売上件数
        $this->order_month_cnt = $this->lfGetOrderMonth('COUNT');

        // 会員の累計ポイント
        $this->customer_point = $this->lfGetTotalCustomerPoint();

        //昨日のレビュー書き込み数
        $this->review_yesterday_cnt = $this->lfGetReviewYesterday();

        //レビュー書き込み非表示数
        $this->review_nondisp_cnt = $this->lfGetReviewNonDisp();

        // 品切れ商品
        $this->arrSoldout = $this->lfGetSoldOut();

        // 新規受付一覧
        $this->arrNewOrder = $this->lfGetNewOrder();

        // お知らせ一覧の取得
        $this->arrInfo = $this->lfGetInfo();
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
     * PHPバージョンの取得
     *
     * @return string PHPバージョン情報
     */
    function lfGetPHPVersion() {
        return "PHP " . phpversion();
    }

    /**
     * DBバージョンの取得
     *
     * @return mixed DBバージョン情報
     */
    function lfGetDBVersion() {
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        return $dbFactory->sfGetDBVersion();
    }

    /**
     * 現在の会員数の取得
     *
     * @return integer 会員数
     */
    function lfGetCustomerCnt() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = "COUNT(customer_id)";
        $table = 'dtb_customer';
        $where = "del_flg = 0 AND status = 2";
        return $objQuery->get($col, $table, $where);
    }

    /**
     * 昨日の売上データの取得
     *
     * @param string $method 取得タイプ 件数:'COUNT' or 金額:'SUM'
     * @return integer 結果数値
     */
    function lfGetOrderYesterday($method) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $sql = $dbFactory->getOrderYesterdaySql($method);
        return $objQuery->getOne($sql);
    }

    /**
     * 今月の売上データの取得
     *
     * @param string $method 取得タイプ 件数:'COUNT' or 金額:'SUM'
     * @return integer 結果数値
     */
    function lfGetOrderMonth($method) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $month = date("Y/m", mktime());

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $sql = $dbFactory->getOrderMonthSql($method);
        return $objQuery->getOne($sql, array($month));
    }

    /**
     * 会員の保持ポイント合計の取得
     *
     * @return integer 会員の保持ポイント合計
     */
    function lfGetTotalCustomerPoint() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $col = "SUM(point)";
        $where = "del_flg = 0";
        $from = 'dtb_customer';
        return $objQuery->get($col, $from, $where);
    }

    /**
     * 昨日のレビュー書き込み数の取得
     *
     * @return integer 昨日のレビュー書き込み数
     */
    function lfGetReviewYesterday() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $sql = $dbFactory->getReviewYesterdaySql();
        return $objQuery->getOne($sql);
    }

    /**
     * レビュー書き込み非表示数の取得
     *
     * @return integer レビュー書き込み非表示数
     */
    function lfGetReviewNonDisp() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $table = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id";
        $where = "A.del_flg = 0 AND A.status = 2 AND B.del_flg = 0";
        return $objQuery->count($table, $where);
    }

    /**
     * 品切れ商品の取得
     *
     * @return array 品切れ商品一覧
     */
    function lfGetSoldOut() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $cols = "product_id, name";
        $table = 'dtb_products';
        $where = "product_id IN ("
                  . "SELECT product_id FROM dtb_products_class "
                  . "WHERE stock_unlimited = ? AND stock <= 0)";
        return $objQuery->select($cols, $table, $where, array(UNLIMITED_FLG_LIMITED));
    }

    /**
     * 新規受付一覧の取得
     *
     * @return array 新規受付一覧配列
     */
    function lfGetNewOrder() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

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
                        ord.order_id = det.order_id
                    ORDER BY det.order_detail_id
                    LIMIT 1
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
        $arrNewOrder = $objQuery->getAll($sql);
        foreach ($arrNewOrder as $key => $val) {
            $arrNewOrder[$key]['create_date'] = str_replace("-", "/", substr($val['create_date'], 0,19));

        }
        return $arrNewOrder;
    }

    /**
     * リリース情報を取得する.
     *
     * @return array 取得した情報配列
     */
    function lfGetInfo() {
        // 更新情報の取得ON/OFF確認
        if (!ECCUBE_INFO) return array();

        // パラメーター「UPDATE_HTTP」が空文字の場合、処理しない。
        // XXX これと別に on/off を持たせるべきか。
        if (strlen(UPDATE_HTTP) == 0) return array();

        $query = '';
        // サイト情報の送信可否設定
        // XXX インストール時に問い合わせて送信可否設定を行うように設定すべきか。
        // XXX (URLは強制送信すべきではないと思うが)バージョンは強制送信すべきか。
        if (UPDATE_SEND_SITE_INFO === true) {
            $query = '?site_url=' . HTTP_URL . '&eccube_version=' . ECCUBE_VERSION;
        }

        $url = UPDATE_HTTP . $query;

        // タイムアウト時間設定
        $context = array('http' => array('timeout' => HTTP_REQUEST_TIMEOUT));

        $jsonStr = @file_get_contents($url, false, stream_context_create($context));

        $arrTmpData = is_string($jsonStr) ? SC_Utils_Ex::jsonDecode($jsonStr) : null;

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
