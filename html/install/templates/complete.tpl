<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="80"></td></tr>
<tr>
	<td align="center" class="fs12">
		<strong>EC CUBE ���󥹥ȡ��뤬��λ���ޤ�����</strong><br>
		<br>
		<a href="/admin/">��������</a>�˥�����Ǥ��ޤ���<br>
		ID:admin PASSWORD:password
		
	</td>
</tr>
<tr><td height="80"></td></tr>

</table>
