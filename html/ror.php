<?php
/**
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/**
 * ROR Sitemap�ץ�ȥ��� �ե����������⥸�塼��
 * PHP4(PHP5�Ǥ�ư���Ȼפ��ޤ�)
 *
 * (1)�⥸�塼�복��
 *    ���Υ⥸�塼��� ROR Sitemap�ץ�ȥ�����б����� XML�ե��������Ϥ��롣
 *    ROR�Ȥ�Resources of a Resource��ά�ǡ����ƤΥ��������󥸥���Ф��ƻȤ��륵���ȥޥåס�
 *    see) http://www.rorweb.com/
 *
 *
 * (2)���Υ⥸�塼��ˤǤ��뤳��
 *    ���Υ⥸�塼��ˤ�ꡢ�ʲ��Υڡ����Υ����ȥޥåפ���������롣
 *    1. $staticURL �ǻ��ꤷ���ڡ���
 *    2. �������̤Υǥ����������������������Ū�ڡ���
 *    3. ��������Ƥ��뤹�٤Ƥξ��ʰ����ڡ���
 *    4. ��������Ƥ��뤹�٤Ƥξ��ʾܺ٥ڡ���
 * 
 * (3)������ˡ
 *    EC-CUBE ���󥹥ȡ���ǥ��쥯�ȥ�� html�ǥ��쥯�ȥ�ľ�������֤��뤳�Ȥˤ��ư��롣
 *    ���ָ塢meta�����ǰʲ��Τ褦����Ͽ����ȡ��Ƹ������󥸥�Υ����顼��
 *    ��ưŪ�˽�����褦�ˤʤ롣
 *    ex) <link rel="alternate" type="application/rss+xml" title="ROR" href="http://your-site.com/ror.php" />
 *
 *    �ޤ��������ȥޥåפ���Ͽ���뤳�Ȥˤ��, �������󥸥�Υ���ǥå�������¥�ʤ���롣
 *    see) https://www.google.com/webmasters/tools/siteoverview?hl=ja
 *    see) https://siteexplorer.search.yahoo.com/mysites
 *
 * @author Kentaro Ohkouchi
 * @modified Issei Homan
 */

require_once("require.php");
$objSiteInfo = new SC_SiteInfo();

//Ź�޾���򥻥å�
$arrSiteInfo = $objSiteInfo->data;

// ưŪ����������ʤ��ڡ���������ǻ���
$staticURL = array("RSS Feeds" => SITE_URL."rss/index.php");

// ----------------------------------------------------------------------------
// }}}
// {{{ View Logic

/**
 * <item /> ����������.
 *
 * @param string $title      �ڡ��������ȥ�
 * @param string $link       �ڡ����� URL ��ɬ��
 * @param string $desc       ���ס�û�˾��ʰ���������
 * @param string $descLong   ���ס�Ĺ�˾��ʾܺ٥ᥤ�󥳥���
 * @param string $keywords   ����޶��ڤꥭ����� comment3
 * @param string $updated    �ե�����κǽ������� YYYY-MM-DD or W3C Datetime ����
 * @param string $period     �ڡ����ι�������
 * @param string $image      �ᥤ����� main_image
 * @param string $imageLarge ����ᥤ����� main_large_image
 * @param int    $sort       ��������
 * @return Sitemap ������ <item />
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

//����å��夷�ʤ�(ǰ�Τ���)
header("Paragrama: no-cache");

//XML�ƥ����Ƚ���
header("Content-type: application/xml; charset=utf-8");

// ɬ�� UTF-8 �Ȥ��ƽ���
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

// ��Ū�ʥڡ��������
foreach($staticURL as $key => $url) {
    createSitemap($key, $url, '', '', '', '', 'daily', '', '', 1);
}
// �ڡ����Υǡ��������
$objPageData = new LC_PageLayout;

// TOP�ڡ��������
$topPage = getTopPage($objPageData->arrPageList);
createSitemap($topPage[0]['page_name'], $topPage[0]['url'], '', '', '', date2W3CDatetime($topPage[0]['update_date']),'daily', '', '', 1);

// �Խ���ǽ�ڡ��������
$editablePages = getEditablePage($objPageData->arrPageList);
foreach($editablePages as $editablePage) {
    createSitemap($editablePage['page_name'], $editablePage['url'], '', '', '', date2W3CDatetime($editablePage['update_date']), '', '', '', 2);
}

// ���ʰ����ڡ��������
$products = getAllProducts();
foreach($products as $product) {
    createSitemap($product['title'], $product['url'], '', '', '', '', 'daily', '', '', 2);
}

// ���ʾܺ٥ڡ��������
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
 * TOP�ڡ����ξ�������
 *
 * @param array $pageData ���٤ƤΥڡ������������
 * @return TOP�ڡ����ξ���
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
 * ���٤Ƥ��Խ���ǽ�ڡ����ξ�����������.
 *
 * @param array $pageData ���٤ƤΥڡ������������
 * @return �Խ���ǽ�ڡ���
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
 * date������ʸ����� W3C Datetime �������Ѵ����ƽ��Ϥ���.
 *
 * @param date $date �Ѵ���������
 */
