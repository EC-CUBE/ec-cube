<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">����������� 1/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="set1">

	<font color="#FF0000">*��ɬ�ܹ��ܤǤ���</font><br>
	<br>

	�ڥ᡼�륢�ɥ쥹��<font color="#FF0000">��</font><br>
	<font color="#FF0000"><!--{$arrErr.email}--></font>
<!--{if @$tpl_kara_mail_from}-->
  <!--{$tpl_kara_mail_from|escape}-->
<!--{else}-->
	<input type="text" name="email" value="<!--{$email|escape}-->" istyle="3">
<!--{/if}-->
  <br>

	�ڥѥ���ɡ�<font color="#FF0000">��</font><br>
	��Ⱦ�ѱѿ���<!--{$smarty.const.PASSWORD_LEN1}-->ʸ���ʾ�<!--{$smarty.const.PASSWORD_LEN2}-->ʸ�������<br>
	<font color="#FF0000"><!--{$arrErr.password}--></font>
	<!--{assign var="size" value="`$smarty.const.PASSWORD_LEN2+2`"}-->
	<input type="text" name="password" value="<!--{$arrForm.password}-->" istyle="4" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" size="<!--{$size}-->"><br>

	�ڥѥ���ɳ�ǧ�Ѥμ����<font color="#FF0000">��</font><br>
	<font color="#FF0000"><!--{$arrErr.reminder}--></font>
	<select name="reminder">
		<option value="">���򤷤Ƥ�������</option>
		<!--{html_options options=$arrReminder selected=$reminder}-->
	</select><br>

	�ڼ����������<font color="#FF0000">��</font><br>
	<font color="#FF0000"><!--{$arrErr.reminder_answer}--></font>
	<input type="text" name="reminder_answer" value="<!--{$reminder_answer|escape}-->" istyle="1"><br>

	�ڤ�̾����<font color="#FF0000">��</font><br>
	<font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
	�����㡧��ë��<br>
	<input type="text" name="name01" value="<!--{$name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

	̾���㡧�ֻҡ�<br>
	<input type="text" name="name02" value="<!--{$name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
	<font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>

	����/�����㡧���֥��<br>
	<input type="text" name="kana01" value="<!--{$kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	����/̾���㡧�ϥʥ���<br>
	<input type="text" name="kana02" value="<!--{$kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	<input type="submit" name="submit" value="����">

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
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
