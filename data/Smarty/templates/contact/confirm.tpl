<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
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
		<!--{foreach key=key item=item from=$arrForm}-->
		<!--{if $key ne 'mode'}-->
		<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
		<!--{/if}-->
		<!--{/foreach}-->
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/contact/title.jpg" width="580" height="40" alt="���䤤��碌"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">�����������Ƥ��������Ƥ������Ǥ��礦����<br>
				�������С����ֲ��Ρ������ץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--���ϥե����ळ������-->
				<table width="" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">��̾��<span class="red">��</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12"><!--{$arrForm.name01|escape}-->��<!--{$arrForm.name02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrForm.kana01|escape}-->��<!--{$arrForm.kana02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">͹���ֹ�</td>
						<td bgcolor="#ffffff" class="fs12"><!--{if strlen($arrForm.zip01) > 0 && strlen($arrForm.zip02) > 0}-->��<!--{$arrForm.zip01|escape}-->-<!--{$arrForm.zip02|escape}--><!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">����</td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|escape}--><!--{$arrForm.addr02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">�����ֹ�</td>
						<td bgcolor="#ffffff" class="fs12"><!--{if strlen($arrForm.tel01) > 0 && strlen($arrForm.tel02) > 0 && strlen($arrForm.tel03) > 0}--><!--{$arrForm.tel01|escape}-->-<!--{$arrForm.tel02|escape}-->-<!--{$arrForm.tel03|escape}--><!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�᡼�륢�ɥ쥹<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><a href="<!--{$arrForm.email|escape}-->"><!--{$arrForm.email|escape}--></a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">���䤤��碌����<span class="red">��</span><br>
						<span class="mini">������1000���ʲ���</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrForm.contents|escape|nl2br}--></td>
					</tr>
				</table>
				<!--���ϥե����ळ���ޤ�-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','back02');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','back02');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" name="back02" id="back02" /></a><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_complete_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_complete.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_complete.gif" width="150" height="30" alt="��λ�ڡ�����" border="0" name="send" id="send" />	
				</td>
			</tr>
		</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->





