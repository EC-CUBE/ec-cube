<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>配送先指定</center>

<hr>

<!--{if $arrErr.deli != ""}-->
<font color="#ff0000"><!--{$arrErr.deli}--></font>
<!--{/if}-->

<!--▼CONTENTS-->
<!--{section name=cnt loop=$arrAddr}-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="deli" value="<!--{$smarty.section.cnt.iteration}-->">
<!--{if $smarty.section.cnt.first}-->
<input type="hidden" name="mode" value="customer_addr">
<input type="hidden" name="other_deliv_id" value="">
<!--{else}-->
<input type="hidden" name="mode" value="other_addr">
<input type="hidden" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
<!--{/if}-->
■配送先<!--{$smarty.section.cnt.iteration}--><br>
〒<!--{$arrAddr[cnt].zip01}-->-<!--{$arrAddr[cnt].zip02}--><br>
<!--{assign var=key value=$arrAddr[cnt].pref}--><!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|escape}--><br>
<!--{if $arrAddr[cnt].addr02 != ""}-->
<!--{$arrAddr[cnt].addr02|escape}--><br>
<!--{/if}-->
<center><input type="submit" value="ここに送る"></center>
</form>
<!--{/section}-->

<br>

■その他のお届け先を指定<br>
<form method="get" action="deliv_addr.php">
<center><input type="submit" value="新規登録"></center>
</form>
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
