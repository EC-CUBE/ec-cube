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
				<input type="hidden" name="mode" value="complete">
			<!--{foreach from=$list_data key=key item=item}-->
				<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
			<!--{/foreach}-->
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="�����Ͽ"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">���������Ƥ��������Ƥ������Ǥ��礦����<br>
				�������С����ֲ��Ρֲ����Ͽ��λ�ءץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--���ϥե����ळ������-->
				<table width="580" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">��̾��<span class="red">��</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12"><!--{$list_data.name01|escape}-->��<!--{$list_data.name02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$list_data.kana01|escape}-->��<!--{$list_data.kana02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">͹���ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12">��<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">����<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">�����ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$list_data.tel01|escape}--> - <!--{$list_data.tel02|escape}--> - <!--{$list_data.tel03|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
						<td bgcolor="#ffffff" class="fs12"><!--{if strlen($list_data.fax01) > 0 && strlen($list_data.fax02) > 0 && strlen($list_data.fax03) > 0}--><!--{$list_data.fax01|escape}--> - <!--{$list_data.fax02|escape}--> - <!--{$list_data.fax03|escape}--><!--{else}-->̤��Ͽ<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�᡼�륢�ɥ쥹<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><a href="mailto:<!--{$list_data.email|escape}-->"><!--{$list_data.email|escape}--></a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">����<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><!--{if $list_data.sex eq 1}-->����<!--{else}-->����<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0"  class="fs12n">����</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{$arrJob[$list_data.job]|escape|default:"̤��Ͽ"}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">��ǯ����</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}--><!--{$list_data.year|escape}-->ǯ<!--{$list_data.month|escape}-->��<!--{$list_data.day|escape}-->��<!--{else}-->̤��Ͽ<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" ><span class="fs12">��˾����ѥ����<span class="red">��</span></span><br>
						<span class="fs10">�ѥ���ɤϹ�������ɬ�פǤ�</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$passlen}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">�ѥ���ɤ�˺�줿���Υҥ��<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">���䡧</td>
								<td class="fs12n"><!--{$arrReminder[$list_data.reminder]|escape}--></td>
							</tr>
							<tr>
								<td class="fs12n">������</td>
								<td class="fs12n"><!--{$list_data.reminder_answer|escape}--></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">�᡼��ޥ��������դˤĤ���<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{if $list_data.mailmaga_flg eq 1}-->HTML�᡼��ܥƥ����ȥ᡼���������<!--{elseif $list_data.mailmaga_flg eq 2}-->�ƥ����ȥ᡼���������<!--{else}-->�������ʤ�<!--{/if}--></td>
					</tr>
				</table>
				<!--���ϥե����ळ���ޤ�-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','back')" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','back')"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" border="0" name="back" id="back" /></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/entry/b_entrycomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/entry/b_entrycomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/entry/b_entrycomp.gif" width="150" height="30" alt="����" border="0" name="send" id="send" />
				</td>
			</tr>
		</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
