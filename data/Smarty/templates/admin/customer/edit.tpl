<script type="text/javascript">
<!--
	
	function fnReturn() {
		document.form_search.action = './index.php';
		document.form_search.submit();
		return false;
	}

	function fnOrderidSubmit(order_id, order_id_value) {
	if(order_id != "" && order_id_value != "") {
		document.form2[order_id].value = order_id_value;
	}
	document.form2.action = '../order/edit.php';
	document.form2.submit();
	}

//-->
</script>

<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form_search" method="post" action="">
<input type="hidden" name="mode" value="search">
<!--{foreach from=$smarty.post key="key" item="item"}-->
	<!--{if $key ne "customer_id" && $key ne "mode" && $key ne "del_mode" && $key ne "edit_customer_id" && $key ne "del_customer_id" && $key ne "csv_mode"}--><input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->"><!--{/if}-->
<!--{/foreach}-->
</form>

<form name="form2" id="form2" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="edit_email" value="<!--{$tpl_edit_email}-->">
<input type="hidden" name="customer_id" value="<!--{$list_data.customer_id|escape}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
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
								<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�ܵ��Խ�</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">

									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">�ܵ�ID<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><!--{$list_data.customer_id|escape}--></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">�������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527">
											<span class="red12"><!--{$arrErr.status}--></span>
											<input type="radio" name="status"value=1 id="no_mem" <!--{if $list_data.status == 1}--> checked="checked" <!--{/if}--> <!--{if $list_data.status == 2}-->disabled<!--{/if}-->><label for="no_mem">�����</label>
											<input type="radio" name="status"value=2 id="mem"<!--{if $list_data.status == 2}--> checked="checked" disabled<!--{/if}-->><label for="mem">�ܲ��</label>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">��̾��<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span><input type="text" name="name01" value="<!--{$list_data.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;&nbsp;<input type="text" name="name02" value="<!--{$list_data.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">�եꥬ��<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><spanl class="red12"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span><input type="text" name="kana01" value="<!--{$list_data.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30"  <!--{if $arrErr.kana01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;&nbsp;<input type="text" name="kana02" value="<!--{$list_data.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30"  <!--{if $arrErr.kana02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">͹���ֹ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>�� <input type="text" name="zip01" value="<!--{$list_data.zip01|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" size="6" class="box6" maxlength="3"  <!--{if $arrErr.zip01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="zip02" value="<!--{$list_data.zip02|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" size="6" class="box6" maxlength="4"  <!--{if $arrErr.zip02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><input type="button" name="address_input" value="��������" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01');" /></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="190" class="fs12">������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527">
										<table width="527" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<span class="red12"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
													<select name="pref"  <!--{if $arrErr.pref != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
													<option value="" selected="selected">��ƻ�ܸ�������</option>
													<!--{html_options options=$arrPref selected=$list_data.pref}-->
													</select>
												</td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs10n">
												<td><input type="text" name="addr01" value="<!--{$list_data.addr01|escape}-->" size="60" class="box60" <!--{if $arrErr.addr01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
												���Զ�Į¼������ ���㡧�����̶�Ʋ���</td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs10n">
												<td><input type="text" name="addr02" value="<!--{$list_data.addr02|escape}-->" size="60" class="box60" <!--{if $arrErr.addr02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
												�����ϡ���ʪ���ޥ󥷥��̾�ʤɤ����� ���㡧2����1-31 ORIXƲ��ӥ�5����</td>
											</tr>
										</table>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="190" class="fs12n">�᡼�륢�ɥ쥹<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527" class="fs10n"><span class="red12"><!--{$arrErr.email}--></span><input type="text" name="email" value="<!--{$list_data.email|escape}-->" size="60" class="box60" <!--{if $arrErr.email != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">�����ֹ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" value="<!--{$list_data.tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel02" value="<!--{$list_data.tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel03" value="<!--{$list_data.tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">FAX</td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" value="<!--{$list_data.fax01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax02" value="<!--{$list_data.fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.tel02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax03" value="<!--{$list_data.fax03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.fax03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.sex}--></span><input type="radio" name="sex" value="1" <!--{if $arrErr.sex != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.sex eq 1}-->checked<!--{/if}--> />���� <input type="radio" name="sex" value="2" <!--{if $arrErr.sex != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.sex eq 2}-->checked<!--{/if}--> />����</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">������</td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.job}--></span>
											<select name="job" <!--{if $arrErr.job != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
											<option value="" selected="selected">���򤷤Ƥ�������</option>
											<!--{html_options options=$arrJob selected=$list_data.job}-->
											</select>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">��ǯ����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.year}--></span>
											<select name="year" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
												<option value="" selected="selected">------</option>
												<!--{html_options options=$arrYear selected=$list_data.year}-->
											</select>ǯ
											<select name="month" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
												<option value="" selected="selected">----</option>
												<!--{html_options options=$arrMonth selected=$list_data.month}-->
											</select>��
											<select name="day" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
												<option value="" selected="selected">----</option>
												<!--{html_options options=$arrDay selected=$list_data.day"}-->		
											</select>��
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">�ѥ����<span class="red"> *</span></td>
										<td class="red10" bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.password}--></span><input type="password" name="password" value="<!--{$list_data.password|escape}-->" size="30" class="box30" <!--{if $arrErr.password != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />��Ⱦ�ѱѿ���ʸ��4��10ʸ���ʵ����Բġ�</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="190" class="fs12">�ѥ���ɤ�˺�줿�Ȥ��Υҥ��<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527">
										<table width="527" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr class="fs12n">
												<td><span class="red12"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>���䡧 <select name="reminder" <!--{if $arrErr.reminder != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
												<option value="" selected="selected">���򤷤Ƥ�������</option>
												<!--{html_options options=$arrReminder selected=$list_data.reminder}-->
												</select>
												</td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs12n">
												<td>������ <input type="text" name="reminder_answer" value="<!--{$list_data.reminder_answer|escape}-->" size="30" class="box30" <!--{if $arrErr.reminder_answer != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
											</tr>
										</table>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">�᡼��ޥ�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.mail_flag}--></span><input type="radio" name="mail_flag" value="1" <!--{if $arrErr.mail_flag != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.mail_flag eq 1}-->checked<!--{/if}--> />HTML��<input type="radio" name="mail_flag" value="2" <!--{if $arrErr.mail_flag != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.mail_flag eq 2}-->checked<!--{/if}--> />�ƥ����ȡ�<input type="radio" name="mail_flag" value="3" <!--{if $arrErr.mail_flag != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.mail_flag eq "" || $list_data.mail_flag eq 3}-->checked<!--{/if}--> />��˾���ʤ�</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="960" class="fs12n">SHOP�ѥ��</td>
										<td bgcolor="#ffffff" width="527" class="fs10n"><span class="red12"><!--{$arrErr.note}--></span><textarea name="note" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" <!--{if $arrErr.note != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> cols="60" rows="8" class="area60"><!--{$list_data.note|escape}--></textarea></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="190">����ݥ����</td>
										<td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.point}--></span><input type="text" name="point" value="<!--{$list_data.point|escape}-->" maxlength="<!--{$smarty.const.TEL_LEN}-->" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> size="6" class="box6" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> pt</td>
									</tr>

								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="/img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_search_back_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_search_back.jpg',this)" src="/img/contents/btn_search_back.jpg" width="123" height="24" alt="�������̤����" border="0" name="subm" onclick="return fnReturn();">
													<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_confirm.jpg',this)" src="/img/contents/btn_confirm.jpg" width="123" height="24" alt="��ǧ�ڡ�����" border="0" name="subm" >
												</td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>

								</td>
								<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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

<!--�������������������-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
	<input type="hidden" name="mode" value="edit">
	<input type="hidden" name="order_id" value="">
	<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
	<input type="hidden" name="edit_customer_id" value="<!--{$edit_customer_id}-->" >
	<tr><td colspan="2"><img src="/img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
	<tr bgcolor="cbcbcb">
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/contents/search_left.gif" width="19" height="22" alt=""></td>
				<td>
				<!--�����������-->
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="/img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_top_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/reselt_left_bg.gif"><img src="/img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#393a48" class="white10">�������������<span class="reselt"><!--�����������--><!--{$tpl_linemax}-->��</span>&nbsp;���������ޤ�����</td>
						<td background="/img/contents/reselt_right_bg.gif"><img src="/img/common/_.gif" width="22" height="8" alt=""></td>
					</tr>
					<tr>
						<td><img src="/img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
						<td background="/img/contents/reselt_bottom_bg.gif"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
						<td><img src="/img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
					</tr>
				</table>
				<!--�����������-->
				</td>
				<td><img src="/img/common/_.gif" width="8" height="1" alt=""></td>
			</tr>
		</table>
		</td>
		<td align="right">
			<!--{include file=$tpl_pager}-->
		</td>									
	</tr>
	<tr><td bgcolor="cbcbcb" colspan="2"><img src="/img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#f0f0f0" align="center">
			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<!--{if $tpl_linemax > 0}-->
					<td bgcolor="#cccccc">
					<!--�����������ɽ���ơ��֥�-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="160"><span class="white">����</span></td>
							<td width="140"><span class="white">�����ֹ�</span></td>
							<td width="140"><span class="white">�������</span></td>
							<td width="160"><span class="white">ȯ����</span></td>
							<td width="140"><span class="white">��ʧ��ˡ</span></td>
						</tr>
						<!--{section name=cnt loop=$arrPurchaseHistory}-->
						<tr bgcolor="#ffffff" align="center" class="fs12">
						<td width=""><!--{$arrPurchaseHistory[cnt].create_date|sfDispDBDate}--></td>
						<td width=""><a href="#" onclick="fnOpenWindow('../order/edit.php?order_id=<!--{$arrPurchaseHistory[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrPurchaseHistory[cnt].order_id}--></a></td>
						<td width=""><!--{$arrPurchaseHistory[cnt].payment_total|number_format}-->��</td>
						<td width=""><!--{if $arrPurchaseHistory[cnt].status eq 5}--><!--{$arrPurchaseHistory[cnt].commit_date|sfDispDBDate}--><!--{else}-->̤ȯ��<!--{ /if }--></td>
						<!--{assign var=payment_id value="`$arrPurchaseHistory[cnt].payment_id`"}-->
						<td width=""><!--{$arrPayment[$payment_id]|escape}--></td>
						</tr>
						<!--{/section}-->
					</table>
					<!--�����������ɽ���ơ��֥�-->
					</td>
					<!--{else}-->
					<td align="center" class="fs12">��������Ϥ���ޤ���</td>
					<!--{/if}-->
				</tr>
			</table>
		</td>
	</tr>
</form>
</table>		
<!--����������̰�������-->		

