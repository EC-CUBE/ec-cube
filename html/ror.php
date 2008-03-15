<?php
/**
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * ROR Sitemapプロトコル ファイル生成モジュール
 * PHP4(PHP5でも動作すると思われます)
 *
 * (1)モジュール概要
 *    このモジュールは ROR Sitemapプロトコルに対応した XMLファイルを出力する。
 *    RORとはResources of a Resourceの略で、全てのサーチエンジンに対して使えるサイトマップ。
 *    see) http://www.rorweb.com/
 *
 *
 * (2)このモジュールにできること
 *    このモジュールにより、以下のページのサイトマップが生成される。
 *    1. $staticURL で指定したページ
 *    2. 管理画面のデザイン管理から生成した静的ページ
 *    3. 公開されているすべての商品一覧ページ
 *    4. 公開されているすべての商品詳細ページ
 * 
 * (3)使用方法
 *    EC-CUBE インストールディレクトリの htmlディレクトリ直下へ配置することにより動作する。
 *    設置後、metaタグで以下のように登録すると、各検索エンジンのクローラーに
 *    自動的に拾われるようになる。
 *    ex) <link rel="alternate" type="application/rss+xml" title="ROR" href="http://your-site.com/ror.php" />
 *
 *    また、サイトマップを登録することにより, 検索エンジンのインデックス化が促進される。
 *    see) https://www.google.com/webmasters/tools/siteoverview?hl=ja
 *    see) https://siteexplorer.search.yahoo.com/mysites
 *
 * @author Kentaro Ohkouchi
 * @modified Issei Homan
 */

require_once("require.php");
$objSiteInfo = new SC_SiteInfo();

//店舗情報をセット
$arrSiteInfo = $objSiteInfo->data;

// 動的に生成されないページを配列で指定
$staticURL = array("RSS Feeds" => SITE_URL."rss/index.php");

// ----------------------------------------------------------------------------
// }}}
// {{{ View Logic

/**
 * <item /> を生成する.
 *
 * @param string $title      ページタイトル
 * @param string $link       ページの URL ※必須
 * @param string $desc       概要（短）商品一覧コメント
 * @param string $descLong   概要（長）商品詳細メインコメント
 * @param string $keywords   カンマ区切りキーワード comment3
 * @param string $updated    ファイルの最終更新日 YYYY-MM-DD or W3C Datetime 形式
 * @param string $period     ページの更新頻度
 * @param string $image      メイン画像 main_image
 * @param string $imageLarge 拡大メイン画像 main_large_image
 * @param int    $sort       ソート値
 * @return Sitemap 形式の <item />
 * see) http://www.rorweb.com/
 */
function createSitemap($title = "", $link, $desc = "", $descLong = "", $keywords = "", $updated = "", $period = "", $image = "", $imageLarge = "", $sort = "") {
    printf("\t<item>\n");
    if (!empty($title)) {
        printf("\t\t<title>%s</title>\n", mb_convert_encoding($title, "UTF-8", "EUC-JP"));
    }
    printf("\t\t<link>%s</link>\n", htmlentities($link, ENT_QUOTES, "UTF-8"));
    if (!empty($desc)) {
        printf("\t\t<description><![CDATA[%s]]></description>\n", mb_convert_encoding($desc, "UTF-8", "EUC-JP"));
    }
    if (!empty($descLong)) {
        printf("\t\t<ror:descLong><![CDATA[%s]]></ror:descLong>\n", mb_convert_encoding($descLong, "UTF-8", "EUC-JP"));
    }
    if (!empty($keywords)) {
        printf("\t\t<ror:keywords><![CDATA[%s]]></ror:keywords>\n", mb_convert_encoding($keywords, "UTF-8", "EUC-JP"));
    }
    if (!empty($updated)) {
        printf("\t\t<ror:updated>%s</ror:updated>\n", $updated);
    }
    if (!empty($period)) {
        printf("\t\t<ror:updatePeriod>%s</ror:updatePeriod>\n", $period);
    }
    if (!empty($image)) {
        printf("\t\t<ror:image>%s</ror:image>\n", $image);
    }
    if (!empty($imageLarge)) {
        printf("\t\t<ror:imageLarge>%s</ror:imageLarge>\n", $imageLarge);
    }
    if(!empty($sort)) {
        printf("\t\t<ror:sortOrder>%d</ror:sortOrder>\n", $sort);
    }
    printf("\t\t<ror:resourceOf>SiteMap</ror:resourceOf>\n");
    printf("\t</item>\n");
}

$objQuery = new SC_Query();

//キャッシュしない(念のため)
header("Paragrama: no-cache");

//XMLテキスト出力
header("Content-type: application/xml; charset=utf-8");

// 必ず UTF-8 として出力
mb_http_output("UTF-8");
ob_start('mb_output_handler');

