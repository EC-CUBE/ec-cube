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

<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <p class="inforamtion">You can add multiple addresses here.<br />
        * Up to <span class="attention"><!--{$smarty.const.DELIV_ADDR_MAX|h}--> items</span>  can be registered.</p>

        <!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
            <!--{* 退会時非表示 *}-->
            <!--{if $tpl_login}-->
                <p class="add_address">
                    <a class="bt01" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','600','640'); return false;" target="_blank">Add new delivery destination</a>
                </p>
            <!--{/if}-->
        <!--{/if}-->

        <!--{if $tpl_linemax > 0}-->
        <form name="form1" method="post" action="?" >
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="" />
            <input type="hidden" name="other_deliv_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

            <table summary="Delivery destination">
            <col width="5%" />
            <col width="25%" />
            <col width="50%" />
            <col width="10%" />
            <col width="10%" />
                <tr>
                    <th colspan="5">Delivery destination</th>
                </tr>
                <!--{section name=cnt loop=$arrOtherDeliv}-->
                    <tr>
                        <td class="alignC"><!--{$smarty.section.cnt.iteration}--></td>
                        <td><label for="add<!--{$smarty.section.cnt.iteration}-->">Delivery address</label></td>
                        <td>
                            <!--{* <!--{$arrOtherDeliv[cnt].zip01}-->-<!--{$arrOtherDeliv[cnt].zip02}--><br /> *}-->
                            <!--{$arrOtherDeliv[cnt].zipcode}--><br />
                            <!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--><br />
                            <!--{$arrOtherDeliv[cnt].name01|h}-->&nbsp;<!--{$arrOtherDeliv[cnt].name02|h}-->
                        </td>
                        <td class="alignC">
                            <a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','600','640'); return false;">Change</a>
                        </td>
                        <td class="alignC">
                            <a href="#" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->'); return false;">Delete</a>
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
        </form>
        <!--{else}-->
        <p class="delivempty"><strong>There are no new delivery destinations.</strong></p>
        <!--{/if}-->
    </div>
</div>
