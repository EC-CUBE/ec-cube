<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
<!--
// ���쥯�ȥܥå����˹��ܤ������Ƥ롣
function lnSetSelect(name1, name2, id, val) {
	sele1 = document.form1[name1];
	sele2 = document.form1[name2];
	lists = eval('lists' + id);
	vals = eval('vals' + id);
	
	if(sele1 && sele2) {
		index = sele1.selectedIndex;
		
		// ���쥯�ȥܥå����Υ��ꥢ
		count = sele2.options.length;
		for(i = count; i >= 0; i--) {
			sele2.options[i] = null;
		}
		
		// ���쥯�ȥܥå������ͤ������Ƥ�
		len = lists[index].length;
		for(i = 0; i < len; i++) {
			sele2.options[i] = new Option(lists[index][i], vals[index][i]);
			if(val != "" && vals[index][i] == val) {
				sele2.options[i].selected = true;
			}
		}
	}
}
//-->
</script>

<!--��CONTENTS-->
<table width="" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="left">
		<!--��MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr valign="top">
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
				<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
				<input type="hidden" name="mode" value="">
				<input type="hidden" name="orderby" value="<!--{$orderby}-->">
				<input type="hidden" name="product_id" value="">

				<td id="right">
				<!--�����ȥ뤳������-->
				<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/products/title_top.gif" width="580" height="8" alt=""></td></tr>
					<tr bgcolor="#ffebca">
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/products/title_icon.gif" width="29" height="24" alt=""></td>
						<td>
						<table width="546" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr valign="top">
								<td class="fs18"><span class="blackst"><!--�������ȥ��--><!--{$tpl_subtitle}--></span></td>
							</tr>
						</table>
						</td>
						<td><img src="<!--{$smarty.const.URL_DIR}-->img/products/title_left.gif" width="5" height="24" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/products/title_under.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="10"></td></tr>
				</table>
				<!--�����ȥ뤳���ޤ�-->

				<!--������盧������-->
				<!--{if $tpl_subtitle == "�������"}-->
				<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr>
						<td align="center" bgcolor="#f3f3f3">
						<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr bgcolor="#9e9e9e">
								<td rowspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
								<td bgcolor="#9e9e9e"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="558" height="1" alt=""></td>
								<td rowspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
							</tr>
							<tr>
								<td align="center" bgcolor="#ffffff">
								<table width="540" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="10"></td></tr>
									<tr>
										<td class="fs12"><!--��������̡�--><span class="blackst">���ʥ��ƥ��ꡧ</span><span class="black"><!--{$arrSearch.category|escape}--></span><br>
										<span class="blackst">����̾��</span><span class="black"><!--{$arrSearch.name|escape}--></span>
									</tr>
									<tr><td height="10"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td bgcolor="#9e9e9e"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="558" height="1" alt=""></td></tr>
						</table>
						</td>
					</tr>
					<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="15"></td></tr>
				</table>
				<!--{/if}-->
				<!--������盧���ޤ�-->
				
				<!--�����������-->
				<!--{if $tpl_linemax > 0}-->
				<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr>
						<td align="center" bgcolor="#f3f3f3">
						<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12" align="left"><span class="redst"><!--{$tpl_linemax}--></span>��ξ��ʤ��������ޤ���<!--{$tpl_strnavi}--></td>
								<td class="fs12" align="right"><!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">���ʽ�</a><!--{else}--><strong>���ʽ�</strong><!--{/if}-->��<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">�����</a><!--{else}--><strong>�����</strong><!--{/if}--></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="15"></td></tr>
				</table>
				<!--��������ޤ�-->
				<!--{else}-->
				<!--{include file="frontparts/search_zero.tpl"}-->
				<!--{/if}-->

				<table width="580" cellspacing="0" cellpadding="0" summary=" " id="contents">
				<!--{section name=cnt loop=$arrProducts}-->
				<!--{assign var=id value=$arrProducts[cnt].product_id}-->
				<!--�����ʤ�������-->
				<tr valign="top">
					<td><a name="product<!--{$id}-->" id="product<!--{$id}-->"></a></td>
					<td align="center" valign="middle"><!--��������--><div id="picture"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->"><!--���ʼ̿�--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" alt="<!--{$arrProducts[cnt].name|escape}-->" /></a></div></td>
					<td align="right">
						<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--��������-->
							<tr>
								<td colspan="2">
								<!--���ʥ��ơ�����-->
								<!--{assign var=sts_cnt value=0}-->
								<!--{section name=flg loop=$arrProducts[cnt].product_flag|count_characters}-->
									<!--{if $arrProducts[cnt].product_flag[flg] == "1"}-->
										<!--{assign var=key value="`$smarty.section.flg.iteration`"}--><img src="<!--{$arrSTATUS_IMAGE[$key]}-->" width="65" height="17" alt="<!--{$arrSTATUS[$key]}-->"/>
										<!--{assign var=sts_cnt value=$sts_cnt+1}-->
									<!--{/if}-->
								<!--{/section}-->
								<!--���ʥ��ơ�����-->
								</td>
							</tr>
							<!--��������-->
							<!--{if $sts_cnt > 0}-->
							<tr><td height="8"></td></tr>
							<!--{/if}-->
							<tr>
								<td colspan="2" align="center" bgcolor="#f9f9ec">
								<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs14"><!--������̾��-->��<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->" class="over"><!--����̾--><strong><!--{$arrProducts[cnt].name|escape}--></strong></a></td>
									</tr>
									<tr><td height="5"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td colspan="2" bgcolor="#ebebd6"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="2" alt=""></td></tr>
							<tr><td height="8"></td></tr>
							<tr>
								<td colspan="2" class="fs12"><!--�������ȡ�--><!--{$arrProducts[cnt].main_list_comment|escape|nl2br}--></td>
							</tr>
							<tr><td height="8"></td></tr>
							<tr>
								<td>
									<span class="red"><span class="fs12">����</span><span class="fs10">(�ǹ�)</span></span><span class="redst"><span class="fs12">��
									<!--{if $arrProducts[cnt].price02_min == $arrProducts[cnt].price02_max}-->
										<!--{$arrProducts[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrProducts[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->��<!--{$arrProducts[cnt].price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
									<!--{/if}-->
									��</span></span>
								</td>
								<!--{assign var=name value="detail`$smarty.section.cnt.iteration`"}-->
								<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_detail_on.gif','<!--{$name}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_detail.gif','<!--{$name}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_detail.gif" width="115" height="25" alt="�ܤ����Ϥ�����" name="<!--{$name}-->" id="<!--{$name}-->" /></a></td>
							</tr>
							<!--{if $arrProducts[cnt].stock_max == 0 && $arrProducts[cnt].stock_unlimited_max != 1}-->
								<tr>
									<td class="fs12"><span class="red">�������������ޤ��󤬡��������ڤ���Ǥ���</span></td>
								</tr>
							<!--{else}-->
								<!--���㤤ʪ����-->
								<tr><td height=5></td></tr>
								<tr valign="top" align="right" id="price">
									<td id="right" colspan=2>
										<table cellspacing="0" cellpadding="0" summary=" " id="price">
											<tr>
												<td align="center">
												<table width="285" cellspacing="0" cellpadding="0" summary=" ">
													<!--{if $tpl_classcat_find1[$id]}-->
													<!--{assign var=class1 value=classcategory_id`$id`_1}-->
													<!--{assign var=class2 value=classcategory_id`$id`_2}-->
													<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class1] != ""}-->�� <!--{$tpl_class_name1[$id]}-->�����Ϥ��Ʋ�������<!--{/if}--></span></td></tr>
													<tr>
														<td align="right" class="fs12st"><!--{$tpl_class_name1[$id]|escape}-->�� </td>
														<td>
															<select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
															<option value="">���򤷤Ƥ�������</option>
															<!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
															</select>
														</td>
													</tr>
													<!--{/if}-->
													<!--{if $tpl_classcat_find2[$id]}-->
													<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class2] != ""}-->�� <!--{$tpl_class_name2[$id]}-->�����Ϥ��Ʋ�������<!--{/if}--></span></td></tr>
													<tr>
														<td align="right" class="fs12st"><!--{$tpl_class_name2[$id]|escape}-->�� </td>
														<td>
															<select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
															<option value="">���򤷤Ƥ�������</option>
															</select>
														</td>
													</tr>
													<!--{/if}-->
													<!--{assign var=quantity value=quantity`$id`}-->		
													<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{$arrErr[$quantity]}--></span></td></tr>
													<tr>
														<td align="right" width="115" class="fs12st">�Ŀ��� 
															<!--{if $arrErr.quantity != ""}--><br/><span class="redst"><!--{$arrErr.quantity}--></span><!--{/if}-->
															<input type="text" name="<!--{$quantity}-->" size="3" class="box3" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" >
														</td>
														<td width="170" align="center">
															<a href="" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="�����������" name="cart<!--{$id}-->" id="cart<!--{$id}-->" /></a>
														</td>
													</tr>
													<tr><td height="10"></td></tr>
												</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<!--���㤤ʪ����-->	
							<!--{/if}-->					
						</table>
					</td>
				</tr>
				<tr><td colspan=3 height="40"><img src="<!--{$smarty.const.URL_DIR}-->img/common/line_580.gif" width="580" height="1" alt=""></td></tr>
				<!--{/section}-->
				</table>

				<!--�����������-->
				<!--{if $tpl_linemax > 0}-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr>
						<td align="center" bgcolor="#f3f3f3">
						<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12" align="left"><span class="redst"><!--{$tpl_linemax}--></span>��ξ��ʤ��������ޤ���<!--{$tpl_strnavi}--></td>
								<td class="fs12" align="right"><!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">���ʽ�</a><!--{else}--><strong>���ʽ�</strong><!--{/if}-->��<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">�����</a><!--{else}--><strong>�����</strong><!--{/if}--> </td>
							</tr>
						</table>
						</td>
					</tr>
					<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="15"></td></tr>
				</table>
				<!--��������ޤ�-->
				<!--{/if}-->
				</form>
				<!--��RIGHT CONTENTS-->
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->