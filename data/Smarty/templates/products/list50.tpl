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
				
				<!--�����ƥ���-->
					<!--{include_php file=$tpl_category_php}-->
				<!--�����ƥ���-->
				
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
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
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
					<tr>
						<td><img src="../img/_.gif" width="130" height="1" alt="" /></td>
						<td><img src="../img/_.gif" width="17" height="1" alt=""></td>
						<td><img src="../img/_.gif" width="130" height="1" alt=""></td>
						<td><img src="../img/_.gif" width="16" height="1" alt=""></td>
						<td><img src="../img/_.gif" width="130" height="1" alt=""></td>
						<td><img src="../img/_.gif" width="17" height="1" alt=""></td>
						<td><img src="../img/_.gif" width="130" height="1" alt=""></td>
					</tr>
					<!--{section name=cnt loop=$arrProducts step=4}-->
					<tr valign="top" align="center">
						<td>
						<!--1����1-->
						<!--{assign var=id value=$arrProducts[cnt].product_id}-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90��90�̿�-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[cnt].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90��90�̿�-->
						</table>
						<table width="110" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td><span class="fs10st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--{$arrProducts[cnt].name|escape}--></a></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<!--{assign var=price02_min value=$arrProducts[cnt].price02_min}-->
								<!--{assign var=price02_max value=$arrProducts[cnt].price02_max}-->
								<td><span class="fs10">�ȡ���Ʋ����:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								��</span><span class="red10">���ǹ���</span></td>
							</tr>
						</table>
						<!--1����1-->
						</td>
						<!--{assign var=cnt2 value=$smarty.section.cnt.index+1}-->
						<!--{assign var=id value=$arrProducts[$cnt2].product_id}-->
						<!--{if $id != ""}-->
						<td background="../img/right_product/line_side.gif"><img src="../img/_.gif" width="10" height="1" alt=""></td>
						<td>
						<!--1����2-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90��90�̿�-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$cnt2].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$cnt2].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90��90�̿�-->
						</table>
						<table width="110" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td><span class="fs10st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--{$arrProducts[$cnt2].name|escape}--></a></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<!--{assign var=price02_min value=$arrProducts[$cnt2].price02_min}-->
								<!--{assign var=price02_max value=$arrProducts[$cnt2].price02_max}-->
								<td><span class="fs10">�ȡ���Ʋ����:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								��</span><span class="red10">���ǹ���</span></td>
							</tr>
						</table>
						<!--{/if}-->
						<!--1����2-->
						</td>
						<!--{assign var=cnt3 value=$smarty.section.cnt.index+2}-->
						<!--{assign var=id value=$arrProducts[$cnt3].product_id}-->
						<!--{if $id != ""}-->
						<td background="../img/right_product/line_side.gif"><img src="../img/_.gif" width="10" height="1" alt=""></td>
						<td>
						<!--1����3-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90��90�̿�-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$cnt3].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$cnt3].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90��90�̿�-->
						</table>
						<table width="110" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td><span class="fs10st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--{$arrProducts[$cnt3].name|escape}--></a></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<!--{assign var=price02_min value=$arrProducts[$cnt3].price02_min}-->
								<!--{assign var=price02_max value=$arrProducts[$cnt3].price02_max}-->
								<td><span class="fs10">�ȡ���Ʋ����:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								��</span><span class="red10">���ǹ���</span></td>
							</tr>
						</table>
						<!--{/if}-->
						<!--1����3-->
						</td>
						<!--{assign var=cnt4 value=$smarty.section.cnt.index+3}-->
						<!--{assign var=id value=$arrProducts[$cnt4].product_id}-->
						<!--{if $id != ""}-->
						<td background="../img/right_product/line_side.gif"><img src="../img/_.gif" width="10" height="1" alt=""></td>
						<td>
						<!--1����4-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90��90�̿�-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$cnt4].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$cnt4].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90��90�̿�-->
						</table>
						<table width="110" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td><span class="fs10st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><!--{$arrProducts[$cnt4].name|escape}--></a></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<!--{assign var=price02_min value=$arrProducts[$cnt4].price02_min}-->
								<!--{assign var=price02_max value=$arrProducts[$cnt4].price02_max}-->
								<td><span class="fs10">�ȡ���Ʋ����:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								��</span><span class="red10">���ǹ���</span></td>
							</tr>
						</table>
						<!--{/if}-->
						<!--1����4-->
						</td>
					</tr>
					<!--{if $smarty.section.cnt.total >= 2 && !$smarty.section.cnt.last}-->
					<tr>
						<td colspan="7" height="30"><img src="../img/right_product/line_top.gif" width="570" height="1" alt="" /></td>
					</tr>
					<!--{/if}-->
					<!--{/section}-->
					<tr><td height="30"></td></tr>
				</table>
				
				<div id="page">
				<!--���ڡ����ʥ�-->
				<!--{$tpl_strnavi}-->
				<!--���ڡ����ʥ�-->
				</div>
				</form>
				<!--��RIGHT CONTENTS-->
				</tr>
			<tr>
				<td bgcolor="#ffffff">
				<!-- EBiS start -->
				<script type="text/javascript">
				if ( location.protocol == 'http:' ){ 
					strServerName = 'http://daikoku.ebis.ne.jp'; 
				} else { 
					strServerName = 'https://secure2.ebis.ne.jp/ver3';
				}
				cid = 'tqYg3k6U'; pid = 'list-c<!--{$category_id}-->'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
				document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
				</script>
				<!-- EBiS end -->								
				</td>
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--��CONTENTS-->