<!-- ▼タイトル ここから -->
<center><!--{$tpl_subtitle|escape}--></center>
<!-- ▲タイトル ここまで -->

<hr>

<!--{foreach from=$arrProducts key=i item=arrProduct}-->
<!-- ▼商品 ここから -->
<!--{if $i+1<9}--><!--{$i+1|numeric_emoji}--><!--{else}-->[<!--{$i+1}-->]<!--{/if}-->
<!-- 商品名 --><!--{$arrProduct.name|escape}--><br>

価格：
<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
￥<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{else}-->
￥<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->〜￥<!--{$arrProduct.price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
<br>

<div align="right">
<!--{if $i+1<9}-->
<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->" accesskey="<!--{$i+1}-->">商品詳細へ→</a>
<!--{else}-->
<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->">商品詳細へ→</a>
<!--{/if}-->
</div>

<br>
<!-- ▲商品 ここまで -->
<!--{/foreach}-->

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
