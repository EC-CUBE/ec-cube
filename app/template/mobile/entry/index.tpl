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
        <input type="hidden" name="mode" value="confirm">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <font color="#FF0000">*は必須項目です。</font><br>
        <br>

        ●お名前<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
        姓（例：渋谷）<br>
        <input type="text" name="name01" value="<!--{$arrForm.name01.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        名（例：花子）<br>
        <input type="text" name="name02" value="<!--{$arrForm.name02.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        ●お名前(フリガナ)<!--{if !$smarty.const.FORM_COUNTRY_ENABLE}--><font color="#FF0000"> *</font><!--{/if}--><br>
        <font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>
        フリガナ/姓（例：シブヤ）<br>
        <input type="text" name="kana01" value="<!--{$arrForm.kana01.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

        フリガナ/名（例：ハナコ）<br>
        <input type="text" name="kana02" value="<!--{$arrForm.kana02.value|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

        ●会社名<br>
        <font color="#FF0000"><!--{$arrErr.company_name}--></font>
        <input type="text" name="company_name" value="<!--{$arrForm.company_name.value|h}-->" istyle="1"><br>

        ●性別<font color="#FF0000"> *</font><br>
        <!--{assign var=key1 value="sex"}-->
        <!--{if $arrErr[$key1]}-->
            <font color="#FF0000"><!--{$arrErr[$key1]}--></font>
        <!--{/if}-->
        <!--{html_radios name=$key1 options=$arrSex selected=$arrForm[$key1].value separator='&nbsp;'}--><br>

        ●職業<br>
        <font color="#FF0000"><!--{$arrErr.job}--></font>
        <select name="job">
            <option value="">選択してください</option>
            <!--{html_options options=$arrJob selected=$arrForm.job.value}-->
        </select><br>

        ●生年月日<br>
        <font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
        <input type="text" name="year" value="<!--{$arrForm.year.value|h}-->" size="4" maxlength="4" istyle="4">年<br>
        <select name="month">
            <!--{html_options options=$arrMonth selected=$arrForm.month.value|h}-->
        </select>月<br>
        <select name="day">
            <!--{html_options options=$arrDay selected=$arrForm.day.value|h}-->
        </select>日<br>

        <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
        ●国<font color="#FF0000">※</font><br>
        <font color="#FF0000"><!--{$arrErr.country_id}--></font>
        <select name="country_id">
            <option value="">選択してください</option>
            <!--{html_options options=$arrCountry selected=$arrForm.country_id.value|h|default:$smarty.const.DEFAULT_COUNTRY_ID}-->
        </select><br>
        
        ●ZIP CODE<br>
        <font color="#FF0000"><!--{$arrErr.zipcode}--></font>
        <input type="text" name="zipcode" value="<!--{$arrForm.zipcode.value|h}-->"><br>
        <!--{else}-->
        <input type="hidden" name="country_id" value="<!--{$smarty.const.DEFAULT_COUNTRY_ID}-->" />
        <!--{/if}-->
        
        <!--{assign var=key1 value="zip01"}-->
        <!--{assign var=key2 value="zip02"}-->
        ●郵便番号<!--{if !$smarty.const.FORM_COUNTRY_ENABLE}--><font color="#FF0000"> *</font><!--{/if}--><br>
        <font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
        <!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
        <!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
        <input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01.value == ""}--><!--{$arrOtherDeliv.zip01.value|h}--><!--{else}--><!--{$arrForm.zip01.value|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02.value == ""}--><!--{$arrOtherDeliv.zip02.value|h}--><!--{else}--><!--{$arrForm.zip02.value|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>
        <input type="submit" name="submit_address" value="自動住所入力"><br>
    郵便番号を入力後、クリックしてください。<br>

        ●都道府県<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.pref}--></font>
        <select name="pref">
            <option value="">都道府県を選択</option>
            <!--{html_options options=$arrPref selected=$arrForm.pref.value}-->
        </select><br>

        ●住所1<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.addr01}--></font>
        <input type="text" name="addr01" value="<!--{$arrForm.addr01.value|h}-->" istyle="1"><br>

        ●住所2<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.addr02}--></font>
        <input type="text" name="addr02" value="<!--{$arrForm.addr02.value|h}-->" istyle="1"><br>

        ●電話番号<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></font>
        <!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
        <input type="text" size="<!--{$size}-->" name="tel01" value="<!--{$arrForm.tel01.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="tel02" value="<!--{$arrForm.tel02.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="tel03" value="<!--{$arrForm.tel03.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

        ●FAX<br>
        <!--{assign var=key1 value="`$prefix`fax01"}-->
        <!--{assign var=key2 value="`$prefix`fax02"}-->
        <!--{assign var=key3 value="`$prefix`fax03"}-->
        <!--{if $arrErr[fax01] || $arrErr[fax02] || $arrErr[fax03]}-->
            <font color="#FF0000"><!--{$arrErr[fax01]}--><!--{$arrErr[fax02]}--><!--{$arrErr[fax03]}--></font><br>
        <!--{/if}-->
        <input type="text" size="<!--{$size}-->" name="fax01" value="<!--{$arrForm.fax01.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4" />&nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="fax02" value="<!--{$arrForm.fax02.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4" />&nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="fax03" value="<!--{$arrForm.fax03.value|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4" /><br>

        ●メールアドレス<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.email}--></font>
        <input type="text" name="email" value="<!--{$arrForm.email.value|h}-->" istyle="3">
        <br>

        ●パスワード<font color="#FF0000"> *</font><br>
        （半角英数字<!--{$smarty.const.PASSWORD_MIN_LEN}-->文字以上<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字以内）<br>
        <font color="#FF0000"><!--{$arrErr.password}--></font>
        <!--{assign var="size" value="`$smarty.const.PASSWORD_MAX_LEN+2`"}-->
        <input type="password" name="password" value="<!--{$arrForm.password.value|h}-->" istyle="4" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" size="<!--{$size}-->"><br>

        ●パスワード確認用の質問<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.reminder}--></font>
        <select name="reminder">
            <option value="">選択してください</option>
            <!--{html_options options=$arrReminder selected=$arrForm.reminder.value|h}-->
        </select><br>

        ●質問の答え<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.reminder_answer}--></font>
        <input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer.value|h}-->" istyle="1"><br>

        ●メールマガジン<br>
        お得な情報を希望されますか？<br>
        <input type="hidden" name="mailmaga_flg" value="3">
        配信希望<input type="checkbox" name="mailmaga_flg" value="2" <!--{if $arrForm.mailmaga_flg.value == 2}-->checked<!--{/if}-->><br>
        <!--{if $arrForm.mailmaga_flg.value == 2}-->（希望されない場合はチェックをはずしてください）<br><!--{/if}-->
        <br>

        <center><input type="submit" name="submit" value="次へ"></center>

        <!--{foreach from=$list_data key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
        <!--{/foreach}-->
    </form>
<!--{/strip}-->
