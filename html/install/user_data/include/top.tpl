<!--▼CONTENTS-->

<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left">

		<!--▼MAIN CONTENTS-->

		<table cellspacing="0" cellpadding="0" summary=" " id="container">

			<tr><td height="8"></td></tr>
			<tr>
				
			<td colspan="3"><img src="./img/top/mainimage.jpg" alt="イメージ" width="760" height="250" usemap="#Map" /></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr valign="top" height="500">
				<td id="left">

				<!--▼LEFT CONTENTS-->

				<!--▼バナー-->
					<!--{include file=$tpl_banner}-->
				<!--▲バナー-->

				<!--▼商品検索-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--▲商品検索-->

				<!--▼テレビでご紹介した商品-->
					<!--{include file=$tpl_tv_products}-->
				<!--▲テレビでご紹介した商品-->

				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->

				<!--▲LEFT CONTENTS-->

				</td>
				<td id="right">

				<!--▼RIGHT CONTENTS-->
				<!--▼新着情報-->
				<table cellspacing="0" cellpadding="0" summary=" " id="osusumetitle">
					<tr>
						<td><img src="./img/top/info.jpg" width="570" height="33" alt="トーカ堂からのお知らせ" /></td>
					</tr>
						<td><span class="fs10st">☆★☆ 新着情報はRSSで配信しています。★☆★ <a href="/rss/index.php"> > RSS </a></span></td>

					<tr><td height="10"></td></tr>
					<!--{section name=data loop=$arrNews}-->
					<tr>
						<td><span class="fs10st"><!--{$arrNews[data].news_date_disp|escape}--></span><br /> <span class="fs12"><!--{if $arrNews[data].news_url}--><a href="<!--{$arrNews[data].news_url}-->" <!--{if $arrNews[data].link_method eq "2"}-->target="_blank"<!--{/if}--> ><!--{/if}--><!--{$arrNews[data].news_comment|escape|nl2br}--><!--{if $arrNews[data].news_url}--></a><!--{/if}--></span></td>
					</tr>
					<!--{if !$smarty.section.data.last}-->
					<tr>
						<td height="15"><img src="./img/top/info_line.gif" width="570" height="1" alt="" /></td>
					</tr>
					<!--{/if}-->
					<!--{/section}-->
				</table>
				<!--▲新着情報-->
				<!--▼トーカ堂のオススメ品-->
				<table cellspacing="0" cellpadding="0" summary=" " id="osusumetitle">
					<tr><td height="20"></td></tr>
					<tr>
						<td><img src="/img/right_product/recommend.jpg" width="570" height="33" alt="トーカ堂のオススメ商品" /></td>
					</tr>
					<tr><td height="10"></td></tr>
				</table>
				<table cellspacing="0" cellpadding="0" summary=" " id="osusume">
					<!--{section name=cnt loop=$arrBestItems step=2}-->
					<tr valign="top">
						<td>
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr valign="top">
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[cnt].main_list_image`"}-->
								<!--（<!--{$smarty.section.cnt.iteration}-->）-->
								<td id="left"><div id="picture"><a href="/products/detail.php?product_id=<!--{$arrBestItems[cnt].product_id}-->"><!--商品写真--><img src="<!--{$image_path}-->" width="65" height="65" alt="<!--{$arrBestItems[cnt].name|escape}-->" /></a></div></td>
								<td id="spacer"></td>
								<td id="right"><span class="fs12"><strong><a href="/products/detail.php?product_id=<!--{$arrBestItems[cnt].product_id}-->"><!--{$arrBestItems[cnt].name|escape}--></a></strong></span><br />
								<span class="fs12">トーカ堂価格：</span>
								<span class="red12st">
									<!--{if $arrBestItems[cnt].price02_min == $arrBestItems[cnt].price02_max}-->				
										<!--{$arrBestItems[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$arrBestItems[cnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									円
								</span><span class="red10">（税込）</span>
								<br />
								<span class="fs12">ポイント：</span>
								<span class="red12st">
									<!--{if $arrBestItems[cnt].price02_min == $arrBestItems[cnt].price02_max}-->				
										<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
									<!--{else}-->
										<!--{if $arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id == $arrBestItems[cnt].price02_max|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
											<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
										<!--{else}-->
											<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->〜<!--{$arrBestItems[cnt].price02_max|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span><br />
								<span class="fs12"><!--{$arrBestItems[cnt].comment|escape|nl2br}--></span></td>
								<!--（<!--{$smarty.section.cnt.iteration}-->）-->
							</tr>
						</table>
						</td>
						<td id="spacer"></td>
						<!--{assign var=nextCnt value=$smarty.section.cnt.index+1}-->
						<td>
						<!--{if $arrBestItems[$nextCnt].product_id}-->
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr valign="top">
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[$nextCnt].main_list_image`"}-->
								<!--（<!--{$smarty.section.cnt.index_next}-->）-->
								<td id="left"><div id="picture"><a href="/products/detail.php?product_id=<!--{$arrBestItems[$nextCnt].product_id}-->"><!--商品写真--><img src="<!--{$image_path}-->" width="65" height="65" alt="<!--{$arrBestItems[$nextCnt].name|escape}-->" /></a></div></td>
								<td id="spacer"></td>
								<td id="right"><span class="fs12"><strong><a href="/products/detail.php?product_id=<!--{$arrBestItems[$nextCnt].product_id}-->"><!--{$arrBestItems[$nextCnt].name|escape}--></a></strong></span><br />
								<span class="fs12">トーカ堂価格：</span>
								<span class="red12st">
									<!--{if $arrBestItems[$nextCnt].price02_min == $arrBestItems[$nextCnt].price02_max}-->				
										<!--{$arrBestItems[$nextCnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[$nextCnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$arrBestItems[$nextCnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									円
								</span><span class="red10">（税込）</span>
								<br />
								<span class="fs12">ポイント：</span>
								<span class="red12st">
									<!--{if $arrBestItems[$nextCnt].price02_min == $arrBestItems[$nextCnt].price02_max}-->				
										<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
									<!--{else}-->
										<!--{if $arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id == $arrBestItems[$nextCnt].price02_max|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
											<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
										<!--{else}-->
											<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->〜<!--{$arrBestItems[$nextCnt].price02_max|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span><br />
								<span class="fs12"><!--{$arrBestItems[$nextCnt].comment|escape|nl2br}--></span></td>
								<!--（<!--{$smarty.section.cnt.index_next}-->）-->
							</tr>
						</table>
						<!--{/if}-->
						</td>
					</tr>
					<!--{if !$smarty.section.cnt.last}-->
					<tr>
						<td height="25"><img src="/img/right_product/recommend_line.gif" width="270" height="1" alt="" /></td>
						<td id="spacer"></td>
						<td align="right"><img src="/img/right_product/recommend_line.gif" width="270" height="1" alt="" /></td>
					</tr>
					<!--{/if}-->
					<!--{/section}-->

				</table>
				
				<!--▲トーカ堂のオススメ品-->
				</td>
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
<!-- EBiS start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = 'http://daikoku.ebis.ne.jp'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = 'tqYg3k6U'; pid = '1'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<!-- EBiS end -->
		</td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--▲CONTENTS-->