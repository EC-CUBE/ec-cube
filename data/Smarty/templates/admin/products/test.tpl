<table>
	<!--{section name=cnt loop=$arrProducts}-->
	<!--������<!--{$smarty.section.cnt.iteration}-->-->
	<!--{assign var=status value="`$arrProducts[cnt].status`"}-->
	<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10">
		<td rowspan="2" align="center"><!--{$arrProducts[cnt].product_id}--></td>
		<td rowspan="2" align="center">
		<!--{if $arrProducts[cnt].main_list_image != ""}-->
			<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts[cnt].main_list_image`"}-->
		<!--{else}-->
			<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
		<!--{/if}-->
		<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts[cnt].name|escape}-->" />
		</td>
		<td><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
		<td><!--{$arrProducts[cnt].name|escape}--></td>
		<td align="center">
		<!--{* �߸� *}-->
		<!--{if $arrProducts[cnt].stock_unlimited == '1'}-->
		̵����
		<!--{else}-->
		<!--{$arrProducts[cnt].stock|escape|default:"-"}-->
		<!--{/if}-->
		</td>
		<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >�Խ�</a></span></td>
		<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&admin=on" target="_blank">��ǧ</a></td>
		<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
		<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >����</a></td>
		<!--{/if}-->
		<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;">���</a></span></td>
	</tr>
	<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
		<td align="right">
		<!--{* ���� *}-->
		<!--{if $arrProducts[cnt].price02 != ""}-->
		<!--{$arrProducts[cnt].price02|number_format}-->
		<!--{else}-->
		-
		<!--{/if}-->
		</td>
		<td>
		<!--{* ���ƥ���̾ *}-->
		<!--{assign var=key value=$arrProducts[cnt].category_id}-->
		<!--{$arrCatList[$key]|sfTrim}-->
		</td>
		<!--{* ɽ�� *}-->
		<!--{assign var=key value=$arrProducts[cnt].status}-->
		<td align="center"><!--{$arrDISP[$key]}--></td>
	</tr>
	<!--������<!--{$smarty.section.cnt.iteration}-->-->
	<!--{/section}-->
</table>