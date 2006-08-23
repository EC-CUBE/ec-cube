<!--Çä¤ì¶Ú£Â£Å£Ó£Ô£µ¤³¤³¤«¤é-->
<!--{if $arrBestProducts}-->
<div class="ranking">
<table border="1" cellspacing="1" cellpadding="5" summary=" " class="ranking-tbl">
	<tr><td colspan=3 align="center">Çä¤ì¶Ú£Â£Å£Ó£Ô£µ</td></tr>
	<tr>
		<td height="10"><img src="./img/_.gif" width="14" height="1" alt="" /></td>
		<td><img src="./img/_.gif" width="86" height="1" alt="" /></td>
		<td><img src="./img/_.gif" width="80" height="1" alt="" /></td>
	</tr>
	<!--{section name=cnt loop=$arrBestProducts}-->
	<tr valign="top"><!--{if $arrBestProducts[cnt].main_list_image != ""}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestProducts[cnt].main_list_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}-->
		<td><strong><!--{$smarty.section.cnt.iteration}--></strong></td>
		<td class="fg"><a href="./products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->"><img src="<!--{$image_path|sfRmDupSlash}-->" width="70" height="70" alt="<!--{$arrBestProducts[cnt].name|escape}-->" /></a></td>
		<td><p><a href="./products/detail.php?product_id=<!--{$arrBestProducts[cnt].product_id}-->"><strong><!--{$arrBestProducts[cnt].name|escape}--></strong></a><br/>
		<!--{assign var=price01 value=`$arrBestProducts[cnt].price01_min`}-->
		<!--{assign var=price02 value=`$arrBestProducts[cnt].price02_min`}-->
		<span class="mini">
		<!--{if $price02 == ""}-->
		<!--{$price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{else}-->
		<!--{$price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{/if}-->
		±ß¡ÊÀÇ¹þ¡Ë</span></p></td>
	</tr>
	<tr>	
		<td colspan="3"><p><span class="mini"><!--{$arrBestProducts[cnt].comment|escape|nl2br}--></span></p></td>
	</tr>
	<!--{/section}-->
</table>
</div>
<!--{/if}-->
<!--Çä¤ì¶Ú£Â£Å£Ó£Ô£µ¤³¤³¤Þ¤Ç-->