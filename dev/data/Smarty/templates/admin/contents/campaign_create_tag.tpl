<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript">
</script>
<title>ECサイト管理者ページ</title>
</head>


<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<!--▼CONTENTS-->
<div align="center">
　
<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input name="mode" type="hidden" value="search">
<input name="anchor_key" type="hidden" value="">
<input name="search_pageno" type="hidden" value="">
<input type="hidden" name="product_id" value="">

<!--{if $tpl_create_tag != ""}-->
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr class="fs12n">
		<td bgcolor="#f0f0f0">生成タグ</td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#ffffff">
			<textarea name="" cols="45" rows="18" class="area40" readonly><!--{$tpl_create_tag}--></textarea>
		</td>
	</tr>
</table>
<br />
<input type="button" value="検索結果へ戻る" onClick="history.back()" />

<!--{* タグ表示の時以下を表示しない *}-->
<!--{else}-->
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr class="fs12n">
		<td bgcolor="#f0f0f0" width="100">カテゴリ</td>
		<td bgcolor="#ffffff" width="287"><select name="search_category_id">
		<option value="" selected="selected">選択してください</option>
		<!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
		</select>
		</td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#f0f0f0">商品ID</td>
		<td bgcolor="#ffffff"><input type="text" name="search_product_id" value="<!--{$arrForm.search_product_id}-->" size="35" class="box35" /></td>
	</tr>
	<tr class="fs12n">
		<td bgcolor="#f0f0f0">商品名</td>
		<td bgcolor="#ffffff"><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
	</tr>
</table>
<br />
<input type="submit" name="subm" value="検索を開始" />
<br />
<br />

	<!--▼検索結果表示-->
	<!--{if $tpl_linemax}-->
	<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#FFFFFF">
		<tr class="fs12">
			<td align="left"><!--{$tpl_linemax}-->件が該当しました。	</td>
		</tr>
		<tr class="fs12">
			<td align="center">
			<!--▼ページナビ-->
			<!--{$tpl_strnavi}-->
			<!--▲ページナビ-->
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
		
	<!--▼検索後表示部分-->
	<table width="420" border="0" cellspacing="1" cellpadding="5" bgcolor="#cccccc">
		<tr bgcolor="#f0f0f0" align="center" class="fs12">
			<td>商品画像</td>
			<td>商品ID</td>
			<td>商品名</td>
			<td>決定</td>
		</tr>
		<!--{section name=cnt loop=$arrProducts}-->
		<!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
		<tr bgcolor="#FFFFFF" class="fs12n">
			<td width="90" align="center">
			<!--{if $arrProducts[cnt].main_list_image != ""}-->
				<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_DIR`/`$arrProducts[cnt].main_list_image`"}-->
			<!--{else}-->
				<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
			<!--{/if}-->
			<img src="<!--{$smarty.const.SITE_URL}-->resize_image.php?image=<!--{$image_path}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->">
			</td>	
			<td><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
			<td><!--{$arrProducts[cnt].name|escape}--></td>
			<td align="center"><a href="#" onClick="fnFormModeSubmit('form1', 'view', 'product_id', '<!--{$arrProducts[cnt].product_id}-->'); return false;">決定</a></td>
		</tr>
		<!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
		<!--{sectionelse}-->
		<tr bgcolor="#FFFFFF" class="fs10n">
			<td colspan="4">商品が登録されていません</td>
		</tr>	
		<!--{/section}-->
	</table>
	<!--{/if}-->
	<!--▲検索結果表示-->
<!--{/if}-->
<!--{* タグ表示しないここまで *}-->

</form>

</div>
<!--▲CONTENTS-->
</body>
</html>