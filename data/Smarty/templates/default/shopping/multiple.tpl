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

<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
    $(document).ready(function() {
        $("a.expansion").facebox({
            loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.jpg',
            closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
        });
    });
//]]></script>
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area">
            <img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_01.jpg" alt="購入手続きの流れ" />
        </p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <p class="information">各商品のお届け先を選択してください。<br />（※数量の合計は、カゴの中の数量と合わせてください。）</p>
        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p>一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。</p>
        <!--{/if}-->
        <p class="mini attention">※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>

        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p class="addbtn">
                <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','addition');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','addition');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
            </p>
        <!--{/if}-->
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <table summary="商品情報">
                <col width="10%" />
                <col width="35%" />
                <col width="10%" />
                <col width="45%" />
                <tr>
                    <th class="alignC">商品写真</th>
                    <th class="alignC">商品名</th>
                    <th class="alignC">数量</th>
                    <th class="alignC">お届け先</th>
                </tr>
                <!--{section name=line loop=$arrForm.line_of_num.value}-->
                    <!--{assign var=index value=$smarty.section.line.index}-->
                    <tr>
                        <td class="alignC">
                            <a
                                <!--{if $arrForm.main_image[$index]|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrForm.main_image.value[$index]|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                                <!--{/if}-->
                            >
                                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrForm.main_list_image.value[$index]|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrForm.name.value[$index]|h}-->" /></a>
                        </td>
                        <td><!--{* 商品名 *}--><strong><!--{$arrForm.name.value[$index]|h}--></strong><br />
                            <!--{if $arrForm.classcategory_name1.value[$index] != ""}-->
                                <!--{$arrForm.class_name1.value[$index]|h}-->：<!--{$arrForm.classcategory_name1.value[$index]|h}--><br />
                            <!--{/if}-->
                            <!--{if $arrForm.classcategory_name2.value[$index] != ""}-->
                                <!--{$arrForm.class_name2.value[$index]|h}-->：<!--{$arrForm.classcategory_name2.value[$index]|h}--><br />
                            <!--{/if}-->
                            <!--{$arrForm.price.value[$index]|sfCalcIncTax|number_format}-->円
                        </td>
                        <td>
                            <!--{assign var=key value="quantity"}-->
                            <!--{if $arrErr[$key][$index] != ''}-->
                                <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                            <!--{/if}-->
                            <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" class="box40" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->" />
                        </td>
                        <td>
                            <input type="hidden" name="cart_no[<!--{$index}-->]" value="<!--{$index}-->" />
                            <!--{assign var=key value="product_class_id"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="name"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="class_name1"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="class_name2"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="classcategory_name1"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="classcategory_name2"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="main_image"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="main_list_image"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="price"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="shipping"}-->
                            <!--{if strlen($arrErr[$key][$index]) >= 1}-->
                                <div class="attention"><!--{$arrErr[$key][$index]}--></div>
                            <!--{/if}-->
                            <select name="<!--{$key}-->[<!--{$index}-->]" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->">
                                <!--{html_options options=$addrs selected=$arrForm[$key].value[$index]}-->
                            </select>
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
            <div class="btn_area">
                <ul>
                    <li>
                    <a href="<!--{$smarty.const.CART_URLPATH}-->" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg',back03)">
                        <img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" border="0" name="back03" id="back03" /></a>
                    </li>
                    <li>
                    <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_address_select_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_address_select.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_address_select.jpg" alt="選択したお届け先に送る" class="box190" name="send_button" id="send_button" />
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
