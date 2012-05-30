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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<table class="form">
    <tr>
        <th>SQL文</th>
        <td>SELECT <!--{$arrForm.csv_sql|h|nl2br}--></td>
    </tr>
    <tr>
        <th>エラー内容</th>
        <td>
            <!--{if $arrErr}-->
                <!--{foreach key=key item=item from=$arrErr key=key}-->
                    <!--{$item}-->
                <!--{/foreach}-->
            <!--{/if}-->
            <!--{if $sqlerr != ""}-->
                <!--{$sqlerr|h|nl2br}-->
            <!--{elseif !$arrErr}-->
                エラーはありません
            <!--{/if}-->
        </td>
    </tr>
</table>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
