<div align="center">��������ܺ�</div>
<hr>

��<!--{$arrDisp.create_date|sfDispDBDate}--><br>
��ʸ�ֹ�:<!--{$arrDisp.order_id}--><br>
<br>
���������<br>
	<!--{assign var=key1 value="deliv_name01"}--><!--{assign var=key2 value="deliv_name02"}-->
	<!--{$arrDisp[$key1]|escape}-->&nbsp;<!--{$arrDisp[$key2]|escape}--><br>
	<!--{assign var=key1 value="deliv_zip01"}--><!--{assign var=key2 value="deliv_zip02"}-->
	��<!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}--><br>
	<!--{assign var=pref value=`$arrDisp.deliv_pref`}--><!--{$arrPref[$pref]}-->
	<!--{assign var=key value="deliv_addr01"}--><!--{$arrDisp[$key]|escape}-->
	<!--{assign var=key value="deliv_addr02"}--><!--{$arrDisp[$key]|escape}--><br>
<br>
���������������<br>
<!--{if $arrDisp.deliv_date eq "" and $arrDelivTime[$arrDisp.deliv_time_id] eq ""}-->
	����ʤ�<br>
<!--{else}-->
	<!--{$arrDisp.deliv_date|escape}--> <!--{$arrDelivTime[$arrDisp.deliv_time_id]|escape}--><br>
<!--{/if}-->
<br>
�ڤ���ʧ����ˡ��<br>
<!--{$arrPayment[$arrDisp.payment_id]|escape}--><br>
<br>
�ڤ���ʸ���ơ�<br>
<!--{section name=cnt loop=$arrDisp.quantity}-->
<!--{$arrDisp.product_name[cnt]|escape}--><br>
<a href="<!--{$smarty.const.URL_DIR}-->products/detail.php?product_id=<!--{$arrDisp.product_id[cnt]}-->">���ʹ����ܺ٢�</a><br>
<!--{/section}-->
<br>
�ڹ�����ۡ�<br>
���ʹ��:<!--{$arrDisp.subtotal|number_format}-->��<br>
����:<!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|escape|number_format}-->��<br>
���:<!--{$arrDisp.payment_total|number_format}-->��<br>
<br>

<form action="order.php" method="post">
	<input type="hidden" name="order_id" value="<!--{$arrDisp.order_id}-->">
	<div align="center"><input type="submit" name="submit" value="����ʸ"></div>
</form>

<br>

<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
