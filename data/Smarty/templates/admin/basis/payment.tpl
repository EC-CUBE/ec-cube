<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--����Ͽ�ơ��֥뤳������-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->��ʧ��ˡ����</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr align="center" bgcolor="#f2f1ec" class="fs12n">
										<td width="134">��ʧ��ˡ</td>
										<td width="69">������ʱߡ�</td>
										<td width="124">���Ѿ��</td>
										<td width="84">���������ӥ�</td>
										<td width="44">�Խ�</td>
										<td width="44">���</td>
										<td width="69">��ư</td>
									</tr>
									<!--{section name=cnt loop=$arrPaymentListFree}-->
									<tr bgcolor="#ffffff" class="fs12n">
										<td><!--{$arrPaymentListFree[cnt].payment_method|escape}--></td>
										<!--{if $arrPaymentListFree[cnt].charge_flg == 2}-->
											<td align="center">-</td>
										<!--{else}-->
											<td align="right"><!--{$arrPaymentListFree[cnt].charge|escape|number_format}--></td>
										<!--{/if}-->
										<td align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr class="fs12">
													<td align="center" width="80"><!--{if $arrPaymentListFree[cnt].rule > 0}--><!--{$arrPaymentListFree[cnt].rule|escape|number_format}--><!--{else}-->0<!--{/if}-->��</td>
													<td align="center"> �� </td>
													<td align="center" width="80"><!--{if $arrPaymentListFree[cnt].upper_rule > 0}--><!--{$arrPaymentListFree[cnt].upper_rule|escape|number_format}-->��<!--{else}-->̵����<!--{/if}--></td>
												</tr>
											</table>
										<td><!--{assign var=key value="`$arrPaymentListFree[cnt].deliv_id`"}--><!--{$arrDelivList[$key]|default:"̤��Ͽ"}--></td>
										<td align="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win03('./payment_input.php?mode=pre_edit&payment_id=<!--{$arrPaymentListFree[cnt].payment_id}-->','payment_input','530','400'); return false;">�Խ�</a><!--{else}-->-<!--{/if}--></td>
										<td align="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">���</a><!--{else}-->-<!--{/if}--></td>
										<td align="center">
										<!--{if $smarty.section.cnt.iteration != 1}-->
										<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">���</a>
										<!--{/if}-->
										<!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
										<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">����</a>
										<!--{/if}-->
										</td>
									</tr>
									<!--{/section}-->
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="button" name="subm2" value="��ʧ��ˡ���ɲ�" onclick="win03('./payment_input.php','payment_input','550','400');" /></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--�ᥤ�󥨥ꥢ-->
			</table>
			<!--����Ͽ�ơ��֥뤳���ޤ�-->
		</td>
	</tr>
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->