<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" id="search_form" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�����������</span></td>
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
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">�����ֹ�</td>
								<td bgcolor="#ffffff" width="194">
									<!--{assign var=key1 value="search_order_id1"}-->
									<!--{assign var=key2 value="search_order_id2"}-->
									<span class="red12"><!--{$arrErr[$key1]}--></span>
									<span class="red12"><!--{$arrErr[$key2]}--></span>
									<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box6" />
									 �� 
									<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" />
								</td>
								<td bgcolor="#f2f1ec" width="110">�б�����</td>
								<td bgcolor="#ffffff" width="195">
									<!--{assign var=key value="search_order_status"}-->
									<span class="red12"><!--{$arrErr[$key]}--></span>
									<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
									<option value="">���򤷤Ƥ�������</option>
									<!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
									</select>
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">�ܵ�̾</td>
								<td bgcolor="#ffffff" width="194">
								<!--{assign var=key value="search_order_name"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />				
								</td>
								<td bgcolor="#f2f1ec" width="110">�ܵ�̾�ʥ��ʡ�</td>
								<td bgcolor="#ffffff" width="195">
								<!--{assign var=key value="search_order_kana"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />				
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">�᡼�륢�ɥ쥹</td>
								<td bgcolor="#ffffff" width="194">
									<!--{assign var=key value="search_order_email"}-->
									<span class="red12"><!--{$arrErr[$key]}--></span>
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />				
								</td>
								<td bgcolor="#f2f1ec" width="110">TEL</td>
								<td bgcolor="#ffffff" width="195">
									<!--{assign var=key value="search_order_tel"}-->
									<span class="red12"><!--{$arrErr[$key]}--></span>
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />				
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">��ǯ����</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<span class="red"><!--{$arrErr.search_sbirthyear}--></span>
									<span class="red"><!--{$arrErr.search_ebirthyear}--></span>		
									<select name="search_sbirthyear" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrBirthYear selected=$arrForm.search_sbirthyear.value}-->
									</select>ǯ
									<select name="search_sbirthmonth" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_sbirthmonth.value}-->
									</select>��
									<select name="search_sbirthday" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_sbirthday.value}-->
									</select>����
									<select name="search_ebirthyear" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrBirthYear selected=$arrForm.search_ebirthyear.value}-->
									</select>ǯ
									<select name="search_ebirthmonth" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_ebirthmonth.value}-->
									</select>��
									<select name="search_ebirthday" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_ebirthday.value}-->
									</select>��
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">����</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
								<!--{assign var=key value="search_order_sex"}-->
								<span class="red12"><!--{$arrErr[$key]}--></span>
								<!--{html_checkboxes name="$key" options=$arrSex selected=$arrForm[$key].value}-->
							</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">��ʧ��ˡ</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
								<!--{assign var=key value="search_payment_id"}-->
								<span class="red12"><!--{$arrErr[$key]|escape}--></span>
								<!--{html_checkboxes name="$key" options=$arrPayment|escape selected=$arrForm[$key].value}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">��Ͽ��������</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<span class="red"><!--{$arrErr.search_startyear}--></span>
									<span class="red"><!--{$arrErr.search_endyear}--></span>		
									<select name="search_startyear"  style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
									<option value="">----</option>
									<!--{html_options options=$arrRegistYear selected=$arrForm.search_startyear.value}-->
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
									<!--{html_options options=$arrRegistYear selected=$arrForm.search_endyear.value}-->
									</select>ǯ
									<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.search_endmonth.value}-->
									</select>��
									<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
									<option value="">--</option>
									<!--{html_options options=$arrDay selected=$arrForm.search_endday.value}-->
									</select>��
								</td>
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="110">�������</td>
								<td bgcolor="#ffffff" width="499" colspan="3">
									<!--{assign var=key1 value="search_total1"}-->
									<!--{assign var=key2 value="search_total2"}-->
									<span class="red12"><!--{$arrErr[$key1]}--></span>
									<span class="red12"><!--{$arrErr[$key2]}--></span>
									<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box6" />
									
									�� �� 
									<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" />
									��
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
										<td class="fs12n">�������ɽ�����
											<!--{assign var=key value="search_page_max"}-->
											<span class="red12"><!--{$arrErr[$key]}--></span>
											<select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
											<!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
											</select> ��
										</td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="10" height="1" alt=""></td>
										<td><input type="image" name="subm" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_search.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_search.jpg" width="123" height="24" alt="���ξ��Ǹ�������" border="0" ></td>
									</tr>
								</table>
								</td>
								<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
							</tr>
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
</form>	
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->						

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete') }-->

