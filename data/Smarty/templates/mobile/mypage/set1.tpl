<div align="center">��Ͽ�����ѹ� 2/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="set2">

	<font color="#FF0000">*��ɬ�ܹ��ܤǤ���</font><br>
	<br>

	�����̡�<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.sex}--></font>
	<input type="radio" name="sex" value="1" <!--{if $arrForm.sex eq "1"}-->checked<!--{/if}--> />����&nbsp;
	<input type="radio" name="sex" value="2" <!--{if $arrForm.sex eq "2"}-->checked<!--{/if}--> />����<br>

	����ǯ������<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
	<select name="year">
		<!--{html_options options=$arrYear selected=$arrForm.year}-->
	</select>ǯ<br>
	<select name="month">
		<option value="">--</option>
		<!--{html_options options=$arrMonth selected=$arrForm.month}-->
	</select>��<br>
	<select value="" name="day">
		<option value="">--</option>
		<!--{html_options options=$arrDay selected=$arrForm.day}-->
	</select>��<br>

	<!--{assign var=key1 value="zip01"}-->
	<!--{assign var=key2 value="zip02"}-->
	��͹���ֹ��<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
	<!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
	<!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
	<input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01 == ""}--><!--{$arrForm.zip01|escape}--><!--{else}--><!--{$arrForm.zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02 == ""}--><!--{$arrForm.zip02|escape}--><!--{else}--><!--{$arrForm.zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>

	<input type="submit" name="confirm" value="����">

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
