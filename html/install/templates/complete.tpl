<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="80"></td></tr>
<tr>
	<td align="center" class="fs12">
		<strong>EC CUBE ���󥹥ȡ��뤬��λ���ޤ�����</strong><br>
		<br>
		<a href="<!--{$tpl_sslurl}-->admin/">��������</a>�˥�����Ǥ��ޤ���
	</td>
</tr>
<tr>
	<td align="center" class="fs10">
		��ۤ���Ͽ����ID���ѥ���ɤ��Ѥ��ƥ����󤷤Ƥ���������
	</td>
</tr>
<tr><td height="80"></td></tr>

</table>
