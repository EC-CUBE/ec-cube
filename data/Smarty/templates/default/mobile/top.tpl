<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!-- ▼ロゴ ここから -->
<center><img src="<!--{$TPL_DIR}-->img/header/logo.gif"></center>
<!-- ▲ロゴ ここまで -->

<br>

<!-- ▼新着情報 ここから -->
<!--{include_php file=`$smarty.const.MOBILE_HTML_PATH`frontparts/bloc/news.php}-->
<!-- ▲新着情報 ここまで -->

<br>

<!-- ▼ピックアップ商品 ここから -->
<hr>
<!--{include_php file=`$smarty.const.MOBILE_HTML_PATH`frontparts/bloc/best5.php}-->
<hr>
<!-- ▲ピックアップ商品 ここまで -->

<br>

<!-- ▼メニュー ここから -->
<!--{1|numeric_emoji}-->商品カテゴリ<br>
<!--{include_php file=`$smarty.const.MOBILE_HTML_PATH`frontparts/bloc/category.php}-->
<a href="products/search.php" accesskey="2"><!--{2|numeric_emoji}-->商品検索</a><br>
<!--{if $isLogin eq true}-->
<a href="mypage/refusal.php" accesskey="3"><!--{3|numeric_emoji}-->会員退会</a><br>
<!--{else}-->
<a href="entry/new.php" accesskey="3"><!--{3|numeric_emoji}-->会員登録</a><br>
<!--{/if}-->
<a href="guide/index.php" accesskey="4"><!--{4|numeric_emoji}-->ご利用ガイド</a><br>
<a href="contact/index.php" accesskey="5"><!--{5|numeric_emoji}-->お問い合せ</a><br>
<a href="mypage/index.php" accesskey="6" utn><!--{6|numeric_emoji}-->MYページ</a><br>
<!-- ▲メニュー ここまで -->

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
