<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--��CONTENTS-->
<table width="" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="left"> 
		<!--��MAIN CONTENTS-->
		<!--�ѥ󥯥�-->
		<div id="pan"><span class="fs12n"><a href="<!--{$smarty.const.URL_DIR}-->index.php">�ȥåץڡ���</a>��<span class="redst">���������꾦��</span></span></div>
		<!--�ѥ󥯥�-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">
			
				<!--��RIGHT CONTENTS-->
				<td id="right">
				<!--�����ȥ�-->
				<div id="maintitle"><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="570" height="40" alt="�ޥ��ڡ���" /></div>
				<!--�����ȥ�-->
				<!--��MY�ڡ����ʥ�-->
				<!--{include file=$tpl_navi}-->
				<!--��MY�ڡ����ʥ�-->
				<!--���֥����ȥ�-->
				<div id="subtitle"><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle07.gif" width="110" height="16" alt="��������" /></div>
				<!--���֥����ȥ�-->
				
				<!--{if $arrForm}-->
				<div id="comment"><span class="fs12">��󥯥�å��Ǳ������ʤΥڡ����˹Ԥ������Ǥ��ޤ���<br />
				<span class="asterisk">��</span>����<!--{$smarty.const.CUSTOMER_READING_MAX}-->��ޤ�ɽ�����ޤ���</span></div>
				<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
				<input type="hidden" name="product_id" value="">
				<input type="hidden" name="mode" value="">
				<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
				
				<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr class="fs12">
					<tr><td height="5"></td></tr>
				<tr class="fs12">
					<td align="center">
					<!--���ڡ����ʥ�-->
					<!--{$tpl_strnavi}-->
					<!--���ڡ����ʥ�-->
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				</table>
				
				<table cellspacing="1" cellpadding="10" summary=" " id="frame">

					<tr class="fs12n">
						<td class="left01b">���</td>
						<td class="left02b">��������</td>
						<td class="left03b">����̾</td>
						<td class="left04b">ñ��</td>
					</tr>
					<!--{section name=cnt loop=$arrForm}-->
					<tr class="fs12n">
						<td class="left01w"><a href="#" onclick="fnModeSubmit('delete','product_id','<!--{$arrForm[cnt].reading_product_id}-->');" >���</a></td>
						<td class="left02w"><!--{$arrForm[cnt].update_date|sfDispDBDate}--></td>
						<td class="left03w"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrForm[cnt].reading_product_id}-->"><!--{$arrForm[cnt].name|escape}--></a></td>
						<!--{assign var=price02_min value=$arrForm[cnt].price02_min}-->
						<!--{assign var=price02_max value=$arrForm[cnt].price02_max}-->
						<td class="left04w">
						<!--{if $price02_min == $price02_max}-->
						<!--{$price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
						<!--{else}-->
						<!--{$price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->��<br/>��<br/><!--{$price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
						<!--{/if}-->
						��</td>
					</tr>
					<!--{/section}-->
					
				</table>
				
				<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="5"></td></tr>
				<tr class="fs12">
					<td align="center">
					<!--���ڡ����ʥ�-->
					<!--{$tpl_strnavi}-->
					<!--���ڡ����ʥ�-->
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				</table>
				</form>
				<!--{else}-->
				<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="5"></td></tr>
				<tr class="fs12">
					<td align="center">��������Ϥ���ޤ���</td>
				</tr>
				<tr><td height="10"></td></tr>
				</table>
				<!--{/if}-->
				
				</td>
				<!--��RIGHT CONTENTS-->
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
		</td>

	</tr>
</table>
<!--��CONTENTS-->