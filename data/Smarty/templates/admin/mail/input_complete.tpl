<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<!--{foreach key=key item=val from=$arrHidden}-->	
	<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->">
<!--{/foreach}-->
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
										<!--{if $arrBlaynEngine.now_version > 0 && $arrBlaynEngine.blayn_ip > 0}--><!--{assign var=key value="4"}--><!--{else}--><!--{assign var=key value="3"}--><!--{/if}-->
										<td colspan="<!--{$key}-->"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�ۿ����ꡧ�ۿ���������</span><!--{if $arrBlaynEngine.now_version > 0 && $arrBlaynEngine.blayn_ip > 0}--></td><td align="right"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/blayn_icon.jpg"><!--{/if}--></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="<!--{$key}-->"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="<!--{$key}-->"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr>
										<td bgcolor="#ffffff" width="547" class="fs12n">
											�᡼���ۿ����꤬��λ���ޤ������������˥᡼���ۿ����Ϥޤ�ޤ���
											<br />�ۿ�����ˤ��ۿ����򤬤������������ޤ���
											<br /><br /><a href="./index.php">��³�������ꤹ��</a><br />
										</td>
									</tr>
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
													<input type="hidden" name="mode" value="template">
													<input type="button" name="subm02" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_back', '' )" value="�ƥ�ץ졼��������̤����" />
													��<input type="button" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' )" value="�ۿ���ͽ�󤹤�" <!--{$list_data.template_id|sfGetEnabled}-->/>
													</form>
													<form name="form2" id="form2" method="post" action="./preview.php" target="_blank">
													<input type="hidden" name="subject" value="<!--{$list_data.subject|escape}-->">
													<input type="hidden" name="body" value="<!--{$list_data.body|escape}-->">
													</form>
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

<!--��CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--��SUB NAVI-->
				<td class="fs12n"><!--{include file=$tpl_subnavi}--></td>
				<!--��SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--��MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>��HTML�᡼�����</strong></td>
			</tr>
			<tr><td height="20"</td></tr>
		</table>
		
		<!--����Ͽ�ơ��֥뤳������-->
		<table bgcolor="#ffffff" width="730" border="0" cellspacing="1" cellpadding="5" summary=" ">
			<tr>
				<td bgcolor="#ffffff" width="547" class="fs12n">
					�᡼���ۿ����꤬��λ���ޤ������������˥᡼���ۿ����Ϥޤ�ޤ���
					<br />�ۿ�����ˤ��ۿ����򤬤������������ޤ���
					<br /><br /><a href="./index.php">��³�������ꤹ��</a><br />
				</td>
			</tr>
		</table>
		<!--����Ͽ�ơ��֥뤳���ޤ�-->
		
		<br />


		<!--��MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
