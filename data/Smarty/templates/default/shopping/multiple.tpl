<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--▼CONTENTS-->
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
        <p class="information">各商品のお届け先を選択してください。</p>
        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p>一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。</p>
        <!--{/if}-->
        <p class="mini attention">※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>

        <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
            <p class="addbtn">
                <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->','new_deiv','600','640'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','addition');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','addition');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
            </p>
        <!--{/if}-->
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <table summary="商品情報">
                <colgroup width="10%"></colgroup>
                <colgroup width="35%"></colgroup>
                <colgroup width="10%"></colgroup>
                <colgroup width="45%"></colgroup>
                <tr>
                    <th>商品写真</th>
                    <th>商品名</th>
                    <th>数量</th>
                    <th>お届け先</th>
                </tr>
                <!--{foreach from=$items item=item name=cartItem}-->
                    <!--{assign var=index value=$smarty.foreach.cartItem.index}-->
                    <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                        <td class="alignC">
                        <a
                            <!--{if $item.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                            <!--{/if}-->
                        >
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="&lt;!--{$item.productsClass.name|h}--&gt;" /></a>                        </td>
                        <td><!--{* 商品名 *}--><strong><!--{$item.name|h}--></strong><br />
                            <!--{if $item.classcategory_name1 != ""}-->
                                <!--{$item.class_name1}-->：<!--{$item.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.classcategory_name2 != ""}-->
                                <!--{$item.class_name2}-->：<!--{$item.classcategory_name2}--><br />
                            <!--{/if}-->
                            <!--{$item.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円                        </td>
                        <td>
                        <!--{assign var=key value="quantity`$index`"}-->
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" class="box40" /></td>
                        <td>
                          <input type="hidden" name="cart_no<!--{$index}-->" value="<!--{$index}-->" />
                          <input type="hidden" name="product_class_id<!--{$index}-->" value="<!--{$item.product_class_id}-->" />
                          <!--{assign var=key value="shipping`$index`"}-->
                          <select name="<!--{$key}-->"><!--{html_options options=$addrs selected=$arrForm[$key].value}--></select>                        </td>
                     </tr>
                  <!--{/foreach}-->
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
