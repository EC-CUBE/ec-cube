<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--����Ͽ�ơ��֥뤳������-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�᡼��ƥ�ץ졼�Ȱ���</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="120">������</td>
										<td width="333">�ƥ�ץ졼��̾</td>
										<td width="70">�᡼�����</td>
										<td width="40">�Խ�</td>
										<td width="40">���</td>
										<td width="70">�ץ�ӥ塼</td>
									</tr>
									<!--{section name=data loop=$list_data}-->
									<tr bgcolor="#ffffff" class="fs12n">
										<td width="120" align="center"><!--{$list_data[data].create_date|escape}--></td>
										<td width="333"><!--{$list_data[data].template_name|escape}--></td>
										<!--{assign var=type value=$list_data[data].mail_method|escape}-->
										<td width="70" align="center"><!--{if $list_data[data].send_type eq 0}-->�ѥ�����<!--{else}-->����<!--{/if}--></td>
										<td width="40" align="center"><a href="./mail.php?mode=edit&template_id=<!--{$list_data[data].template_id}-->">�Խ�</a></td>
										<td width="40" align="center">
										<!--{if $list_data[data].template_id > 2}-->
										<a href="" onclick="fnDelete('<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&id=<!--{$list_data[data].template_id}-->'); return false;">���</a>
										<!--{else}-->(����)<!--{/if}-->
										</td>
										<td width="70" align="center"><!--{if $list_data[data].send_type eq 0}--><a href="" onclick="win03('./preview.php?mode=preview&id=<!--{$list_data[data].template_id}-->','preview','750','550'); return false;" target="_blank"><!--{else}--><a href="" onclick="win03('./preview.php?id=<!--{$list_data[data].template_id}-->','preview','650','700'); return false;" target="_blank"><!--{/if}-->�ץ�ӥ塼</a></td>
									</tr>
									<!--{/section}-->
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<input type="button" name="subm" onclick="location.href='./template_input.php'" value="��������" />��
													<!-- �ȣԣ̺ͣ������������ɤ���α �ʼ�����ȯ��
													<input type="button" name="subm" onclick="location.href='./htmlmail.php'" value="HTML�ƥ�ץ졼�Ⱥ�������������" />
													-->
												</td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--�ᥤ�󥨥ꥢ-->
			</table>
			<!--����Ͽ�ơ��֥뤳���ޤ�-->
		</td>
	</tr>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->

