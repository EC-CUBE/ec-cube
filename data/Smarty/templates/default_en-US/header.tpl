<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 *}-->

<!--▼HEADER-->
<div id="header_wrap">
    <div id="header" class="clearfix">
        <div id="logo_area">
            <h1>
                <a href="<!--{$smarty.const.TOP_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/common/logo.png" alt="<!--{$arrSiteInfo.shop_name|h}-->" /><span><!--{$arrSiteInfo.shop_name|h}-->/<!--{$tpl_title|h}--></span></a>
            </h1>
        </div>
        <div id="header_utility">
            <div id="headerInternalColumn">
            <!--{* ▼HeaderInternal COLUMN*}-->
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
            <!--{* ▲HeaderInternal COLUMN*}-->
            </div>
            <div id="header_navi">
                <ul>
                    <li class="mypage">
                    <a href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php">MY page</a>
                    </li>
                    <li class="entry">
                        <a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php">Member registration</a>
                    </li>
                    <li class="view_basket">
                        <a href="<!--{$smarty.const.CART_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/button/icon_cart.png" alt="" />View cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--▲HEADER-->
