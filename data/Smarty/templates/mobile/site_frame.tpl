<!--{*
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->

<!--{strip}-->
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
            <title><!--{$arrSiteInfo.shop_name|h}--><!--{if $tpl_subtitle|strlen >= 1}-->/<!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}-->/<!--{$tpl_title|h}--><!--{/if}--></title>
            <meta name="author" content="<!--{$arrPageLayout.author|h}-->">
            <meta name="description" content="<!--{$arrPageLayout.description|h}-->">
            <meta name="keywords" content="<!--{$arrPageLayout.keyword|h}-->">
            <!--{* ▼Head COLUMN *}-->
            <!--{if $arrPageLayout.HeadNavi|@count > 0}-->
                <!--{* ▼上ナビ *}-->
                <!--{foreach key=HeadNaviKey item=HeadNaviItem from=$arrPageLayout.HeadNavi}-->
                    <!-- ▼「<!--{$HeadNaviItem.bloc_name|h}-->」ブロック -->
                    <!--{if $HeadNaviItem.php_path != ""}-->
                        <!--{include_php file=$HeadNaviItem.php_path items=$HeadNaviItem}-->
                    <!--{else}-->
                        <!--{include file=$HeadNaviItem.tpl_path items=$HeadNaviItem}-->
                    <!--{/if}-->
                    <!-- ▲「<!--{$HeadNaviItem.bloc_name|h}-->」ブロック -->
                <!--{/foreach}-->
                <!--{* ▲上ナビ *}-->
            <!--{/if}-->
            <!--{* ▲Head COLUMN *}-->
        </head>
        <!-- ▼ ＢＯＤＹ部 スタート -->
        <!--{include file='./site_main.tpl'}-->
        <!-- ▲ ＢＯＤＹ部 エンド -->
    </html>
<!--{/strip}-->
