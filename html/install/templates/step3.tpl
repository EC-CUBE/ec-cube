<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
<!--
	// モードとキーを指定してSUBMITを行う。
	function fnModeSubmit(mode) {
		switch(mode) {
		case 'drop':
			if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')){
				return;
			}
			break;
		default:
			break;
		}
		document.form1['mode'].value = mode;
		document.form1.submit();	
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
<tr><td align="left" class="fs12st">■データベースの初期化</td></tr>
<tr><td align="left" class="fs12"><!--{if $tpl_db_version != ""}-->接続情報：<!--{$tpl_db_version}--><!--{/if}--></td></tr>
<tr><td align="left" class="fs12">データベースの初期化を開始します</td></tr>
<tr><td align="left" class="fs12">※すでにテーブル等が作成されている場合は中断されます</td></tr>
<!--{if $tpl_mode != 'complete'}-->
<tr><td align="left" class="fs12"><input type="checkbox" id="skip" name="db_skip" <!--{if $tpl_db_skip == "on"}-->checked<!--{/if}-->> <label for="skip">データベースの初期化処理を行わない</label></td></tr>
<!--{/if}-->

<!--{if count($arrErr) > 0 || $tpl_message != ""}-->
<tr>
	<td bgcolor="#cccccc" class="fs12">
	<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
		<tr>
			<td bgcolor="#ffffff" class="fs12" height="50">
			<!--{$tpl_message}--><br>
			<span class="red"><!--{$arrErr.all}--></span>
			<!--{if $arrErr.all != ""}-->
			<input type="button" onclick="fnModeSubmit('drop');" value="既存データをすべて削除する">
			<!--{/if}-->
			</td>
		</tr>
	</table>
	</td>
</tr>
<!--{/if}-->
</table>

<table width="500" border="0" cellspacing="1" cellpadding="8" summary=" ">
	<tr><td height="20"></td></tr>
	<tr>
		<td align="center">
		<a href="#" onmouseover="chgImg('../img/install/back_on.jpg','back')" onmouseout="chgImg('../img/install/back.jpg','back')" onclick="document.form1['mode'].value='return_step2';document.form1.submit();return false;" /><img  width="105" src="../img/install/back.jpg"  height="24" alt="前へ戻る" border="0" name="back"></a>
		<input type="image" onMouseover="chgImgImageSubmit('../img/install/next_on.jpg',this)" onMouseout="chgImgImageSubmit('../img/install/next.jpg',this)" src="../img/install/next.jpg" width="105" height="24" alt="次へ進む" border="0" name="next" onClick="document.body.style.cursor = 'wait';">
		</td>
	</tr>
	<tr><td height="30"></td></tr>
</from>
</table>								
