<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
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
<tr><td align="left" class="fs12st">■ECサイトの設定</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">店名<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="shop_name"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			<br><span class="fs10">※あなたの店名をご記入ください。</span>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">管理者メールアドレス<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332">
			<!--{assign var=key value="admin_mail"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			<br><span class="fs10">※受注メールなどの宛先になります。：(例)example@ec-cube.net</span>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td height="20"></td></tr>
<tr><td align="left" class="fs12st">■WEBサーバの設定</td></tr>
<tr>
	<td bgcolor="#cccccc">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">		
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">HTMLパス<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="install_dir"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(通常)<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="normal_url"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">URL(セキュア)<span class="red">※</span></td>
			<td bgcolor="#ffffff" width="332" class="fs12">
			<!--{assign var=key value="secure_url"}-->
			<span class="red"><!--{$arrErr[$key]}--></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			</td>
		</tr>
		<tr>
			<td bgcolor="#f2f1ec" width="150" class="fs12n">共通ドメイン</td>
			<td bgcolor="#ffffff" width="332">	
			<!--{assign var=key value="domain"}-->
			<span class="red"><span class="fs12n"><!--{$arrErr[$key]}--></span></span>
			<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="50" class="box50" />
			<br><span class="fs10">※通常URLとセキュアURLでサブドメインが異なる場合に指定します。</span>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step0';document.form1.submit();return false;" /><img  width="105" src="<!--{$smarty.const.URL_DIR}-->img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>
