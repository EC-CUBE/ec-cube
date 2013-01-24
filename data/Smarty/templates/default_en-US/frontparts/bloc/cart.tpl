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

<div class="block_outer">
    <div id="cart_area">
    <h2 class="cart"><img src="<!--{$TPL_URLPATH}-->img/title/icon_bloc_cart.gif" alt="" /><span class="title">Cart</span></h2>
        <div class="block_body">
            <div class="information">
                <p class="item">Items in cart:<span class="attention"><!--{$arrCartList.0.TotalQuantity|number_format|default:0}--></span></p>
                <p class="total">Total amount:<span class="price">&#36; <!--{$arrCartList.0.ProductsTotal|number_format|default:0}--></span></p>
                <!--{*************************************
                     * カゴの中に商品がある場合にのみ表示
                     * 複数の商品種別が存在する場合は非表示
                     *************************************}-->
                <!--{if $arrCartList.0.TotalQuantity > 0 and $arrCartList.0.free_rule > 0 and !$isMultiple and !$hasDownload}-->
                <p class="postage">
                    <!--{if $arrCartList.0.deliv_free > 0}-->
                        <span class="price">&#36; <!--{$arrCartList.0.deliv_free|number_format|default:0}--> (including tax)</span> until shipping and processing fees are free.
                    <!--{else}-->
                        Currently, shipping is "<span class="price">free</span>".
                    <!--{/if}-->
                </p>
                <!--{/if}-->
            </div>
            <div class="btn">				
                <a class="bt03" href="<!--{$smarty.const.CART_URLPATH}-->">View cart</a>
            </div>
        </div>
    </div>
</div>
