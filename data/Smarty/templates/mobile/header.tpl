<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<!--{strip}-->
    <!--▼HEADER-->
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

    <!--{* ▼タイトル *}-->
    <!--{if $tpl_title != "" || $tpl_subtitle != ""}-->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td bgcolor="#FFA85C" align="center"><font color="#ffffff"><!--{if $tpl_subtitle != ""}--><!--{$tpl_subtitle|h}--><!--{else}--><!--{$tpl_title|h}--><!--{/if}--></font></td>
            </tr>
        </table>
    <!--{/if}-->
    <!--{* ▲タイトル *}-->
    <!--▲HEADER-->
<!--{/strip}-->
