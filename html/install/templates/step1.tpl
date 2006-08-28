<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■ECサイトの設定</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">インストールディレクトリ</td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="install_dir"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(通常)</td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="normal_url"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(セキュア)</td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="secure_url"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">ドメイン</td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="domain"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
</from>
</table>								
