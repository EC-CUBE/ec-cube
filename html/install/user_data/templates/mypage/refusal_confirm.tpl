<!--{*
 * Copyright (c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="complete">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MY�ڡ���"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
				<!--��NAVI-->
					<!--{include file=$tpl_navi}-->
				<!--��NAVI-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><!--�������ȥ�--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle04.gif" width="515" height="32" alt="����³��"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td align="center" bgcolor="#cccccc">
						<table width="505" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td align="center" bgcolor="#ffffff">
								<!--ɽ����������-->
								<table width="465" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="55"></td></tr>
									<tr>
										<td align="center" class="fs12n">����³����¹Ԥ��Ƥ������Ǥ��礦����</td>
									</tr>
									<tr><td height="55"></td></tr>
									<tr>
										<td align="center">
											<a href="./refusal.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/mypage/b_no_on.gif','refusal_no');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/mypage/b_no.gif','refusal_no');"><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/b_no.gif" width="180" height="30" alt="����������񤷤ޤ���" name="refusal_no" id="refusal_no" /></a>��
											<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/mypage/b_yes_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/mypage/b_yes.gif',this);" src="<!--{$smarty.const.URL_DIR}-->img/mypage/b_yes.gif" width="180" height="30" alt="�Ϥ����Ϥ�����񤷤ޤ�" name="refusal_yes" id="refusal_yes" />
										</td>
									</tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td class="fs10"><span class="red">������³������λ���������ǡ�������¸����Ƥ����������䡢���Ϥ������ξ���Ϥ��٤Ƥʤ��ʤ�ޤ��ΤǤ���դ���������</span></td>
									</tr>
									<tr><td height="30"></td></tr>
								</table>
								<!--ɽ�������ޤ�-->
								</td>
							</tr>
							<tr><td height="5"></td></tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</form>
</table>
<!--��CONTENTS-->


