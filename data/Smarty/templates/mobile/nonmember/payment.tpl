<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>支払い方法指定</center>

<hr>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="text" name="mode" value="deliv_date">
<input type="text" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="text" name="deliv_date" value="<!--{$arrForm.deliv_date.value}-->">
<input type="text" name="deliv_time_id" value="<!--{$arrForm.deliv_time_id.value}-->">
<input type="text" name="zip01" value="<!--{$arrAddr[0].zip01}-->">
<input type="text" name="zip02" value="<!--{$arrAddr[0].zip02}-->">
<input type="text" name="pref" value="<!--{$arrAddr[0].pref}-->">
<input type="text" name="addr01" value="<!--{$arrAddr[0].addr01}-->">
<input type="text" name="addr02" value="<!--{$arrAddr[0].addr02}-->">
<!--<input type="text" name="message" value="">-->
<!--{if $tpl_login == 1}-->
<!--<input type="text" name="point_check" value="2">-->
<!--{/if}-->

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
