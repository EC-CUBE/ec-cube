<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>���쥸�åȷ��</center>

<hr>
<br>
<form method="post" action="<!--{$order_url|escape}-->">
	<!--{foreach key=key item=item from=$arrSendData}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item}-->">
	<!--{/foreach}-->
	<center><input type="submit" value="���쥸�åȷ�Ѥ˿ʤ�"></center>
</form>

<br>
<hr>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
