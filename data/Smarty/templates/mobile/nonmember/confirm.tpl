<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>����ʸ���Ƴ�ǧ</center>

<hr>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

�����Τ���ʸ���Ƥ˴ְ㤤�Ϥ������ޤ��󤫡�<br>

<br>

���������<br>
<!--{if $arrData.deliv_check == 1}-->
<!--{$arrData.deliv_name01|escape}--> <!--{$arrData.deliv_name02|escape}--><br>
��<!--{$arrData.deliv_zip01|escape}-->-<!--{$arrData.deliv_zip02|escape}--><br>
<!--{$arrPref[$arrData.deliv_pref]}--><!--{$arrData.deliv_addr01|escape}--><!--{$arrData.deliv_addr02|escape}--><br>
<!--{else}-->
<!--{$arrData.order_name01|escape}--> <!--{$arrData.order_name02|escape}--><br>
��<!--{$arrData.order_zip01|escape}-->-<!--{$arrData.order_zip02|escape}--><br>
<!--{$arrPref[$arrData.order_pref]}--><!--{$arrData.order_addr01|escape}--><!--{$arrData.order_addr02|escape}--><br>
<!--{/if}-->

<br>

���������������<br>
����<!--{$arrData.deliv_date|escape|default:"����ʤ�"}--><br>
���֡�<!--{$arrData.deliv_time|escape|default:"����ʤ�"}--><br>

<br>

�ڤ���ʧ����ˡ��<br>
<!--{$arrData.payment_method|escape}--><br>

<br>

�ڤ���ʸ���ơ�<br>
<!--{section name=cnt loop=$arrProductsClass}-->
<!--{$arrProductsClass[cnt].name}--> <!--{$arrProductsClass[cnt].quantity|number_format}-->��<br>
<!--{/section}-->

<br>

�ڹ�����ۡ�<br>
���ʹ�ס�<!--{$tpl_total_pretax|number_format}--><br>
������<!--{$arrData.deliv_fee|number_format}--><br>
<!--{if $arrData.charge > 0}-->�������<!--{$arrData.charge|number_format}--><br><!--{/if}-->
��ס�<!--{$arrData.payment_total|number_format}--><br>
(������ǡ�<!--{$arrData.tax|number_format}-->)<br>

<br>

<center><input type="submit" value="��ʸ"></center>
</form>
<!--<form action="<!--{$smarty.const.MOBILE_URL_SHOP_PAYMENT}-->" method="post">-->
<form action="./payment.php" method="post">
<input type="hidden" name="mode" value="deliv_date">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="payment_id" value="<!--{$arrData.payment_id}-->">
<input type="hidden" name="deliv_date" value="<!--{$arrData.deliv_date}-->">
<input type="hidden" name="deliv_time_id" value="<!--{$arrData.deliv_time_id}-->">
<center><input type="submit" value="���"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<center>LOCKON CO.,LTD.</center>
<!-- ���եå��� �����ޤ� -->
