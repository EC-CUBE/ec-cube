<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="nonmember_confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<!--������³����ή��-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow01.gif" width="700" height="36" alt="������³����ή��"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--������³����ή��-->
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/info_title.jpg" width="700" height="40" alt="�����;�������"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">�������ܤˤ����Ϥ�����������<span class="red">��</span>�װ�������ɬ�ܹ��ܤǤ���<br>
				���ϸ塢���ֲ��Ρֳ�ǧ�ڡ����ءץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--���ϥե����ळ������-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="170" bgcolor="#f0f0f0" class="fs12n">��̾��<span class="red">��</span></td>
						<td width="487" bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="order_name01"}-->
							<!--{assign var=key2 value="order_name02"}-->
							<span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
							��&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />��̾&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="order_kana01"}-->
							<!--{assign var=key2 value="order_kana02"}-->
							<span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
							����&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />���ᥤ&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">͹���ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="2" >
									<!--{assign var=key1 value="order_zip01"}-->
									<!--{assign var=key2 value="order_zip02"}-->
									<span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span></span>
									��
									<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" onblur="fnSendZipcode()"/>
									 - 
									<input type="text"  name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" onblur="fnSendZipcode()"/>��
									<a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">͹���ֹ渡��</span></a>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><a href="<!--{$smarty.const.URL_DIR}-->address/index.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="���꼫ư����" /></a></td>
								<td class="fs10n">&nbsp;͹���ֹ�����ϸ塢����å����Ƥ���������</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">����<span class="red">��</span></td>
						<td bgcolor="#ffffff">

						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">
									<!--{assign var=key value="order_pref"}-->
									<span class="red"><!--{$arrErr.order_pref}--><!--{$arrErr.order_addr01}--><!--{$arrErr.order_addr02}--></span>
									<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">							
									<option value="" selected="">��ƻ�ܸ�������</option>
									<!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
									</select>
								</td>
							</tr>
							<tr><td height="7"></td>
							</tr>
							<tr>
								<td class="fs12">
									<!--{assign var=key value="order_addr01"}-->
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40" class="box40" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><span class="mini"></span>
								</td>
							</tr>
							<tr>
								<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td>
									<!--{assign var=key value="order_addr02"}-->
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40" class="box40" maxlength=<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><span class="mini"></span>
								</td>
							</tr>
							<tr>
								<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS2}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs10"><span class="red">�����2�Ĥ�ʬ���Ƥ��������������ޤ����ޥ󥷥��̾��ɬ���������Ƥ���������</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�����ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="order_tel01"}-->
							<!--{assign var=key2 value="order_tel02"}-->
							<!--{assign var=key3 value="order_tel03"}-->
							<span class="red"><!--{$arrErr[$key1]}--></span>
							<span class="red"><!--{$arrErr[$key2]}--></span>
							<span class="red"><!--{$arrErr[$key3]}--></span>
							<input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" /> - 
							<input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" /> - 
							<input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="order_fax01"}-->
							<!--{assign var=key2 value="order_fax02"}-->
							<!--{assign var=key3 value="order_fax03"}-->
							<span class="red"><!--{$arrErr[$key1]}--></span>
							<span class="red"><!--{$arrErr[$key2]}--></span>
							<span class="red"><!--{$arrErr[$key3]}--></span>
							<input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" /> - 
							<input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" /> - 
							<input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�᡼�륢�ɥ쥹<span class="red">��</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">
									<!--{assign var=key value="order_email"}-->
									<span class="red"><!--{$arrErr[$key]}--></span>
									<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
									<!--{assign var=key value="order_email_check"}-->
									<span class="red"><!--{$arrErr[$key]}--></span>
									<input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box40" />
								</td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10n"><span class="red">��ǧ�Τ���2�����Ϥ��Ƥ���������</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">����<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key value="order_sex"}-->
							<span class="red"><!--{$arrErr[$key]}--></span>
							<!--{if $arrErr[$key]}-->
							<!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
							<!--{/if}-->
							<!--{html_radios name="$key" options=$arrSex selected=$arrForm[$key].value style="$err"}-->
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">����</td>
						<!--{assign var=key value="order_job"}-->
						<!--{if $arrErr[$key]}-->
						<!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
						<!--{/if}-->
						<td bgcolor="#ffffff">
							<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">							
							<option value="">���򤷤Ʋ�����</option>
							<!--{html_options options=$arrJob selected=$arrForm[$key].value}-->
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��ǯ����</td>
						<td bgcolor="#ffffff" class="fs12n">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<!--{html_options options=$arrYear selected=$arrForm.year.value}--><!--{$arrForm.year.value}-->
									</select>ǯ
									<select name="month" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<option value="">--</option>
										<!--{html_options options=$arrMonth selected=$arrForm.month.value}-->
									</select>��
									<select value="" name="day" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<option value="">--</option>\
										<!--{html_options options=$arrDay selected=$arrForm.day.value}-->
									</select>��</td>
							</tr>
							<tr><td height="2"></td></tr>
						</table>
						</td>
					</tr>
					<!--{* �����������ϥ��ޥ����դϹԤ�ʤ�
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">�᡼��ޥ��������դˤĤ���<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12">
							<!--{assign var=key value="mailmaga_flg"}-->
							<!--{if $arrErr[$key]}-->
							<!--{assign var=err_mail value="background-color: `$smarty.const.ERR_COLOR`"}-->
							<!--{/if}-->
							<span class="red"><!--{$arrErr[$key]}--></span>
							<input type="radio" name="<!--{$key}-->" id="html" value="1" <!--{if $arrForm[$key].value == 1}-->checked="checked"<!--{/if}--> style="<!--{$err_mail}-->" /><label for="html">HTML�᡼��ܥƥ����ȥ᡼���������</label><br />
							<input type="radio" name="<!--{$key}-->" id="text" value="2" <!--{if $arrForm[$key].value == 2}-->checked="checked"<!--{/if}--> style="<!--{$err_mail}-->" /><label for="text">�ƥ����ȥ᡼���������</label><br />
							<input type="radio" name="<!--{$key}-->" id="no" value="3" <!--{if $arrForm[$key].value == 3}-->checked="checked"<!--{/if}--> style="<!--{$err_mail}-->" /><label for="no">�������ʤ�</label><br />
						</td>
					</tr>
					*}-->
					<tr>
						<td colspan="2" bgcolor="#f0f0f0" class="fs12n">
							<!--{assign var=key value="deliv_check"}-->
							<input type="checkbox" name="<!--{$key}-->" value="1" onclick="fnCheckInputDeliv();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />
							<label for="deliv_label"><span class="blackst">����������</span>�����嵭�����Ϥ��줿�������Ʊ��ξ��Ͼ�ά��ǽ�Ǥ���</label>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��̾��<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="deliv_name01"}-->
							<!--{assign var=key2 value="deliv_name02"}-->
							<span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
							��&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />��̾&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="deliv_kana01"}-->
							<!--{assign var=key2 value="deliv_kana02"}-->
							<span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
							����&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />���ᥤ&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">͹���ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="2">
									<!--{assign var=key1 value="deliv_zip01"}-->
									<!--{assign var=key2 value="deliv_zip02"}-->
									<span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span></span>
									��
									<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" />
									 - 
									<input type="text"  name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" />��
									<a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">͹���ֹ渡��</span></a>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><a href="<!--{$smarty.const.URL_DIR}-->address/index.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', '<!--{$key1}-->', '<!--{$key2}-->', 'deliv_pref', 'deliv_addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="���꼫ư����" /></a></td>
								<td class="fs10n">&nbsp;͹���ֹ�����ϸ塢����å����Ƥ���������</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">����<span class="red">��</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">
									<!--{assign var=key value="deliv_pref"}-->
									<span class="red"><!--{$arrErr.deliv_pref}--><!--{$arrErr.deliv_addr01}--><!--{$arrErr.deliv_addr02}--></span>
									<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">							
									<option value="" selected="">��ƻ�ܸ�������</option>
									<!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
									</select>
								</td>
							</tr>
							<tr><td height="7"></td>
							</tr>
							<tr>
								<td>
									<!--{assign var=key value="deliv_addr01"}-->
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40" class="box40" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
								</td>
							</tr>
							<tr>
								<td class="fs10n">�Զ�Į¼̾���㡧�����̶�Ʋ���</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td>
									<!--{assign var=key value="deliv_addr02"}-->
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="40" class="box40" maxlength=<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
								</td>
							</tr>
							<tr>
								<td class="fs10n">���ϡ��ӥ�̾���㡧6-1-1��</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs10"><span class="red">�����2�Ĥ�ʬ���Ƥ��������������ޤ����ޥ󥷥��̾��ɬ���������Ƥ���������</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�����ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{assign var=key1 value="deliv_tel01"}-->
							<!--{assign var=key2 value="deliv_tel02"}-->
							<!--{assign var=key3 value="deliv_tel03"}-->
							<span class="red"><!--{$arrErr[$key1]}--></span>
							<span class="red"><!--{$arrErr[$key2]}--></span>
							<span class="red"><!--{$arrErr[$key3]}--></span>
							<input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" /> - 
							<input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" /> - 
							<input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" />
						</td>
					</tr>
				</table>
				<!--���ϥե����ळ���ޤ�-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="����" border="0" name="next" id="next" />
				</td>
			</tr>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</form>
</table>
<!--��CONTENTS-->