function date2W3CDatetime($date) {
    $arr = array();
    // ����ɽ����ʸ��������
    ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",
        $date, $arr);
    // :TODO: time zone ���������٤�...
    return sprintf("%04d-%02d-%02dT%02d:%02d:%02d+09:00",
            $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $arr[6]);
}

// ----------------------------------------------------------------------------
// }}}
// {{{ DB Access Objects

/**
 * �ڡ����ǡ����򰷤����饹.
 */
class LC_PageLayout {

    var $arrPageData;		// �ڡ����ǡ�����Ǽ��
    var $arrPageList;		// �ڡ����ǡ�����Ǽ��

    /**
     * ���󥹥ȥ饯��.
     */
    function LC_PageLayout() {
        $this->arrPageList = $this->getPageData();
    }

    /**
     * �֥�å�������������.
     *
     * @param string $where WHERE��
     * @param array  $arrVal WHERE����ͤ��Ǽ��������
     * @return �֥�å�����
     */
    function getPageData($where = '', $arrVal = ''){
        $objDBConn = new SC_DbConn;
        $sql = "";
        $arrRet = array();

        // SQL����(url �� update_date �ʳ������ס�)
        $sql .= " SELECT";
        $sql .= " page_id";      // �ڡ���ID
        $sql .= " ,page_name";   // ̾��
        $sql .= " ,url";         // URL
        $sql .= " ,php_dir";     // php��¸��ǥ��쥯�ȥ�
        $sql .= " ,tpl_dir";     // tpl��¸��ǥ�d�쥯�ȥ�
        $sql .= " ,filename";    // �ե�����̾��
        $sql .= " ,header_chk "; // �إå�������FLG
        $sql .= " ,footer_chk "; // �եå�������FLG
        $sql .= " ,author";      // author����
        $sql .= " ,description"; // description����
        $sql .= " ,keyword";     // keyword����
        $sql .= " ,update_url";  // ����URL
        $sql .= " ,create_date"; // �ǡ���������
        $sql .= " ,update_date"; // �ǡ���������
        $sql .= " FROM ";
        $sql .= " dtb_pagelayout";

        // where��λ��꤬������ɲ�
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
 * ���٤Ƥξ��ʰ����ڡ������������.
 *
 * @param boolean $isMobile ��Х���ڡ�������������� true
 * @return �������󥸥󤫤饢��������ǽ�ʾ��ʰ����ڡ����ξ���
 */
function getAllProducts() {
    $conn = new SC_DBConn();
    $sql = "SELECT category_id, category_name FROM dtb_category WHERE del_flg = 0";
    $result = $conn->getAll($sql);

    $arrRet = array();
    for ($i = 0; $i < count($result); $i++) {
        // :TODO: ���ƥ���κǽ�������������Ǥ���褦�ˤ���
        $page = array("title" => $result[$i]['category_name'], "url" => SITE_URL . sprintf("products/list.php?category_id=%d", $result[$i]['category_id']));
        $arrRet[$i] = $page;
    }
    return $arrRet;
}

/**
 * ���٤Ƥξ��ʾܺ٥ڡ������������.
 *
 * @param boolean $isMobile ��Х���ڡ�������������� true
 * @return �������󥸥󤫤饢��������ǽ�ʾ��ʾܺ٥ڡ����ξ���
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
