<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
							<form name="form1" id="form1" method="post" action="">
							<!--{foreach key=key item=item from=$arrSearchHidden}-->
								<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
							<!--{/foreach}-->
							<input type="hidden" name="mode" value="edit">
							<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
							<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->������Ͽ</span></td>
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
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">����̾</td>
										<td bgcolor="#ffffff" width="557" class="fs12n"><!--{$arrForm.product_name}--></td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">����1<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs12n">
										<span class="red12"><!--{$arrErr.select_class_id1}--></span>
										<select name="select_class_id1">
											<option value="">���򤷤Ƥ�������</option>
											<!--{html_options options=$arrClass selected=$arrForm.select_class_id1}-->
										</select>
										</td>
									</tr>
									<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">����2</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<span class="red12"><!--{$arrErr.select_class_id2}--></span>
									<select name="select_class_id2">
										<option value="">���򤷤Ƥ�������</option>
										<!--{html_options options=$arrClass selected=$arrForm.select_class_id2}-->
									</select>
									</td>
									</tr>
									<tr>
										<td align="center" bgcolor="#f2f1ec" colspan=2>
											<input type="button" value="������̤����" onclick="fnChangeAction('<!--{$smarty.const.URL_SEARCH_TOP}-->'); fnModeSubmit('search','',''); return false;" >
											<input type="button" name="btn" value="ɽ������" onclick="fnModeSubmit('disp','','')">
											<!--{if count($arrClassCat) > 0}-->
											<input type="button" name="btn" value="�������" onclick="fnModeSubmit('delete','','');">
											<!--{/if}-->
										</td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>

								<!--{if count($arrClassCat) > 0}-->
						
									<!--{foreach item=item name=i from=$arrClassCat}-->
									<!--{if $smarty.foreach.i.first}-->
									<!--{assign var=cnt value=$smarty.foreach.i.total}-->	
									<!--{/if}-->
									<!--{/foreach}-->
						
									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<tr bgcolor="#f2f1ec">
											<td class="fs12n" align="left">
												<a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnAllCheck(); return false;">������</a>��
												<a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnAllUnCheck(); return false;">�����</a>��
												<a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnCopyValue('<!--{$cnt}-->', '<!--{$smarty.const.DISABLED_RGB}-->'); return false;">����ܤΥǡ����򥳥ԡ�����</a></td>
										</tr>
									</table>

									<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
										<!--{assign var=class_id1 value=$arrForm.class_id1}-->
										<!--{assign var=class_id2 value=$arrForm.class_id2}-->
										<input type="hidden" name="class_id1" value="<!--{$class_id1}-->">
										<input type="hidden" name="class_id2" value="<!--{$class_id2}-->">
										<tr bgcolor="#f2f1ec" align="center" class="fs12n">
											<td width="30">��Ͽ</td>
											<td width="100">����1(<!--{$arrClass[$class_id1]|default:"̤����"}-->)</td>
											<td width="100">����2(<!--{$arrClass[$class_id2]|default:"̤����"}-->)</td>
											<td width="80">���ʥ�����</td>
											<td width="160">�߸�(��)</td>
											<td width="100">���ͻԾ����(��)</td>
											<td width="100">����(��)</td>
										</tr>
										<!--{section name=cnt loop=$arrClassCat}-->
										<!--{assign var=key value="error:`$smarty.section.cnt.iteration`"}-->
										<!--{if $arrErr[$key] != ""}-->
										<tr bgcolor="#ffffff" class="fs12">
											<td bgcolor="#ffffff" class="fs12" colspan="8"><span class="red12"><!--{$arrErr[$key]}--></span></td>
										</tr>
										<!--{/if}-->
										<tr  bgcolor="#ffffff" class="fs10n">
											<input type="hidden" name="classcategory_id1:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].classcategory_id1}-->">
											<input type="hidden" name="classcategory_id2:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].classcategory_id2}-->">
											<input type="hidden" name="name1:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].name1}-->">
											<input type="hidden" name="name2:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].name2}-->">
											<!--{assign var=key value="check:`$smarty.section.cnt.iteration`"}-->
											<td align="center"><input type="checkbox" name="check:<!--{$smarty.section.cnt.iteration}-->" value="1" <!--{if $arrForm[$key] == 1}-->checked="checked"<!--{/if}-->></td>
											<td><!--{$arrClassCat[cnt].name1}--></td>
											<td><!--{$arrClassCat[cnt].name2}--></td>
											<!--{assign var=key value="product_code:`$smarty.section.cnt.iteration`"}-->
											<td align="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.STEXT_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
											<!--{assign var=key value="stock:`$smarty.section.cnt.iteration`"}-->
											<!--{assign var=chkkey value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
											<td align="center">
											<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
											<!--{assign var=key value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
											<input type="checkbox" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key] == "1"}-->checked<!--{/if}--> onClick="fnCheckStockNoLimit('<!--{$smarty.section.cnt.iteration}-->','<!--{$smarty.const.DISABLED_RGB}-->');"/>̵����</td>
											</td>
											<!--{assign var=key value="price01:`$smarty.section.cnt.iteration`"}-->
											<td align="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
											<!--{assign var=key value="price02:`$smarty.section.cnt.iteration`"}-->
											<td align="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
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
												<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" ></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								
								<!--{/if}-->
								
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