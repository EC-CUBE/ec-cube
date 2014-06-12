<!--{*
/*
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
 */
*}-->

<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="register" />

    <div id="ownersstore" class="contents-main">

        <table class="list center">
            <tr>
                <th>モジュール名</th>
                <th>ステータス</th>
                <th>日時</th>
                <th>詳細</th>
                <!--{*<th>復元</th>*}-->
            </tr>
            <!--{foreach from=$arrInstallLogs item=log name=log_loop}-->
                <tr>
                    <td class="left"><!--{$log.module_name|h}--></td>
                    <td><!--{if $log.error_flg}-->失敗<!--{else}-->成功<!--{/if}--></td>
                    <td class="left"><!--{$log.update_date|sfDispDBDate|h}--></td>
                    <td>
                            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/log.php?mode=detail&amp;log_id=<!--{$log.log_id}-->">
                            詳細</a>
                    </td>
                    <!--{*<td><!--{$log.log_id}--></td>*}-->
                </tr>
            <!--{/foreach}-->
        </table>

    </div>
</form>
