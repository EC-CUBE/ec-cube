<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>�ѥ���ɤ�˺�줿��</center>

<font color="#ff0000">���������ѥ���ɤ�ȯ�Ԥ������ޤ��Τǡ���˺��ˤʤä��ѥ���ɤϤ����ѤǤ��ʤ��ʤ�ޤ���</font>

<hr>

<!--{if $errmsg}-->
<font color="#ff0000"><!--{$errmsg}--></font><br>
<!--{/if}-->

<!--{if @$tpl_kara_mail_to != ''}-->
������Ͽ���Υ᡼�륢�ɥ쥹����᡼�����������ϡ����Υ�󥯤򥯥�å����ƶ��᡼����������Ƥ���������<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|escape:'url'}-->">�᡼������</a></center>

<br>

���᡼�������ʤ����ϡ�����Ͽ���Υ᡼�륢�ɥ쥹�����Ϥ��ơּ��ءץܥ���򥯥�å����Ƥ���������<br>
<!--{else}-->
����Ͽ���Υ᡼�륢�ɥ쥹�����Ϥ��ơּ��ءץܥ���򥯥�å����Ƥ���������<br>
<!--{/if}-->

<form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post">
<input type="hidden" name="mode" value="mail_check">

�᡼�륢�ɥ쥹��<input type="text" name="email" value="<!--{$tpl_login_email|escape}-->" size="50" istyle="3"><br>

<center><input type="submit" value="����" name="next"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
