<!-- �������ȥ� �������� -->
<center><!--{$tpl_subtitle|escape}--></center>
<!-- �������ȥ� �����ޤ� -->

<hr>

<!--{foreach from=$arrProducts key=i item=arrProduct}-->
<!-- ������ �������� -->
<!--{if $i+1<9}--><!--{$i+1|numeric_emoji}--><!--{else}-->[<!--{$i+1}-->]<!--{/if}-->
<!-- ����̾ --><!--{$arrProduct.name|escape}--><br>

���ʡ�
<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
��<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{else}-->
��<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->����<!--{$arrProduct.price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
<br>

<div align="right">
<!--{if $i+1<9}-->
<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->" accesskey="<!--{$i+1}-->">���ʾܺ٤آ�</a>
<!--{else}-->
<a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->">���ʾܺ٤آ�</a>
<!--{/if}-->
</div>

<br>
<!-- ������ �����ޤ� -->
<!--{/foreach}-->

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
