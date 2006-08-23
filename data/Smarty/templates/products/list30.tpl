<!--¢§CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
	<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
	<td bgcolor="#ffffff" align="left">
	<!--¢§MAIN CONTENTS-->
	<!--¥Ñ¥ó¥¯¥º-->
		<!--{include_php file=$tpl_pankuzu_php}-->
	<!--¥Ñ¥ó¥¯¥º-->
	<table cellspacing="0" cellpadding="0" summary=" " id="container">
		<tr><td height="10"></td></tr>
		<tr valign="top">
		<!--¢§LEFT CONTENTS-->
		<td id="left">
		<!--¢§¥Ð¥Ê¡¼-->
			<!--{include file=$tpl_banner}-->
		<!--¢¥¥Ð¥Ê¡¼-->
		<!--¢§¾¦ÉÊ¸¡º÷-->
			<!--{include_php file=$tpl_search_products_php}-->
		<!--¢¥¾¦ÉÊ¸¡º÷-->
		
		<!--¢§¥«¥Æ¥´¥ê¡¼-->
			<!--{include_php file=$tpl_category_php}-->
		<!--¢¥¥«¥Æ¥´¥ê¡¼-->
		
		<!--¢§º¸¥Ê¥Ó-->
			<!--{include file=$tpl_leftnavi}-->
		<!--¢¥º¸¥Ê¥Ó-->
		
				<!--¢§º¸¥Ê¥Ó-->
					<!--{include file=$tpl_leftnavi}-->
				<!--¢¥º¸¥Ê¥Ó-->
		</td>
		<!--¢¥LEFT CONTENTS-->
		
		<!--¢§RIGHT CONTENTS-->
		<td id="right">
		<form name="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
		<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="orderby" value="<!--{$orderby}-->">
		<input type="hidden" name="product_id" value="">
		<!--{* ¾®¸«½Ð¤·²èÁü *}-->
			<!--{include file=$tpl_maintitle}-->
		<!--{* ¾®¸«½Ð¤·²èÁü *}-->
		<!--{if $tpl_linemax > 0}-->
		<div id="hit"><span class="red12st"><!--{$tpl_linemax}--></span><span class="fs12">·ï¤¬³ºÅö¤·¤Þ¤·¤¿</span><br />
		<span class="fs12">É½¼¨·ï¿ô¡¡
		<select name="disp_number" onChange="document.form1.submit();">
		<!--{html_options options=$arrPRODUCTLISTMAX selected=$disp_number}-->
		</select><br />
		<!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">²Á³Ê½ç</a><!--{else}--><strong>²Á³Ê½ç</strong><!--{/if}-->¡¡<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">¿·Ãå½ç</a><!--{else}--><strong>¿·Ãå½ç</strong><!--{/if}-->
		</div>
		<!--{else}-->
		<!--{include file="frontparts/search_zero.tpl"}-->
		<!--{/if}-->
		<div id="page">
		<!--¢§¥Ú¡¼¥¸¥Ê¥Ó-->
		<!--{$tpl_strnavi}-->
		<!--¢¥¥Ú¡¼¥¸¥Ê¥Ó-->
		</div>
		
		<table width="570" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<!--{section name=cnt loop=$arrProducts step=2}-->
			<tr valign="top">
				<!--{assign var=id value=$arrProducts[cnt].product_id}-->
				<!--{if $id != ""}-->
				<td>
				<!--±¦-->
				<table width="275" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr valign="top">
						<td>
						<!--1ÃÊÌÜ1-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--¾¦ÉÊ¼Ì¿¿--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[cnt].name|escape}-->" border="0" /></a></td>
							</tr>
						</table>
						</td>
						<td align="right">
						<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td>
								<table width="165" border="0" cellspacing="0" cellpadding="10" summary=" ">
									<tr>
										<td bgcolor="#efefef" class="fs12st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--¾¦ÉÊÌ¾--><!--{$arrProducts[cnt].name|escape}--></a></td>
									</tr>
								</table>
								<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<!--{assign var=price02_min value=$arrProducts[cnt].price02_min}-->
										<!--{assign var=price02_max value=$arrProducts[cnt].price02_max}-->
										<td><span class="fs10">¥È¡¼¥«Æ²²Á³Ê¡§</span><span class="red12st">
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{else}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->¡Á<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{/if}-->
										±ß</span><span class="red10">¡ÊÀÇ¹þ¡Ë</span><br />
										<!--{assign var=price01_min value=$arrProducts[cnt].price01_min}-->
										<!--{assign var=price01_max value=$arrProducts[cnt].price01_max}-->
										<!--{if $price01_max > 0}-->
										<span class="fs10">»²¹Í»Ô¾ì²Á³Ê¡§
										<!--{if $price01_min == $price01_max}-->
										<!--{$price01_min|number_format}-->
										<!--{else}-->
										<!--{$price01_min|number_format}-->¡Á<!--{$price01_max|number_format}-->
										<!--{/if}-->
										±ß</span><br />
										<!--{/if}-->
										<span class="fs10">¥Ý¥¤¥ó¥È¡§</span><span class="red12st">
										<!--{assign var=point_rate value=$arrProducts[cnt].point_rate}-->
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
										<!--{else}-->
											<!--{if $price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id == $price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
											<!--{else}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->¡Á<!--{$price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[cnt].product_id}-->
											<!--{/if}-->
										<!--{/if}-->
										</span><span class="red10">Pt</span> </td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<!--{assign var=index value=$smarty.section.cnt.index}-->
										<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail01<!--{$index}-->');" onmouseout="chgImg('../img/right_product/detail.gif','detail01<!--{$index}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="¾¦ÉÊ¤ò¾Ü¤·¤¯¸«¤ë" name="detail01<!--{$index}-->" border="0"/></a></td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<!--1ÃÊÌÜ1-->
						<!--{/if}-->
					</tr>
				</table>
				<!--±¦-->
				</td>
				<!--{assign var=nextcnt value=$smarty.section.cnt.index+1}-->
				<!--{assign var=id value=$arrProducts[$nextcnt].product_id}-->
				<!--{if $id != ""}-->
				<td align="right">
				<!--º¸-->
				<table width="275" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr valign="top">
						<td>
						<!--1ÃÊÌÜ2-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--¾¦ÉÊ¼Ì¿¿--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$nextcnt].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$nextcnt].name|escape}-->" border="0" /></a></td>
							</tr>
						</table>
						</td>
						<td align="right">
						<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td>
								<table width="165" border="0" cellspacing="0" cellpadding="10" summary=" ">
									<tr>
										<td bgcolor="#efefef" class="fs12st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--¾¦ÉÊÌ¾--><!--{$arrProducts[$nextcnt].name|escape}--></a></td>
									</tr>
								</table>
								<table width="165" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<!--{assign var=price02_min value=$arrProducts[$nextcnt].price02_min}-->
										<!--{assign var=price02_max value=$arrProducts[$nextcnt].price02_max}-->
										<td><span class="fs10">¥È¡¼¥«Æ²²Á³Ê¡§</span><span class="red12st">
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{else}-->
										<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->¡Á<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
										<!--{/if}-->
										±ß</span><span class="red10">¡ÊÀÇ¹þ¡Ë</span><br />
										<!--{assign var=price01_min value=$arrProducts[$nextcnt].price01_min}-->
										<!--{assign var=price01_max value=$arrProducts[$nextcnt].price01_max}-->
										<!--{if $price01_max > 0}-->
										<span class="fs10">»²¹Í»Ô¾ì²Á³Ê¡§
										<!--{if $price01_min == $price01_max}-->
										<!--{$price01_min|number_format}-->
										<!--{else}-->
										<!--{$price01_min|number_format}-->¡Á<!--{$price01_max|number_format}-->
										<!--{/if}-->
										±ß</span><br />
										<!--{/if}-->
										<span class="fs10">¥Ý¥¤¥ó¥È¡§</span><span class="red12st">
										<!--{assign var=point_rate value=$arrProducts[$nextcnt].point_rate}-->
										<!--{if $price02_min == $price02_max}-->
										<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
										<!--{else}-->
											<!--{if $price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id == $price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
											<!--{else}-->
											<!--{$price02_min|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->¡Á<!--{$price02_max|sfPrePoint:$point_rate:$smarty.const.POINT_RULE:$arrProducts[$nextcnt].product_id}-->
											<!--{/if}-->
										<!--{/if}-->
										</span><span class="red10">Pt</span></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail02<!--{$nextcnt}-->');" onmouseout="chgImg('../img/right_product/detail.gif','detail02<!--{$nextcnt}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="¾¦ÉÊ¤ò¾Ü¤·¤¯¸«¤ë" name="detail02<!--{$nextcnt}-->" border="0" /></a></td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						<!--1ÃÊÌÜ2-->
						</td>
					</tr>
				</table>
				<!--º¸-->
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
		<!--¢§¥Ú¡¼¥¸¥Ê¥Ó-->
		<!--{$tpl_strnavi}-->
		<!--¢¥¥Ú¡¼¥¸¥Ê¥Ó-->
		</div>
		<!--¢¥RIGHT CONTENTS-->
		</tr>
	</table>
	<!--¢¥MAIN CONTENTS-->
	</td>
	<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
	<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--¢¥CONTENTS-->
