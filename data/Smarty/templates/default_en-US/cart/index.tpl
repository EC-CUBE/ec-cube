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
        $('a.expansion').facebox({
            loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
            closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
        });
    });
//]]></script>

<div id="undercolumn">
    <div id="undercolumn_cart">
        <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!--{if $smarty.const.USE_POINT !== false || count($arrProductsClass) > 0}-->
        <!--★ポイント案内★-->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <div class="point_announce">
                <!--{if $tpl_login}-->
                     <span class="user_name"><!--{$tpl_name|h}--></span>, you currently have "<span class="point"><!--{$tpl_user_point|number_format|default:0}--> pts</span>."<br />
                <!--{else}-->
                    If using the point system, please complete member registration and log in.<br />
                <!--{/if}-->
                It is possible to use points for this purchase.<span class="price">1pts = &#36; <!--{$smarty.const.POINT_VALUE}--></span>.<br />
            </div>
        <!--{/if}-->
    <!--{/if}-->

    <p class="totalmoney_area">
        <!--{* カゴの中に商品がある場合にのみ表示 *}-->
        <!--{if count($cartKeys) > 1}-->
            <span class="attentionSt"><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}--> and <!--{/if}--><!--{/foreach}--> cannot be purchased simultaneously.<br />
                        Please carry out separate purchasing procedures.
            </span>
        <!--{/if}-->

        <!--{if strlen($tpl_error) != 0}-->
            <p class="attention"><!--{$tpl_error|h}--></p>
        <!--{/if}-->

        <!--{if strlen($tpl_message) != 0}-->
            <p class="attention"><!--{$tpl_message|h|nl2br}--></p>
        <!--{/if}-->
    </p>

    <!--{if count($cartItems) > 0}-->
    <!--{foreach from=$cartKeys item=key}-->
    <div class="form_area">
        <form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="cart_no" value="" />
            <input type="hidden" name="cartKey" value="<!--{$key}-->" />
            <input type="hidden" name="category_id" value="<!--{$tpl_category_id|h}-->" />
            <!--{if count($cartKeys) > 1}-->
            <h3><!--{$arrProductType[$key]}--></h3>
                <p>
                    The total amount for <!--{$arrProductType[$key]}--> is "<span class="price">&#36; <!--{$tpl_total_inctax[$key]|number_format}--></span>".
                    <!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                        <!--{if $arrInfo.free_rule > 0}-->
                            <!--{if !$arrData[$key].is_deliv_free}-->
                                Spend "<span class="price">&#36; <!--{$tpl_deliv_free[$key]|number_format}--></span>" more for free shipping!!
                            <!--{else}-->
                                "<span class="attention">Free shipping</span>" now!!
                            <!--{/if}-->
                        <!--{/if}-->
                    <!--{/if}-->
                </p>
            <!--{else}-->
                <p>
                    The total of your purchase is "<span class="price">&#36; <!--{$tpl_total_inctax[$key]|number_format}--></span>".
                    <!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                        <!--{if $arrInfo.free_rule > 0}-->
                            <!--{if !$arrData[$key].is_deliv_free}-->
                                Spend "<span class="price">&#36; <!--{$tpl_deliv_free[$key]|number_format}--></span>" more for free shipping!!
                            <!--{else}-->
                                "<span class="attention">Free shipping</span>" now!!
                            <!--{/if}-->
                        <!--{/if}-->
                    <!--{/if}-->
                </p>
            <!--{/if}-->

            <table summary="Product information">
                <col width="10%" />
                <col width="15%" />
                <col width="30%" />
                <col width="15%" />
                <col width="15%" />
                <col width="15%" />
                <tr>
                    <th class="alignC">Delete</th>
                    <th class="alignC">Product photo</th>
                    <th class="alignC">Product name</th>
                    <th class="alignC">Unit price</th>
                    <th class="alignC">Quantity</th>
                    <th class="alignC">Subtotal</th>
                </tr>
                <!--{foreach from=$cartItems[$key] item=item}-->
                    <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                        <td class="alignC"><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;">Delete</a>
                        </td>
                        <td class="alignC">
                        <a class="expansion" target="_blank"
                                <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                                <!--{/if}-->
                                >
                                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" />
                            </a>
                        </td>
                        <td><!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong><br />
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                <!--{$item.productsClass.class_name1}-->:<!--{$item.productsClass.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <!--{$item.productsClass.class_name2}-->:<!--{$item.productsClass.classcategory_name2}-->
                            <!--{/if}-->
                        </td>
                        <td class="alignR">
                            &#36;<!--{$item.price|sfCalcIncTax|number_format}-->
                        </td>
                        <td class="alignC"><!--{$item.quantity}-->
                            <ul id="quantity_level">
                                <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="16" height="16" alt="+" /></a></li>
                                <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="16" height="16" alt="-" /></a></li>
                            </ul>
                        </td>
                        <td class="alignR">&#36;<!--{$item.total_inctax|number_format}--></td>
                    </tr>
                <!--{/foreach}-->
                <tr>
                    <th colspan="5" class="alignR">Subtotal</th>
                    <td class="alignR">&#36;<!--{$tpl_total_inctax[$key]|number_format}--></td>
                </tr>
                <tr>
                    <th colspan="5" class="alignR">Total</th>
                    <td class="alignR"><span class="price">&#36;<!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}--></span></td>
                </tr>
                <!--{if $smarty.const.USE_POINT !== false}-->
                    <!--{if $arrData[$key].birth_point > 0}-->
                        <tr>
                            <th colspan="5" class="alignR">Birthday points</th>
                            <td class="alignR"><!--{$arrData[$key].birth_point|number_format}-->pts</td>
                        </tr>
                    <!--{/if}-->
                    <tr>
                        <th colspan="5" class="alignR">Points added at this time</th>
                        <td class="alignR"><!--{$arrData[$key].add_point|number_format}-->pts</td>
                    </tr>
                <!--{/if}-->
            </table>
                <!--{if strlen($tpl_error) == 0}-->
                    <p class="alignC">If you are finished shopping, please click the "Checkout" button.</p>
                <!--{/if}-->
            <div class="btn_area">
                <ul>
                    <li>
                        <!--{if $tpl_prev_url != ""}-->
                            <a class="bt04" href="<!--{$tpl_prev_url|h}-->">Go back</a>
                        <!--{/if}-->
                    </li>
                    <li>
                        <!--{if strlen($tpl_error) == 0}-->
                            <input type="hidden" name="cartKey" value="<!--{$key}-->" />
							<button class="bt02">Checkout</button>
                        <!--{/if}-->
                    </li>
                </ul>
            </div>
        </form>
        </div>
    <!--{/foreach}-->
    <!--{else}-->
        <p class="empty"><span class="attention">* Your shopping cart is empty.  </span></p>
    <!--{/if}-->
    </div>
</div>
