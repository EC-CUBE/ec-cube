<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title>EC�����ȴ����ԥڡ���</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<div align="center">
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td class="mainbg">
			<!--����Ͽ�ơ��֥뤳������-->
			<table width="680" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
						<table width="660" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="668" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="640" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="600" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->���С���Ͽ/�Խ�</span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="640" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="640" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="640" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">�ܵ�̾</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.name|escape|default:"��̤�����"}--></td>
											<td bgcolor="#f0f0f0" width="110">�ܵ�̾�ʥ��ʡ�</td>
											<td bgcolor="#ffffff" width="249"><!--{$list_data.kana|escape|default:"��̤�����"}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">��ƻ�ܸ�</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.pref_disp|default:"��̤�����"}--></td>
											<td bgcolor="#f0f0f0" width="110">TEL</td>
											<td bgcolor="#ffffff" width="249"><!--{$list_data.tel|escape|default:"��̤�����"}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">����</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.sex_disp|default:"��̤�����"}--></td>
											<td bgcolor="#f0f0f0" width="110">������</td>
											<td bgcolor="#ffffff" width="249"><!--{if $list_data.birth_month}--><!--{$list_data.birth_month|escape}-->��<!--{else}-->��̤�����<!--{/if}--></td>				
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">�ۿ�����</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.htmlmail_disp|escape|default:"��̤�����"}--></td>
											<td bgcolor="#f0f0f0" width="110">�������</td>
											<td bgcolor="#ffffff" width="199"><!--{if $list_data.buy_times_from}--><!--{$list_data.buy_times_from|escape}-->�� �� <!--{$list_data.buy_times_to|escape}-->��<!--{else}-->��̤�����<!--{/if}--></td>
										</tr>
										<tr class="fs12n">
										<!--{*����������Ǥ��ʤ�
											<td bgcolor="#f0f0f0" width="110">����</td>
											<td bgcolor="#ffffff" width="198">
											<!--{$list_data.customer|escape|default:"���٤�"}-->
											</td>
										*}-->
											<td bgcolor="#f0f0f0" width="110">�������ʥ�����</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.buy_product_code|escape|default:"��̤�����"}--></td>
											<td bgcolor="#f0f0f0" width="110">�������</td>
											<td bgcolor="#ffffff" width="199"><!--{if $list_data.buy_total_from}--><!--{$list_data.buy_total_from|escape}-->�� �� <!--{$list_data.buy_total_to|escape}-->��<!--{else}-->��̤�����<!--{/if}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">�᡼�륢�ɥ쥹</td>
											<td bgcolor="#ffffff" width="507" colspan="3"><!--{$list_data.email|escape|default:"��̤�����"}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">����</td>
											<td bgcolor="#ffffff" width="507" colspan="3"><!--{$list_data.job_disp|escape|default:"��̤�����"}--></td>
										</tr>
							
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">��ǯ����</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<!--{if $list_data.b_start_year}-->
												<!--{$list_data.b_start_year}-->ǯ<!--{$list_data.b_start_month}-->��<!--{$list_data.b_start_day}-->��&nbsp;��&nbsp;<!--{$list_data.b_end_year}-->ǯ<!--{$list_data.b_end_month}-->��<!--{$list_data.b_end_day}-->��
											<!--{else}-->��̤�����<!--{/if}-->
											</td>
										</tr>	
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">��Ͽ��</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<!--{if $list_data.start_year}-->
												<!--{$list_data.start_year}-->ǯ<!--{$list_data.start_month}-->��<!--{$list_data.start_day}-->��&nbsp;��&nbsp;<!--{$list_data.end_year}-->ǯ<!--{$list_data.end_month}-->��<!--{$list_data.end_day}-->��
											<!--{else}-->��̤�����<!--{/if}-->
											</td>
										</tr>			
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">�ǽ�������</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<!--{if $list_data.buy_start_year}-->
												<!--{$list_data.buy_start_year}-->ǯ<!--{$list_data.buy_start_month}-->��<!--{$list_data.buy_start_day}-->��&nbsp;��&nbsp;<!--{$list_data.buy_end_year}-->ǯ<!--{$list_data.buy_end_month}-->��<!--{$list_data.buy_end_day}-->��
											<!--{else}-->��̤�����<!--{/if}-->	
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">��������̾</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.buy_product_name|escape|default:"��̤�����"}--></td>
											<td bgcolor="#f0f0f0" width="110">���ƥ���</td>
											<td bgcolor="#ffffff" width="199"><!--{$list_data.category_name|escape|default:"��̤�����"}--></td>
										</tr>
									</table>
	
									<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="638" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="button" name="close" value="������ɥ����Ĥ���" onclick="window.close();" /></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="640" height="8" alt=""></td>
										</tr>
									</table>
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="668" height="14" alt=""></td>
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
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
</div>

</body>
</html>