<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!-- �������ȥ� �������� -->
<center><!--{$tpl_subtitle|escape}--></center>
<!-- �������ȥ� �����ޤ� -->

<hr>

<!--{if isset($tpl_previous_page|smarty:nodefaults) || isset($tpl_next_page|smarty:nodefaults)}-->
<!--{if isset($tpl_previous_page|smarty:nodefaults)}-->
<a href="<!--{$tpl_previous_page|escape}-->">����</a>
<!--{/if}-->
<!--{if isset($tpl_previous_page|smarty:nodefaults) && isset($tpl_next_page|smarty:nodefaults)}-->
��
<!--{/if}-->
<!--{if isset($tpl_next_page|smarty:nodefaults)}-->
<a href="<!--{$tpl_next_page|escape}-->">����</a>
<!--{/if}-->
<br><br>
<!--{/if}-->

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
<a href="<!--{$smarty.const.MOBILE_DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->" accesskey="<!--{$i+1}-->">���ʾܺ٤آ�</a>
<!--{else}-->
<a href="<!--{$smarty.const.MOBILE_DETAIL_P_HTML}--><!--{$arrProduct.product_id}-->">���ʾܺ٤آ�</a>
<!--{/if}-->
</div>

<br>
<!-- ������ �����ޤ� -->
<!--{foreachelse}-->
<!--{if $tpl_search_mode}-->
�������0��Ǥ���¾�θ���������ɤ����ٸ����򤷤Ƥ���������<br>
<!--{else}-->
���ʤ�����ޤ���<br>
<!--{/if}-->
<!--{/foreach}-->

<!--{if isset($tpl_previous_page|smarty:nodefaults) || isset($tpl_next_page|smarty:nodefaults)}-->
<!--{if isset($tpl_previous_page|smarty:nodefaults)}-->
<a href="<!--{$tpl_previous_page|escape}-->">����</a>
<!--{/if}-->
<!--{if isset($tpl_previous_page|smarty:nodefaults) && isset($tpl_next_page|smarty:nodefaults)}-->
��
<!--{/if}-->
<!--{if isset($tpl_next_page|smarty:nodefaults)}-->
<a href="<!--{$tpl_next_page|escape}-->">����</a>
<!--{/if}-->
<br>
<!--{/if}-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
