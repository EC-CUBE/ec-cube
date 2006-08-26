<script type="text/javascript">
<!--
// セレクトボックスに項目を割り当てる。
function lnSetSelect(form, name1, name2, val) {
	
	sele11 = document[form][name1];
	sele12 = document[form][name2];
	
	if(sele11 && sele12) {
		index = sele11.selectedIndex;
		
		// セレクトボックスのクリア
		count = sele12.options.length;
		for(i = count; i >= 0; i--) {
			sele12.options[i] = null;
		}
		
		// セレクトボックスに値を割り当てる
		len = lists[index].length;
		for(i = 0; i < len; i++) {
			sele12.options[i] = new Option(lists[index][i], vals[index][i]);
			if(val != "" && vals[index][i] == val) {
				sele12.options[i].selected = true;
			}
		}
	}
}

//-->
</script>

<!--▼CONTENTS-->
<table width="" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" valign="top" align="left">
		<!--▼MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td align="right" bgcolor="#ffffff">
				<!--タイトルここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td colspan="3"><img src="../img/products/title_top.gif" width="580" height="8" alt=""></td></tr>
					<tr bgcolor="#ffebca">
						<td><img src="../img/products/title_icon.gif" width="29" height="24" alt=""></td>
						<td>
						<table width="546" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr valign="top">
								<td class="fs18"><span class="blackst"><!--★タイトル★--><!--{$tpl_subtitle|escape}--></span></td>
							</tr>
						</table>
						</td>
						<td><img src="../img/products/title_left.gif" width="5" height="24" alt=""></td>
					</tr>
					<tr><td colspan="3"><img src="../img/products/title_under.gif" width="580" height="8" alt=""></td></tr>
					<tr><td height="10"></td></tr>
				</table>
				<!--タイトルここまで-->

				<!--詳細ここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td colspan="2" class="fs12"><!--★詳細メインコメント★--><!--{$arrProduct.main_comment}--></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr valign="top">
						<td>
						<table width="290" border="0" cellspacing="0" cellpadding="0" summary=" ">	
							<tr>
								<!--{if $arrProduct.main_large_image != ""}-->
									<div id="picture"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&image=main_large_image<!--{if $smarty.get.admin == 'on'}-->&admin=on<!--{/if}-->','detail_image','520','534'); return false;" target="_blank"><!--メイン画像--><!--{assign var=key value="main_image"}--><img src="<!--{$arrFile[$key].filepath}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" alt="<!--{$arrProduct.name|escape}-->" /></a></div>						
								<!--{else}-->
									<div id="picture"><!--メイン画像--><!--{assign var=key value="main_image"}--><img src="<!--{$arrFile[$key].filepath}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" alt="<!--{$arrProduct.name|escape}-->" /></div>
								<!--{/if}-->
							</tr>
							<tr><td height="8"></td></tr>
							<tr>
								<td>
								<!--{if $arrProduct.main_large_image != ""}-->
									<!--★拡大する★--><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&image=main_large_image<!--{if $smarty.get.admin == 'on'}-->&admin=on<!--{/if}-->','detail_image', '520', '534'); return false;" target="_blank"><img src="/img/products/b_expansion.gif" width="94" height="13" alt="画像を拡大する" /></a>
								<!--{/if}-->
								</td>
							</tr>
						</table>		
						</td>
						<td align="right">
						<table width="280" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<!--アイコン-->
							<tr>
								<td>
									<!--{section name=flg loop=$arrProduct.product_flag|count_characters}--><!--{if $arrProduct.product_flag[flg] == "1"}--><!--{assign var=key value="`$smarty.section.flg.iteration`"}--><img src="<!--{$arrSTATUS_IMAGE[$key]}-->" width="60" height="17" alt="<!--{$arrSTATUS[$key]}-->" id="icon" /><!--{/if}--><!--{/section}-->
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<!--アイコン-->
							<tr>
								<td class="fs18"><span class="orangest"><!--★商品名★--><!--{$arrProduct.name|escape}--></span></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td><span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
								<!--★価格★-->
									<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->				
										<!--{$arrProduct.price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrProduct.price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$arrProduct.price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									円</span></span>
									<br/>
								<!--★ポイント★-->
									<span class="red"><span class="fs12"> ポイント</span></span><span class="redst"><span class="fs12">：
								<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->				
									<!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
								<!--{else}-->
									<!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
										<!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
									<!--{else}-->
										<!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->〜<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
									<!--{/if}-->
								<!--{/if}-->
								Pt</span></span>
							</tr>
							<tr><td height="15"></td></tr>
							<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
							<input type="hidden" name="mode" value="cart">
							<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
							
							<!--{if $tpl_classcat_find1}-->
							<tr><td height="5" colspan="2" align="left" class="fs12"><span class="redst"><!--{if $arrErr.classcategory_id1 != ""}-->※ <!--{$tpl_class_name1}-->を入力して下さい。<!--{/if}--></span></td></tr>
							<tr>
								<td class="fs12"><img src="/img/common/arrow_gray.gif" width="15" height="10" alt=""><strong><!--{$tpl_class_name1}--></strong></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td>
									<select name="classcategory_id1" style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->" onchange="lnSetSelect('form1', 'classcategory_id1', 'classcategory_id2', ''); ">
									<option value="">選択してください</option>
									<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
									</select>
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							<!--{/if}-->
							<!--{if $tpl_classcat_find2}-->
							<tr><td height="5" colspan="2" align="left" class="fs12"><span class="redst"><!--{if $arrErr.classcategory_id2 != ""}-->※ <!--{$tpl_class_name2}-->を入力して下さい。<!--{/if}--></span></td></tr>
							<tr>
								<td class="fs12"><img src="/img/common/arrow_gray.gif" width="15" height="10" alt=""><strong><!--{$tpl_class_name2}--></strong></td>
							</tr>
							<tr><td height="3"></td></tr>
							<tr>
								<td>
									<select name="classcategory_id2" style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
									<option value="">選択してください</option>
									</select>
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							<!--{/if}-->
							<tr>
								<td class="fs12"><img src="../img/common/arrow_gray.gif" width="15" height="10" alt=""><strong>個　数</strong>
								<!--{*
									<select name="quantity">
									<!--{section name=cnt loop=$tpl_sale_limit}-->
									<option value="<!--{$smarty.section.cnt.iteration}-->"><!--{$smarty.section.cnt.iteration}--></option>
									<!--{/section}-->
									</select>
								*}-->
									<input name="quantity" size="6" class="box6" value="<!--{$arrForm.quantity}-->" type="text">
								</td>
							</tr>
							<tr><td height="20"><img src="../img/common/line_280.gif" width="280" height="1" alt=""></td></tr>
							<tr>
								<td align="center">
									<!--{if $tpl_stock_find}-->
										<!--★カゴに入れる★--><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.submit(); return false;" onmouseover="chgImg('/img/products/b_cartin_on.gif','cart');" onmouseout="chgImg('/img/products/b_cartin.gif','cart');"><img src="/img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart" id="cart" /></a>
									<!--{else}-->
										<table width="285" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td height="10"></td></tr>
										<tr>
											<td align="center" class="fs12">
											<span class="red">申し訳ございませんが、只今品切れ中です。</span>
											</td>
										</tr>
										<tr><td height="10"></td></tr>
										</table>
									<!--{/if}-->
								</td>
							</tr>
							</form>
						</table>
						</td>
					</tr>
					<tr><td height="35"></td></tr>
				</table>
				<!--詳細ここまで-->
				
				<!--▼サブコメントここから-->		
				<!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
				<!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
				<!--{if $arrProduct[$key] != ""}-->
					<table width="580" border="0" cellspacing="0" cellpadding="7" summary=" ">
						<tr>
							<td bgcolor="#e4e4e4" class="fs12"><span class="blackst"><!--★サブタイトル★--><!--{$arrProduct[$key]|escape}--></span></td>
						</tr>
					</table>
					
					<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr><td height="10"></td></tr>
						<tr valign="top">
							<!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
							<td class="fs12" align="left"><!--★サブテキスト★--><!--{$arrProduct[$key]}--></td>
							<!--拡大写真がある場合ここから-->
							<!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
							<!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.iteration`"}-->
							<!--{if $arrFile[$key].filepath != ""}-->
							<td align="right">
							<table width="215" border="0" cellspacing="0" cellpadding="0" summary=" ">	
								<tr>
									<!--{if $arrFile[$lkey].filepath != ""}-->
										<td align="right"><div id="picture"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&image=<!--{$lkey}--><!--{if $smarty.get.admin == 'on'}-->&admin=on<!--{/if}-->','detail_image','520','534'); return false;" target="_blank"><!--サブ画像--><img src="<!--{$arrFile[$key].filepath}-->" width="200" height="200" alt="<!--{$arrProduct.name|escape}-->" /></a></div>
									<!--{else}-->
										<td align="right"><img src="<!--{$arrFile[$key].filepath}-->" width="200" height="200" alt="<!--{$arrProduct.name|escape}-->" /></td>
									<!--{/if}-->
								</tr>
								<tr><td height="8"></td></tr>
								<tr>
									<!--{if $arrFile[$lkey].filepath != ""}-->
										<td align="right"><div id="more"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win01('./detail_image.php?product_id=<!--{$arrProduct.product_id}-->&image=<!--{$lkey}--><!--{if $smarty.get.admin == 'on'}-->&admin=on<!--{/if}-->','detail_image','520','534'); return false;" onmouseover="chgImg('../img/products/b_expansion_on.gif','expansion02');" onmouseout="chgImg('../img/products/b_expansion.gif','expansion02');" target="_blank"><img src="../img/products/b_expansion.gif" width="94" height="13" alt="画像を拡大する" /></a></div></td>
									<!--{/if}-->
								</tr>
							</table>
							</td>
							<!--{/if}-->
							<!--拡大写真がある場合ここまで-->
						</tr>
						<tr><td height="30"></td></tr>
					</table>
				<!--{/if}-->
				<!--{/section}-->
				<!--▲サブコメントここまで-->
				
				<!--お客様の声ここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td><img src="../img/products/title_voice.jpg" width="580" height="30" alt="この商品に対するお客様の声"></td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td>
						<!--{if count($arrReview) < $smarty.const.REVIEW_REGIST_MAX}-->
							<!--★新規コメントを書き込む★--><a href="./review.php" onClick="win02('./review.php?product_id=<!--{$arrProduct.product_id}-->','review','580','580'); return false;" onMouseOver="chgImg('../img/products/b_comment_on.gif','review');" onMouseOut="chgImg('../img/right_product/review.gif','review');" target="_blank"><img src="../img/right_product/review.gif" width="150" height="22" alt="新規コメントを書き込む" name="review" id="review" /></a>
						<!--{/if}-->
						</td>
					</tr>
					<tr><td height="10"></td></tr>
		
					<!--{section name=cnt loop=$arrReview}-->
					<tr>
						<td class="fs12"><strong><!--{$arrReview[cnt].create_date|sfDispDBDate:false}--></strong>　投稿者：<!--{if $arrReview[cnt].reviewer_url}--><a href="<!--{$arrReview[cnt].reviewer_url}-->" target="_blank"><!--{$arrReview[cnt].reviewer_name|escape}--></a><!--{else}--><!--{$arrReview[cnt].reviewer_name|escape}--><!--{/if}-->　おすすめレベル：<span class="red"><!--{assign var=level value=$arrReview[cnt].recommend_level}--><!--{$arrRECOMMEND[$level]|escape}--></span></td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td class="fs14"><strong><!--{$arrReview[cnt].title|escape}--></strong></td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td class="fs12"><!--{$arrReview[cnt].comment|escape|nl2br}--></td>
					</tr>
			
					<!--{if !$smarty.section.cnt.last}-->
					<tr><td height="20"><img src="../img/common/line_580.gif" width="580" height="1" alt=""></td></tr>
					<!--{/if}-->
					
					<!--{/section}-->
					
					<tr><td height="30"></td></tr>
				</table>
				<!--お客様の声ここまで-->

				<!--{if $arrRecommend}-->
				<!--▼オススメ商品ここから-->
				<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td colspan=3><img src="/img/products/title_recommend.jpg" width="570" height="33" alt="オススメ商品" /></td>
					</tr>
					<tr><td colspan=3 height="10"></td></tr>
					<tr>

					<!--{section name=cnt loop=$arrRecommend step=2}-->
					<!--{if $smarty.section.cnt.index >= 2}-->
					<tr>
						<td height="25"><img src="../img/right_product/recommend_line.gif" width="270" height="1" alt="" /></td>
						<td></td>
						<td align="left"><img src="../img/right_product/recommend_line.gif" width="270" height="1" alt="" /></td>
					</tr>
					<!--{/if}-->
					
					<tr valign="top">
						<td>
							<!-- 左列 -->
							<table width="220" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr valign="top">
									<td><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[cnt].product_id}-->"><!--{if $arrRecommend[cnt].main_list_image != ""}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrRecommend[cnt].main_list_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}--><img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrRecommend[cnt].name|escape}-->" /></a></td>
									<td align="right">
									<table width="145" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min`}-->
											<!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max`}-->
											<td><span class="fs12"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[cnt].product_id}-->"><!--{$arrRecommend[cnt].name|escape}--></a></span><br>
											<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
											<!--{if $price02_min == $price02_max}-->
												<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
											<!--{else}-->
												<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->~<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
											<!--{/if}-->
											円</span></span></td>
										</tr>
										<tr><td height="5"></td></tr>
										<tr>
											<td class="fs10"><!--{$arrRecommend[cnt].comment|escape|nl2br}--></td>
										</tr>
									</table>
									</td>
								</tr>
							</table>
							<!-- 左列 -->
						</td>
						<!--{assign var=nextCnt value=$smarty.section.cnt.index+1}-->
						
						<td id="spacer"></td>
						
						<td>
						<!--{if $arrRecommend[$nextCnt].product_id}-->
						
							<!-- 右列 -->
							<table width="220" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr valign="top">
									<td><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[$nextCnt].product_id}-->"><!--{if $arrRecommend[$nextCnt].main_list_image != ""}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrRecommend[$nextCnt].main_list_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}--><img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrRecommend[$nextCnt].product_id}-->" /></a></td>
									<td align="right">
									<table width="145" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<!--{assign var=price02_min value=`$arrRecommend[$nextCnt].price02_min`}-->
											<!--{assign var=price02_max value=`$arrRecommend[$nextCnt].price02_max`}-->
											<td><span class="fs12"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrRecommend[cnt].product_id}-->"><!--{$arrRecommend[cnt].name|escape}--></a></span><br>
											<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
											<!--{if $price02_min == $price02_max}-->
												<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
											<!--{else}-->
												<!--{$price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->~<!--{$price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
											<!--{/if}-->
											円</span></span></td>
										</tr>
										<tr><td height="5"></td></tr>
										<tr>
											<td class="fs10"><!--{$arrRecommend[$nextCnt].comment|escape|nl2br}--></td>
										</tr>
									</table>
									</td>
								</tr>
							</table>
							<!-- 右列 -->
						<!--{/if}-->
						</td>
					</tr>
					<!--{/section}-->
					<tr><td colspan=3 height="25"></td></tr>
				</table>
				<!--{/if}-->
				<!--▲オススメ商品ここまで-->
				
				</td>
				<!--▲RIGHT CONTENTS-->
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
