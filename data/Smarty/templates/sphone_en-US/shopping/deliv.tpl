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

<!--▼コンテンツここから -->
<section id="undercolumn">

    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!--★インフォメーション★-->
    <div class="information">
        <p>Select the delivery address from the list</p>
    </div>

    <!--▼フォームここから -->
    <div class="form_area">
        <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->shopping/deliv.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="customer_addr" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="other_deliv_id" value="" />
            <!--{if $arrErr.deli != ""}-->
                <p class="attention"><!--{$arrErr.deli}--></p>
            <!--{/if}-->

            <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                <!--☆右にスライドボタン -->
                <div class="bubbleBox">
                    <div class="bubble_announce clearfix">
                        <p><a rel="external" href="javascript:fnModeSubmit('multiple', '', '');">Do you want to send to multiple delivery destinations?</a></p>
                    </div>
                    <div class="bubble_arrow_line"><!--矢印空タグ --></div>
                    <div class="bubble_arrow"><!--矢印空タグ --></div>
                </div>
            <!--{/if}-->

            <div class="formBox">
                <!--{section name=cnt loop=$arrAddr}-->
                    <dl class="deliv_check">
                        <!--{if $smarty.section.cnt.first}-->
                            <dt class="first">
                                <p>
                                    <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> class="data-role-none" />
                                    <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">Member registration address</label>
                                </p>
                            </dt>
                        <!--{else}-->
                            <dt>
                                <p>
                                    <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> class="data-role-none" />
                                    <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">Additional registered address</label>
                                </p>
                                <ul class="edit">
                                    <li><a rel="external" href="javascript:void(0);" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','600','640'); return false;" class="b_edit">Edit</a></li>
                                    <li><img src="<!--{$TPL_URLPATH}-->img/button/btn_delete.png" width="21" height="20" alt="Delete" onclick="fnModeSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->');" /></li>
                                </ul>
                            </dt>
                        <!--{/if}-->
                        <dd <!--{if $smarty.section.cnt.last && !($tpl_addrmax < $smarty.const.DELIV_ADDR_MAX)}-->class="end"<!--{/if}-->>
                            <!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}--><br />
                            <!--{$arrAddr[cnt].name01|h}--> <!--{$arrAddr[cnt].name02|h}-->
                        </dd>
                    </dl>
                <!--{/section}-->

                <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
                    <div class="inner">
                        <a rel="external" href="javascript:void(0);" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;" class="btn_sub addbtn">Add new delivery destination</a>
                    </div>
                <!--{/if}-->
            </div><!-- /.formBox -->

            <ul class="btn_btm">
                <li><a rel="external" href="javascript:fnModeSubmit('customer_addr','','');" class="btn bt_wide">Send to the selected address</a></li>
                <li><a rel="external" href="<!--{$smarty.const.CART_URLPATH}-->" class="btn_back">Go back</a></li>
            </ul>

        </form>
    </div><!-- /.form_area -->
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="Enter keywords" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
<!--▲コンテンツここまで -->
