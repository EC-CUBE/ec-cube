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

<div id="topbox">
<!--{* ▼ロゴ ここから *}-->
<div id="header">
<h1>
    <a href="<!--{$smarty.const.SPHONE_SSL_URL}-->">
      <em><!--{$arrSiteInfo.shop_name|escape}-->/<!--{$tpl_title|escape}--></em></a>
  </h1>
</div>
<!--{* ▲ロゴ ここまで *}-->

<!--{* ヘッダメニュー *}-->
<div id="headmenu">

<!--{* 検索 *}-->
<div id="searchbar">
<form method="get" action="<!--{$smarty.const.SMARTPHONE_SITE_URL}-->products/list.php">
<input type="text" name="name" maxlength="50" value="<!--{$smarty.get.name|escape}-->" placeholder="商品検索" ><input type="submit" name="search" value="検索">
</form>
<!--{* searchmenu *}--></div>
<!--{* headmenu *}--></div>
<!--{* topbox *}--></div>

<!--{* 新着情報 *}-->
<!--{include_php file=`$smarty.const.SMARTPHONE_HTML_PATH`/frontparts/bloc/news.php}-->

<!--{* ログイン *}-->
<!--{include_php file=`$smarty.const.SMARTPHONE_HTML_PATH`/frontparts/bloc/login.php}-->

<!--{* おすすめ商品 *}-->
<!--{include_php file=`$smarty.const.SMARTPHONE_HTML_PATH`/frontparts/bloc/best5.php}-->

<!-- ▼メニュー ここから -->
<!--{include_php file=`$smarty.const.SMARTPHONE_HTML_PATH`/frontparts/bloc/category.php}-->