<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>��ã��������</center>

<hr>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->">
<input type="hidden" name="zip01" value="<!--{$arrAddr[0].zip01}-->">
<input type="hidden" name="zip02" value="<!--{$arrAddr[0].zip02}-->">
<input type="hidden" name="pref" value="<!--{$arrAddr[0].pref}-->">
<input type="hidden" name="addr01" value="<!--{$arrAddr[0].addr01}-->">
<input type="hidden" name="addr02" value="<!--{$arrAddr[0].addr02}-->">
<!--<input type="hidden" name="message" value="">-->
<!--{if $tpl_login == 1}-->
<!--<input type="hidden" name="point_check" value="2">-->
<!--{/if}-->

������������<br>
<!--{assign var=key value="deliv_date"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{if $arrDelivDate}-->
<select name="<!--{$key}-->">
<option value="">����ʤ�</option>
<!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
</select>
<!--{else}-->
������ĺ���ޤ���
<!--{/if}-->
<br><br>

�������ӻ���<br>
<!--{assign var=key value="deliv_time_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<select name="<!--{$key}-->">
<option value="">����ʤ�</option>
<!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
</select>
<br>

<center><input type="submit" value="����"></center>
<center><input type="submit" name="return" value="���"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<center>LOCKON CO.,LTD.</center>
<!-- ���եå��� �����ޤ� -->