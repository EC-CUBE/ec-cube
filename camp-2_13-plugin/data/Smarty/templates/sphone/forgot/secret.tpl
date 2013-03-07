<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

<section id="windowcolumn">
    <h2 class="title">パスワードを忘れた方</h2>
    <div class="intro">
        <p>ご登録時に入力した下記質問の答えを入力して「次へ」ボタンをクリックしてください。</p>
    </div>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="secret_check" />
        <!--{foreach key=key item=item from=$arrForm}-->
            <!--{if $key ne 'reminder_answer'}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->
        <div class="window_area clearfix">

            <p>
                <span class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
                <!--{$arrReminder[$arrForm.reminder]}--><br />

                <!--★答え入力★-->
                <input type="text" name="reminder_answer"
                    value="" class="boxLong text data-role-none"
                    style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->" /><br />
                <span class="attention"><!--{$errmsg}--></span>
            </p>

            <hr />

            <p>※質問の答えをお忘れになられた場合は、<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentitiy'}--></a>までご連絡ください。</p>

        </div>

        <p class="btn_area"><input type="submit" class="btn data-role-none" value="次へ" name="next" id="next" /></p>
    </form>
</section>
