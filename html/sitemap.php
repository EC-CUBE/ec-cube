<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 */

/**
 * Sitemapプロトコル ファイル生成モジュール.
 * PHP versions 4 and 5
 *
 * <pre>
 * このモジュールは Sitemapプロトコルに対応した XMLファイルを出力する.
 * EC-CUBE インストールディレクトリの htmlディレクトリへ配置することにより動作する.
 *
 * このモジュールにより, 以下のページのサイトマップが生成される.
 * 1. $staticURL で指定したページ
 * 2. 管理画面のデザイン管理から生成したページ
 * 3. 公開されているすべての商品一覧ページ
 * 4. 公開されているすべての商品詳細ページ
 * 5. html/mobile 以下の上記ページ
 *
 * このモジュールを設置後, 各検索エンジンにサイトマップを登録することにより, 検索エンジンの
 * インデックス化が促進される.
 * </pre>
 * @see https://www.google.com/webmasters/tools/siteoverview?hl=ja
 * @see https://siteexplorer.search.yahoo.com/mysites
 *
 * @author Kentaro Ohkouchi
 * @version $Id$
 *
 */
require_once("require.php");
// --------------------------------------------------------------------- 初期設定
// :TODO: filemtime 関数を使えば、静的なページの更新時間も取得するようにできそう
//
// 動的に生成されないページを配列で指定
$staticURL = array(SITE_URL, MOBILE_SITE_URL, SITE_URL . "rss/index.php");
// :TODO: 各ページの changefreq や priority を指定できるようにする
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
// }}}
// {{{ View Logic

/**
 * Sitemap の <url /> を生成する.
 *
 * @param string $loc ページの URL ※必須
 * @param string $lastmod ファイルの最終更新日 YYYY-MM-DD or  W3C Datetime 形式
 * @param string $changefreq ページの更新頻度
 * @param double $priority URL の優先度
 * @return Sitemap 形式の <url />
 * @see https://www.google.com/webmasters/tools/docs/ja/protocol.html#xmlTagDefinitions
 */
function createSitemap($loc, $lastmod = "", $changefreq = "", $priority = "") {
    printf("\t<url>\n");
    printf("\t\t<loc>%s</loc>\n", htmlentities($loc, ENT_QUOTES, "UTF-8"));
    if (!empty($lastmod)) {
        printf("\t\t<lastmod>%s</lastmod>\n", $lastmod);
    }
    if (!empty($changefreq)) {
        printf("\t\t<changefreq>%s</changefreq>\n", $changefreq);
    }
    if(!empty($priority)) {
        printf("\t\t<priority>%01.1f</priority>\n", $priority);
    }
    printf("\t</url>\n");
}

$objQuery = new SC_Query();

//キャッシュしない(念のため)
header("Paragrama: no-cache");

//XMLテキスト
header("Content-type: application/xml; charset=utf-8");

// 必ず UTF-8 として出力
mb_http_output("UTF-8");
ob_start('mb_output_handler');

print("<?xml version='1.0' encoding='UTF-8'?>\n");
print("<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n");

// ----------------------------------------------------------------------------
// }}}
// {{{ Controller Logic

// 静的なページを処理
foreach($staticURL as $url) {
    createSitemap($url, '', 'daily', 1.0);
}
// ページのデータを取得
$objPageData = new LC_PageLayout;

// TOPページを処理
$topPage = getTopPage($objPageData->arrPageList);
createSitemap($topPage[0]['url'], date2W3CDatetime($topPage[0]['update_date']),
                'daily', 1.0);

// 編集可能ページを処理
$editablePages = getEditablePage($objPageData->arrPageList);
foreach($editablePages as $editablePage) {
    createSitemap($editablePage['url'], date2W3CDatetime($editablePage['update_date']));
}

// 商品一覧ページを処理
$products = getAllProducts();
foreach($products as $product) {
    createSitemap($product['url'], '', 'daily');
}
$mobileProducts = getAllProducts(true);
foreach($mobileProducts as $mobileProduct) {
    createSitemap($mobileProduct['url'], '', 'daily');
}

// 商品詳細ページを処理
$details = getAllDetail();
foreach($details as $detail) {
    createSitemap($detail['url'], date2W3CDatetime($detail['update_date']));
}
$mobileDetails = getAllDetail(true);
foreach($mobileDetails as $mobileDetail) {
    createSitemap($mobileDetail['url'], date2W3CDatetime($mobileDetail['update_date']));
}

print("</urlset>\n");

// ----------------------------------------------------------------------------
// }}}
// {{{ Model Logic

/**
 * TOPページの情報を取得
 *
 * @param array $pageData すべてのページ情報の配列
 * @return TOPページの情報
 */
