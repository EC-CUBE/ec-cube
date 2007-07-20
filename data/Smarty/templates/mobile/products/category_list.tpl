<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center><!--{$arrCategory.category_name|escape}--></center>

<hr>

<!--{foreach from=$arrChildren key=i item=arrChild}-->
<!--{if $arrChild.has_children}-->
<!--{assign var=path value="`$smarty.const.MOBILE_URL_DIR`products/category_list.php"}-->
<!--{else}-->
<!--{assign var=path value="`$smarty.const.MOBILE_URL_DIR`products/list.php"}-->
<!--{/if}-->
<!--{if $i+1<9}-->
<a href="<!--{$path}-->?category_id=<!--{$arrChild.category_id}-->" accesskey="<!--{$i+1}-->"><!--{$i+1|numeric_emoji}--><!--{$arrChild.category_name|escape}-->(<!--{$arrChild.product_count}-->)</a><br>
<!--{else}-->
[<!--{$i+1}-->]<a href="<!--{$path}-->?category_id=<!--{$arrChild.category_id}-->"><!--{$arrChild.category_name|escape}-->(<!--{$arrChild.product_count}-->)</a><br>
<!--{/if}-->
<!--{/foreach}-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
