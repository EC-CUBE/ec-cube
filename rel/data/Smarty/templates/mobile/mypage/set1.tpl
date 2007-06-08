<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">登録内容変更 2/3</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="set2">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【性別】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.sex}--></font>
	<input type="radio" name="sex" value="1" <!--{if $arrForm.sex eq "1"}-->checked<!--{/if}--> />男性&nbsp;
	<input type="radio" name="sex" value="2" <!--{if $arrForm.sex eq "2"}-->checked<!--{/if}--> />女性<br>

	【生年月日】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
	<input type="text" name="year" value="<!--{$arrForm.year|escape}-->" size="4" maxlength="4" istyle="4">年<br>
	<select name="month">
		<option value="">--</option>
		<!--{html_options options=$arrMonth selected=$arrForm.month}-->
	</select>月<br>
	<select name="day">
		<option value="">--</option>
		<!--{html_options options=$arrDay selected=$arrForm.day}-->
	</select>日<br>

	<!--{assign var=key1 value="zip01"}-->
	<!--{assign var=key2 value="zip02"}-->
	【郵便番号】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
	<!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
	<!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
	<input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01 == ""}--><!--{$arrForm.zip01|escape}--><!--{else}--><!--{$arrForm.zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02 == ""}--><!--{$arrForm.zip02|escape}--><!--{else}--><!--{$arrForm.zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>

	<input type="submit" name="confirm" value="次へ"><br>
	<input type="submit" name="return" value="戻る">

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
