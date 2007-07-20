<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--▼商品カテゴリーここから-->
<!--{section name=cnt loop=$arrCat}-->
<!--{assign var=disp_name value="`$arrCat[cnt].category_name`"}-->
<!--{if $arrCat[cnt].has_children}-->
<!--{assign var=path value="`$smarty.const.MOBILE_URL_DIR`products/category_list.php"}-->
<!--{else}-->
<!--{assign var=path value="`$smarty.const.MOBILE_URL_DIR`products/list.php"}-->
<!--{/if}-->
　<font color="<!--{cycle values="#000000,#880000,#8888ff,#88ff88,#ff0000"}-->">■</font><a href="<!--{$path}-->?category_id=<!--{$arrCat[cnt].category_id}-->"><!--{$disp_name|sfCutString:20|escape}--></a><br>
<!--{/section}-->
<!--▲商品カテゴリーここまで-->
