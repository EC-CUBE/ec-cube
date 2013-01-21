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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->

<div id="undercolumn">
    <div id="undercolumn_shopping">
        <div class="flow_area">
			<ol>
			<li class="active"><span>&gt; STEP1</span><br />Delivery destination</li>
			<li class="large"><span>&gt; STEP2</span><br />Payment method and delivery time</li>
			<li><span>&gt; STEP3</span><br />Confirmation</li>
			<li class="last"><span>&gt; STEP4</span><br />Order complete</li>
			</ol>
		</div>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <div id="address_area" class="clearfix">
            <div class="information">
                <p>Select the delivery address from the list and click the "Send to the selected address" button.</p>
                <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
                    <p>If you do not find the desired address in the list, add and register it by selecting "Add new delivery destination".</p>
                <!--{/if}-->
                <p class="mini attention">* Up to <!--{$smarty.const.DELIV_ADDR_MAX|h}--> addresses can be registered.</p>

            </div>
            <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                <div class="add_multiple">
                    <p>Will you be sending this product multiple destinations?</p>

                    <a class="bt01" href="javascript:;" onclick="fnModeSubmit('multiple', '', ''); return false">Designate multiple delivery destinations</a>
                </div>
            <!--{/if}-->
        </div>

        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p class="addbtn">
                <a class="bt01" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;">Add new delivery destination</a>
            </p>
        <!--{/if}-->

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="customer_addr" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
        <input type="hidden" name="other_deliv_id" value="" />
        <!--{if $arrErr.deli != ""}-->
            <p class="attention"><!--{$arrErr.deli}--></p>
        <!--{/if}-->
        <table summary="Designation of delivery destination">
            <col width="10%" />
            <col width="20%" />
            <col width="50%" />
            <col width="10%" />
            <col width="10%" />
            <tr>
                <th class="alignC">Selection</th>
                <th class="alignC">Address type</th>
                <th class="alignC">Delivery destination</th>
                <th class="alignC">Change</th>
                <th class="alignC">Delete</th>
            </tr>
            <!--{section name=cnt loop=$arrAddr}-->
                <tr>
                    <td class="alignC">
                        <!--{if $smarty.section.cnt.first}-->
                            <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> />
                        <!--{else}-->
                            <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> />
                        <!--{/if}-->
                    </td>
                    <td class="alignC">
                        <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
                            <!--{if $smarty.section.cnt.first}-->
                                Member registration address
                            <!--{else}-->
                                Additional registered address
                            <!--{/if}-->
                        </label>
                    </td>
                    <td>
                        <!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}--><br />
                        <!--{$arrAddr[cnt].name01|h}--> <!--{$arrAddr[cnt].name02|h}-->
                    </td>
                    <td class="alignC">
                        <!--{if !$smarty.section.cnt.first}-->
                            <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','600','640'); return false;">Change</a>
                            <!--{else}-->
                                -
                            <!--{/if}-->
                    </td>
                    <td class="alignC">
                        <!--{if !$smarty.section.cnt.first}-->
                            <a href="?" onclick="fnModeSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->'); return false">Delete</a>
                            <!--{else}-->
                                -
                            <!--{/if}-->
                    </td>
                </tr>
            <!--{/section}-->
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a class="bt04" href="<!--{$smarty.const.CART_URLPATH}-->">Go back</a>
                </li>
                <li><button class="bt02 bt_wide">Send to the selected address</button>
                </li>
            </ul>
        </div>

        </form>
    </div>
</div>
