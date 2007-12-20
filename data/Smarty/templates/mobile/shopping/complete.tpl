<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>ご注文完了</center>

<hr>

ご注文、有り難うございました。<br>
商品到着をお楽しみにお待ち下さいませ。<br>
どうぞ、今後とも、<!--{$arrInfo.shop_name|escape}-->をよろしくお願いします。<br>
<br>

<!--{if $arrOther.title.value }-->
<!-- ▼その他の決済情報 -->
■<!--{$arrOther.title.name}-->情報<br>
<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value|nl2br}--><br>
<!--{/if}-->
<!--{/foreach}-->
<br>
<!-- ▲その他の決済情報 -->
<!--{/if}-->

<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページに戻る</a><br>

<br>
<hr>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
