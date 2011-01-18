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
各商品のお届け先を選択してください。<br>
<br>

<form method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="mode" value="confirm">
<!--{foreach from=$items item=item name=cartItem}-->
<!--{assign var=index value=$smarty.foreach.cartItem.index}-->
<input type="hidden" name="cart_no<!--{$index}-->" value="<!--{$index}-->" />
<input type="hidden" name="product_class_id<!--{$index}-->" value="<!--{$item.product_class_id}-->" />

<!--{* 商品名 *}--><!--{$item.name|h}--><br>
<!--{* 規格名1 *}--><!--{if $item.classcategory_name1 != ""}--><!--{$item.class_name1}-->：<!--{$item.classcategory_name1}--><br><!--{/if}-->
<!--{* 規格名2 *}--><!--{if $item.classcategory_name2 != ""}--><!--{$item.class_name2}-->：<!--{$item.classcategory_name2}--><br><!--{/if}-->
<!--{* 販売価格 *}-->
<!--{$item.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円<br>
<!--{assign var=key value="quantity`$index`"}-->
数量：<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" size="4" />
<br>
<!--{assign var=key value="shipping`$index`"}-->
お届け先：<br>
<select name="<!--{$key}-->"><!--{html_options options=$addrs selected=$arrForm[$key].value}--></select>
<br>
<hr>
<!--{/foreach}-->

<center><input type="submit" value="選択したお届け先に送る"></center>
</form>

<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
<form method="post" action="<!--{$smarty.const.URL_PATH}-->mypage/delivery_addr.php">
    <input type="hidden" name="ParentPage" value="<!--{$smarty.const.MULTIPLE_URLPATH}-->">
    一覧にご希望の住所が無い場合は、お届け先を新規登録してください。<br>
    <center><input type="submit" value="新規登録"></center>
    ※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。<br>
</form>
<!--{/if}-->

<form action="<!--{$smarty.const.SHOPPING_URL}-->" method="get">
<center><input type="submit" name="return" value="戻る"></center>
</form>

<br>
