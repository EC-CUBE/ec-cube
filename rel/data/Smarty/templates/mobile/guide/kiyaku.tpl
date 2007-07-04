<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>ご利用規約(<!--{$tpl_kiyaku_index+1}-->/<!--{$tpl_kiyaku_last_index+1}-->)</center>

<hr>

<!-- ▼本文 ここから -->
<font color="#ff0000"><!--{$tpl_kiyaku_title|escape}--></font><br>
<!--{$tpl_kiyaku_text|escape}--><br>
<!-- ▲本文 ここまで -->

<!--{if !$tpl_kiyaku_is_first || !$tpl_kiyaku_is_last}-->
<br>
<!--{if !$tpl_kiyaku_is_first}-->
<a href="kiyaku.php?page=<!--{$tpl_kiyaku_index-1}-->" accesskey="1"><!--{1|numeric_emoji}-->戻る</a><br>
<!--{/if}-->
<!--{if !$tpl_kiyaku_is_last}-->
<a href="kiyaku.php?page=<!--{$tpl_kiyaku_index+1}-->" accesskey="2"><!--{2|numeric_emoji}-->進む</a><br>
<!--{/if}-->
<!--{/if}-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
