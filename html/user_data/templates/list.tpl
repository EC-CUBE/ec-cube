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
<table width="" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="left">
		<!--▼MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr valign="top">
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
				<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
				<input type="hidden" name="mode" value="">
				<input type="hidden" name="orderby" value="<!--{$orderby}-->">
				<input type="hidden" name="product_id" value="">

				<td id="right">
				<!--タイトルここから-->
				<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td colspan="3"><img src="../img/products/title_top.gif" width="580" height="8" alt=""></td></tr>
					<tr bgcolor="#ffebca">
						<td><img src="../img/products/title_icon.gif" width="29" height="24" alt=""></td>
						<td>
						<table width="546" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr valign="top">
								<td class="fs18"><span class="blackst"><!--★タイトル★--><!--{$tpl_subtitle}--></span></td>
							</tr>
						</table>
						</td>
						<td><img src="../img/products/title_left.gif" width="5" height="24" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="../img/products/title_under.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="10"></td></tr>
				</table>
				<!--タイトルここまで-->

				<!--検索条件ここから-->
				<!--{if $tpl_subtitle == "検索結果"}-->
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
										<td class="fs12"><!--★検索結果★--><span class="blackst">商品カテゴリ：</span><span class="black"><!--{$arrSearch.category|escape}--></span><br>
										<span class="blackst">商品名：</span><span class="black"><!--{$arrSearch.name|escape}--></span>
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
				<!--検索条件ここまで-->
				
				<!--件数ここから-->
				<!--{if $tpl_linemax > 0}-->
				<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td><img src="../img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr>
						<td align="center" bgcolor="#f3f3f3">
						<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12" align="left"><span class="redst"><!--{$tpl_linemax}--></span>件の商品がございます。<!--{$tpl_strnavi}--></td>
								<td class="fs12" align="right"><!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">価格順</a><!--{else}--><strong>価格順</strong><!--{/if}-->　<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">新着順</a><!--{else}--><strong>新着順</strong><!--{/if}--></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr><td><img src="../img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="15"></td></tr>
				</table>
				<!--件数ここまで-->
				<!--{else}-->
				<!--{include file="frontparts/search_zero.tpl"}-->
				<!--{/if}-->

				<table width="580" cellspacing="0" cellpadding="0" summary=" " id="contents">
				<!--{section name=cnt loop=$arrProducts}-->
				<!--{assign var=id value=$arrProducts[cnt].product_id}-->
				<!--▼商品ここから-->
				<tr valign="top">
					<td><a name="product<!--{$id}-->" id="product<!--{$id}-->"></a></td>
					<td><!--★画像★--><div id="picture"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->"><!--商品写真--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" width="130" height="130" alt="<!--{$arrProducts[cnt].name|escape}-->" /></a></div></td>
					<td align="right">
						<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--アイコン-->
							<tr>
								<td colspan="2">
								<!--商品ステータス-->
								<!--{assign var=sts_cnt value=0}-->
								<!--{section name=flg loop=$arrProducts[cnt].product_flag|count_characters}-->
									<!--{if $arrProducts[cnt].product_flag[flg] == "1"}-->
										<!--{assign var=key value="`$smarty.section.flg.iteration`"}--><img src="<!--{$arrSTATUS_IMAGE[$key]}-->" width="65" height="17" alt="<!--{$arrSTATUS[$key]}-->"/>
										<!--{assign var=sts_cnt value=$sts_cnt+1}-->
									<!--{/if}-->
								<!--{/section}-->
								<!--商品ステータス-->
								</td>
							</tr>
							<!--アイコン-->
							<!--{if $sts_cnt > 0}-->
							<tr><td height="8"></td></tr>
							<!--{/if}-->
							<tr>
								<td colspan="2" align="center" bgcolor="#f9f9ec">
								<table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs14"><!--★商品名★-->　<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->" class="over"><!--商品名--><strong><!--{$arrProducts[cnt].name|escape}--></strong></a></td>
									</tr>
									<tr><td height="5"></td></tr>
								</table>
								</td>
							</tr>
							<tr><td colspan="2" bgcolor="#ebebd6"><img src="../img/common/_.gif" width="1" height="2" alt=""></td></tr>
							<tr><td height="8"></td></tr>
							<tr>
								<td colspan="2" class="fs12"><!--★コメント★--><!--{$arrProducts[cnt].main_list_comment|escape|nl2br}--></td>
							</tr>
							<tr><td height="8"></td></tr>
							<tr>
								<td>
									<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
									<!--{if $arrProducts[cnt].price02_min == $arrProducts[cnt].price02_max}-->
										<!--{$arrProducts[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrProducts[cnt].price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜<!--{$arrProducts[cnt].price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
									<!--{/if}-->
									円</span></span>
								</td>
								<!--{assign var=name value="detail`$smarty.section.cnt.iteration`"}-->
								<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_detail_on.gif','<!--{$name}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_detail.gif','<!--{$name}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_detail.gif" width="115" height="25" alt="詳しくはこちら" name="<!--{$name}-->" id="<!--{$name}-->" /></a></td>
							</tr>
							<!--{if $arrProducts[cnt].stock_max == 0 && $arrProducts[cnt].stock_unlimited_max != 1}-->
								<tr>
									<td class="fs12"><span class="red">申し訳ございませんが、只今品切れ中です。</span></td>
								</tr>
							<!--{else}-->
								<!--▼買い物かご-->
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
													<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class1] != ""}-->※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
													<tr>
														<td align="right" class="fs12st"><!--{$tpl_class_name1[$id]|escape}-->： </td>
														<td>
															<select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
															<option value="">選択してください</option>
															<!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
															</select>
														</td>
													</tr>
													<!--{/if}-->
													<!--{if $tpl_classcat_find2[$id]}-->
													<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class2] != ""}-->※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
													<tr>
														<td align="right" class="fs12st"><!--{$tpl_class_name2[$id]|escape}-->： </td>
														<td>
															<select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
															<option value="">選択してください</option>
															</select>
														</td>
													</tr>
													<!--{/if}-->
													<!--{assign var=quantity value=quantity`$id`}-->		
													<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{$arrErr[$quantity]}--></span></td></tr>
													<tr>
														<td align="right" width="115" class="fs12st">個数： 
															<!--{if $arrErr.quantity != ""}--><br/><span class="redst"><!--{$arrErr.quantity}--></span><!--{/if}-->
															<input type="text" name="<!--{$quantity}-->" size="3" class="box3" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" >
														</td>
														<td width="170" align="center">
															<a href="" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<!--{$id}-->" id="cart<!--{$id}-->" /></a>
														</td>
													</tr>
													<tr><td height="10"></td></tr>
												</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<!--▲買い物かご-->	
							<!--{/if}-->					
						</table>
					</td>
				</tr>
				<tr><td colspan=3 height="40"><img src="../img/common/line_580.gif" width="580" height="1" alt=""></td></tr>
				<!--{/section}-->
				</table>

				<!--件数ここから-->
				<!--{if $tpl_linemax > 0}-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td><img src="../img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr>
						<td align="center" bgcolor="#f3f3f3">
						<table width="560" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12" align="left"><span class="redst"><!--{$tpl_linemax}--></span>件の商品がございます。<!--{$tpl_strnavi}--></td>
								<td class="fs12" align="right"><!--{if $orderby != 'price'}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'price')">価格順</a><!--{else}--><strong>価格順</strong><!--{/if}-->　<!--{if $orderby != "date"}--><a href="#" onclick="fnModeSubmit('', 'orderby', 'date')">新着順</a><!--{else}--><strong>新着順</strong><!--{/if}--> </td>
							</tr>
						</table>
						</td>
					</tr>
					<tr><td><img src="../img/products/flame_top.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="15"></td></tr>
				</table>
				<!--件数ここまで-->
				<!--{/if}-->
				</form>
				<!--▲RIGHT CONTENTS-->
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->