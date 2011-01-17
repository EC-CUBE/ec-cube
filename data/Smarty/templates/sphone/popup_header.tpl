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

<meta name="viewport" content="width=320,maximum-scale=1.0,user-scalable=no">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<!--{* 共通CSS *}-->
<link rel="stylesheet" media="only screen" href="<!--{$TPL_DIR}-->css/import.css" type="text/css" />

<script type="text/javascript" src="<!--{$smarty.const.URL_PATH}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_PATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_PATH}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_PATH}-->js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/barbutton.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/category.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/news.js"></script>

<title><!--{$arrSiteInfo.shop_name|h}--><!--{if $tpl_subtitle|strlen >= 1}--> / <!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}--> / <!--{$tpl_title|h}--><!--{/if}--></title>
<!--{* iPhone用アイコン画像 *}-->
<link rel="apple-touch-icon" href="<!--{$smarty.const.SMARTPHONE_URLPATH}-->sphone/apple-touch-icon.png" />

<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
        <!--{$tpl_onload}-->
    });
//]]>
</script>
</head>

<body>
<noscript>
  <p><em>JavaScriptを有効にしてご利用下さい.</em></p>
</noscript>

<a name="top" id="top"></a>

<!--▼CONTENTS-->
<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->
