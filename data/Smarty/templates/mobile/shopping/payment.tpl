<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>配達日・支払い指定</center>

<hr>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<!--<input type="hidden" name="message" value="">-->
<!--{if $tpl_login == 1}-->
<!--<input type="hidden" name="point_check" value="2">-->
<!--{/if}-->

■配達日時指定<br>
配送日指定<br>
<!--{assign var=key value="deliv_date"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{if $arrDelivDate}-->
<select name="<!--{$key}-->">
<option value="">指定なし</option>
<!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
</select>
<!--{else}-->
ご指定頂けません。
<!--{/if}-->
<br>

時間帯指定<br>
<!--{assign var=key value="deliv_time_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<select name="<!--{$key}-->">
<option value="">指定なし</option>
<!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
</select>
<br>

<br>

<center>支払い方法指定</center>

<hr>

<!--{assign var=key value="payment_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{section name=cnt loop=$arrPayment}-->
<input type="radio" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}-->>
<!--{$arrPayment[cnt].payment_method|escape}-->
<br>
<!--{/section}-->

<center><input type="submit" value="次へ"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
