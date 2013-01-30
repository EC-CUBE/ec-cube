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
    <!--{if $smarty.const.USE_POINT !== false}-->
        <!--★ポイント案内★-->
        <div class="information">
            <p class="fb">The total product amount is "<span class="price">&#036; <!--{$tpl_all_total_inctax|number_format}--></span>".</p>

            <!--{if $tpl_login}-->
                <p class="point_announce"><span class="user_name"><!--{$tpl_name|h}--></span>, you currently have "<span class="point"><!--{$tpl_user_point|number_format|default:0}--> pts.</span>"<br />

                    It is possible to use points for this purchase. <span class="price">1pts = &#036; <!--{$smarty.const.POINT_VALUE}--></span></p>
            <!--{else}-->
                <p class="point_announce">The point system requires you to login first.</p>
            <!--{/if}-->
        </div>
    <!--{/if}-->

    <!--{if strlen($tpl_error) != 0}-->
        <p class="attention"><!--{$tpl_error|h}--></p>
    <!--{/if}-->

    <!--{if strlen($tpl_message) != 0}-->
        <p class="attention"><!--{$tpl_message|h|nl2br}--></p>
    <!--{/if}-->

    <!--▼フォームここから -->
    <div class="form_area">

        <!--{* カゴの中に商品がある場合にのみ表示 *}-->
        <!--{if count($cartKeys) > 1}-->
            <p class="attentionSt">
                <!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}--> and <!--{/if}--><!--{/foreach}-->cannot be purchased simultaneously. Please carry out a separate purchasing procedure.</p>
        <!--{/if}-->

        <!--{if count($cartItems) > 0}-->

            <!--{foreach from=$cartKeys item=key}-->

                <!--☆送料無料アナウンス右にスライドボタン -->
                <!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                    <!--{if $arrInfo.free_rule > 0}-->
                        <div class="bubbleBox">
                            <div class="bubble_announce clearfix">
                                <p><a rel="external" href="<!--{$tpl_prev_url|h}-->">
                                    <!--{if !$arrData[$key].is_deliv_free}-->
                                        <span class="price">Shipping is free</span> if you spend another "<span class="price">&#036; <!--{$tpl_deliv_free[$key]|number_format}--></span>"!
                                    <!--{else}-->
                                        "<span class="price">Free shipping</span>" now!!
                                    <!--{/if}-->
                                    <br />
                                    Do you want to add this product?</a></p>
                            </div>
                            <div class="bubble_arrow_line"><!--矢印空タグ --></div>
                            <div class="bubble_arrow"><!--矢印空タグ --></div>
                        </div>
                    <!--{/if}-->
                <!--{/if}-->

                <form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="<!--{$smarty.const.CART_URLPATH|h}-->">
                    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                    <!--{if 'sfGMOCartDisplay'|function_exists}-->
                        <!--{'sfGMOCartDisplay'|call_user_func}-->
                    <!--{/if}-->

                    <input type="hidden" name="mode" value="confirm" />
                    <input type="hidden" name="cart_no" value="" />
                    <input type="hidden" name="cartKey" value="<!--{$key}-->" />

                    <div class="formBox">

                        <!--{if count($cartKeys) > 1}-->
                            <div class="box_header">
                                <h3><!--{$arrProductType[$key]}--></h3>
                            </div>
                            <div class="totalmoney_area">
                                The total amount for <!--{$arrProductType[$key]}--> is "<span class="price">&#036; <!--{$tpl_total_inctax[$key]|number_format}--></span>".
                            </div>
                        <!--{/if}-->

                        <!--▼カートの中の商品一覧 -->
                        <div class="cartinarea clearfix">
                            <!--{foreach from=$cartItems[$key] item=arrItem}-->
                                <!--▼商品 -->
                                <div class="cartitemBox">
                                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrItem.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$arrItem.productsClass.name|h}-->" class="photoL" />
                                    <div class="cartinContents">
                                        <div>
                                            <p><em><!--{$arrItem.productsClass.name|h}--></em><br />
                                                <!--{if $arrItem.productsClass.classcategory_name1 != ""}-->
                                                    <span class="mini"><!--{$arrItem.productsClass.class_name1}-->:<!--{$arrItem.productsClass.classcategory_name1}--></span><br />
                                                <!--{/if}-->
                                                <!--{if $arrItem.productsClass.classcategory_name2 != ""}-->
                                                    <span class="mini"><!--{$arrItem.productsClass.class_name2}-->:<!--{$arrItem.productsClass.classcategory_name2}--></span><br />
                                                <!--{/if}-->
                                                <span class="mini">Price:</span>&#036; <!--{$arrItem.price|sfCalcIncTax|number_format}-->
                                            </p>
                                            <p class="btn_delete">
                                                <img src="<!--{$TPL_URLPATH}-->img/button/btn_delete.png" onClick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$arrItem.cart_no}-->');" class="pointer" width="21" height="20" alt="Delete" /></p>
                                        </div>
                                        <ul>
                                            <li class="quantity"><span class="mini">Quantity:</span><!--{$arrItem.quantity|number_format}--></li>
                                            <li class="quantity_btn"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.png" width="22" height="21" alt="+" onclick="fnFormModeSubmit('form<!--{$key}-->', 'up','cart_no','<!--{$arrItem.cart_no}-->'); return false" /></li>
                                            <li class="quantity_btn"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.png" width="22" height="21" alt="-" onclick="fnFormModeSubmit('form<!--{$key}-->', 'down','cart_no','<!--{$arrItem.cart_no}-->'); return false" /></a></li>
                                            <li class="result"><span class="mini">Subtotal:</span>&#036; <!--{$arrItem.total_inctax|number_format}--></li>
                                        </ul>
                                    </div>
                                </div>
                                <!--▲商品 -->
                            <!--{/foreach}-->
                        </div>
                        <!--▲カートの中の商品一覧ここまで -->

                        <div class="total_area">
                            <div><span class="mini">Total:</span><span class="price fb">&#036; <!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}--></span></div>
                            <!--{if $smarty.const.USE_POINT !== false}-->
                                <!--{if $arrData[$key].birth_point > 0}-->
                                    <div><span class="mini">Birthday points:</span> <!--{$arrData[$key].birth_point|number_format}--> Pts</div>
                                <!--{/if}-->
                                <div><span class="mini">Points added at this time:</span> <!--{$arrData[$key].add_point|number_format}--> Pts</div>
                            <!--{/if}-->
                        </div>
                        <!--{if strlen($tpl_error) == 0}-->
                            <div class="btn_area_btm">
                                <input type="hidden" name="cartKey" value="<!--{$key}-->" />
                                <input type="submit" value="Checkout" name="confirm" class="btn data-role-none" />
                            </div>
                        <!--{/if}-->
                    </div><!-- /.formBox -->
                </form>
            <!--{/foreach}-->
        <!--{else}-->
            <p class="empty"><em>* Cart is empty. </em></p>
        <!--{/if}-->

        <p><a rel="external" href="<!--{$smarty.const.ROOT_URLPATH}-->" class="btn_sub">Continue shopping</a></p>

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
