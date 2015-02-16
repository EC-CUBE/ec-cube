<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(確認ページ)"}-->

<article id="popup_pw_secret" class="window_area">
    <h1 class="title">パスワードの再発行 秘密の質問の確認</h1>
    <p class="information">
        ご登録時に入力した下記質問の答えを入力して「次へ」ボタンをクリックしてください。<br />
        ※下記質問の答えをお忘れになられた場合は、<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentitiy'}--></a>までご連絡ください。<br />
    </p>
    <p class="message">
        【重要】新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</p>
    </p>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="secret_check" />

        <!--{foreach key=key item=item from=$arrForm}-->
            <!--{if $key ne 'reminder_answer'}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

        <div id="completebox">
        <p>
            <span class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
            <!--{$arrReminder[$arrForm.reminder]}-->：&nbsp;<!--★答え入力★--><input type="text" name="reminder_answer" value="" class="box300" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->" /></p>
            <span class="attention"><!--{$errmsg}--></span>
        </div>
        <div class="btn_area">
            <ul>
                <li><input type="submit" class="btn btn-success" value="次へ" name="next" id="next" /></li>
            </ul>
        </div>
    </form>
</article>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->