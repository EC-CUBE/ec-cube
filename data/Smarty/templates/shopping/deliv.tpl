<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		
		<!--������³����ή��-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow01.gif" width="700" height="36" alt="������³����ή��"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--������³����ή��-->

		<!--��MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
		<input type="hidden" name="mode" value="customer_addr">
		<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
		<input type="hidden" name="other_deliv_id" value="">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/deliv_title.jpg" width="700" height="40" alt="���Ϥ���λ���"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">����������ꤪ�Ϥ��轻������򤷤ơ������򤷤����Ϥ��������ץܥ���򥯥�å����Ƥ���������
				�����ˤ���˾�ν��̵꤬�����ϡ��ֿ��������Ϥ�����ɲä���פ���ɲ���Ͽ���Ƥ���������<br>
				������20��ޤ���Ͽ�Ǥ��ޤ���</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td>
					<a href="../mypage/delivery_addr.php" onclick="win02('../mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|escape}-->','new_deiv','600','640'); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/newadress_on.gif','addition');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/newadress.gif','addition');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/newadress.gif" width="160" height="22" alt="���������Ϥ�����ɲä���" name="addition" id="addition" /></a>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--ɽ�����ꥢ��������-->
				
				<!--{if $arrErr.deli != ""}-->
				<table width="700" border="0" cellspacing="2" cellpadding="10" summary=" " bgcolor="#ff7e56">
					<tr>
						<td align="center" class="fs14" bgcolor="#ffffff">
							<span class="red"><strong><!--{$arrErr.deli}--></strong></span>
						</td>
					</tr>
				</table>
				</td></tr><tr><td height=15></td></tr><tr><td bgcolor="#cccccc">
				<!--{/if}-->
				
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr align="center" bgcolor="#f0f0f0">
						<td width="40" class="fs12">����</td>
						<td width="100" class="fs12">�������</td>
						<td width="374" class="fs12">���Ϥ���</td>
						<td width="40" class="fs12">�ѹ�</td>
						<td width="40" class="fs12">���</td>
					</tr>

					<!--{section name=cnt loop=$arrAddr}-->		
						<tr class="fs12" bgcolor="#ffffff">
							<td align="center">
								<!--{if $smarty.section.cnt.first}-->
								<input type="radio" name="deli" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$smarty.section.cnt.iteration}-->" onclick="mode.value='customer_addr';">
								<!--{else}-->
								<input type="radio" name="deli" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$smarty.section.cnt.iteration}-->" onclick="mode.value='other_addr'; other_deliv_id.value=<!--{$arrAddr[cnt].other_deliv_id}-->;">
								<!--{/if}-->
							</td>
							<td>
								<label for="chk_id_<!--{$smarty.section.cnt.iteration}-->"><!--{if $smarty.section.cnt.first}-->�����Ͽ����<!--{else}-->�ɲ���Ͽ����<!--{/if}--></label>
							</td>
							<td>
								<!--{assign var=key value=$arrAddr[cnt].pref}--><!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|escape}--><!--{$arrAddr[cnt].addr02|escape}--><br/>
								<!--{$arrAddr[cnt].name01|escape}--> <!--{$arrAddr[cnt].name02|escape}-->
							</td>
							<td align="center">
								<!--{if !$smarty.section.cnt.first}--><a href="<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php" onclick="win02('/mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|escape}-->&other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','600','640'); return false;">�ѹ�</a><!--{/if}-->
							</td>
							<td align="center">
								<!--{if !$smarty.section.cnt.first}--><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->'); return false">���</a><!--{/if}-->
							</td>
						</tr>
					<!--{/section}-->

				</table>
				<!--ɽ�����ꥢ�����ޤ�-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_select_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_select.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/shopping/b_select.gif" width="190" height="30" alt="���򤷤����Ϥ��������" border="0" name="send_button" id="send_button" />
				</td>
			</tr>
		</form>
		</table>
		<!--��MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->
