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

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="If you have forgotten your password (input page)"}-->

<div id="window_area">
    <h2>Retrieve Password</h2>
    <p class="information">Please enter you registered e-mail address and name. When finished, click the 'Next' button.<br /> <span class="attention">*You will receive a new password and you will no longer be able to use your old password.</span></p>
    <form action="?" method="post" name="form1">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="mail_check" />

    <div id="forgot">
        <div class="contents">
            <div class="mailaddres">
                <p class="attention"><!--{$arrErr.email}--></p>
                <p>
                    E-mail address:&nbsp;
                    <input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" class="box300" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" />
                </p>
            </div>
            <div class="name">
                <p class="attention">
                    <!--{$arrErr.name01}--><!--{$arrErr.name02}-->
                    <!--{$errmsg}-->
                </p>
                <p>
                    Last name&nbsp;<input type="text" class="box120" name="name01" value="<!--{$arrForm.name01|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->;" />
                    First name&nbsp;<input type="text" class="box120" name="name02" value="<!--{$arrForm.name02|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->;" />
                </p>
            </div>
        </div>
    </div>
    <div class="btn_area">
        <ul>
            <li><button class="bt02">Next</button></li>
        </ul>
    </div>
    </form>
</div>
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->

