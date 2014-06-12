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
    <!--{if $errmsg}-->
        <font color="#ff0000"><!--{$errmsg}--></font><br>
    <!--{/if}-->

    ご登録時に入力した下記質問の答えを入力して「次へ」ボタンをクリックしてください。<br>
    <br>
    ※下記質問の答えをお忘れになられた場合は、<a href="mailto:<!--{$arrSiteInfo.email02|h}-->"><!--{$arrSiteInfo.email02|h}--></a>までご連絡ください。<br>
    <br>
    <form action="?" method="post">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="secret_check">
        <!--{foreach key=key item=item from=$arrForm}-->
            <!--{if $key ne 'reminder_answer'}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->">
            <!--{/if}-->
        <!--{/foreach}-->

        <!--{$arrReminder[$arrForm.reminder]|h}-->：<input type="text" name="reminder_answer" value="" size="40"><br>
        <font color="#FF0000"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></font><br>

        <center><input type="submit" value="次へ" name="next"></center>
    </form>
<!--{/strip}-->
