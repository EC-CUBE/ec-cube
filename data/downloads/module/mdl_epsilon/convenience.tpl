<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--��MAIN ONTENTS-->
		<!--������³����ή��-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="������³����ή��"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--������³����ή��-->
		
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/confirm_title.jpg" width="700" height="40" alt="���������ƤΤ���ǧ"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">��������ʸ���Ƥ��������Ƥ������Ǥ��礦����<br>
				�������С����ֲ��Ρ�<!--{if $payment_type != ""}-->����<!--{else}-->����ʸ��λ�ڡ�����<!--{/if}-->�ץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
			<input type="hidden" name="mode" value="confirm">
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
			<tr>
				<td bgcolor="#cccccc">
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌��������-->		
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td colspan="2" bgcolor="#f0f0f0" class="fs12n"><strong>������ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌</strong></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">����ʧ��ˡ</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.payment_method|escape}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">���Ϥ���</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_date|escape|default:"����ʤ�"}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">���Ϥ�����</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.deliv_time|escape|default:"����ʤ�"}--></td>
					</tr>
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">����¾���䤤��碌</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.message|escape|nl2br}--></td>
					</tr>
					
					<!--{if $tpl_login == 1}-->
					<tr>
						<td width="150" bgcolor="#f0f0f0" class="fs12">�ݥ���Ȼ���</td>
						<td width="507" bgcolor="#ffffff" class="fs12"><!--{$arrData.use_point|default:0}-->Pt</td>
					</tr>
					<!--{/if}-->
					
				</table>
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌�����ޤ�-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<a href="<!--{$smarty.server.PHP_SELF}-->" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif',back03)" onclick="fnModeSubmit('return', '', ''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" border="0" name="back03" id="back03"/></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<!--{if $payment_type != ""}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="����" border="0" name="next" id="next" />
					<!--{else}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif" width="150" height="30" alt="����ʸ��λ�ڡ�����" border="0" name="next" id="next" />
					<!--{/if}-->
				</td>
			</tr>
			</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->