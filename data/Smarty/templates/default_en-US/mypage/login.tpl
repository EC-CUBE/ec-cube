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

<div id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <div id="undercolumn_login">
        <form name="login_mypage" id="login_mypage" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />

        <div class="login_area">
            <h3>Customers who have already completed member registration</h3>
            <p class="inputtext">If you are a member, please log in with your e-mail address and password.</p>
            <div class="inputbox">
                <dl class="formlist clearfix">
                    <!--{assign var=key value="login_email"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <dt>E-mail address&nbsp;:</dt>
                    <dd>
                        <input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" />
                        <p class="login_memory">
                            <!--{assign var=key value="login_memory"}-->
                            <input type="checkbox" name="<!--{$key}-->" value="1"<!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" />
                            <label for="login_memory">Remember e-mail address</label>
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
                * Forgot your password? Please click <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','460'); return false;" target="_blank">here</a>.<br />
                * Forgot your e-mail address? Please view the  <a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->">Inquiry page</a>.
            </p>
        </div>

        <div class="login_area">
            <h3>Customers who have not yet registered</h3>
            <p class="inputtext">After you register as a member, you can access MY page.<br />
                By simply logging in, you can enjoy shopping without having to enter your name and address each time.
            </p>
            <div class="inputbox">
                <div class="btn_area">
                    <ul>
                        <li>
                            <a class="bt02 bt_wide" href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php">Register as a member</a>
                        </li>
                    </ul>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
