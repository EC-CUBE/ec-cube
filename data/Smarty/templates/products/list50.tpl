<script type="text/javascript">
<!--
// セレクトボックスに項目を割り当てる。
function lnSetSelect(name1, name2, id, val) {
	sele1 = document.form1[name1];
	sele2 = document.form1[name2];
	lists = eval('lists' + id);
	vals = eval('vals' + id);
	
	if(sele1 && sele2) {
		index = sele1.selectedIndex;
		
		// セレクトボックスのクリア
		count = sele2.options.length;
		for(i = count; i >= 0; i--) {
			sele2.options[i] = null;
		}
		
		// セレクトボックスに値を割り当てる
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

<!--▼CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left">
		<!--▼MAIN CONTENTS-->
		<!--パンクズ-->
			<!--{include_php file=$tpl_pankuzu_php}-->
		<!--パンクズ-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">
				<!--▼LEFT CONTENTS-->
				<td id="left">
				
				<!--▼バナー-->
					<!--{include file=$tpl_banner}-->
				<!--▲バナー-->
				
				<!--▼商品検索-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--▲商品検索-->
				
				<!--▼カテゴリ-->
					<!--{include_php file=$tpl_category_php}-->
				<!--▲カテゴリ-->
				
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->				
				</td>
				<!--▲LEFT CONTENTS-->
				<!--▼RIGHT CONTENTS-->
				<td id="right">
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
				<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
				<input type="hidden" name="mode" value="">
				<input type="hidden" name="orderby" value="<!--{$orderby}-->">
				<input type="hidden" name="product_id" value="">
				<!--{* 小見出し画像 *}-->
					<!--{include file=$tpl_maintitle}-->
				<!--{* 小見出し画像 *}-->
				<!--{if $tpl_linemax > 0}-->
				<div id="hit"><span class="red12st"><!--{$tpl_linemax}--></span><span class="fs12">件が該当しました</span><br />
				<span class="fs12">表示件数　
				<select name="disp_number" onChange="document.form1.submit();">
				<!--{html_options options=$arrPRODUCTLISTMAX selected=$disp_number}-->
				</select><br />
				<!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">価格順</a><!--{else}--><strong>価格順</strong><!--{/if}-->　<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">新着順</a><!--{else}--><strong>新着順</strong><!--{/if}-->
				</div>
				<!--{else}-->
				<!--{include file="frontparts/search_zero.tpl"}-->
				<!--{/if}-->
				
				<div id="page">
				<!--▼ページナビ-->
				<!--{$tpl_strnavi}-->
				<!--▲ページナビ-->
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
						<!--1段目1-->
						<!--{assign var=id value=$arrProducts[cnt].product_id}-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90×90写真-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[cnt].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90×90写真-->
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
								<td><span class="fs10">トーカ堂価格:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								円</span><span class="red10">（税込）</span></td>
							</tr>
						</table>
						<!--1段目1-->
						</td>
						<!--{assign var=cnt2 value=$smarty.section.cnt.index+1}-->
						<!--{assign var=id value=$arrProducts[$cnt2].product_id}-->
						<!--{if $id != ""}-->
						<td background="../img/right_product/line_side.gif"><img src="../img/_.gif" width="10" height="1" alt=""></td>
						<td>
						<!--1段目2-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90×90写真-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$cnt2].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$cnt2].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90×90写真-->
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
								<td><span class="fs10">トーカ堂価格:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								円</span><span class="red10">（税込）</span></td>
							</tr>
						</table>
						<!--{/if}-->
						<!--1段目2-->
						</td>
						<!--{assign var=cnt3 value=$smarty.section.cnt.index+2}-->
						<!--{assign var=id value=$arrProducts[$cnt3].product_id}-->
						<!--{if $id != ""}-->
						<td background="../img/right_product/line_side.gif"><img src="../img/_.gif" width="10" height="1" alt=""></td>
						<td>
						<!--1段目3-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90×90写真-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$cnt3].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$cnt3].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90×90写真-->
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
								<td><span class="fs10">トーカ堂価格:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								円</span><span class="red10">（税込）</span></td>
							</tr>
						</table>
						<!--{/if}-->
						<!--1段目3-->
						</td>
						<!--{assign var=cnt4 value=$smarty.section.cnt.index+3}-->
						<!--{assign var=id value=$arrProducts[$cnt4].product_id}-->
						<!--{if $id != ""}-->
						<td background="../img/right_product/line_side.gif"><img src="../img/_.gif" width="10" height="1" alt=""></td>
						<td>
						<!--1段目4-->
						<table width="92" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--90×90写真-->
							<tr>
								<td bgcolor="#cccccc" height="92" align="center"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$id}-->.html"><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[$cnt4].main_list_image}-->" width="90" height="90" alt="<!--{$arrProducts[$cnt4].name|escape}-->" border="0"></a></td>
							</tr>
							<!--90×90写真-->
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
								<td><span class="fs10">トーカ堂価格:</span><br>
								<span class="red12st">
								<!--{if $price02_min == $price02_max}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
								<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								円</span><span class="red10">（税込）</span></td>
							</tr>
						</table>
						<!--{/if}-->
						<!--1段目4-->
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
				<!--▼ページナビ-->
				<!--{$tpl_strnavi}-->
				<!--▲ページナビ-->
				</div>
				</form>
				<!--▲RIGHT CONTENTS-->
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
		<!--▲MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--▲CONTENTS-->