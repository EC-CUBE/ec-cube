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
    <form name="form1" id="form1" method="post" action="?" utn>
        <input type="hidden" name="mode" value="complete">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <!--{foreach from=$arrForm key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item.value|h}-->">
        <!--{/foreach}-->
        下記の内容でご登録してもよろしいですか？<br>
        <br>

        ●お名前<br>
        <!--{$arrForm.name01.value|h}-->　<!--{$arrForm.name02.value|h}--><br>

        ●お名前(フリガナ)<br>
        <!--{$arrForm.kana01.value|h}-->　<!--{$arrForm.kana02.value|h}--><br>

        ●会社名<br>
        <!--{$arrForm.company_name.value|h}--><br>

        ●性別<br>
        <!--{assign var=key1 value="sex"}-->
        <!--{assign var="sex_id" value=$arrForm[$key1].value}-->
        <!--{$arrSex[$sex_id]|h}--><br>

        ●職業<br>
        <!--{if $arrForm.job}--><!--{$arrJob[$arrForm.job.value]|h}--><!--{else}-->未登録<!--{/if}--><br>

        ●生年月日<br>
        <!--{if strlen($arrForm.year.value) > 0 && strlen($arrForm.month.value) > 0 && strlen($arrForm.day.value) > 0}--><!--{$arrForm.year.value|h}-->年<!--{$arrForm.month.value|h}-->月<!--{$arrForm.day.value|h}-->日生まれ<!--{else}-->未登録<!--{/if}--><br>

        <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
        ●国<br>
        <!--{assign var="country_id" value=$arrForm.country_id.value}-->
        <!--{$arrCountry[$country_id]|h}--><br>

        ●ZIP CODE<br>
        <!--{$arrForm.zipcode.value|h}--><br>
        <!--{/if}-->

        ●住所<br>
        〒<!--{$arrForm.zip01.value|h}--> - <!--{$arrForm.zip02.value|h}--><br>
        <!--{$arrPref[$arrForm.pref.value]|h}--><!--{$arrForm.addr01.value|h}--><!--{$arrForm.addr02.value|h}--><br>

        ●電話番号<br>
        <!--{$arrForm.tel01.value|h}-->-<!--{$arrForm.tel02.value|h}-->-<!--{$arrForm.tel03.value|h}--><br>

        ●ﾒｰﾙｱﾄﾞﾚｽ<br>
        <!--{$arrForm.email.value|h}--><br>

        ●ﾊﾟｽﾜｰﾄﾞ確認用質問<br>
        <!--{$arrReminder[$arrForm.reminder.value]|h}--><br>

        ●質問の答え<br>
        <!--{$arrForm.reminder_answer.value|h}--><br>

        ●ﾒｰﾙﾏｶﾞｼﾞﾝ<br>
        <!--{assign var=key1 value="mailmaga_flg"}-->
        <!--{assign var="mailmaga_flg_id" value=$arrForm[$key1].value}-->
        <!--{$arrMAILMAGATYPE[$mailmaga_flg_id]|h}--><br>
        <br>

        <center>
            <input type="submit" name="submit" value="登録"><br>
            <input type="submit" name="return" value="戻る">
        </center>
    </form>
<!--{/strip}-->
