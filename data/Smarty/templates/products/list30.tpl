<!--��CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
	<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
	<td bgcolor="#ffffff" align="left">
	<!--��MAIN CONTENTS-->
	<!--�ѥ󥯥�-->
		<!--{include_php file=$tpl_pankuzu_php}-->
	<!--�ѥ󥯥�-->
	<table cellspacing="0" cellpadding="0" summary=" " id="container">
		<tr><td height="10"></td></tr>
		<tr valign="top">
		<!--��LEFT CONTENTS-->
		<td id="left">
		<!--���Хʡ�-->
			<!--{include file=$tpl_banner}-->
		<!--���Хʡ�-->
		<!--�����ʸ���-->
			<!--{include_php file=$tpl_search_products_php}-->
		<!--�����ʸ���-->
		
		<!--�����ƥ��꡼-->
			<!--{include_php file=$tpl_category_php}-->
		<!--�����ƥ��꡼-->
		
		<!--�����ʥ�-->
			<!--{include file=$tpl_leftnavi}-->
		<!--�����ʥ�-->
		
				<!--�����ʥ�-->
					<!--{include file=$tpl_leftnavi}-->
				<!--�����ʥ�-->
		</td>
		<!--��LEFT CONTENTS-->
		
		<!--��RIGHT CONTENTS-->
		<td id="right">
		<form name="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
		<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="orderby" value="<!--{$orderby}-->">
		<input type="hidden" name="product_id" value="">
		<!--{* �����Ф����� *}-->
			<!--{include file=$tpl_maintitle}-->
		<!--{* �����Ф����� *}-->
		<!--{if $tpl_linemax > 0}-->
		<div id="hit"><span class="red12st"><!--{$tpl_linemax}--></span><span class="fs12">�郎�������ޤ���</span><br />
		<span class="fs12">ɽ�������
		<select name="disp_number" onChange="document.form1.submit();">
		<!--{html_options options=$arrPRODUCTLISTMAX selected=$disp_number}-->
		</select><br />
		<!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">���ʽ�</a><!--{else}--><strong>���ʽ�</strong><!--{/if}-->��<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">�����</a><!--{else}--><strong>�����</strong><!--{/if}-->
		</div>
		<!--{else}-->
		<!--{include file="frontparts/search_zero.tpl"}-->
		<!--{/if}-->
		<div id="page">
		<!--���ڡ����ʥ�-->
		<!--{$tpl_strnavi}-->
		<!--���ڡ����ʥ�-->
		</div>
		
		<table width="570" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<!--{section name=cnt loop=$arrProducts step=2}-->
			<tr valign="top">
				<!--{assign var=id value=$arrProducts[cnt].product_id}-->
				<!--{if $id != ""}-->
				<td>
				<!--��-->
				<table width="275" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr valign="top">
						<td>
						<!--1����1-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--���ʼ̿�--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[cnt].name|escape}-->" border="0" /></a></td>
							</tr>
						</table>
						</td>
						<td align="right">
						<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td>
								<table width="165" border="0" cellspacing="0" cellpadding="10" summary=" ">
									<tr>
										<td bgcolor="#efefef" class="fs12st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--����̾--><!--{$arrProducts[cnt].name|escape}--></a></td>
									</tr>
								</table>
								<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<!--{assign var=price02_min value=$arrProducts[cnt].price02_min}-->
										<!--{assign var=price02_max value=$arrProducts[cnt].price02_max}-->
										<td><span class="fs10">�ȡ���Ʋ���ʡ�</span><span class="red12st">
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{else}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{/if}-->
										��</span><span class="red10">���ǹ���</span><br />
										<!--{assign var=price01_min value=$arrProducts[cnt].price01_min}-->
										<!--{assign var=price01_max value=$arrProducts[cnt].price01_max}-->
										<!--{if $price01_max > 0}-->
										<span class="fs10">���ͻԾ���ʡ�
										<!--{if $price01_min == $price01_max}-->
										<!--{$price01_min|number_format}-->
										<!--{else}-->
										<!--{$price01_min|number_format}-->��<!--{$price01_max|number_format}-->
										<!--{/if}-->
										��</span><br />
										<!--{/if}-->
										<span class="fs10">�ݥ���ȡ�</span><span class="red12st">
										<!--{assign var=point_rate value=$arrProducts[cnt].point_rate}-->
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
										<!--{else}-->
											<!--{if $price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id == $price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
											<!--{else}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->��<!--{$price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
											<!--{/if}-->
										<!--{/if}-->
										</span><span class="red10">Pt</span> </td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<!--{assign var=index value=$smarty.section.cnt.index}-->
										<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail01<!--{$index}-->');" onmouseout="chgImg('../img/right_product/detail.gif','detail01<!--{$index}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="���ʤ�ܤ�������" name="detail01<!--{$index}-->" border="0"/></a></td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<!--1����1-->
						<!--{/if}-->
					</tr>
				</table>
				<!--��-->
				</td>
				<!--{assign var=nextcnt value=$smarty.section.cnt.index+1}-->
				<!--{assign var=id value=$arrProducts[$nextcnt].product_id}-->
				<!--{if $id != ""}-->
				<td align="right">
				<!--��-->
				<table width="275" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr valign="top">
						<td>
						<!--1����2-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--���ʼ̿�--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$nextcnt].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$nextcnt].name|escape}-->" border="0" /></a></td>
							</tr>
						</table>
						</td>
						<td align="right">
						<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td>
								<table width="165" border="0" cellspacing="0" cellpadding="10" summary=" ">
									<tr>
										<td bgcolor="#efefef" class="fs12st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--����̾--><!--{$arrProducts[$nextcnt].name|escape}--></a></td>
									</tr>
								</table>
								<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<!--{assign var=price02_min value=$arrProducts[$nextcnt].price02_min}-->
										<!--{assign var=price02_max value=$arrProducts[$nextcnt].price02_max}-->
										<td><span class="fs10">�ȡ���Ʋ���ʡ�</span><span class="red12st">
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{else}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{/if}-->
										��</span><span class="red10">���ǹ���</span><br />
										<!--{assign var=price01_min value=$arrProducts[$nextcnt].price01_min}-->
										<!--{assign var=price01_max value=$arrProducts[$nextcnt].price01_max}-->
										<!--{if $price01_max > 0}-->
										<span class="fs10">���ͻԾ���ʡ�
										<!--{if $price01_min == $price01_max}-->
										<!--{$price01_min|number_format}-->
										<!--{else}-->
										<!--{$price01_min|number_format}-->��<!--{$price01_max|number_format}-->
										<!--{/if}-->
										��</span><br />
										<!--{/if}-->
										<span class="fs10">�ݥ���ȡ�</span><span class="red12st">
										<!--{assign var=point_rate value=$arrProducts[$nextcnt].point_rate}-->
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
										<!--{else}-->
											<!--{if $price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id == $price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
											<!--{else}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->��<!--{$price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
											<!--{/if}-->
										<!--{/if}-->
										</span><span class="red10">Pt</span></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail02<!--{$nextcnt}-->');" onmouseout="chgImg('../img/right_product/detail.gif','detail02<!--{$nextcnt}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="���ʤ�ܤ�������" name="detail02<!--{$nextcnt}-->" border="0" /></a></td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						<!--1����2-->
						</td>
					</tr>
				</table>
				<!--��-->
				</td>
			</tr>
			<!--{/if}-->
			<tr>
				<td height="30"><img src="../img/right_product/line_half.gif" width="275" height="1" alt="" /></td>
				<td align="right"><img src="../img/right_product/line_half.gif" width="275" height="1" alt="" /></td>
			</tr>
			<!--{/section}-->
			<tr><td height="30"></td></tr>
			</form>
		</table>
		
		<div id="page">
		<!--���ڡ����ʥ�-->
		<!--{$tpl_strnavi}-->
		<!--���ڡ����ʥ�-->
		</div>
		<!--��RIGHT CONTENTS-->
		</tr>
	</table>
	<!--��MAIN CONTENTS-->
	</td>
	<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
	<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--��CONTENTS-->
