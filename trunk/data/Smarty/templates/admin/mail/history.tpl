<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP}-->">
<input type="hidden" name="search_pageno" value="">
<input type="hidden" name="mode" value="">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�ۿ�����</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="110">�ۿ����ϻ���</td>
										<td width="350" rowspan="2">Subject</td>
										<td width="40" rowspan="2">�ץ�ӥ塼</td>
										<td width="40" rowspan="2">�ۿ����</td>
										<td width="86">�ۿ�ͽ����</td>
										<td width="37" rowspan="2">���</td>
									</tr>
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td>�ۿ���λ����</td>
										<td>�ۿ����</td>
									</tr>
									<!--{section name=cnt loop=$arrDataList}-->
									<tr bgcolor="#ffffff" class="fs12n">
										<td align="center"><!--{$arrDataList[cnt].start_date|sfDispDBDate|escape}--></td>
										<td rowspan="2"><!--{$arrDataList[cnt].subject|escape}--></td>
										<td align="center" rowspan="2"><a href="./preview.php?send_id=<!--{$arrDataList[cnt].send_id|escape}-->" target="_blank">��ǧ</a></td>
										<td align="center" rowspan="2"><a href="#" onclick="win03('./index.php?mode=query&send_id=<!--{$arrDataList[cnt].send_id|escape}-->','query','720','420'); return false;">��ǧ</a></td>
										<td align="center"><!--{$arrDataList[cnt].send_count|escape}--></td>
										<td align="center" rowspan="2"><a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&send_id=<!--{$arrDataList[cnt].send_id|escape}-->" onclick="return window.confirm('�ۿ�����������Ƥ⵹�����Ǥ��礦��');">���</a></td>
									</tr>
									<tr bgcolor="#ffffff" class="fs12n">
										<td align="center"><!--{$arrDataList[cnt].end_date|sfDispDBDate|escape}--></td>
										<td align="center"><!--{$arrDataList[cnt].complete_count|escape}--></td>
									</tr>
									<!--{/section}-->
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
