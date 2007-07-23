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
	<input type="hidden" name="mode" value="set1">

	<font color="#FF0000">*は必須項目です。</font><br>
	<br>

	【お名前】<font color="#FF0000">※</font><br>
	<font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
	姓（例：渋谷）<br>
	<input type="text" name="name01" value="<!--{$arrForm.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

	名（例：花子）<br>
	<input type="text" name="name02" value="<!--{$arrForm.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
	<font color="#FF0000"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></font>

	カナ/姓（例：シブヤ）<br>
	<input type="text" name="kana01" value="<!--{$arrForm.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	カナ/名（例：ハナコ）<br>
	<input type="text" name="kana02" value="<!--{$arrForm.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

	<br>

	<!--{assign var=key1 value="zip01"}-->
	<!--{assign var=key2 value="zip02"}-->
	【郵便番号】<font color="#FF0000">*</font><br>
	<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
	<!--{assign var="size1" value="`$smarty.const.ZIP01_LEN+2`"}-->
	<!--{assign var="size2" value="`$smarty.const.ZIP02_LEN+2`"}-->
	<input size="<!--{$size1}-->" type="text" name="zip01" value="<!--{if $arrForm.zip01 != ""}--><!--{$arrForm.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" istyle="4">
	&nbsp;-&nbsp;
	<input size="<!--{$size2}-->" type="text" name="zip02" value="<!--{if $arrForm.zip02 != ""}--><!--{$arrForm.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" istyle="4"><br>

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
