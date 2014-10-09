<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="edit">
        <input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id|h}-->">
        <input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->">

        <font color="#FF0000">*は必須項目です。</font><br>
        <br>

        【お名前】<font color="#FF0000">※</font><br>
        <font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
        姓（例：渋谷）<br>
        <input type="text" name="name01" value="<!--{$arrForm.name01.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        名（例：花子）<br>
        <input type="text" name="name02" value="<!--{$arrForm.name02.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        <font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>
        フリガナ/姓（例：シブヤ）<br>
        <input type="text" name="kana01" value="<!--{$arrForm.kana01.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

        フリガナ/名（例：ハナコ）<br>
        <input type="text" name="kana02" value="<!--{$arrForm.kana02.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

        【会社名】<br>
        <font color="#FF0000"><!--{$arrErr.company_name}--></font>
        <input type="text" name="company_name" value="<!--{$arrForm.company_name.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        <!--{if !$smarty.const.FORM_COUNTRY_ENABLE}-->
            <input type="hidden" name="country_id" value="<!--{$smarty.const.DEFAULT_COUNTRY_ID}-->" >
        <!--{else}-->
            【国】<br>
            <font color="#FF0000"><!--{$arrErr.country_id}--></font>
            <select name="country_id" style="<!--{$arrErr.country_id|sfGetErrorColor}-->">
                <option value="" selected="selected">国を選択</option>
                <!--{html_options options=$arrCountry selected=$arrForm.country_id.value|h|default:$smarty.const.DEFAULT_COUNTRY_ID}-->
            </select><br>

            【ZIP CODE】<br>
            <font color="#FF0000"><!--{$arrErr.zipcode}--></font>
            <input type="text" name="zipcode" value="<!--{$arrForm.zipcode.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="4" ><br>
        <!--{/if}-->

        <!--{assign var=key1 value="zip01"}-->
        <!--{assign var=key2 value="zip02"}-->
        【郵便番号】<font color="#FF0000">*</font><br>
        <font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
        <!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
        <!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
        <input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01.value != ""}--><!--{$arrForm.zip01.value|h}--><!--{else}--><!--{$zip01|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02.value != ""}--><!--{$arrForm.zip02.value|h}--><!--{else}--><!--{$zip02|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>

        <br>
        【都道府県】<font color="#FF0000">*</font><br>
        <font color="#FF0000"><!--{$arrErr.pref}--></font>
        <select name="pref">
            <option value="">都道府県を選択</option>
            <!--{html_options options=$arrPref selected=$arrForm.pref.value|h}-->
        </select><br>

        【住所1】<font color="#FF0000">*</font><br>
        <font color="#FF0000"><!--{$arrErr.addr01}--></font>
        <input type="text" name="addr01" value="<!--{$arrForm.addr01.value|h}-->" istyle="1"><br>

        【住所2】<font color="#FF0000">*</font><br>
        <font color="#FF0000"><!--{$arrErr.addr02}--></font>
        <input type="text" name="addr02" value="<!--{$arrForm.addr02.value|h}-->" istyle="1"><br>

        【電話番号】<font color="#FF0000">*</font><br>
        <font color="#FF0000"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></font>
        <!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
        <input type="text" size="<!--{$size}-->" name="tel01" value="<!--{$arrForm.tel01.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="tel02" value="<!--{$arrForm.tel02.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="tel03" value="<!--{$arrForm.tel03.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

        <br>

        <div align="center"><input type="submit" name="submit" value="次へ"></div>

        <!--{foreach from=$list_data key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item.value|h}-->">
        <!--{/foreach}-->
    </form>

    <br>
<!--{/strip}-->
