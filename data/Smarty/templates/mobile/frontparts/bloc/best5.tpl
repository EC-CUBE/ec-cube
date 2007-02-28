<!--{if count($arrBestProducts) > 0}-->
<center>
<!--{foreach from=$arrBestProducts item=arrProduct name=best_products}-->

<!-- ▼オススメコメント ここから -->
<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->">
<!--{$arrProduct.comment|escape|nl2br}-->
</a>
<!-- ▲オススメコメント ここまで -->

<!--{if !$smarty.foreach.best_products.last}--><br><br><!--{/if}-->
<!--{/foreach}-->
</center>
<!--{/if}-->
