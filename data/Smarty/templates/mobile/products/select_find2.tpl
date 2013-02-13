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
    <div align="center"><!--{$tpl_class_name2}--></div>
    <hr>

    <!--{if $arrErr.classcategory_id2 != ""}-->
        <font color="#FF0000">※<!--{$tpl_class_name2}-->を入力して下さい｡</font><br>
    <!--{/if}-->
    <form method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <select name="classcategory_id2">
            <option value="">選択してください</option>
            <!--{html_options options=$arrClassCat2 selected=$arrForm.classcategory_id2.value}-->
        </select><br>
        <input type="hidden" name="mode" value="selectItem">
        <input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value|h}-->">
        <input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
        <center><input type="submit" name="submit" value="次へ"></center>
    </form>
<!--{/strip}-->
