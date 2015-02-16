<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
*}-->
<!DOCTYPE html>
<!--[if lt IE 7]><html class="ie ie6 lt-ie9 lt-ie8 lt-ie7" lang="ja"><![endif]-->
<!--[if IE 7]><html class="ie ie7 lt-ie9 lt-ie8" lang="ja"><![endif]-->
<!--[if IE 8]><html class="ie ie8 lt-ie9" lang="ja"><![endif]-->
<!--[if IE 9]><html class="ie ie9 gte-ie9" lang="ja"><![endif]-->
<!--[if !IE]><!--><html class="mdbw" lang="ja"><!--<![endif]-->
<head>
<meta charset="<!--{$smarty.const.CHAR_CODE}-->">
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title><!--{$arrSiteInfo.shop_name}-->/<!--{$subtitle|h}--></title>

<!--{*デザインライブラリ bootstamp CDN*}-->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/csslib.css" type="text/css" media="all">
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/style.css" type="text/css" media="all">
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.colorbox/colorbox.css" type="text/css" media="all">
<link rel="shortcut icon" href="<!--{$TPL_URLPATH}-->img/common/favicon.ico">
<link rel="icon" type="image/vnd.microsoft.icon" href="<!--{$TPL_URLPATH}-->img/common/favicon.ico">

<!--[if lt IE 9]>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery-1.11.1.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery-2.1.1.min.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/eccube.js"></script>
<!-- #2342 次期メジャーバージョン(2.14)にてeccube.legacy.jsは削除予定.モジュール、プラグインの互換性を考慮して2.13では残します. -->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/eccube.legacy.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
        <!--{$tpl_onload}-->
    });
//]]></script>
</head>

<body id="page_<!--{$tpl_page_class_name|h}-->" class="<!--{$tpl_page_class_name|h}--> popup">
<noscript name="top" id="top">
    <p><em>JavaScriptを有効にしてご利用ください。</em></p>
</noscript>

<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->
