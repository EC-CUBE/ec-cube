<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--{*
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
 *}-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="<!--{$TPL_DIR}-->css/import.css" type="text/css" media="all" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="<!--{$smarty.const.SITE_URL}-->rss/<!--{$smarty.const.DIR_INDEX_URL}-->" />
<script type="text/javascript" src="http://www.google.com/jsapi?key=xxx"></script>
<script type="text/javascript">//<![CDATA[
    google.load("maps", "3", {other_params:'sensor=false'});
//]]>
</script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/jquery-1.4.2.min.js"></script>
<title><!--{$arrSiteInfo.shop_name|escape}--><!--{if $tpl_subtitle|strlen >= 1}--> / <!--{$tpl_subtitle|escape}--><!--{elseif $tpl_title|strlen >= 1}--> / <!--{$tpl_title|escape}--><!--{/if}--></title>
<!--{if $arrPageLayout.author|strlen >= 1}-->
    <meta name="author" content="<!--{$arrPageLayout.author|escape}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.description|strlen >= 1}-->
    <meta name="description" content="<!--{$arrPageLayout.description|escape}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.keyword|strlen >= 1}-->
    <meta name="keywords" content="<!--{$arrPageLayout.keyword|escape}-->" />
<!--{/if}-->

<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
//]]>
</script>
<!--{* ▼Head COLUMN*}-->
  <!--{if $arrPageLayout.HeadNavi|@count > 0}-->
    <!--{* ▼上ナビ *}-->
      <!--{foreach key=HeadNaviKey item=HeadNaviItem from=$arrPageLayout.HeadNavi}-->
        <!--{* ▼<!--{$HeadNaviItem.bloc_name}--> ここから*}-->
          <!--{if $HeadNaviItem.php_path != ""}-->
            <!--{include_php file=$HeadNaviItem.php_path}-->
          <!--{else}-->
            <!--{include file=$HeadNaviItem.tpl_path}-->
          <!--{/if}-->
        <!--{* ▲<!--{$HeadNaviItem.bloc_name}--> ここまで*}-->
      <!--{/foreach}-->
    <!--{* ▲上ナビ *}-->
  <!--{/if}-->
<!--{* ▲Head COLUMN*}-->
</head>

<!-- ▼BODY部 スタート -->
<!--{include file='./site_main.tpl'}-->
<!-- ▲BODY部 エンド -->

</html>
