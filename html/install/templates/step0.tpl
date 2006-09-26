<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■アクセス権限のチェック</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12">
			<!--{$mess}-->
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>

<!--{if !$err_file}-->
<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<tr><td height="15"></td></tr>
<tr><td align="left" class="fs12">必要なファイルのコピーを開始します。</td></tr>
</table>
<!--{/if}-->

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<!--{if !$err_file}-->
		<a href="#" onmouseover="chgImg('/img/install/back_on.jpg','back')" onmouseout="chgImg('/img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="/img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('/img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/install/next.jpg',this)" src="/img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
		<!--{else}-->
		<a href="#" onmouseover="chgImg('/img/install/back_on.jpg','back')" onmouseout="chgImg('/img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="/img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<img src="/img/install/next_off.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">		
		<!--{/if}-->
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
