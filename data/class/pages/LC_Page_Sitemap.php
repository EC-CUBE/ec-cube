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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

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
 * @version $Id:sitemap.php 15532 2007-08-31 14:39:46Z nanasess
 *
 * :TODO: 各ページの changefreq や priority を指定できるようにする
 * :TODO: filemtime 関数を使えば、静的なページの更新時間も取得できそう
 */
class LC_Page_Sitemap extends LC_Page {

    // }}}
    // {{{ properties

    /** 動的に生成しないページの配列 */
    var $staticURL;

    /** ページリスト */
    var $arrPageList;


    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        
        $this->staticURL = array();
        
        $this->staticURL[] = HTTP_URL . 'rss/' . DIR_INDEX_URL;
        if (USE_MOBILE !== false) {
            $this->staticURL[] = MOBILE_HTTP_URL;
        }
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        // ページのデータを取得
        $this->arrPageList = $this->getPageData();

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

        // TOPページを処理
        $topPage = $this->getTopPage($this->arrPageList);
        $this->createSitemap($topPage[0]['url'],
                             $this->date2W3CDatetime($topPage[0]['update_date']),
                             'daily', 1.0);

        // 静的なページを処理
        foreach ($this->staticURL as $url) {
            $this->createSitemap($url, '', 'daily', 1.0);
        }

        // 編集可能ページを処理
        $editablePages = $this->getEditablePage($this->arrPageList);
        foreach ($editablePages as $editablePage) {
            $this->createSitemap($editablePage['url'],
                                 $this->date2W3CDatetime($editablePage['update_date']));
        }

        // 商品一覧ページを処理
        $products = $this->getAllProducts();
        foreach ($products as $product) {
            $this->createSitemap($product['url'], '', 'daily');
        }

        // 商品詳細ページを処理
        $details = $this->getAllDetail();
        foreach ($details as $detail) {
            $this->createSitemap($detail['url'],
                                 $this->date2W3CDatetime($detail['update_date']));
        }

        print("</urlset>\n");
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
     * Sitemap の <url /> を生成する.
     *
     * @param string $loc ページの URL ※必須
     * @param string $lastmod ファイルの最終更新日 YYYY-MM-DD or W3C Datetime 形式
     * @param string $changefreq ページの更新頻度
     * @param double $priority URL の優先度
     * @return Sitemap 形式の <url />
     * @see https://www.google.com/webmasters/tools/docs/ja/protocol.html#xmlTagDefinitions
     * TODO Smarty に移行すべき?
     */
    function createSitemap($loc, $lastmod = "", $changefreq = "",
                           $priority = "") {
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

    /**
     * TOPページの情報を取得する.
     *
     * @param array $pageData すべてのページ情報の配列
     * @return array TOPページの情報
     */
    function getTopPage($pageData) {
        $arrRet = array();
        foreach ($pageData as $page) {
            if ($page['page_id'] == "1") {
                $arrRet[0] = $page;
                return $arrRet;
            }
        }
    }

    /**
     * すべての編集可能ページの情報を取得する.
     *
     * @param array $pageData すべてのページ情報の配列
     * @return array 編集可能ページ
     */
    function getEditablePage($pageData) {
        $arrRet = array();
        foreach ($pageData as $page) {
            if ($page['page_id'] > 4) {
                $arrRet[] = $page;
            }
        }
        return $arrRet;
    }

    /**
     * すべての商品一覧ページを取得する.
     *
     * @return array 検索エンジンからアクセス可能な商品一覧ページの情報
     */
    function getAllProducts() {
        
        // XXX: 商品登録の無いカテゴリーは除外する方が良い気もする
        $objQuery = new SC_Query();
        $sql = "SELECT category_id FROM dtb_category WHERE del_flg = 0";
        $result = $objQuery->getAll($sql);

        $arrRet = array();
        foreach ($result as $row) {
            // :TODO: カテゴリの最終更新日を取得できるようにする
            
            $page["url"] = HTTP_URL . 'products/list.php?category_id=' . $row['category_id'];
            $arrRet[] = $page;
            
            // モバイルサイト
            if (USE_MOBILE !== false) {
                $page["url"] = MOBILE_HTTP_URL . 'products/list.php?category_id=' . $row['category_id'];
                $arrRet[] = $page;
            }
        }
        return $arrRet;
    }

    /**
     * すべての商品詳細ページを取得する.
     *
     * @return array 検索エンジンからアクセス可能な商品詳細ページの情報
     */
    function getAllDetail() {
        $objQuery = new SC_Query();
        $sql = "SELECT product_id, update_date FROM dtb_products WHERE del_flg = 0 AND status = 1";
        $result = $objQuery->getAll($sql);

        $arrRet = array();
        foreach ($result as $row) {
            
            $page["update_date"] = $row['update_date'];
            
            $page["url"] = HTTP_URL . substr(DETAIL_P_HTML, strlen(URL_PATH)) . $row['product_id'];
            $arrRet[] = $page;
            
            // モバイルサイト
            if (USE_MOBILE !== false) {
                $page["url"] = HTTP_URL . substr(MOBILE_DETAIL_P_HTML, strlen(URL_PATH)) . $row['product_id'];
                $arrRet[] = $page;
            }
        }
        return $arrRet;
    }


    /**
     * ブロック情報を取得する.
     *
     * @param string $where WHERE句
     * @param array  $arrVal WHERE句の値を格納した配列
     * @return ブロック情報
     */
    function getPageData($where = '', $arrVal = ''){
        $objQuery = new SC_Query();     // DB操作オブジェクト
        $sql = "";                      // データ取得SQL生成用
        $arrRet = array();              // データ取得用

        // SQL生成(url と update_date 以外は不要？)
        $sql .= " SELECT";
        $sql .= " page_id";             // ページID
        $sql .= " ,page_name";          // 名称
        $sql .= " ,url";                // URL
        $sql .= " ,php_dir";            // php保存先ディレクトリ
        $sql .= " ,tpl_dir";            // tpl保存先ディdレクトリ
        $sql .= " ,filename";           // ファイル名称
        $sql .= " ,header_chk ";        // ヘッダー使用FLG
        $sql .= " ,footer_chk ";        // フッター使用FLG
        $sql .= " ,author";             // authorタグ
        $sql .= " ,description";        // descriptionタグ
        $sql .= " ,keyword";            // keywordタグ
        $sql .= " ,update_url";         // 更新URL
        $sql .= " ,create_date";        // データ作成日
        $sql .= " ,update_date";        // データ更新日
        $sql .= " FROM ";
        $sql .= "     dtb_pagelayout";

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= " WHERE " . $where;
        }

        $sql .= " ORDER BY page_id";

        $pageData = $objQuery->getAll($sql, $arrVal);
        
        // URL にプロトコルの記載が無い場合、HTTP_URL を前置する。
        foreach (array_keys($pageData) as $key) {
            $page =& $pageData[$key];
            if (!preg_match('|^https?://|i', $page['url'])) {
                $page['url'] = HTTP_URL . $page['url'];
            }
            $page['url'] = preg_replace('|/' . preg_quote(DIR_INDEX_FILE) . '$|', '/' . DIR_INDEX_URL, $page['url']);
        }
        unset($page);
        
        return $pageData;
    }

    /**
     * date形式の文字列を W3C Datetime 形式に変換して出力する.
     *
     * @param date $date 変換する日付
     * @return void
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

}

?>
