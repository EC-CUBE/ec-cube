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
    <form name="form1" method="post" action="?">
        <input type="hidden" name="mode" value="confirm">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <font color="#FF0000">*は必須項目です。</font><br>
        <br>

        ●お名前<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
        姓（例：渋谷）<br>
        <input type="text" name="name01" value="<!--{$arrForm.name01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        名（例：花子）<br>
        <input type="text" name="name02" value="<!--{$arrForm.name02|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
        <font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>

        ●お名前(フリガナ)<font color="#FF0000"> *</font><br>
        フリガナ/姓（例：シブヤ）<br>
        <input type="text" name="kana01" value="<!--{$arrForm.kana01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

        フリガナ/名（例：ハナコ）<br>
        <input type="text" name="kana02" value="<!--{$arrForm.kana02|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

        ●性別<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.sex}--></font>
        <input type="radio" name="sex" value="1" <!--{if $arrForm.sex eq 1}-->checked<!--{/if}-->>男性&nbsp;<input type="radio" name="sex" value="2" <!--{if $arrForm.sex eq 2}-->checked<!--{/if}-->>女性<br>

        ●職業<br>
        <font color="#FF0000"><!--{$arrErr.job}--></font>
        <select name="job">
            <option value="">選択してください</option>
            <!--{html_options options=$arrJob selected=$arrForm.job}-->
        </select><br>

        ●生年月日<br>
        <font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
        <input type="text" name="year" value="<!--{$arrForm.year|h}-->" size="4" maxlength="4" istyle="4">年<br>
        <select name="month">
            <!--{html_options options=$arrMonth selected=$arrForm.month}-->
        </select>月<br>
        <select name="day">
            <!--{html_options options=$arrDay selected=$arrForm.day}-->
        </select>日<br>

        <!--{assign var=key1 value="zip01"}-->
        <!--{assign var=key2 value="zip02"}-->
        ●郵便番号<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
        <!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
        <!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
        <input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01 == ""}--><!--{$arrOtherDeliv.zip01|h}--><!--{else}--><!--{$arrForm.zip01|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02 == ""}--><!--{$arrOtherDeliv.zip02|h}--><!--{else}--><!--{$arrForm.zip02|h}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>
        <input type="submit" name="submit_address" value="自動住所入力"><br>
    郵便番号を入力後、クリックしてください。<br>

        ●都道府県<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.pref}--></font>
        <select name="pref">
            <option value="">都道府県を選択</option>
            <!--{html_options options=$arrPref selected=$arrForm.pref}-->
        </select><br>

        ●住所1<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.addr01}--></font>
        <input type="text" name="addr01" value="<!--{$arrForm.addr01|h}-->" istyle="1"><br>

        ●住所2<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.addr02}--></font>
        <input type="text" name="addr02" value="<!--{$arrForm.addr02|h}-->" istyle="1"><br>

        ●電話番号<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></font>
        <!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
        <input type="text" size="<!--{$size}-->" name="tel01" value="<!--{$arrForm.tel01|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="tel02" value="<!--{$arrForm.tel02|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
        &nbsp;-&nbsp;
        <input type="text" size="<!--{$size}-->" name="tel03" value="<!--{$arrForm.tel03|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

        ●メールアドレス<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.email}--></font>
        <input type="text" name="email" value="<!--{$arrForm.email|h}-->" istyle="3">
        <br>

        ●パスワード<font color="#FF0000"> *</font><br>
        （半角英数字<!--{$smarty.const.PASSWORD_MIN_LEN}-->文字以上<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字以内）<br>
        <font color="#FF0000"><!--{$arrErr.password}--></font>
        <!--{assign var="size" value="`$smarty.const.PASSWORD_MAX_LEN+2`"}-->
        <input type="password" name="password" value="<!--{$arrForm.password}-->" istyle="4" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" size="<!--{$size}-->"><br>

        ●パスワード確認用の質問<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.reminder}--></font>
        <select name="reminder">
            <option value="">選択してください</option>
            <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
        </select><br>

        ●質問の答え<font color="#FF0000"> *</font><br>
        <font color="#FF0000"><!--{$arrErr.reminder_answer}--></font>
        <input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" istyle="1"><br>

        ●メールマガジン<br>
        お得な情報を希望されますか？<br>
        <input type="hidden" name="mailmaga_flg" value="3">
        配信希望<input type="checkbox" name="mailmaga_flg" value="2" <!--{if $arrForm.mailmaga_flg == 2}-->checked<!--{/if}-->><br>
        <!--{if $arrForm.mailmaga_flg == 2}-->（希望されない場合はチェックをはずしてください）<br><!--{/if}-->
        <br>

        <center><input type="submit" name="submit" value="次へ"></center>

        <!--{foreach from=$list_data key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
        <!--{/foreach}-->
    </form>
<!--{/strip}-->
