<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
*}-->
<div align="center">�����;������� 3/3</div>
<hr>
	<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="text" name="mode" value="nonmember">
	<input type="text" name="mode2" value="deliv">
	<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
	
	<font color="#FF0000">*��ɬ�ܹ��ܤǤ���</font><br>
	<br>

	����ƻ�ܸ���<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.pref}--></font>
	<select name="pref">
		<option value="">��ƻ�ܸ�������</option>
		<!--{html_options options=$arrPref selected=$pref}-->
	</select><br>

	�ڻԶ�Į¼��<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.addr01}--></font>
	<input type="text" name="addr01" value="<!--{$addr01|escape}-->" istyle="1"><br>

	�����ϡ�<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.addr02}--></font>
	<input type="text" name="addr02" value="<!--{$addr02|escape}-->" istyle="1"><br>

	�������ֹ��<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></font>
	<!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
	<input type="text" size="<!--{$size}-->" name="tel01" value="<!--{$tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input type="text" size="<!--{$size}-->" name="tel02" value="<!--{$tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input type="text" size="<!--{$size}-->" name="tel03" value="<!--{$tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

	<!--�ڥ᡼��ޥ������<br>
	�����ʾ�����˾����ޤ�����<br>
	�ۿ���˾<input type="checkbox" name="mailmaga_flg" value="on" <!--{if $mailmaga_flg eq 'on'}-->checked<!--{/if}--> /><br>
	�ʴ�˾����ʤ����ϥ����å���Ϥ����Ƥ���������<br>
	<br>-->

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
