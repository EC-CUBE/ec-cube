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
		<!--{*������³����ή��-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="������³����ή��"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--������³����ή��*}-->
		
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/convenience_title.jpg" width="700" height="40" alt="����ӥ˷��"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">�������顢����ʧ�����륳��ӥˤ����򤷡�ɬ�׻�������Ϥ��Ƥ���������<br />
				���ϸ塢���ֲ��Ρ֤���ʸ��λ�ڡ����ءץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="send">
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
			<tr><td class="fs12n"><span class="red"><!--{$arrErr.convenience}--></span></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌��������-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="10%" align="center" bgcolor="#f0f0f0" class="fs12">����</td>
						<td width="90%" bgcolor="#f0f0f0" class="fs12">����ӥˤμ���</td>
					</tr>
					<!--{foreach key=key item=item from=$arrConv}-->
					<tr>
						<td align="center" bgcolor="#ffffff" class="fs12"><input type="radio" name="convenience" id="<!--{$key}-->" value="<!--{$key}-->" style="<!--{$arrErr.convenience|sfGetErrorColor}-->" <!--{if $smarty.post.convenience == $key}-->checked<!--{/if}-->></td>
						<td bgcolor="#ffffff" class="fs12"><label for="<!--{$key}-->"><!--{$item|escape}--></label></td>
					</tr>
					<!--{/foreach}-->
				</table>
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌�����ޤ�-->
				</td>
			</tr>
			
			<tr><td height="20"></td></tr>
			
			<tr>
				<td bgcolor="#cccccc">
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌��������-->		
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="20%" bgcolor="#f0f0f0" class="fs12n">��̾���ʥ������ʡ�<span class="red">��</span></td>
						<td width="80%" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.order_kana01}--><!--{$arrErr.order_kana02}--></span>����&nbsp;<input type="text" name="order_kana01" size="15" class="box15" value="<!--{$arrForm.order_kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.order_kana01|sfGetErrorColor}-->; ime-mode: active;" />���ᥤ&nbsp;<input type="text" name="order_kana02" size="15" class="box15" value="<!--{$arrForm.order_kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.order_kana02|sfGetErrorColor}-->; ime-mode: active;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�����ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.order_tel01}--><!--{$arrErr.order_tel02}--><!--{$arrErr.order_tel03}--></span><input type="text" name="order_tel01" size="6" value="<!--{$arrForm.order_tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.order_tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="order_tel02" size="6" value="<!--{$arrForm.order_tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.order_tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="order_tel03" size="6" value="<!--{$arrForm.order_tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.order_tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
					</tr>				
				</table>
				<!--����ʧ��ˡ�����Ϥ����֤λ��ꡦ����¾���䤤��碌�����ޤ�-->
				</td>
			</tr>
			
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center">
					<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif',back03)" onclick="fnModeSubmit('return', '', ''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="���" border="0" name="back03" id="back03"/></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif" width="150" height="30" alt="����ʸ��λ�ڡ�����" border="0" name="next" id="next" />
				</td>
			</tr>
			</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->