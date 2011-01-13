<!--{*
/*
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
 */
*}-->
<!--{if count($arrBestProducts) > 0}-->
<center>
<!--{foreach from=$arrBestProducts item=arrProduct name=best_products}-->

<!-- ▼おすすめ商品コメント ここから -->
<a href="<!--{$smarty.const.MOBILE_P_DETAIL_URL_PATH}--><!--{$arrProduct.product_id|u}-->">
<!--{$arrProduct.comment|h|nl2br}--></a>
<!-- ▲おすすめ商品コメント ここまで -->

<!--{if !$smarty.foreach.best_products.last}--><br><br><!--{/if}-->
<!--{/foreach}-->
</center>
<hr>
<br>
<!--{/if}-->
