<!--{*
/*
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
 */
*}-->

<!--{strip}-->
    <div align="center">数量指定</div>
    <hr>

    <!--{if $arrErr.quantity != ""}-->
    	<font color="#FF0000">※数量を入力して下さい｡</font><br>
    <!--{/if}-->
    <form method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
    	<input type="text" name="quantity" size="3" value="<!--{$arrForm.quantity.value|default:1|h}-->" maxlength=<!--{$smarty.const.INT_LEN}--> istyle="4"><br>
    	<input type="hidden" name="mode" value="cart">
    	<input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value}-->">
    	<input type="hidden" name="classcategory_id2" value="<!--{$arrForm.classcategory_id2.value}-->">
    	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
        <input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->">
        <input type="hidden" name="product_type" value="<!--{$tpl_product_type}-->">
    	<center><input type="submit" name="submit" value="かごに入れる"></center>
    </form>
<!--{/strip}-->
