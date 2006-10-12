<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼おすすめ情報ここから-->
<table width="400" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="2"><img src="<!--{$smarty.const.URL_DIR}-->img/top/osusume.jpg" width="400" height="29" alt="おすすめ情報"></td>
	</tr>
	<tr><td height="10"></td></tr>

	<!--{section name=cnt loop=$arrBestProducts step=2}-->
	<tr valign="top">
		<td>
		
		<!--{if $arrBestProducts[cnt].main_list_image != ""}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestProducts[cnt].main_list_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}-->
		
		<table width="190" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td><a href="./products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->"><img src="<!--{$image_path|sfRmDupSlash}-->" width="48" height="48" alt="<!--{$arrBestProducts[cnt].name|escape}-->" /></a></td>
				<td align="right">
				<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<!--{assign var=price01 value=`$arrBestProducts[cnt].price01_min`}-->
						<!--{assign var=price02 value=`$arrBestProducts[cnt].price02_min`}-->
						<td><span class="fs12"><a href="./products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->"><!--{$arrBestProducts[cnt].name|escape}--></a></span><br>
						<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
						<!--{if $price02 == ""}-->
						<!--{$price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{else}-->
						<!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
						<!--{/if}-->
						円</span></span></td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td class="fs10"><!--{$arrBestProducts[cnt].comment|escape|nl2br}--></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		
		</td>
		<td align="right">
		
		<!--{assign var=cnt2 value=`$smarty.section.cnt.iteration*$smarty.section.cnt.step-1` }-->
		<!--{if $arrBestProducts[$cnt2]|count > 0}-->
			<!--{if $arrBestProducts[$cnt2].main_list_image != ""}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestProducts[$cnt2].main_list_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}-->
			<table width="190" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr valign="top">
					<td><a href="./products/detail.php?product_id=<!--{$arrBestProducts[$cnt2].product_id}-->"><img src="<!--{$image_path|sfRmDupSlash}-->" width="48" height="48" alt="<!--{$arrBestProducts[$cnt2].name|escape}-->" /></a></td>
					<td align="right">
					<table width="132" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<tr>
							<!--{assign var=price01 value=`$arrBestProducts[$cnt2].price01_min`}-->
							<!--{assign var=price02 value=`$arrBestProducts[$cnt2].price02_min`}-->
							<td><span class="fs12"><a href="./products/detail.php?product_id=<!--{$arrBestProducts[$cnt2].product_id}-->"><!--{$arrBestProducts[$cnt2].name|escape}--></a></span><br>
							<span class="red"><span class="fs12">価格</span><span class="fs10">(税込)</span></span><span class="redst"><span class="fs12">：
							<!--{if $price02 == ""}-->
							<!--{$price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
							<!--{else}-->
							<!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
							<!--{/if}-->
							円</span></span></td>
						</tr>
						<tr><td height="5"></td></tr>
						<tr>
							<td class="fs10"><!--{$arrBestProducts[$cnt2].comment|escape|nl2br}--></td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
		<!--{/if}-->
	
	<!--{if !$smarty.section.cnt.last}-->
	<tr>
		<td height="20"><img src="<!--{$smarty.const.URL_DIR}-->img/common/line_190.gif" width="190" height="1" alt=""></td>
		<td align="right"><img src="<!--{$smarty.const.URL_DIR}-->img/common/line_190.gif" width="190" height="1" alt=""></td>
	</tr>
	<!--{/if}-->
	
	<!--{/section}-->
<tr><td height="35"></td></tr>
</table>
<!--▲おすすめ情報ここまで-->
		
		