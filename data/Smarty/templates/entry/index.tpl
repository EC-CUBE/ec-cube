<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
 <!--��CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="confirm">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="�����Ͽ"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">����Ͽ����ޤ��ȡ��ޤ��ϲ�����Ȥʤ�ޤ���<br>
				���Ϥ��줿�᡼�륢�ɥ쥹�ˡ���Ϣ���Ϥ��ޤ��Τǡ��ܲ���ˤʤä���Ǥ��㤤ʪ�򤪳ڤ��ߤ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--���ϥե����ळ������-->
				<table width="580" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12n">��̾��<span class="red">��</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>��&nbsp;<input type="text" name="name01" size="15" class="box15" value="<!--{$name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" />��̾&nbsp;<input type="text" name="name02" size="15" class="box15"value="<!--{$name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>����&nbsp;<input type="text" name="kana01" size="15" class="box15" value="<!--{$kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" />���ᥤ&nbsp;<input type="text" name="kana02" size="15" class="box15" value="<!--{$kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">͹���ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="2">
									<!--{assign var=key1 value="zip01"}-->
									<!--{assign var=key2 value="zip02"}-->
									<span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span></span>
									<span class="fs12n">��&nbsp;</span><input type="text" name="zip01" value="<!--{if $zip01 == ""}--><!--{$arrOtherDeliv.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{if $zip02 == ""}--><!--{$arrOtherDeliv.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />��
									<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs10">͹���ֹ渡��</span></a>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><a href="<!--{$smarty.const.URL_DIR}-->address/index.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="���꼫ư����" /></a></td>
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
								<!--{html_options options=$arrPref selected=$pref}-->
								</select></td>
							</tr>
							<tr><td height="7"></td>
							</tr>
							<tr>
								<td><input type="text" name="addr01" size="40" class="box40" value="<!--{$addr01|escape}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;"/></td>
							</tr>
							<tr>
								<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="addr02" size="40" class="box40" value="<!--{$addr02|escape}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /></td>
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
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" size="6" value="<!--{$tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" size="6" value="<!--{$tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel03" size="6" value="<!--{$tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" size="6" value="<!--{$fax01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"  style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax02" size="6" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax03" size="6" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�᡼�륢�ɥ쥹<span class="red">��</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><span class="fs12n"><span class="red"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span></span>
								<td><input type="text" name="email" size="40" class="box40" value="<!--{$email|escape}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="email02" size="40" class="box40" value="<!--{$email02|escape}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
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
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.sex}--></span>
							<input type="radio" name="sex" id="man" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $sex eq 1}-->checked<!--{/if}--> /><label for="man">����</label>��<input type="radio" name="sex" id="woman" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $sex eq 2}-->checked<!--{/if}--> /><label for="woman">����</label>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">����</td>
						<td bgcolor="#ffffff"><span class="red"><!--{$arrErr.job}--></span>
						<select name="job" style="<!--{$arrErr.job|sfGetErrorColor}-->">
						<option value="" selected>���򤷤Ƥ�������</option>
						<!--{html_options options=$arrJob selected=$job}-->
						</select></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��ǯ����</td>
						<td bgcolor="#ffffff" class="fs12n">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<!--{html_options options=$arrYear selected=$year}-->
									</select>ǯ
									<select name="month" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<option value="">--</option>
										<!--{html_options options=$arrMonth selected=$month}-->
									</select>��
									<select value="" name="day" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<option value="">--</option>\
										<!--{html_options options=$arrDay selected=$day}-->
									</select>��</td>
							</tr>
							<tr><td height="2"></td></tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" ><span class="fs12">��˾����ѥ����<span class="red">��</span></span><br>
						<span class="fs10">�ѥ���ɤϹ�������ɬ�פǤ�</span></td>
						<td bgcolor="#ffffff">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n"><span class="red"><!--{$arrErr.password}--><!--{$arrErr.password02}--></span><input type="password" name="password" value="<!--{$arrForm.password}-->"size="15" class="box15"  style="<!--{$arrErr.password|sfGetErrorColor}-->"/></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10n"><span class="red">Ⱦ�ѱѿ���4��10ʸ���Ǥ��ꤤ���ޤ����ʵ����Բġ�</span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="password" name="password02" value="<!--{$arrForm.password02}-->" size="15" class="box15"  style="<!--{$arrErr.password02|sfGetErrorColor}-->"/></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10n"><span class="red">��ǧ�Τ����2�����Ϥ��Ƥ���������</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0"  class="fs12">�ѥ���ɤ�˺�줿���Υҥ��<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">���䡧</td>
								<td>
									<select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
										<option value="" selected>���򤷤Ƥ�������</option>
										<!--{html_options options=$arrReminder selected=$reminder}-->
									</select>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">������</td>

								<td><input type="text" name="reminder_answer" size="33" class="box33" value="<!--{$reminder_answer|escape}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" /></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">�᡼��ޥ��������դˤĤ���<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12"><span class="red"><!--{$arrErr.mailmaga_flg}--></span>
						<input type="radio" name="mailmaga_flg" id="html" value="1" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $mailmaga_flg eq 1 || $mailmaga_flg eq ""}-->checked<!--{/if}--> /><label for="html">HTML�᡼��ܥƥ����ȥ᡼���������</label><br>
						<input type="radio" name="mailmaga_flg" id="text"value="2" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $mailmaga_flg eq 2}-->checked<!--{/if}--> /><label for="text">�ƥ����ȥ᡼���������</label><br>
						<input type="radio" name="mailmaga_flg" id="no" value="3" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $mailmaga_flg eq 3}-->checked<!--{/if}--> /><label for="no">�������ʤ�</label></td>
					</tr>
				</table>
				<!--���ϥե����ळ���ޤ�-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="��ǧ�ڡ�����" border="0" name="confirm" id="confirm" />
				</td>
			</tr>
		</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
