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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="deliv_id" value="" />
<div id="basis" class="contents-main">
    <div class="btn">
        <ul>
            <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./delivery_input.php'); fnModeSubmit('pre_edit','',''); return false;">
                <span class="btn-next">配送方法<!--{if $smarty.const.INPUT_DELIV_FEE}-->・配送料<!--{/if}-->を新規入力</span></a></li>
        </ul>
    </div>
    <table class="list">
        <col width="35%" />
        <col width="30%" />
        <col width="10%" />
        <col width="10%" />
        <col width="15%" />
        <tr>
            <th>配送業者</th>
            <th>名称</th>
            <th>編集</th>
            <th>削除</th>
            <th>移動</th>
        </tr>
        <!--{section name=cnt loop=$arrDelivList}-->
            <tr>
                <td><!--{$arrDelivList[cnt].name|h}--></td>
                <td><!--{$arrDelivList[cnt].service_name|h}--></td>
                <td align="center"><a href="?" onclick="fnChangeAction('./delivery_input.php'); fnModeSubmit('pre_edit', 'deliv_id', <!--{$arrDelivList[cnt].deliv_id}-->); return false;">
                    編集</a></td>
                <td align="center"><a href="?" onclick="fnModeSubmit('delete', 'deliv_id', <!--{$arrDelivList[cnt].deliv_id}-->); return false;">
                    削除</a></td>
                <td align="center">
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="fnModeSubmit('up','deliv_id', '<!--{$arrDelivList[cnt].deliv_id}-->'); return false;">上へ</a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="fnModeSubmit('down','deliv_id', '<!--{$arrDelivList[cnt].deliv_id}-->'); return false;">下へ</a>
                <!--{/if}-->
                </td>
            </tr>
        <!--{/section}-->
    </table>
</div>
</form>
