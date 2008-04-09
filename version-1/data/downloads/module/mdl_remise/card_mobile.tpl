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

■お支払い方法<br>
<!--{foreach key=key item=item from=$arrCreMet name=method_loop}-->
<input type="radio"
       name="METHOD"
       value="<!--{$key}-->" <!--{if $smarty.foreach.method_loop.first}-->checked<!--{/if}--> /> <!--{$item|escape}--><br>
<!--{/foreach}-->

<br>

■分割回数<br>
<!--{assign var=key value="PTIMES"}-->
<span class="red"><!--{$arrErr[$key]}--></span>
<select name="<!--{$key}-->">
<option value="1" selected="">指定なし</option>
<!--{html_options options=$arrCreDiv selected=$arrForm[$key].value}-->
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
