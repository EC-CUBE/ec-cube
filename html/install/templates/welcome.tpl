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

<tr><td height="60"></td></tr>
<tr>
	<td align="center" class="fs12">
		<strong>EC CUBE インストールを開始します</strong>
	</td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="30"></td></tr>
	<tr>
		<td align="center">
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
