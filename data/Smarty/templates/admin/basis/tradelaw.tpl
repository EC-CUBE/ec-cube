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
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->���꾦���ˡ��Ͽ</span></td>
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
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">����ȼ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_company"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">������Ǥ��<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_manager"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="156">͹���ֹ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522"> 
										<!--{assign var=key1 value="law_zip01"}-->
										<!--{assign var=key2 value="law_zip02"}-->
										<span class="red12"><!--{$arrErr[$key1]}--></span>
										<span class="red12"><!--{$arrErr[$key2]}--></span>
										��
										<input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box6" />
										 - 
										<input type="text"  name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" />
										<input type="button" name="address_input" value="��������" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'law_zip01', 'law_zip02', 'law_pref', 'law_addr01');" />
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12">�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522">
										<table width="522" border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<!--{assign var=key value="law_pref"}-->
													<span class="red12"><!--{$arrErr[$key]}--></span>
													<select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
													<option value="" selected="selected">��ƻ�ܸ�������</option>
													<!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
													</select>
												</td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs10n">
												<td>
												<!--{assign var=key value="law_addr01"}-->
												<span class="red12"><!--{$arrErr[$key]}--></span>
												<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span>
												<br />
												<!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
											</tr>
											<tr><td height="5"></td></tr>
											<tr class="fs10n">
												<td>
												<!--{assign var=key value="law_addr02"}-->
												<span class="red12"><!--{$arrErr[$key]}--></span>
												<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span>
												<br />
												<!--{$smarty.const.SAMPLE_ADDRESS2}--></td>
											</tr>
										</table>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="156">TEL<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522">
										<!--{assign var=key1 value="law_tel01"}-->
										<!--{assign var=key2 value="law_tel02"}-->
										<!--{assign var=key3 value="law_tel03"}-->
										<span class="red12"><!--{$arrErr[$key1]}--></span>
										<span class="red12"><!--{$arrErr[$key2]}--></span>
										<span class="red12"><!--{$arrErr[$key3]}--></span>
										<input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> - 
										<input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" /> - 
										<input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="156">FAX</td>
										<td bgcolor="#ffffff" width="522">
										<!--{assign var=key1 value="law_fax01"}-->
										<!--{assign var=key2 value="law_fax02"}-->
										<!--{assign var=key3 value="law_fax03"}-->
										<span class="red12"><!--{$arrErr[$key1]}--></span>
										<span class="red12"><!--{$arrErr[$key2]}--></span>
										<span class="red12"><!--{$arrErr[$key3]}--></span>
										<input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> - 
										<input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> - 
										<input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" /></td>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">�᡼�륢�ɥ쥹<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_email"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">URL<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_url"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">�������ʳ���ɬ������<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_term01"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">��ʸ��ˡ<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_term02"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">��ʧ��ˡ<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_term03"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">��ʧ����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_term04"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">�����Ϥ�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_term05"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="156" class="fs12n">���ʡ��򴹤ˤĤ���<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="522" class="fs10n">
										<!--{assign var=key value="law_term06"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea><span class="red"> �ʾ��<!--{$arrForm[$key].length}-->ʸ����</span></td>
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
												<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" ></td>
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
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
