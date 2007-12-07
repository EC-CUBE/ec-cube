<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>クレジット決済</center>

<hr>

<!--{* 決済時のエラーを表示 *}-->
<font color="red"><!--{$tpl_error}--></font>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="regist">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

■カード番号<br>
<!--{assign var=key_no01 value="card_no01"}-->
<!--{if $arrErr[$key_no01] != ""}-->
<font color="red"><!--{$arrErr[$key_no01]}--></font>
<!--{/if}-->
<!--{assign var=key_no02 value="card_no02"}-->
<!--{if $arrErr[$key_no02] != ""}-->
<font color="red"><!--{$arrErr[$key_no02]}--></font>
<!--{/if}-->
<!--{assign var=key_no03 value="card_no03"}-->
<!--{if $arrErr[$key_no03] != ""}-->
<font color="red"><!--{$arrErr[$key_no03]}--></font>
<!--{/if}-->
<!--{assign var=key_no04 value="card_no04"}-->
<!--{if $arrErr[$key_no04] != ""}-->
<font color="red"><!--{$arrErr[$key_no04]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key_no01}-->" value="<!--{$arrForm[$key_no01].value|escape}-->" maxlength="4" size="4" istyle="4">
<input type="text" name="<!--{$key_no02}-->" value="<!--{$arrForm[$key_no02].value|escape}-->" maxlength="4" size="4" istyle="4">
<input type="text" name="<!--{$key_no03}-->" value="<!--{$arrForm[$key_no03].value|escape}-->" maxlength="4" size="4" istyle="4">
<input type="text" name="<!--{$key_no04}-->" value="<!--{$arrForm[$key_no04].value|escape}-->" maxlength="4" size="4" istyle="4"><br>
<br>

■有効期限<br>
<!--{assign var=key_month value="card_month"}-->
<!--{if $arrErr[$key_month] != ""}-->
<font color="red"><!--{$arrErr[$key_month]}--></font>
<!--{/if}-->
<!--{assign var=key_year value="card_year"}-->
<!--{if $arrErr[$key_year] != ""}-->
<font color="red"><!--{$arrErr[$key_year]}--></font>
<!--{/if}-->
<select name="<!--{$key_month}-->">
<!--{html_options options=$arrMonth selected=$arrForm[$key_month].value}-->
</select>
月
<select name="<!--{$key_year}-->">
<!--{html_options options=$arrYear selected=$arrForm[$key_year].value}-->
</select>
年
<br><br>

■ローマ字氏名<br>
<!--{assign var=key_name01 value="card_name01"}-->
<!--{if $arrErr[$key_name01] != ""}-->
<font color="red"><!--{$arrErr[$key_name01]}--></font>
<!--{/if}-->
<!--{assign var=key_name02 value="card_name02"}-->
<!--{if $arrErr[$key_name02] != ""}-->
<font color="red"><!--{$arrErr[$key_name02]}--></font>
<!--{/if}-->
名<input type="text" name="<!--{$key_name01}-->" value="<!--{$arrForm[$key_name01].value|escape}-->" size="10" istyle="3">
姓<input type="text" name="<!--{$key_name02}-->" value="<!--{$arrForm[$key_name02].value|escape}-->" size="10" istyle="3"><br>

半角英字入力<br>
例：TARO YAMADA<br>
<br>

■お支払い方法<br>
<!--{assign var=key value="paymethod"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<select name="<!--{$key}-->">
<!--{html_options options=$arrPayMethod selected=$arrForm[$key].value}-->
</select>

<br>

<center><input type="submit" name="register" value="注文完了ページへ"></center>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="return">
<center><input type="submit" name="return" value="戻る"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