function getTopPage($pageData) {
    $arrRet = array();
    foreach($pageData as $page) {
        if ($page['page_id'] == "1") {
            $page['url'] = SITE_URL . $page['url'];
            $arrRet[0] = $page;
            return $arrRet;
        }
    }
}

/**
 * すべての編集可能ページの情報を取得する.
 *
 * @param array $pageData すべてのページ情報の配列
 * @return 編集可能ページ
 */
function getEditablePage($pageData) {
    $arrRet = array();
    $i = 0;
    foreach($pageData as $page) {
        if ($page['page_id'] > 4) {
            $arrRet[$i] = $page;
            $i++;
        }
    }
    return $arrRet;
}

/**
 * date形式の文字列を W3C Datetime 形式に変換して出力する.
 *
 * @param date $date 変換する日付
 */
function date2W3CDatetime($date) {
    $arr = array();
    // 正規表現で文字列を抽出
    ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",
        $date, $arr);
    // :TODO: time zone も取得するべき...
    return sprintf("%04d-%02d-%02dT%02d:%02d:%02d+09:00",
            $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $arr[6]);
}

// ----------------------------------------------------------------------------
// }}}
// {{{ DB Access Objects

/**
 * ページデータを扱うクラス.
 */
class LC_PageLayout {

    var $arrPageData;		// ページデータ格納用
    var $arrPageList;		// ページデータ格納用

    /**
     * コンストラクタ.
     */
    function LC_PageLayout() {
        $this->arrPageList = $this->getPageData();
    }

    /**
     * ブロック情報を取得する.
     *
     * @param string $where WHERE句
     * @param array  $arrVal WHERE句の値を格納した配列
     * @return ブロック情報
     */
    function getPageData($where = '', $arrVal = ''){
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用

        // SQL生成(url と update_date 以外は不要？)
        $sql .= " SELECT";
        $sql .= " page_id";				// ページID
        $sql .= " ,page_name";			// 名称
        $sql .= " ,url";				// URL
        $sql .= " ,php_dir";			// php保存先ディレクトリ
        $sql .= " ,tpl_dir";			// tpl保存先ディdレクトリ
        $sql .= " ,filename";			// ファイル名称
        $sql .= " ,header_chk ";		// ヘッダー使用FLG
        $sql .= " ,footer_chk ";		// フッター使用FLG
        $sql .= " ,author";				// authorタグ
        $sql .= " ,description";		// descriptionタグ
        $sql .= " ,keyword";			// keywordタグ
        $sql .= " ,update_url";			// 更新URL
        $sql .= " ,create_date";		// データ作成日
        $sql .= " ,update_date";		// データ更新日
        $sql .= " FROM ";
        $sql .= "     dtb_pagelayout";

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= " WHERE " . $where;
        }

        $sql .= " ORDER BY 	page_id";

        $arrRet = $objDBConn->getAll($sql, $arrVal);

        $this->arrPageData = $arrRet;

        return $arrRet;
    }
}

/**
 * すべての商品一覧ページを取得する.
 *
 * @param boolean $isMobile モバイルページを取得する場合 true
 * @return 検索エンジンからアクセス可能な商品一覧ページの情報
 */
function getAllProducts($isMobile = false) {
    $conn = new SC_DBConn();
    $sql = "SELECT category_id FROM dtb_category WHERE del_flg = 0";
    $result = $conn->getAll($sql);

    $mobile = "";
    if ($isMobile) {
        $mobile = "mobile/";
    }

    $arrRet = array();
    for ($i = 0; $i < count($result); $i++) {
        // :TODO: カテゴリの最終更新日を取得できるようにする
        $page = array("url" => SITE_URL . sprintf("%sproducts/list.php?category_id=%d", $mobile, $result[$i]['category_id']));
        $arrRet[$i] = $page;
    }
    return $arrRet;
}

/**
 * すべての商品詳細ページを取得する.
 *
 * @param boolean $isMobile モバイルページを取得する場合 true
 * @return 検索エンジンからアクセス可能な商品詳細ページの情報
 */
function getAllDetail($isMobile = false) {
    $conn = new SC_DBConn();
    $sql = "SELECT product_id, update_date FROM dtb_products WHERE del_flg = 0 AND status = 1";
    $result = $conn->getAll($sql);

    $mobile = "";
    if ($isMobile) {
        $mobile = "mobile/";
    }

    $arrRet = array();
    for ($i = 0; $i < count($result); $i++) {
        $page = array("url" => SITE_URL. sprintf("%sproducts/detail.php?product_id=%d", $mobile, $result[$i]['product_id']),
                        "update_date" => $result[$i]['update_date']);
        $arrRet[$i] = $page;
    }
    return $arrRet;
}
?>
