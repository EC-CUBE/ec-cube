<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--�������Ͽ�����ѤߤΤ�����-->
<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#cccccc">
		<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="5"></td></tr>
			<tr>
				<td align="center" bgcolor="#ffffff">
				<table width="604" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="13"></td></tr>
					<tr>
						<td><img src="/img/login/member.gif" width="202" height="16" alt="�����Ͽ�����ѤߤΤ�����"></td>
					</tr>
					<tr><td height="20"></td></tr>
				</table>
				<!--�����󤳤�����-->
				<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="member_form" id="member_form" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" onsubmit="return fnCheckLogin('member_form')">
				<input type="hidden" name="mode" value="login">
					<tr>
						<td class="fs12">��������ϡ���Ͽ�������Ϥ��줿�᡼�륢�ɥ쥹�ȥѥ���ɤǥ����󤷤Ƥ���������������</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td align="center" bgcolor="#f0f0f0">
						<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="20"></td></tr>
							<tr>
								<td><img src="/img/login/mailadress.gif" width="92" height="13" alt="�᡼�륢�ɥ쥹"></td>
								<td class="fs12">
									<span class="red"></span>
									<input type="text" name="login_email" value="" maxlength="" style="; ime-mode: disabled;" size="40" class="box40" />
								</td>
							</tr>
							<tr>
								<td align="right"></td>
								<td class="fs10n"><input type="checkbox" name="login_memory" value="1" /><label for="memory">����᡼�륢�ɥ쥹�򥳥�ԥ塼�����˵���������</label></td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td><img src="/img/login/password.gif" width="92" height="13" alt="�ѥ����"></td>
								<td class="fs12">
									<span class="red"></span>
									<input type="password" name="login_pass" maxlength="" style="" size="40" class="box40" />
								</td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<input type="image" onmouseover="chgImgImageSubmit('/img/login/b_login_on.gif',this)" onmouseout="chgImgImageSubmit('/img/login/b_login.gif',this)" src="/img/login/b_login.gif" width="140" height="30" alt="������" name="log" id="log" />
						</td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs10">�ѥ���ɤ�˺�줿����<a href="/forgot/index.php" onclick="win01('/forgot/index.php','forget','600','400'); return false;" target="_blank">������</a>����ѥ���ɤκ�ȯ�Ԥ�ԤäƤ���������<br>
						�᡼�륢�ɥ쥹��˺�줿���ϡ�������Ǥ�����<a href="/contact/index.php">���䤤��碌�ڡ���</a>���餪�䤤��碌����������</td>
					</tr>
					<tr><td height="20"></td></tr>
				</form>
				</table>
				<!--�����󤳤��ޤ�-->
				</td>
			</tr>
			<tr><td height="5"></td></tr>
		</table>
		</td>
	</tr>
	<tr><td height="20"></td></tr>
</table>
<!--�������Ͽ�����ѤߤΤ�����-->
