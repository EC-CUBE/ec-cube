<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">ご利用規約</div>
<hr>
<!--{$tpl_kiyaku_text}--><br>

<BR>
<!--{if $offset != -1}-->
	<a href="kiyaku.php?offset=<!--{$offset}-->">次へ→</a><br><br>
<!--{/if}-->

<a href="index.php" accesskey="1"><!--{1|numeric_emoji}-->同意して登録へ</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="2"><!--{2|numeric_emoji}-->同意しない</a><br>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
