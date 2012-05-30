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

<!--{strip}-->
    各商品のお届け先を選択してください。<br>
    ※数量の合計は、カゴの中の数量と合わせてください。<br>
    <br>

    <form method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
        <input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->">
        <input type="hidden" name="mode" value="confirm">
        <!--{section name=line loop=$arrForm.line_of_num.value}-->
            <!--{assign var=index value=$smarty.section.line.index}-->
            <input type="hidden" name="cart_no[<!--{$index}-->]" value="<!--{$index}-->">
            <!--{assign var=key value="product_class_id"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="name"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="class_name1"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="class_name2"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="classcategory_name1"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="classcategory_name2"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="main_image"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="main_list_image"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">
            <!--{assign var=key value="price"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->">

            <!--{* 商品名 *}-->◎<!--{$arrForm.name.value[$index]|h}--><br>
            <!--{* 規格名1 *}--><!--{if $arrForm.classcategory_name1.value[$index] != ""}--><!--{$arrForm.class_name1.value[$index]}-->：<!--{$arrForm.classcategory_name1.value[$index]}--><br><!--{/if}-->
            <!--{* 規格名2 *}--><!--{if $arrForm.classcategory_name2.value[$index] != ""}--><!--{$arrForm.class_name2.value[$index]}-->：<!--{$arrForm.classcategory_name2.value[$index]}--><br><!--{/if}-->
            <!--{* 販売価格 *}-->
            <!--{$arrForm.price.value[$index]|sfCalcIncTax|number_format}-->円<br>

            <!--{assign var=key value="quantity"}-->
            <!--{if $arrErr[$key][$index] != ''}-->
                <font color="#FF0000"><!--{$arrErr[$key][$index]}--></font>
            <!--{/if}-->
            数量：<input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" size="4">
            <br>

            <!--{assign var=key value="shipping"}-->
            <!--{if strlen($arrErr[$key][$index]) >= 1}-->
                <font color="#FF0000"><!--{$arrErr[$key][$index]}--></font>
            <!--{/if}-->
            お届け先：<br>
            <select name="<!--{$key}-->[<!--{$index}-->]">
                <!--{html_options options=$addrs selected=$arrForm[$key].value[$index]}-->
            </select>
            <br>
            <br>
        <!--{/section}-->
        <center><input type="submit" value="選択したお届け先に送る"></center>
    </form>

    <br>
    <hr>

    <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
        <form method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
            <input type="hidden" name="ParentPage" value="<!--{$smarty.const.MULTIPLE_URLPATH}-->">
            一覧にご希望の住所が無い場合は、お届け先を新規登録してください。<br>
            ※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。<br><br>
            <center><input type="submit" value="新規登録"></center>
        </form>
    <!--{/if}-->


    <form action="<!--{$smarty.const.SHOPPING_URL}-->" method="get">
        <center><input type="submit" name="return" value="戻る"></center>
    </form>
<!--{/strip}-->
