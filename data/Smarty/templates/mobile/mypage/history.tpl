<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">��������</div>
<hr>

<!--{section name=cnt loop=$arrOrder}-->
	��<!--{$arrOrder[cnt].create_date|sfDispDBDate}--><br>
	��ʸ�ֹ�:<!--{$arrOrder[cnt].order_id}--><br>
	<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
	��׶��:<!--{$arrOrder[cnt].payment_total|number_format}-->��<br>

	<div align="center">
	<form name="form1" method="post" action="history_detail.php">
		<input type="hidden" name="order_id" value="<!--{$arrOrder[cnt].order_id}-->">
		<input type="submit" name="submit" value="�ܺ٤򸫤�">
	</form>
	</div>
	<br>
<!--{/section}-->
<br>

<!--{$tpl_strnavi}-->

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
