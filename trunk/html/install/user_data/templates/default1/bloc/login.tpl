<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--�������󤳤�����-->
<!--{if $smarty.post.url == ""}-->
	<!--{assign var=url value="`$smarty.server.REQUEST_URI`"}-->
<!--{else}-->
	<!--{assign var=url value="`$smarty.post.url`"}-->
<!--{/if}-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="login_form" id="login_form" method="post" action="<!--{$smarty.const.URL_DIR}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
<input type="hidden" name="mode" value="login">
<input type="hidden" name="url" value="<!--{$url|escape}-->">
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/side/title_login.jpg" width="166" height="35" alt="������"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<!--������ե�����-->
		<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td height="10"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="50" height="1" alt=""></td>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="96" height="1" alt=""></td>
			</tr>
			<!--{if $tpl_login}-->
				<tr>
					<td align="center" colspan="3" class="fs12">�褦���� <br> <!--{$tpl_name1|escape}-->��<!--{$tpl_name2|escape}--> ��<br />
					����ݥ���ȡ�<span class="redst"> <!--{$tpl_user_point|number_format|default:0}--> pt</span></td>
				</tr>
				<!--{if !$tpl_disable_logout}-->
				<tr>
					<td colspan="3" align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/header/logout.gif" width="44" height="21" alt="��������" /></a></td>
				</tr>
				<!--{/if}-->
			<!--{else}-->
				<tr>
					<td><img src="<!--{$smarty.const.URL_DIR}-->img/side/icon_mail.gif" width="40" height="21" alt="�᡼�륢�ɥ쥹"></td>
					<td><input type="text" name="login_email" value="<!--{$tpl_login_email|escape}-->" size="10" class="box10" /></td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td><img src="<!--{$smarty.const.URL_DIR}-->img/side/icon_pw.gif" width="40" height="22" alt="�ѥ����"></td>
					<td><input type="password" name="login_pass" size="12" class="box12" /></td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td colspan="2" class="fs10n" align="right"><a href="<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php" onclick="win01('<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php','forget','600','400'); return false;" target="_blank">�ѥ���ɤ�˺�줿���Ϥ�����</a></td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td width="50"><input type="checkbox" name="login_memory" value="1" <!--{$tpl_login_memory|sfGetChecked:1}-->/><img src="<!--{$smarty.const.URL_DIR}-->img/header/memory.gif" width="18" height="9" alt="����" /></td>
					<td align="center"><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/side/button_login_on.gif',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/side/button_login.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/side/button_login.gif" width="51" height="22" alt="������" border="0" name="subm"></td>
				</tr>
			<!--{/if}-->
		</table>
		<!--������ե�����-->
		</td>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3" height="14"><img src="<!--{$smarty.const.URL_DIR}-->img/side/flame_bottom03.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</form>
</table>
<!--�������󤳤��ޤ�-->
