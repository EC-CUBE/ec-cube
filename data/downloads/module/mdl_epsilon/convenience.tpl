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
			<tr><td height="20"></td></tr>
			<tr valign="top">
				<!--��CONTENTS-->

				<td>
				<div id="maintitle"><img src="../img/shopping/conveni_title.jpg" width="700" height="40" alt="����ӥ˷��" /></div>
				<div class="fs12n" id="comment01">�������顢����ʧ�����륳��ӥˤ����򤯤������ޤ���<br />
				����塢���ֲ��Ρ֤���ʸ��λ�ڡ����ءץܥ���򥯥�å����Ƥ���������</div>
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
				<input type="hidden" name="mode" value="complete">
				<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
				<span class="red12st"><!--{$arrErr.convenience}--></span>
				<table cellspacing="1" cellpadding="8" summary=" " id="frame">
					<tr class="fs12n">
						<td id="select">����</td>
						<td id="payment">����ӥˤμ���</td>
					</tr>
					<!--{foreach key=key item=item from=$arrConv}-->
					<tr>
						<td id="select_c"><input type="radio" name="convenience" id="<!--{$key}-->" value="<!--{$key}-->" style="<!--{$arrErr.convenience|sfGetErrorColor}-->"></td>
						<label for="<!--{$key}-->"><td class="fs12n" id="payment_c"><!--{$item|escape}--></td></label>
					</tr>
					<!--{/foreach}-->
				</table>
				<div class="red12" id="comment02">���֤���ʸ��λ�ڡ����ءפ򥯥�å��塢��λ�ڡ�����ɽ�������ޤǤ��Ԥ�����������</div>
				<div id="button">
				<!--�����ס���Ͽ��-->
				<a href="<!--{$smarty.server.PHP_SELF}-->" onmouseover="chgImg('/img/button/back03_on.gif','back03')" onmouseout="chgImg('/img/button/back03.gif','back03')" onclick="fnModeSubmit('return', '', ''); return false;" /><img src="/img/button/back03.gif" width="110" height="30" alt="���" border="0" name="back03" id="back03" ></a><img src="../img/_.gif" width="20" height="" alt="" /><input type="image" onmouseover="chgImgImageSubmit('../img/shopping/complete_on.gif',this)" onmouseout="chgImgImageSubmit('../img/shopping/complete.gif',this)" src="../img/shopping/complete.gif" width="170" height="30" alt="����ʸ��λ�ڡ�����" border="0" name="complete" id="complete" />
				</div>
				</form>
				
				<table cellspacing="0" cellpadding="0" summary=" " id="verisign">
					<tr>
						<td><script src=https://seal.verisign.com/getseal?host_name=secure.tokado.jp&size=S&use_flash=YES&use_transparent=NO&lang=ja></script></td>
						<td><img src="../img/_.gif" width="10" height="1" alt="" /></td>
						<td class="fs10">�ȡ���Ʋ���󥿡��ͥåȥ���åԥ󥰤Ǥϡ��̿��ΰ���������ݤ��륻�����ƥ��⡼�ɤ����ꤷ�Ƥ��ޤ����ְŹ沽(SSL)�פ����򤹤�ȡ�����������ǡ������Ź沽���졢ϳ�̤δ������㤯�ʤ�ޤ����ޤ������ܥ٥ꥵ����Ҥˤ�ä��̿������Ф�ǧ�ڤ���뤿�ᡢ�ʤꤹ�ޤ��ʤɤˤ��ID���ѥ���ɤ����Ѥβ�ǽ�����㸺�Ǥ��ޤ���</td>
					</tr>

				</table>
				</td>
				<!--��ONTENTS-->	
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" />

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
