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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 *}-->
<!--▼HEADER-->
<div id="headerWrap">
    <div id="header" class="clearfix">
        <div id="logoArea">
            <p id="siteDescription">EC-CUBEは日本発のオープンソースECサイト構築システムです。</p>
            <h1>
                <a href="<!--{$smarty.const.TOP_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/common/logo.jpg" alt="EC-CUBE ONLINE SHOPPING SITE" /><span><!--{$arrSiteInfo.shop_name|h}-->/<!--{$tpl_title|h}--></span></a>
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
                    <!--{include_php file=$HeaderInternalNaviItem.php_path}-->
                <!--{else}-->
                    <!--{include file=$HeaderInternalNaviItem.tpl_path}-->
                <!--{/if}-->
                <!-- ▲<!--{$HeaderInternalNaviItem.bloc_name}--> -->
                <!--{/foreach}-->
                <!--{* ▲上ナビ *}-->
            <!--{/if}-->
            <!--{* ▲HeaderInternal COLUMN*}-->
            </div>
            <div id="headerNavi">
                <ul>
                    <li class="mypage">
                    <a href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/common/btn_header_mypage_on.jpg','mypage');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/common/btn_header_mypage.jpg','mypage');"><img src="<!--{$TPL_URLPATH}-->img/common/btn_header_mypage.jpg" alt="MYページ" name="mypage" id="mypage" /></a>
                    </li>
                    <li class="entry">
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/common/btn_header_entry_on.jpg','entry');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/common/btn_header_entry.jpg','entry');"><img src="<!--{$TPL_URLPATH}-->img/common/btn_header_entry.jpg" alt="会員登録" name="entry" id="entry" /></a>
                    </li>
                    <li>
                    <a href="<!--{$smarty.const.CART_URLPATH}-->" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/common/btn_header_cart_on.jpg','cartin');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/common/btn_header_cart.jpg','cartin');"><img src="<!--{$TPL_URLPATH}-->img/common/btn_header_cart.jpg" alt="カゴの中を見る" name="cartin" id="cartin" /></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--▲HEADER-->
