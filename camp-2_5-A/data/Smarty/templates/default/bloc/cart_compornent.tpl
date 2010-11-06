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
<div class="cartCompornent">
	<div class="cartContents">
		<span class="item">商品数</span>： <!--{$arrCartList.0.TotalQuantity|number_format|default:0}-->点 / <span class="price">合計： <!--{$arrCartList.0.ProductsTotal|number_format|default:0}-->円</span>
    <!--{* カゴの中に商品がある場合にのみ表示 *}-->
    <!--{if $arrCartList.0.TotalQuantity > 0 and $arrCartList.0.free_rule > 0}--><br>
      <!--{if $arrCartList.0.deliv_free > 0}-->
		    <span class="attention">送料手数料無料まであと<!--{$arrCartList.0.deliv_free|number_format|default:0}-->円（税込）</span>です。
      <!--{else}-->
        現在、送料は「<span class="attention">無料</span>」です。
      <!--{/if}-->
    <!--{/if}-->

	</div>
</div>
