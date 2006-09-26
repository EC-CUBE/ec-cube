<!--{*
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--▼SUB NAVI-->
				<td class="fs12n">
				<!--{include file=$tpl_subnavi}-->
				</td>
				<!--▲SUB NAVI-->
				</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--▼MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>■キャンペーン対象商品検索</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		
<form name="search_form" id="search_form" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="search">
		<!--▼検索テーブルここから-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">商品ID</td>
				<td bgcolor="#ffffff" width="248"><input type="text" name="search_product_id" value="<!--{$arrForm.search_product_id|escape}-->" size="30" class="box30" /></td>
				<td bgcolor="#f0f0f0" width="110">規格ID</td>
				<td bgcolor="#ffffff" width="249"><input type="text" name="search_product_class_id" value="<!--{$arrForm.search_product_class_id|escape}-->" size="30" class="box30" /></td>
			</tr>			
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">商品コード</td>
				<td bgcolor="#ffffff" width="248"><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|escape}-->" size="30" class="box30" /></td>
				<td bgcolor="#f0f0f0" width="110">商品名</td>
				<td bgcolor="#ffffff" width="249"><input type="text" name="search_name" value="<!--{$arrForm.search_name|escape}-->" size="30" class="box30" /></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">カテゴリ</td>
				<td bgcolor="#ffffff" width="607" colspan="7">
				<select name="search_category_id" style="<!--{if $arrErr.search_category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->">
				<option value="">選択してください</option>
				<!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
				</select></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">登録・更新日</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<span class="red"><!--{$arrErr.search_startyear}--></span>
				<span class="red"><!--{$arrErr.search_endyear}--></span>		
				<select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
				<option value="">----</option>
				<!--{html_options options=$arrStartYear selected=$arrForm.search_startyear}-->
				</select>年
				<select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth}-->
				</select>月
				<select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartDay selected=$arrForm.search_startday}-->
				</select>日〜
				<select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
				<option value="">----</option>
				<!--{html_options options=$arrEndYear selected=$arrForm.search_endyear}-->
				</select>年
				<select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth}-->
				</select>月
				<select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndDay selected=$arrForm.search_endday}-->
				</select>日
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">ステータス</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{html_checkboxes name="search_product_flag" options=$arrSTATUS selected=$arrForm.search_product_flag}-->
				</td>
				</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">種別</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{html_checkboxes name="search_status" options=$arrDISP selected=$arrForm.search_status}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">検索結果表示数</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{assign var=key value="search_page_max"}-->
				<span class="red12"><!--{$arrErr[$key]}--></span>
				<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
				<!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
				</select> 件</td>
			</tr>
		</table>
		<!--▲検索テーブルここまで-->
		
		<br />
		<input type="button" name="back" value="戻る" onclick="location.href='./point.php';">　<input type="submit" name="subm" value="この内容で検索する" />
		</form>
		
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="product_id" value="">
		<input type="hidden" name="category_id" value="">
		<!--{foreach key=key item=item from=$arrHidden}-->
		<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
		<!--{/foreach}-->

		<!--{if count($arrProducts) > 0}-->

		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr class="fs12"><td align="left"><!--{$tpl_linemax}-->件が該当しました。	</td></tr>
			<tr class="fs12">
				<td align="center">
				<!--▼ページナビ-->
				<!--{$tpl_strnavi}-->
				<!--▲ページナビ-->
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
				
		<!--▼検索結果テーブルここから-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr bgcolor="#f0f0f0" align="center" class="fs12n">
				<td width="50" rowspan="2">商品ID</td>
				<td width="90" rowspan="2">商品画像</td>
				<td width="90">商品コード</td>
				<td width="260">商品名</td>
				<td width="60">在庫</td>
			</tr>
			<tr bgcolor="#f0f0f0" align="center" class="fs12n">
				<td width="90">価格(円)</td>
				<td width="260">カテゴリ</td>
				<td width="60">種別</td>
			</tr>
						
			<!--{section name=cnt loop=$arrProducts}-->
			<!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
			<!--{assign var=status value="`$arrProducts[cnt].status`"}-->
			<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs12">
				<td width="50" rowspan="2" align="center"><!--{$arrProducts[cnt].product_id}--></td>
				<td width="90" align="center" rowspan="2">
				<!--{if $arrProducts[cnt].main_list_image != ""}-->
					<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrProducts[cnt].main_list_image`"}-->
				<!--{else}-->
					<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
				<!--{/if}-->
				<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrProducts[cnt].name|escape}-->" />
				</td>
				<td width="90" ><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
				<td width="310"><!--{$arrProducts[cnt].name|escape}--></td>
				<td width="60" align="center">
				<!--{* 在庫 *}-->
				<!--{if $arrProducts[cnt].stock_unlimited == '1'}-->
				無制限
				<!--{else}-->
				<!--{$arrProducts[cnt].stock|escape|default:"-"}-->
				<!--{/if}-->
				</td>
			</tr>
			<tr bgcolor="<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->" class="fs12n">
				<td width="90" align="right">
				<!--{* 価格 *}-->
				<!--{if $arrProducts[cnt].price02 != ""}-->
				<!--{$arrProducts[cnt].price02|number_format}-->
				<!--{else}-->
				-
				<!--{/if}-->
				</td>
				<td width="370">
				<!--{* カテゴリ名 *}-->
				<!--{assign var=key value=$arrProducts[cnt].category_id}-->
				<!--{$arrCatList[$key]|sfTrim}-->
				</td>
				<!--{* 表示 *}-->
				<!--{assign var=key value=$arrProducts[cnt].status}-->
				<td width="60" align="center"><!--{$arrDISP[$key]}--></td>
			</tr>
			<!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
			<!--{/section}-->
						
		</table>
		<!--▲検索結果テーブルここまで-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="5"></td></tr>
			<tr><td height="5"></td></tr>
			<tr class="fs12">
				<td align="center">
				<!--▼ページナビ-->
				<!--{$tpl_strnavi}-->
				<!--▲ページナビ-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
		</table>
		
		<br />
		<input type="button" name="btn" value="次へ">
		</form>
		<!--▲検索結果表示エリアここまで-->
		<!--▲MAIN CONTENTS-->
		<!--{else}-->
			<!--{if $smarty.post.mode == 'search' || $smarty.post.mode == 'delete_all'}-->
			<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<tr class="fs12"><td align="center">該当するデータはありません。</td></tr>
			</table>
			<!--{/if}-->
		<!--{/if}-->
		
		</td>
	</tr>

</table>
<!--▲CONTENTS-->