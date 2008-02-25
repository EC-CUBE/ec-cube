<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>クレジット決済</center>

<hr>

<form method="post" action="<!--{$arrSendData.SEND_URL|escape}-->">
<!--{foreach from=$arrSendData key=key item=val}-->
    <!--{if $key != 'SEND_URL'}-->
    <input type="hidden" name="<!--{$key|escape}-->" value="<!--{$val|escape}-->">
    <!--{/if}-->
<!--{/foreach}-->

<!--お支払方法・お届け時間の指定・その他お問い合わせここから-->     
■お名前<br>
<!--{$arrSendData.NAME1|escape}--><!--{$arrSendData.NAME2|escape}--><br><br>
■電話番号<br>
<!--{$arrSendData.TEL|escape}--><br><br>
■合計金額<br>
<!--{$arrSendData.TOTAL|escape}-->円<br>
<!--お支払方法・お届け時間の指定・その他お問い合わせここまで-->

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
