<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">お届け先登録</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="set2">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【都道府県】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.pref}--></font>
	<select name="pref">
		<option value="">都道府県を選択</option>
		<!--{html_options options=$arrPref selected=$arrForm.pref}-->
	</select><br>

	【市区町村】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.addr01}--></font>
	<input type="text" name="addr01" value="<!--{$arrForm.addr01|escape}-->" istyle="1"><br>

	【番地】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.addr02}--></font>
	<input type="text" name="addr02" value="<!--{$arrForm.addr02|escape}-->" istyle="1"><br>

	【電話番号】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></font>
	<!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
	<input type="text" size="<!--{$size}-->" name="tel01" value="<!--{$arrForm.tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input type="text" size="<!--{$size}-->" name="tel02" value="<!--{$arrForm.tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input type="text" size="<!--{$size}-->" name="tel03" value="<!--{$arrForm.tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

	<br>

	<div align="center"><input type="submit" name="submit" value="次へ"></div>
	<div align="center"><input type="submit" name="return" value="戻る"></div>

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
