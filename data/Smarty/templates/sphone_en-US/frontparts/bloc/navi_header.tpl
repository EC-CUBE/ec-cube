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

<nav class="header_navi">
    <ul>
        <li class="mypage"><img src="<!--{$TPL_URLPATH}-->img/header/btn_header_mypage.png" onclick="fnShowPopupmyPage(this)" width="30" height="20" alt="My page" /></li>
        <li class="cart"><img src="<!--{$TPL_URLPATH}-->img/header/btn_header_cart.png" onclick="fnShowPopupCart(this)" width="30" height="20" alt="Cart" /></li>
    </ul>
</nav>
<!--!!空ボックス -->
<div class="popup_mypage">
    <!--{if $tpl_login}-->
        <p><span class="mini">Welcome</span><br />
        <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" rel="external"><!--{$tpl_name1|h}--> <!--{$tpl_name2|h}--></a></p>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <p>Total points <!--{$tpl_user_point|number_format|default:0}-->pts</p>
        <!--{/if}-->
    <!--{else}-->
        <p>Welcome<br />
           Guest</p>
        <p><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" rel="external">Login</a></p>
    <!--{/if}-->
</div>

<div class="popup_cart">
    <!--{if count($arrCartList) > 0}-->
        <h2><a rel="external" href="<!--{$smarty.const.CART_URLPATH|h}-->">Inside of cart</a></h2>
        <!--{foreach from=$arrCartList item=key}-->
            <div class="product_type">
                <!--{if count($arrCartList) > 1}-->
                    <p><span class="product_type">[<!--{$key.productTypeName|h}-->]</span></p>
                <!--{/if}-->
                <p><span class="mini">Product quantity:</span> <span class="quantity"><!--{$key.quantity|number_format}--></span> items<br />
                    <span class="mini">Total:</span><span class="money">&#036; <!--{$key.totalInctax|number_format}--></span>(incl. tax)</p>
                <hr class="dashed" />
                <!--{if $freeRule > 0 && $key.productTypeId|h != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                    <!--{if $key.delivFree > 0}-->
                        <p class="attention free_money_area">Spend &#036; <span class="free_money"><!--{$key.delivFree|number_format}--></span> more for free shipping!!</p>
                    <!--{else}-->
                        <p class="attention free_money_area">Currently, shipping is free</p>
                    <!--{/if}-->
                <!--{/if}-->
            </div>
        <!--{/foreach}-->
    <!--{else}-->
        * Cart is empty. 
    <!--{/if}-->
</div>


<script>
    var stateMyPage = 0;
    var stateCart = 0;
    function fnShowPopupmyPage(el) {
        $("div.popup_mypage").css("left", $(el).offset().left - $("div.popup_mypage").width() + 15);
        $("div.popup_mypage").toggle();
        //表示状態の更新
        if (stateMyPage == 0) {
            stateMyPage = 1;
        } else {
            stateMyPage = 0;
        }

        //カート情報の非表示化
        if (stateCart == 1) {
            $("div.popup_cart").hide();
            stateCart = 0;
        }
    }

    function fnShowPopupCart(el) {
        $("div.popup_cart").css("left", $(el).offset().left - $("div.popup_cart").width() + 15);
        $("div.popup_cart").toggle();
        //表示状態の更新
        if (stateCart == 0) {
            stateCart = 1;
        } else {
            stateCart = 0;
        }

        //カート情報の非表示化
        if (stateMyPage == 1) {
            $("div.popup_mypage").hide();
            stateMyPage = 0;
        }
    }
</script>
