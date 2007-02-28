<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
<!--
// ラジオボタンによる表示・非表示
function fnChangeVisible(check_id, mod_id){
	
    if (document.getElementById(check_id).checked){
		document.getElementById(mod_id).disabled = false;
		document.getElementById(mod_id).src = '../img/install/next.jpg';		
    } else {
		document.getElementById(mod_id).disabled = true;		
		document.getElementById(mod_id).src = '../img/install/next_off.jpg';
    }
}
//-->
</script>

<table width="502" border="0" cellspacing="1" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="step" value="0">

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->

<tr><td height="30"></td></tr>
<tr><td align="left" class="fs12st">■使用許諾契約書の同意</td></tr>
<tr><td align="left" class="fs12">
	以下の使用許諾契約書をお読みください。<br/>
	インストールを続行するにはこの契約書に同意する必要があります。
</td></tr>
<tr>
	<td bgcolor="#cccccc" class="fs12">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12" height="50">
			<div id="agreement">dummy・・・</div>
			</td>
		</tr>
	</table>
	</td>
</tr>
<!--{assign var=key value="send_info"}-->
<tr><td align="left" class="fs12"><input type="radio" id="agreement_yes" name="<!--{$key}-->" value=true onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if $arrForm[$key].value|escape}-->checked<!--{/if}-->><label for="agreement_yes">同意する</label>　<input type="radio" id="agreement_no" name="<!--{$key}-->" value=false onclick="fnChangeVisible('agreement_yes', 'next');" <!--{if !$arrForm[$key].value|escape}-->checked<!--{/if}-->><label for="agreement_no">同意しない</label></td></tr>
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_welcome';document.form1.submit();" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<a href="#" onclick="document.form1.submit();"><input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next" id="next"></a>
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
