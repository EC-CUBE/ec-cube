<!--{*
 * Copyright (c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MY�ڡ���"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
					<!--{include file=$tpl_navi}-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
				<input type="hidden" name="mode" value="complete">
				<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|escape}-->" >
				<!--{foreach from=$arrForm key=key item=item}-->
				<!--{if $key ne "mode" && $key ne "subm"}--><input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->"><!--{/if}-->
				<!--{/foreach}-->
					<tr>
						<td><!--�������ȥ�--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="�����Ͽ�����ѹ�"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs12">���������Ƥ��������Ƥ������Ǥ��礦����<br>
						�������С����ֲ��Ρֲ����Ͽ��λ�ءץܥ���򥯥�å����Ƥ���������</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--���ϥե����ळ������-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td width="122" bgcolor="#f0f0f0" class="fs12n">��̾��<span class="red">��</span></td>
								<td width="350" bgcolor="#ffffff" class="fs12n"><!--{$arrForm.name01|escape}-->��<!--{$arrForm.name02|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrForm.kana01|escape}-->��<!--{$arrForm.kana02|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">͹���ֹ�<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12n">��<!--{$arrForm.zip01}-->-<!--{$arrForm.zip02}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">����<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12"><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|escape}--><!--{$arrForm.addr02|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">�����ֹ�<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrForm.tel01|escape}-->-<!--{$arrForm.tel02}-->-<!--{$arrForm.tel03}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
								<td bgcolor="#ffffff" class="fs12n"><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01}-->-<!--{$arrForm.fax02}-->-<!--{$arrForm.fax03}--><!--{else}-->̤��Ͽ<!--{/if}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">�᡼�륢�ɥ쥹<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12"><a href="<!--{$arrForm.email|escape}-->"><!--{$arrForm.email|escape}--></a></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">����<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrSex[$arrForm.sex]}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">����</td>
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrJob[$arrForm.job]|escape|default:"̤��Ͽ"}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">��ǯ����</td>
								<td bgcolor="#ffffff" class="fs12n"><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|escape}-->ǯ<!--{$arrForm.month|escape}-->��<!--{$arrForm.day|escape}-->��<!--{else}-->̤��Ͽ<!--{/if}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0"><span class="fs12">��˾����ѥ����<span class="red">��</span></span><br>
								<span class="fs10">�ѥ���ɤϹ�������ɬ�פǤ�</span></td>
								<td bgcolor="#ffffff" class="fs12"><!--{$passlen}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">�ѥ���ɤ�˺�줿���Υҥ��<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12">���䡧&nbsp;<!--{$arrReminder[$arrForm.reminder]|escape}--><br>
								������&nbsp;<!--{$arrForm.reminder_answer|escape}--></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">�᡼��ޥ��������դˤĤ���<span class="red">��</span></td>
								<td bgcolor="#ffffff" class="fs12n"><!--{$arrMAILMAGATYPE[$arrForm.mail_flag]}--></td>
							</tr>
						</table>
						<!--���ϥե����ळ���ޤ�-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','back');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','back');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" name="change" id="change" /></a>
							<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
							<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_send_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_send.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_send.gif" width="150" height="30" alt="����" name="complete" id="complete" />
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

