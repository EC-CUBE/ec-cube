<!-- �������ȥ� �������� -->
<center><!--{$tpl_subtitle|escape}--></center>
<!-- �������ȥ� �����ޤ� -->

<hr>

<!--{foreach from=$arrProducts key=i item=arrProduct}-->
<!-- ������ �������� -->
<!--{if $i+1<9}-->[emoji:<!--{$i+125}-->]<!--{else}-->[<!--{$i+1}-->]<!--{/if}-->
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

<hr>

<!--XXX--><a href="#" accesskey="9">[emoji:133]�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0">[emoji:134]TOP�ڡ�����</a><br>
<br>

<!-- ���եå��� �������� -->
<center>LOCKON CO.,LTD.</center>
<!-- ���եå��� �����ޤ� -->
