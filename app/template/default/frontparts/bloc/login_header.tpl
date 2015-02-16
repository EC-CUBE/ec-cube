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
<!--{if !$tpl_login}-->
<script type="text/javascript">//<![CDATA[
    $(function(){
        var $login_email = $('#block_header_login input[name=login_email]');

        if (!$login_email.val()) {
            $login_email
                .val('メールアドレス')
                .css('color', '#AAA');
        }

        $login_email
            .focus(function() {
                if ($(this).val() == 'メールアドレス') {
                    $(this)
                        .val('')
                        .css('color', '#000');
                }
            })
            .blur(function() {
                if (!$(this).val()) {
                    $(this)
                        .val('メールアドレス')
                        .css('color', '#AAA');
                }
            });

        $('#header_login_form').submit(function() {
            if (!$login_email.val()
                || $login_email.val() == 'メールアドレス') {
                if ($('#header_login_area input[name=login_pass]').val()) {
                    alert('メールアドレス/パスワードを入力してください。');
                }
                return false;
            }
            return true;
        });
    });
//]]></script>
<!--{/if}-->
<!--{strip}-->
<aside id="block_header_login" class="block_outer">
	<div class="block_inner cf">
		<div class="block_body cf">
		<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php"<!--{if !$tpl_login}--> onsubmit="return eccube.checkLoginFormInputted('header_login_form')"<!--{/if}-->>
			<input type="hidden" name="mode" value="login" />
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
			<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
			<!--{if $tpl_login}-->
				<p class="ecbtn">
					ようこそ <span class="user_name nowrap"><!--{$tpl_name1|h}--> <!--{$tpl_name2|h}--> 様</span>
					<!--{if $smarty.const.USE_POINT !== false}-->
						&nbsp;&nbsp;/&nbsp;&nbsp;<span class="nowrap">所持ポイント: <span class="point"> <!--{$tpl_user_point|n2s|default:0}--> pt</span></span>&nbsp;&nbsp;
					<!--{/if}-->
					<!--{if !$tpl_disable_logout}-->
						<input type="submit" class="btn btn-success btn-xs" src="<!--{$TPL_URLPATH}-->img/common/btn_header_logout.jpg" onclick="eccube.fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;" value="ログアウト" />
					<!--{/if}-->
				</p>
			<!--{else}-->
				<ul class="formlist cf">
					<li class="mail">
						<input type="email" class="box140" name="login_email" value="<!--{$tpl_login_email|h}-->" style="ime-mode: disabled;" placeholder="メールアドレス" title="メールアドレスを入力してください" />
					</li>
					<li class="login_memory">
						<label for="header_login_memory"><input type="checkbox" name="login_memory" id="header_login_memory" value="1" <!--{$tpl_login_memory|sfGetChecked:1}-->><span>記憶</span></label>
					</li>
					<li class="password">
						<input type="password" class="box100" name="login_pass" placeholder="パスワード" title="パスワードを入力してください">
					</li>
					<li class="ecbtn">
						<input type="submit" class="btn btn-success btn-xs" value="ログイン">
					</li>
					<li class="forgot">
						<a href="<!--{$smarty.const.HTTPS_URL}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="eccube.openWindow('<!--{$smarty.const.HTTPS_URL}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','400',{scrollbars:'no',resizable:'no'});return false;" target="_blank">パスワードを忘れた方</a>
					</li>
				</ul>
			<!--{/if}-->
		</form>
		</div>
	</div>
</aside>
<!--{/strip}-->
