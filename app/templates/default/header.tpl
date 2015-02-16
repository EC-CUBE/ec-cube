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
<!--▼HEADER-->
<!--{strip}-->
<div id="header_wrap">
	<header id="header" class="clearfix">
		<div id="logo_area">
			<!--{if $arrPageLayout.description|strlen >= 1}-->
				<p id="site_description"><!--{$arrPageLayout.description|h}--></p>
			<!--{else}-->
				<p id="site_description">EC-CUBE発!世界中を旅して見つけた立方体グルメを立方隊長が直送！</p>
			<!--{/if}-->
			<h1><a href="<!--{$smarty.const.TOP_URL}-->"><img src="<!--{$TPL_URLPATH}-->img/common/logo.gif" alt="<!--{$arrSiteInfo.shop_name|h}-->/<!--{$tpl_title|h}-->" /></a></h1>
		</div>
		<div id="header_utility">
			<div id="headerInternalColumn">
			<!--{* ▼HeaderInternal COLUMN *}-->
			<!--{if $arrPageLayout.HeaderInternalNavi|@count > 0}-->
				<!--{* ▼上ナビ *}-->
				<!--{foreach key=HeaderInternalNaviKey item=HeaderInternalNaviItem from=$arrPageLayout.HeaderInternalNavi}-->
					<!-- ▼<!--{$HeaderInternalNaviItem.bloc_name}--> -->
					<!--{if $HeaderInternalNaviItem.php_path != ""}-->
						<!--{include_php file=$HeaderInternalNaviItem.php_path items=$HeaderInternalNaviItem}-->
					<!--{else}-->
						<!--{include file=$HeaderInternalNaviItem.tpl_path items=$HeaderInternalNaviItem}-->
					<!--{/if}-->
					<!-- ▲<!--{$HeaderInternalNaviItem.bloc_name}--> -->
				<!--{/foreach}-->
				<!--{* ▲上ナビ *}-->
			<!--{/if}-->
			<!--{* ▲HeaderInternal COLUMN *}-->
			</div>

			<div id="header_navi" class="cf">
				<ul>
					<li class="mypage">
						<a class="btn btn-success btn-xs" href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php">MYページ</a>
					</li>
					<li class="entry">
						<a class="btn btn-success btn-xs" href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php">会員登録</a>
					</li>
					<li class="cart">
						<a class="btn btn-warning btn-xs" href="<!--{$smarty.const.CART_URL}-->">カゴの中を見る</a>
					</li>
				</ul>
			</div>
		</div>
	</header>
</div>
<!--{/strip}-->
<!--▲HEADER-->