<!--����������̰�������-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="order_id" value="">		
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
	<tr><td colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	<tr bgcolor="cbcbcb">
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/search_left.gif" width="19" height="22" alt=""></td>
				<td>
				<!--�������-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_top_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">������̰�����<span class="reselt"><!--������̿�--><!--{$tpl_linemax}-->��</span>&nbsp;���������ޤ�����</td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_bottom_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--�������-->
				<!--{if $smarty.const.ADMIN_MODE == '1'}-->
				<input type="button" name="subm" value="������̤򤹤٤ƺ��" onclick="fnModeSubmit('delete_all','','');" />
				<!--{/if}-->
				</td>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="#" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/contents/btn_csv.jpg','btn_csv');"  onclick="fnModeSubmit('csv','','');" ><img src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="8" height="1" alt=""></td>
				<td><a href="../contents/csv.php?tpl_subno_csv=order"><span class="fs12n"> >> CSV���Ϲ������� </span></a></td>
			</tr>
		</table>
		</td>
		<td align="right">
			<!--{include file=$tpl_pager}-->
		</td>									
	</tr>
	<tr><td bgcolor="cbcbcb" colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>

<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#f0f0f0" align="center">

		<!--{if count($arrResults) > 0}-->		

			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<td bgcolor="#cccccc">
					<!--�������ɽ���ơ��֥�-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">

						<tr bgcolor="#636469" align="center" class="fs12n">
							<td width="130"><span class="white">������</span></td>
							<td width="70"><span class="white">�����ֹ�</span></td>
							<td width="120"><span class="white">�ܵ�̾</span></td>
							<td width="75"><span class="white">��ʧ��ˡ</span></td>
							<td width="80"><span class="white">�������(��)</span></td>
							<td width="130"><span class="white">������ȯ����</span></td>
							<td width="75"><span class="white">�б�����</span></td>
							<td width="50"><span class="white">�Խ�</span></td>
							<td width="50"><span class="white">�᡼��</span></td>
							<td width="50"><span class="white">���</span></td>
						</tr>
						
						<!--{section name=cnt loop=$arrResults}-->
						<!--{assign var=status value="`$arrResults[cnt].status`"}-->
						<tr bgcolor="<!--{$arrORDERSTATUS_COLOR[$status]}-->" class="fs12n">
							<td align="center"><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
							<td align="center"><!--{$arrResults[cnt].order_id}--></td>
							<td><!--{$arrResults[cnt].order_name01|escape}--> <!--{$arrResults[cnt].order_name02|escape}--></td>
							<!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
							<td align="center"><!--{$arrPayment[$payment_id]}--></td>
							<td align="right"><!--{$arrResults[cnt].total|number_format}--></td>
							<td align="center"><!--{$arrResults[cnt].commit_date|sfDispDBDate|default:"̤ȯ��"}--></td>
							<td align="center"><!--{$arrORDERSTATUS[$status]}--></td>
							<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_EDIT}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">�Խ�</span></a></td>
							<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_MAIL}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_mail">����</span></a></td>
							<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'order_id', <!--{$arrResults[cnt].order_id}-->); return false;"><span class="icon_delete">���</span></a></td>
						</tr>
						<!--{/section}-->

					</table>
					<!--�������ɽ���ơ��֥�-->
					</td>
				</tr>
			</table>

		<!--{/if}-->

		</td>
	</tr>
</form>
</table>		
<!--����������̰�������-->		

<!--{/if}-->
