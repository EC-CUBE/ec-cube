<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">�����;������� 2/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="text" name="mode" value="nonmember">
	<input type="text" name="mode2" value="set3">
	<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
	
	<font color="#FF0000">*��ɬ�ܹ��ܤǤ���</font><br>
	<br>

	�����̡�<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.sex}--></font>
	<input type="radio" name="sex" value="1" <!--{if $sex eq 1}-->checked<!--{/if}--> />����&nbsp;<input type="radio" name="sex" value="2" <!--{if $sex eq 2}-->checked<!--{/if}--> />����<br>

	����ǯ������<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
	<input type="text" name="year" value="<!--{$year|escape}-->" size="4" maxlength="4" istyle="4">ǯ(����)<br>
	<select name="month">
		<option value="">--</option>
		<!--{html_options options=$arrMonth selected=$month}-->
	</select>��<br>
	<select name="day">
		<option value="">--</option>
		<!--{html_options options=$arrDay selected=$day}-->
	</select>��<br>

	<!--{assign var=key1 value="zip01"}-->
	<!--{assign var=key2 value="zip02"}-->
	��͹���ֹ��<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
	<!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
	<!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
	<input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $zip01 == ""}--><!--{$arrOtherDeliv.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $zip02 == ""}--><!--{$arrOtherDeliv.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>

	<input type="submit" name="submit" value="����"><br>
	<input type="submit" name="return" value="���">

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="text" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
