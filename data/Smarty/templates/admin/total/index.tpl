<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="200">
			<!-- ���֥ʥ� -->
			<!--{include file=$tpl_subnavi}-->
		</td>
		<td class="mainbg">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�--><!--{$tpl_subtitle}--></span></td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						<!--�����������ơ��֥뤳������-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<form name="search_form1" id="search_form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
							<input type="hidden" name="mode" value="search">
							<input type="hidden" name="form" value="1">
							<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
							<input type="hidden" name="type" value="<!--{$smarty.post.type}-->">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">���ٽ���</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
									<span class="red"><!--{$arrErr.search_startyear_m}--></span>
									<span class="red"><!--{$arrErr.search_endyear_m}--></span>		
									<select name="search_startyear_m"  style="<!--{$arrErr.search_startyear_m|sfGetErrorColor}-->">
									<!--{html_options options=$arrYear selected=$arrForm.search_startyear_m.value}-->
									</select>ǯ
									<select name="search_startmonth_m" style="<!--{$arrErr.search_startyear_m|sfGetErrorColor}-->">
									<!--{html_options options=$arrMonth selected=$arrForm.search_startmonth_m.value}-->
									</select>���� ��<!--{if $smarty.const.CLOSE_DAY == 31}-->��<!--{else}--><!--{$smarty.const.CLOSE_DAY}--><!--{/if}-->�������
									��<input type="submit" name="subm" value="���٤ǽ��פ���" />
								</td>
							</tr>
							</form>
							<form name="search_form2" id="search_form2" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
							<input type="hidden" name="mode" value="search">
							<input type="hidden" name="form" value="2">
							<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
							<input type="hidden" name="type" value="<!--{$smarty.post.type}-->">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">���ֽ���</td>
								<td bgcolor="#ffffff" width="607" colspan="3">
									<span class="red"><!--{$arrErr.search_startyear}--></span>
									<span class="red"><!--{$arrErr.search_endyear}--></span>		
									<select name="search_startyear"  style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrYear selected=$arrForm.search_startyear.value}-->
									</select>ǯ
									<select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_startmonth.value}-->
									</select>��
									<select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_startday.value}-->
									</select>����
									<select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrYear selected=$arrForm.search_endyear.value}-->
									</select>ǯ
									<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_endmonth.value}-->
									</select>��
									<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_endday.value}-->
									</select>��
									��<input type="submit" name="subm" value="���֤ǽ��פ���" />
								</td>
							</tr>
							</form>

						</table>
						<!--�����������ơ��֥뤳���ޤ�-->
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
		</td>
	</tr>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->


<!--{if count($arrResults) > 0}-->
	<!--����������̰�������-->
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="type" value="<!--{$arrForm.type.value}-->">
	<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
	<!--{foreach key=key item=item from=$arrHidden}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->	
		<tr><td colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	</table>
	
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td bgcolor="#f0f0f0" align="center">
	
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#f0f0f0">
						<!--�������ɽ���ơ��֥�-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr><td align="center">
								<!--{* �����ȥ�ɽ�� *}-->
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td><hr noshade size="1" color="#f0f0f0" /></td></tr>
									<tr><td height="5"></td></tr>
									<tr>
										<!--{include file=$tpl_graphsubtitle}-->
									</tr>
								</table>
						
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<td align = center>
										<input type="button" name="subm" value="������̤�CSV���������" onclick="fnModeSubmit('csv','','');" />
										<!--{* PDF��ǽ�ϼ�����ȯ���ɲ�ͽ��
										 <input type="button" name="subm" value="������̤�PDF���������" onclick="fnModeSubmit('pdf','','');" />
										*}-->
										</td>
									</tr>
								</table>

								<!--{* �����ɽ�� *}-->		
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="15"></td></tr>
									<tr>
										<td align="center">
											<!--{* <img src="<!--{$tpl_image}-->?<!--{$cashtime}-->" alt="�����"> *}-->
											<img src="<!--{$smarty.server.PHP_SELF|escape}-->?draw_image=true&mode=search&page=<!--{$smarty.post.page}-->&search_startyear_m=<!--{$smarty.post.search_startyear_m}-->&search_startmonth_m=<!--{$smarty.post.search_startmonth_m}-->&search_startyear=<!--{$smarty.post.search_startyear}-->&search_startmonth=<!--{$smarty.post.search_startmonth}-->&search_startday=<!--{$smarty.post.search_startday}-->&search_endyear=<!--{$smarty.post.search_endyear}-->&search_endmonth=<!--{$smarty.post.search_endmonth}-->&search_endday=<!--{$smarty.post.search_endday}-->" alt="�����">
										</td>
									</tr>
									<tr><td height="15"></td></tr>
								</table>
								<!--{* �����ɽ�� *}-->		
								<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc">
										<!--��������̥ơ��֥뤳������-->
										<!--{include file=$tpl_page_type}-->
										<!--��������̥ơ��֥뤳���ޤ�-->
										</td>
									</tr>
								</table>
								<!--��MAIN CONTENTS-->
								</td>
							</tr>
						</table>
						<!--�������ɽ���ơ��֥�-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</form>
	</table>
<!--���������ɽ�����ꥢ�����ޤ�-->
<!--{else}-->
	<!--{if $smarty.post.mode == 'search'}-->
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="type" value="<!--{$arrForm.type.value}-->">
	<input type="hidden" name="page" value="<!--{$arrForm.page.value}-->">
	<!--{foreach key=key item=item from=$arrHidden}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->	
		<tr><td colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	</table>	
	<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td bgcolor="#f0f0f0" align="center">
				<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#f0f0f0">
						<!--�������ɽ���ơ��֥�-->
						<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr><td align="center">	
								<!--{* �����ȥ�ɽ�� *}-->
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td><hr noshade size="1" color="#f0f0f0" /></td></tr>
									<tr><td height="5"></td></tr>
									<tr>
											<!--{include file=$tpl_graphsubtitle}-->
									</tr>
								</table>
								<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="10"></td></tr>
									<tr class="fs12"><td align="center" height="200">��������ǡ����Ϥ���ޤ���</td></tr>
								</table>
								</td>
							</tr>
						</table>
						<!--�������ɽ���ơ��֥�-->
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</form>
	</table>
	<!--{/if}-->
<!--{/if}-->

<!--����������̰�������-->		
