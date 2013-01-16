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

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="If you have forgotten your password (confirmation page)"}-->

<div id="window_area">
    <h2>Receive new password   Confirmation of secret question</h2>
    <p class="information">
       Please respond to the security question you set during registration. When finished, click the "Next" button.<br />
        * If you have forgotten the answer, contact <a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentitiy'}--></a>.<br />
    </p>
    <p class="message">
        [Important]  You will be receiving a new password.  You will no longer be able to use your old password.</p>
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
        <!--{$arrReminder[$arrForm.reminder]}-->:&nbsp;<!--★答え入力★--><input type="text" name="reminder_answer" value="" class="box300" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->" /></p>
        <span class="attention"><!--{$errmsg}--></span>
    </div>
    <div class="btn_area">
        <ul>
            <li><button class="bt02">Next</button></li>
		</ul>
    </div>
    </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
