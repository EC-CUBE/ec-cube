
			<table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr><td height="12"></td></tr>
				<tr>
					<td bgcolor="#cccccc">
					<!--検索結果表示テーブル-->
					<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="50" rowspan="2"><span class="white">商品ID</span></td>
							<td width="90" rowspan="2"><span class="white">商品画像</span></td>
							<td width="90"><span class="white">商品コード</span></td>
							<td width="350"><span class="white">商品名</span></td>
							<td width="60"><span class="white">在庫</span></td>
							<td width="50" rowspan="2"><span class="white">編集</span></td>
							<td width="50" rowspan="2"><span class="white">確認</span></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td width="50" rowspan="2"><span class="white">規格</span></td>
							<!--{/if}-->
							<td width="50" rowspan="2"><span class="white">削除</span></td>
						</tr>
						<tr bgcolor="#636469" align="center" class="fs10n">
							<td width="90"><span class="white">価格(円)</span></td>
							<td width="430"><span class="white">カテゴリ</span></td>
							<td width="60"><span class="white">種別</span></td>
						</tr>
			
						<!--{section name=cnt loop=$arrProducts}-->
						<!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
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
							<!--{* 在庫 *}-->
							<!--{if $arrProducts[cnt].stock_unlimited == '1'}-->
							無制限
							<!--{else}-->
							<!--{$arrProducts[cnt].stock|escape|default:"-"}-->
							<!--{/if}-->
							</td>
							<td align="center" rowspan="2"><span class="icon_edit"><a href="/" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >編集</a></span></td>
							<td align="center" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.SITE_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&admin=on" target="_blank">確認</a></td>
							<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
							<td align="center" rowspan="2"><span class="icon_class"><a href="/" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >規格</a></td>
							<!--{/if}-->
							<td align="center" rowspan="2"><span class="icon_delete"><a href="/" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;">削除</a></span></td>
						</tr>
						<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs10n">
							<td align="right">
							<!--{* 価格 *}-->
							<!--{if $arrProducts[cnt].price02 != ""}-->
							<!--{$arrProducts[cnt].price02|number_format}-->
							<!--{else}-->
							-
							<!--{/if}-->
							</td>
							<td>
							<!--{* カテゴリ名 *}-->
							<!--{assign var=key value=$arrProducts[cnt].category_id}-->
							<!--{$arrCatList[$key]|sfTrim}-->
							</td>
							<!--{* 表示 *}-->
							<!--{assign var=key value=$arrProducts[cnt].status}-->
							<td align="center"><!--{$arrDISP[$key]}--></td>
						</tr>
						<!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
						<!--{/section}-->
						
					</table>
					<!--検索結果表示テーブル-->
					</td>
				</tr>
			</table>