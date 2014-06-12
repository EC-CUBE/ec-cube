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

<div id="undercolumn">
    <div id="undercolumn_customer">
        <p class="flow_area"><img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_01.jpg" alt="購入手続きの流れ" /></p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <div class="information">
            <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
                <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                    入力後、一番下の「上記のお届け先のみに送る」<br/>
                    または「複数のお届け先に送る」ボタンをクリックしてください。
                <!--{else}-->
                    入力後、一番下の「次へ」ボタンをクリックしてください。
                <!--{/if}-->
            </p>
        </div>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="nonmember_confirm" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <table summary=" ">
            <!--{include file="`$smarty.const.TEMPLATE_REALDIR`frontparts/form_personal_input.tpl" flgFields=2 emailMobile=false prefix="order_"}-->
                <tr>
                    <th colspan="2">
                    <!--{assign var=key value="deliv_check"}-->
                    <input type="checkbox" name="<!--{$key}-->" value="1" onclick="eccube.toggleDeliveryForm();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />
                    <label for="deliv_label"><span class="attention">お届け先を指定</span>　※上記に入力された住所と同一の場合は省略可能です。</label>
                    </th>
                </tr>
            <!--{include file="`$smarty.const.TEMPLATE_REALDIR`frontparts/form_personal_input.tpl" flgFields=1 emailMobile=false prefix="shipping_"}-->
            </table>

            <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                <p class="alignC">この商品を複数のお届け先に送りますか？</p>
            <!--{/if}-->
            <div class="btn_area">
                <ul>
                    <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                        <li>
                            <input type="image" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_singular.jpg" alt="上記のお届け先のみに送る" name="singular" id="singular" />
                        </li>
                        <li>
                            <a href="javascript:;" onclick="eccube.setModeAndSubmit('multiple', '', ''); return false">
                                <img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_multiple.jpg" alt="複数のお届け先に送る" />
                            </a>
                        </li>
                    <!--{else}-->
                        <li>
                            <input type="image" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="singular" id="singular" />
                        </li>
                    <!--{/if}-->
                </ul>
            </div>
        </form>
    </div>
</div>