print("<?xml version='1.0' encoding='UTF-8'?>\n");
print("<rss version='2.0' xmlns:ror='http://rorweb.com/0.1/'>\n");
print("<channel>\n");
print("<title>".mb_convert_encoding($arrSiteInfo['shop_name'], "UTF-8", "EUC-JP")."</title>\n");
print("<link>".$arrSiteInfo['law_url']."/</link>\n");

// ----------------------------------------------------------------------------
// }}}
// {{{ Controller Logic

// 静的なページを処理
foreach($staticURL as $key => $url) {
    createSitemap($key, $url, '', '', '', '', 'daily', '', '', 1);
}
// ページのデータを取得
$objPageData = new LC_PageLayout;

// TOPページを処理
$topPage = getTopPage($objPageData->arrPageList);
createSitemap($topPage[0]['page_name'], $topPage[0]['url'], '', '', '', date2W3CDatetime($topPage[0]['update_date']),'daily', '', '', 1);

// 編集可能ページを処理
$editablePages = getEditablePage($objPageData->arrPageList);
foreach($editablePages as $editablePage) {
    createSitemap($editablePage['page_name'], $editablePage['url'], '', '', '', date2W3CDatetime($editablePage['update_date']), '', '', '', 2);
}

// 商品一覧ページを処理
$products = getAllProducts();
foreach($products as $product) {
    createSitemap($product['title'], $product['url'], '', '', '', '', 'daily', '', '', 2);
}

// 商品詳細ページを処理
$details = getAllDetail();
foreach($details as $detail) {
    createSitemap($detail['title'], $detail['url'], $detail['desc'], $detail['descLong'], $detail['keywords'], date2W3CDatetime($detail['update_date']), 'weekly', $detail['image'], $detail['imageLarge'], 3);
}

print("</channel>\n");
print("</rss>\n");

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
        $objDBConn = new SC_DbConn;
        $sql = "";
        $arrRet = array();

        // SQL生成(url と update_date 以外は不要？)
        $sql .= " SELECT";
        $sql .= " page_id";      // ページID
        $sql .= " ,page_name";   // 名称
        $sql .= " ,url";         // URL
        $sql .= " ,php_dir";     // php保存先ディレクトリ
        $sql .= " ,tpl_dir";     // tpl保存先ディdレクトリ
        $sql .= " ,filename";    // ファイル名称
        $sql .= " ,header_chk "; // ヘッダー使用FLG
        $sql .= " ,footer_chk "; // フッター使用FLG
        $sql .= " ,author";      // authorタグ
        $sql .= " ,description"; // descriptionタグ
        $sql .= " ,keyword";     // keywordタグ
        $sql .= " ,update_url";  // 更新URL
        $sql .= " ,create_date"; // データ作成日
        $sql .= " ,update_date"; // データ更新日
        $sql .= " FROM ";
        $sql .= " dtb_pagelayout";

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= " WHERE " . $where;
        }

        $sql .= " ORDER BY page_id";

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
function getAllProducts() {
    $conn = new SC_DBConn();
    $sql = "SELECT category_id, category_name FROM dtb_category WHERE del_flg = 0";
    $result = $conn->getAll($sql);

    $arrRet = array();
    for ($i = 0; $i < count($result); $i++) {
        // :TODO: カテゴリの最終更新日を取得できるようにする
        $page = array("title" => $result[$i]['category_name'], "url" => SITE_URL . sprintf("products/list.php?category_id=%d", $result[$i]['category_id']));
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
function getAllDetail() {
    $conn = new SC_DBConn();
    $sql = "SELECT product_id, name, comment3, main_comment, main_list_comment, ".
           "main_image, main_large_image, update_date FROM dtb_products ".
           "WHERE del_flg = 0 AND status = 1";
    $result = $conn->getAll($sql);

    $arrRet = array();
    for ($i = 0; $i < count($result); $i++) {
        (file_exists(IMAGE_SAVE_DIR . $result[$i]["main_image"])) ? $dir = IMAGE_SAVE_URL_RSS : $dir = IMAGE_TEMP_URL_RSS;
        $image = $dir . $result[$i]["main_image"];
        (file_exists(IMAGE_SAVE_DIR . $result[$i]["main_large_image"])) ? $dir = IMAGE_SAVE_URL_RSS : $dir = IMAGE_TEMP_URL_RSS;
        $imageLarge = $dir . $result[$i]["main_large_image"];

        $page = array(
            "title"       => $result[$i]['name'],
            "desc"        => $result[$i]['main_list_comment'],
            "descLong"    => $result[$i]['main_comment'],
            "keywords"    => $result[$i]['comment3'],
            "url"         => SITE_URL. sprintf("products/detail.php?product_id=%d", $result[$i]['product_id']),
            "update_date" => $result[$i]['update_date'],
            "image"       => $image,
            "imageLarge"  => $imageLarge
        );
        $arrRet[$i] = $page;
    }
    return $arrRet;
}
?>
