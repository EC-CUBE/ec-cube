<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--{if $smarty.post.url == ""}-->
	<!--{assign var=url value="`$smarty.server.REQUEST_URI`"}-->
<!--{else}-->
	<!--{assign var=url value="`$smarty.post.url`"}-->
<!--{/if}-->

<form name="login_form" id="login_form" method="post" action="<!--{$smarty.const.URL_DIR}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
<input type="hidden" name="mode" value="login">
<input type="hidden" name="url" value="<!--{$url|escape}-->">
<tr>
	<td background="<!--{$smarty.const.URL_DIR}-->img/header/login_left.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="8" height="1" alt="" /></td>
	<td bgcolor="#eeeeee">
	<table width="268" cellspacing="0" cellpadding="0" summary=" ">
		<!--{if $tpl_login}-->
		<tr>
			<td align="center" colspan="3"><span class="fs12">ようこそ <!--{$tpl_name|escape}--> 様<br />
			現在の所持ポイント：</span><span class="red12st"> <!--{$tpl_user_point|default:0}--> pt</span></td>
			<!--{if !$tpl_disable_logout}-->
			<td width="50"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/header/logout.gif" width="44" height="21" alt="ログアウト" /></a></td>
			<!--{/if}-->
		</tr>
		<!--{else}-->
		<tr align="center">
			<td><img src="<!--{$smarty.const.URL_DIR}-->img/header/mailaddress.gif" width="62" height="9" alt="メールアドレス" /></td>
			<td><img src="<!--{$smarty.const.URL_DIR}-->img/header/password.gif" width="43" height="9" alt="パスワード" /></td>
			<td width="50"><input type="checkbox" name="login_memory" value="1" <!--{$tpl_login_memory|sfGetChecked:1}-->/><img src="<!--{$smarty.const.URL_DIR}-->img/header/memory.gif" width="18" height="9" alt="記憶" /></td>
		</tr>
		<tr align="center">
			<td><input type="text" name="login_email" value="<!--{$tpl_login_email|escape}-->" size="15" class="box15" /></td>
			<td><input type="password" name="login_pass" size="10" class="box10" /></td>
			<td><input type="image" name="subm" src="<!--{$smarty.const.URL_DIR}-->img/header/login.gif" width="44" height="21" alt="ログイン" /></a></td>
		</tr>
		<tr>
			<td colspan="2" class="fs10n" align="right"><a href="<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php" onclick="win01('<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php','forget','580','320'); return false;" target="_blank">パスワードを忘れた方はこちら</a></td>
		</tr>
		<!--{/if}-->
	</table>
	</td>
	<td background="<!--{$smarty.const.URL_DIR}-->img/header/login_right.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="8" height="1" alt="" /></td>
</tr>
</form>
