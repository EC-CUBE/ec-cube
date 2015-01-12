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

namespace Eccube\Page\Admin;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Query;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\Util\Utils;

/**
 * 管理画面ホーム のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Home extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'home.tpl';
        $this->tpl_subtitle = 'ホーム';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
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
     * PHPバージョンの取得
     *
     * @return string PHPバージョン情報
     */
    public function lfGetPHPVersion()
    {
        return 'PHP ' . phpversion();
    }

    /**
     * DBバージョンの取得
     *
     * @return mixed DBバージョン情報
     */
    public function lfGetDBVersion()
    {
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');

        return $dbFactory->sfGetDBVersion();
    }

    /**
     * 現在の会員数の取得
     *
     * @return integer 会員数
     */
    public function lfGetCustomerCnt()
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'COUNT(customer_id)';
        $table = 'dtb_customer';
        $where = 'del_flg = 0 AND status = 2';

        return $objQuery->get($col, $table, $where);
    }

    /**
     * 昨日の売上データの取得
     *
     * @param  string  $method 取得タイプ 件数:'COUNT' or 金額:'SUM'
     * @return integer 結果数値
     */
    public function lfGetOrderYesterday($method)
    {
        $objQuery = Application::alias('eccube.query');

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');
        $sql = $dbFactory->getOrderYesterdaySql($method);

        return $objQuery->getOne($sql);
    }

    /**
     * 今月の売上データの取得
     *
     * @param  string  $method 取得タイプ 件数:'COUNT' or 金額:'SUM'
     * @return integer 結果数値
     */
    public function lfGetOrderMonth($method)
    {
        $objQuery = Application::alias('eccube.query');
        $month = date('Y/m', mktime());

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');
        $sql = $dbFactory->getOrderMonthSql($method);

        return $objQuery->getOne($sql, array($month));
    }

    /**
     * 会員の保持ポイント合計の取得
     *
     * @return integer 会員の保持ポイント合計
     */
    public function lfGetTotalCustomerPoint()
    {
        $objQuery = Application::alias('eccube.query');

        $col = 'SUM(point)';
        $where = 'del_flg = 0';
        $from = 'dtb_customer';

        return $objQuery->get($col, $from, $where);
    }

    /**
     * 昨日のレビュー書き込み数の取得
     *
     * @return integer 昨日のレビュー書き込み数
     */
    public function lfGetReviewYesterday()
    {
        $objQuery = Application::alias('eccube.query');

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');
        $sql = $dbFactory->getReviewYesterdaySql();

        return $objQuery->getOne($sql);
    }

    /**
     * レビュー書き込み非表示数の取得
     *
     * @return integer レビュー書き込み非表示数
     */
    public function lfGetReviewNonDisp()
    {
        $objQuery = Application::alias('eccube.query');

        $table = 'dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id';
        $where = 'A.del_flg = 0 AND A.status = 2 AND B.del_flg = 0';

        return $objQuery->count($table, $where);
    }

    /**
     * 品切れ商品の取得
     *
     * @return array 品切れ商品一覧
     */
    public function lfGetSoldOut()
    {
        $objQuery = Application::alias('eccube.query');

        $cols = 'product_id, name';
        $table = 'dtb_products';
        $where = 'product_id IN ('
               . 'SELECT product_id FROM dtb_products_class '
               . 'WHERE del_flg = 0 AND stock_unlimited = ? AND stock <= 0)'
               . ' AND del_flg = 0';

        return $objQuery->select($cols, $table, $where, array(UNLIMITED_FLG_LIMITED));
    }

    /**
     * 新規受付一覧の取得
     *
     * @return array 新規受付一覧配列
     */
    public function lfGetNewOrder()
    {
        $objQuery = Application::alias('eccube.query');

        $objQuery->setOrder('order_detail_id');
        $sql_product_name = $objQuery->getSql('product_name', 'dtb_order_detail', 'order_id = dtb_order.order_id');
        $sql_product_name = $objQuery->dbFactory->addLimitOffset($sql_product_name, 1);

        $cols = <<< __EOS__
            dtb_order.order_id,
            dtb_order.customer_id,
            dtb_order.order_name01 AS name01,
            dtb_order.order_name02 AS name02,
            dtb_order.total,
            dtb_order.create_date,
            ($sql_product_name) AS product_name,
            (SELECT
                pay.payment_method
            FROM
                dtb_payment AS pay
            WHERE
                dtb_order.payment_id = pay.payment_id
            ) AS payment_method
__EOS__;
        $from = 'dtb_order';
        $where = 'del_flg = 0 AND status <> ?';
        $objQuery->setOrder('create_date DESC');
        $objQuery->setLimit(10);
        $arrNewOrder = $objQuery->select($cols, $from, $where, ORDER_CANCEL);

        foreach ($arrNewOrder as $key => $val) {
            $arrNewOrder[$key]['create_date'] = str_replace('-', '/', substr($val['create_date'], 0, 19));
        }

        return $arrNewOrder;
    }

    /**
     * リリース情報を取得する.
     *
     * @return array 取得した情報配列
     */
    public function lfGetInfo()
    {
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

        $arrTmpData = is_string($jsonStr) ? Utils::jsonDecode($jsonStr) : null;

        if (empty($arrTmpData)) {
            Utils::sfErrorHeader('>> 更新情報の取得に失敗しました。');

            return array();
        }
        $arrInfo = array();
        foreach ($arrTmpData as $objData) {
            $arrInfo[] = get_object_vars($objData);
        }

        return $arrInfo;
    }
}
