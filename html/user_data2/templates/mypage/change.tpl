<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MY�ڡ���"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
					<!--{include file=$tpl_navi}-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
				<input type="hidden" name="mode" value="confirm">
				<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|escape}-->">
					<tr>
						<td><!--�������ȥ�--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="�����Ͽ�����ѹ�"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs12">�������ܤˤ����Ϥ�����������<span class="red">��</span>�װ�������ɬ�ܹ��ܤǤ���<br>
						���ϸ塢���ֲ��Ρֳ�ǧ�ڡ����ءץܥ���򥯥�å����Ƥ���������</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--���ϥե����ळ������-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td width="122" bgcolor="#f0f0f0" class="fs12n">��̾��<span class="red">��</span></td>
								<td width="350" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
									��&nbsp;<input type="text" name="name01" value="<!--{$arrForm.name01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />��̾&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />	
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
								����&nbsp;<input type="text" name="kana01" value="<!--{$arrForm.kana01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />���ᥤ&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">͹���ֹ�<span class="red">��</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<!--{assign var=key1 value="zip01"}-->
										<!--{assign var=key2 value="zip02"}-->
										<td colspan="2"><span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
										��&nbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{$arrForm.zip02}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />��
										<a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs12">͹���ֹ渡��</span></a></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><a href="<!--{$smarty.const.URL_DIR}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="���꼫ư����" /></a></td>
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
										<td class="fs12n"><span class="red"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
										<select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
										<option value="" selected>��ƻ�ܸ�������</option>
										<!--{html_options options=$arrPref selected=$arrForm.pref}-->
										</select></td>
									</tr>
									<tr><td height="7"></td>
									</tr>
									<tr>
										<td><input type="text" name="addr01" value="<!--{$arrForm.addr01}-->" size="60" class="box60" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;" /></td>
									</tr>
									<tr>
										<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><input type="text" name="addr02" value="<!--{$arrForm.addr02}-->" size="60" class="box60" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /></td>
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
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" value="<!--{$arrForm.tel01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{$arrForm.tel02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{$arrForm.tel03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" value="<!--{$arrForm.fax01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax02" value="<!--{$arrForm.fax02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax03" value="<!--{$arrForm.fax03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">�᡼�륢�ɥ쥹<span class="red">��</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.email}--></span>
										<input type="text" name="email" value= "<!--{$arrForm.email}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size=40 class="box40" /></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.email02}--></span>
										<input type="text" name="email02" value= "<!--{if $arrForm.email02 == ""}--><!--{$arrForm.email}--><!--{else}--><!--{$arrForm.email02}--><!--{/if}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size=40 class="box40" /></td>
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
								<span class="red"><!--{$arrErr.sex}--></span><input type="radio" id="man" name="sex" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 1}--> checked <!--{/if}--> /><label for="man">����</label>��<input type="radio" id="woman" name="sex" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 2}--> checked <!--{/if}--> /><label for="woman">����</label></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">����</td>
								<td bgcolor="#ffffff">
									<span class="red"><!--{$arrErr.job}--></span>
									<select name="job">
									<option value="" selected>���򤷤Ƥ�������</option>
									<!--{html_options options=$arrJob selected=$arrForm.job}-->
									</select>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">��ǯ����</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
									<option value="" selected>--</option>
									<!--{html_options options=$arrYear selected=$arrForm.year}-->
									</select>&nbsp;ǯ
									<select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.month}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.month}-->
									</select>&nbsp;��
									<select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.day}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrDay selected=$arrForm.day}-->
									</select>&nbsp;��
								</td>
							</tr>

							<tr>
								<td bgcolor="#f0f0f0"><span class="fs12">��˾����ѥ����<span class="red">��</span></span><br>
								<span class="fs10">�ѥ���ɤϹ�������ɬ�פǤ�</span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password}--></span>
										<input type="password" name="password" value="<!--{$arrForm.password}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">Ⱦ�ѱѿ���4��10ʸ���Ǥ��ꤤ���ޤ����ʵ����Բġ�</span></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password02}--></span>
									  	<input type="password" name="password02" value="<!--{$arrForm.password02}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password02|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">��ǧ�Τ����2�����Ϥ��Ƥ���������</span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">�ѥ���ɤ�˺�줿���Υҥ��<span class="red">��</span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder}--></span><select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
											<option value="" selected>���򤷤Ƥ�������</option>
											<!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
											</select>
										</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder_answer}--></span><input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" size=40 class="box40" /></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">�᡼��ޥ��������դˤĤ���<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12">
									<span class="red"><!--{$arrErr.mailmaga_flg}--></span>
									<input type="radio" name="mailmaga_flg" value="1" id="html" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 1}--> checked <!--{/if}--> />HTML�᡼��ܥƥ����ȥ᡼���������</label><br>
									<input type="radio" name="mailmaga_flg" value="2" id="text" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 2}--> checked <!--{/if}--> /><label for="text">�ƥ����ȥ᡼���������</label><br>
									<input type="radio" name="mailmaga_flg" value="3" id="no" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 3}--> checked <!--{/if}--> /><label for="no">�������ʤ�</label>
								</td>
							</tr>
						</table>
						<!--���ϥե����ळ���ޤ�-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="��ǧ�ڡ�����" name="refusal" id="refusal" />
						</td>
					</tr>
				</form>
				</table>
				</td>
			</tr>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->

