<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<div class="bloc_outer">
    <h2><img src="<!--{$TPL_URLPATH}-->img/icon/ico_block_login.gif" width="20" height="20" alt="*" class="title_icon" />
        ログイン</h2>
    <div id="loginarea" class="bloc_body">
        <form name="login_form" id="login_form" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
            <input type="hidden" name="mode" value="login" />
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="url" value="<!--{$smarty.server.PHP_SELF|h}-->" />
            <div id="login">
                <!--{if $tpl_login}-->
                <p>ようこそ<br />
                    <!--{$tpl_name1|h}--> <!--{$tpl_name2|h}--> 様<br />
                    <!--{if $smarty.const.USE_POINT !== false}-->
                        所持ポイント：<span class="price"> <!--{$tpl_user_point|number_format|default:0}--> pt</span>
                    <!--{/if}-->
                </p>
                    <!--{if !$tpl_disable_logout}-->
                <p class="btn">
                    <a href="?" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;">
                        <img src="<!--{$TPL_URLPATH}-->img/button/btn_block_logout.gif" width="61" height="22" alt="ログアウト" /></a>
                </p>
             </div>
                    <!--{/if}-->
                <!--{else}-->
                <p><img src="<!--{$TPL_URLPATH}-->img/icon/ico_mail.gif" width="40" height="21" alt="メールアドレス" /><input type="text" name="login_email" class="box96" value="<!--{$tpl_login_email|h}-->" style="ime-mode: disabled;"/></p>
                <p><img src="<!--{$TPL_URLPATH}-->img/icon/ico_password.gif" width="40" height="22" alt="パスワード" /><input type="password" name="login_pass" class="box96" /></p>
            </div>
                <p class="mini">
                    <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','400'); return false;" target="_blank">パスワードを忘れた方はこちら</a>
                </p>
                <p class="mini">
                    <input type="checkbox" name="login_memory" id="login_memory" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> />
                   <label for="login_memory">記憶</label>
                </p>
                <p class="btn">
                    <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_block_login_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_block_login.gif',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_block_login.gif" width="61" height="22" alt="ログイン" name="subm" />
                </p>
                <!--{/if}-->
                <!--ログインフォーム-->
        </form>
    </div>
</div>
