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

<div id="undercolumn">
    <div id="undercolumn_login">
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <form name="member_form" id="member_form" method="post" action="?" onsubmit="return fnCheckLogin('member_form')">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />

        <div class="login_area">
            <h3>Customers who have already completed member registration</h3>
            <p class="inputtext">If you are a member, log in by using the e-mail address and password used during registration</p>
            <div class="inputbox">
                <dl class="formlist clearfix">
                    <!--{assign var=key value="login_email"}-->
                    <dt>E-mail address&nbsp;:</dt>
                    <dd>
                        <!--{if strlen($arrErr[$key]) >= 1}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                        <input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" />
                        <p class="login_memory">
                            <!--{assign var=key value="login_memory"}-->
                            <input type="checkbox" name="<!--{$key}-->" value="1"<!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" />
                            <label for="login_memory">Have the computer memorize your e-mail address</label>
                        </p>
                    </dd>
                </dl>
                <dl class="formlist clearfix">
                    <dt>
                        <!--{assign var=key value="login_pass"}-->
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                        Password&nbsp;:
                    </dt>
                    <dd>
                        <input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box300" />
                    </dd>
                </dl>
                <div class="btn_area">
                    <ul>
                        <li>
							<button class="bt02">Login</button>
                        </li>
                    </ul>
                </div>
            </div>
            <p>
                * If you have forgotten your password, request for a password to be reissued from <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','460'); return false;" target="_blank">here</a>.<br />
                * If you have forgotten your e-mail address, inquire from <a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->">the Inquiry page</a>.
            </p>
        </div>
        </form>
        <div class="login_area">
            <h3>Customers who have not yet registered as a member</h3>


            <h4>Register as a member</h4>
            <p class="inputtext">When you register as a member, you can use the convenient MY page.<br />
                By simply logging in, you can enjoy shopping without having to enter your name and address each time.
            </p>
            <div class="inputbox">
                <div class="btn_area">
                    <ul>
                        <li>
                            <a class="bt02" href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php">Register as a member</a>
                        </li>
                    </ul>
                </div>
            </div>

            <h4>Proceed to purchase items without registering as a member</h4>
            <p class="inputtext">If you want to continue the purchasing procedure without registering as a member, proceed from below.</p>
            <form name="member_form2" id="member_form2" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="nonmember" />
            <div class="inputbox">
                <div class="btn_area">
                    <ul>
                        <li>
							<button class="bt02">Purchase without an account</button>
                        </li>
                    </ul>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
