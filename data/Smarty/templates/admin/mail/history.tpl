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
<input type="hidden" name="search_pageno" value="" />
<input type="hidden" name="mode" value="" />
<!--{if count($arrDataList) > 0}-->
    <!--{include file=$tpl_pager}-->
    <div id="mail" class="contents-main">
        <table class="list center">
            <tr>
                <th><!--{t string="tpl_Delivery start time_01"}--></th>
                <th><!--{t string="tpl_Delivery completion time_01"}--></th>
                <th><!--{t string="tpl_Subject_01"}--></th>
                <th><!--{t string="tpl_Preview_01"}--></th>
                <th><!--{t string="tpl_Delivery conditions_01"}--></th>
                <th><!--{t string="tpl_Total number delivered_01"}--></th>
                <th><!--{t string="tpl_Number already delivered_01"}--></th>
                <th><!--{t string="tpl_Number of failed deliveries_01"}--></th>
                <th><!--{t string="tpl_Number not delivered_01"}--></th>
                <th><!--{t string="tpl_Retry_01"}--></th>
                <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
            </tr>
            <!--{section name=cnt loop=$arrDataList}-->
            <tr>
                <td><!--{$arrDataList[cnt].start_date|sfDispDBDate|h}--></td>
                <td><!--{$arrDataList[cnt].end_date|sfDispDBDate|h}--></td>
                <td class="left"><!--{$arrDataList[cnt].subject|h}--></td>
                <td><a href="javascript:;" onclick="win03('./preview.php?mode=history&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->', 'confirm', '720', '600'); return false;"><!--{t string="tpl_Confirm_02"}--></a></td>
                <td><a href="javascript:;" onclick="win03('./<!--{$smarty.const.DIR_INDEX_PATH}-->?mode=query&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->','query','615','800'); return false;"><!--{t string="tpl_Confirm_02"}--></a></td>
                <td><!--{$arrDataList[cnt].count_all|h}--></td>
                <td><!--{$arrDataList[cnt].count_sent|h}--></td>
                <td style="<!--{if $arrDataList[cnt].count_error >= 1}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                    <!--{$arrDataList[cnt].count_error|h}-->
                </td>
                <td style="<!--{if $arrDataList[cnt].count_unsent >= 1}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                    <!--{$arrDataList[cnt].count_unsent|h}-->
                </td>
                <td>
                    <!--{if $arrDataList[cnt].count_error >= 1 || $arrDataList[cnt].count_unsent >= 1}-->
                        <a href="index.php?mode=retry&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->" onclick="return window.confirm('<!--{t string="tpl_Try to send to failed address again?_01"}-->');"><!--{t string="tpl_Execute_01"}--></a>
                    <!--{/if}-->
                </td>
                <td><a href="?mode=delete&send_id=<!--{$arrDataList[cnt].send_id|h}-->" onclick="return window.confirm('<!--{t string="tpl_Do you want to delete the delivery history?_01"}-->');"><!--{t string="tpl_Remove_01"}--></a></td>
            </tr>
            <!--{/section}-->
        </table>
    </div>
<!--{else}-->
    <div id="complete">
        <div class="complete-top"></div>
        <div class="contents">
            <div class="message">
                <!--{t string="tpl_There is no delivery history_01"}-->
            </div>
        </div>
        <div class="btn-area-top"></div>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="./<!--{$smarty.const.DIR_INDEX_PATH}-->"><span class="btn-prev"><!--{t string="tpl_Return_02"}--></span></a></li>
            </ul>
        </div>
        <div class="btn-area-bottom"></div>
    </div>
<!--{/if}-->
</form>
