<!--{*
 * Copyright (c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/login/title.jpg" width="700" height="40" alt="������"></td>
			</tr>
			<tr><td height="20"></td></tr>
		</table>
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
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/login/member.gif" width="202" height="16" alt="�����Ͽ�����ѤߤΤ�����"></td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						<!--�����󤳤�����-->
						<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<form name="login_mypage" id="login_mypage" method="post" action="./login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
						<input type="hidden" name="mode" value="login" >
							<tr>
								<td class="fs12">��������ϡ���Ͽ�������Ϥ��줿�᡼�륢�ɥ쥹�ȥѥ���ɤǥ����󤷤Ƥ���������</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td align="center" bgcolor="#f0f0f0">
								<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="20"></td></tr>
									<tr>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/login/mailadress.gif" width="92" height="13" alt="�᡼�륢�ɥ쥹"></td>
										<td>
											<!--{assign var=key value="mypage_login_email"}-->
											<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
										</td>
									</tr>
									<tr>
										<td align="right"></td>
										<!--{assign var=key value="mypage_login_memory"}-->
										<td class="fs10n"><input type="checkbox" name="<!--{$key}-->" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> /><label for="memory">����᡼�륢�ɥ쥹�򥳥�ԥ塼�����˵���������</label></td>
									</tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/login/password.gif" width="92" height="13" alt="�ѥ����"></td>
										<td>
											<!--{assign var=key value="mypage_login_pass"}-->
											<input type="password" name="<!--{$key}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
										</td>
									</tr>
									<tr><td height="20"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td height="20"></td></tr>
							<tr>
								<td align="center">
									<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/login/b_login_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/login/b_login.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/login/b_login.gif" width="140" height="30" alt="������" name="log" id="log" /></a>
								</td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td class="fs10">�ѥ���ɤ�˺�줿����<a href="<!--{$smarty.const.URL_DIR}-->forget/index.php" onclick="win01('/forgot/index.php','forget','600','400'); return false;" target="_blank">������</a>����ѥ���ɤκ�ȯ�Ԥ�ԤäƤ���������<br>
								�᡼�륢�ɥ쥹��˺�줿���ϡ�������Ǥ�����<a href="<!--{$smarty.const.URL_DIR}-->contact/index.php">���䤤��碌�ڡ���</a>���餪�䤤��碌����������</td>
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
		<!--���ޤ������Ͽ����Ƥ��ʤ�������-->
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
								<td><img src="<!--{$smarty.const.URL_DIR}-->img/login/guest.gif" width="247" height="16" alt="�ޤ������Ͽ����Ƥ��ʤ�������"></td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						
						<table width="530" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12">�����Ͽ�򤹤��������My�ڡ��������Ѥ��������ޤ���<br>
								�ޤ��������󤹤�����ǡ����̾���佻��ʤɤ����Ϥ��뤳�Ȥʤ����ࡼ���ˤ��㤤ʪ�򤪳ڤ��ߤ��������ޤ���</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td align="center" bgcolor="#f0f0f0">
								<table width="490" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/login/b_gotoentry_on.gif','b_gotoentry');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/login/b_gotoentry.gif','b_gotoentry');"><img src="<!--{$smarty.const.URL_DIR}-->img/login/b_gotoentry.gif" width="130" height="30" alt="�����Ͽ�򤹤�" border="0" name="b_gotoentry"></a>��
										</td>
									</tr>
									<tr><td height="20"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td height="20"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
		</table>
		<!--���ޤ������Ͽ����Ƥ��ʤ�������-->
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->

