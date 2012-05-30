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

<form name="index_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <div class="btn">
        <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('index_form', 'confirm', '', '');"><span class="btn-next">変更する</span></a>
    </div>
    <table class="list">
        <col width="5%" />
        <col width="5%" />
        <col width="25%" />
        <col width="25%" />
        <col width="40%" />
        <tr>
            <th colspan="2">インデックス</th>
            <th rowspan="2">テーブル名</th>
            <th rowspan="2">カラム名</th>
            <th rowspan="2">説明</th>
        </tr>
        <tr>
            <th>ON</th>
            <th>OFF</th>
        </tr>

        <!--{section name=cnt loop=$arrForm}-->
            <tr>
                <td class="center"><input type="radio" name="indexflag_new[<!--{$smarty.section.cnt.iteration}-->]" value="1" <!--{if $arrForm[cnt].indexflag == "1"}-->checked<!--{/if}--> /></td>
                <td class="center"><input type="radio" name="indexflag_new[<!--{$smarty.section.cnt.iteration}-->]" value="" <!--{if $arrForm[cnt].indexflag != "1"}-->checked<!--{/if}--> /></td>
                <th class="column"><!--{$arrForm[cnt].table_name}--></th>
                <th class="column"><!--{$arrForm[cnt].column_name}--></th>
                <td><!--{$arrForm[cnt].recommend_comment}--></td>
            </tr>
            <input type="hidden" name="table_name[<!--{$smarty.section.cnt.iteration}-->]" value="<!--{$arrForm[cnt].table_name}-->" />
            <input type="hidden" name="column_name[<!--{$smarty.section.cnt.iteration}-->]" value="<!--{$arrForm[cnt].column_name}-->" />
            <input type="hidden" name="indexflag[<!--{$smarty.section.cnt.iteration}-->]" value="<!--{$arrForm[cnt].indexflag}-->" />
        <!--{/section}-->
    </table>

    <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('index_form', 'confirm', '', ''); return false;"><span class="btn-next">変更する</span></a>
</form>
