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
<div class="bloc_outer">
    <h2><img src="<!--{$TPL_DIR}-->img/icon/ico_block_cart.gif" width="20" height="20" alt="*" class="title_icon" />
        現在のカゴの中</h2>
    <div id="cartarea" class="bloc_body">
        <p class="item">商品数：<!--{$arrCartList.0.TotalQuantity|number_format|default:0}-->点</p>
        <p>合計：<span class="price"><!--{$arrCartList.0.ProductsTotal|number_format|default:0}-->円</span><br />
            <!--{* カゴの中に商品がある場合にのみ表示 *}-->
            <!--{if $arrCartList.0.TotalQuantity > 0 and $arrCartList.0.free_rule > 0}-->
                <!--{if $arrCartList.0.deliv_free > 0}-->
                    送料手数料無料まであと<!--{$arrCartList.0.deliv_free|number_format|default:0}-->円（税込）です。
                <!--{else}-->
                    現在、送料は「<span class="price">無料</span>」です。
                <!--{/if}-->
            <!--{/if}-->
        </p>
        <p class="btn">
            <a href="<!--{$smarty.const.CART_URL_PATH}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_block_cartin_on.gif','button_cartin');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_block_cartin.gif','button_cartin');">
                <img src="<!--{$TPL_DIR}-->img/button/btn_block_cartin.gif" width="87" height="22" alt="カゴの中を見る" border="0" name="button_cartin" id="button_cartin" /></a>
        </p>
    </div>
</div>
