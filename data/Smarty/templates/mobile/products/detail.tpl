<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--��CONTENTS-->
<!--��MAIN CONTENTS-->
<!--�����Ď٤�������-->
<!--������̾��-->
<div align="center"><!--{$arrProduct.name|escape}--></div>
<hr>
<!--�����Ď٤����ޤ�-->
<!--�ܺ٤�������-->
<!--{assign var=key value="main_image"}-->
<img src="<!--{$arrFile[$key].filepath}-->"><br>
<!--���َܺҎ��ݎ��Ҏݎġ�-->
[emoji:76]<!--{$arrProduct.main_comment|nl2br}--><br>
<br>
<!--��������-->
<!--�����ʡ�-->
<font color="#FF0000">����(�ǹ�):
<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
	<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{else}-->
	<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->��<!--{$arrProduct.price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
��</font><br/>
<!--{if $arrProduct.price01_max > 0}-->
<font color="#FF0000">���ͻԾ����:
<!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
<!--{$arrProduct.price01_min|number_format}-->
<!--{else}-->
<!--{$arrProduct.price01_min|number_format}-->��<!--{$arrProduct.price01_max|number_format}-->
<!--{/if}-->
��</font><br>
<!--{/if}-->
<form name="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<input type="hidden" name="mode" value="select">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<!--{if $tpl_stock_find}-->
	<!--�����ʤ����֡�-->
	<center><input type="submit" name="select" id="cart" value="���ξ��ʤ�����"></center>
<!--{else}-->
	<font color="#FF0000">�������������ޤ��󤬎��������ڤ���Ǥ���</font>
<!--{/if}-->
</form>
<!--�ܺ٤����ޤ�-->
<!--��CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
