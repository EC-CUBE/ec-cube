<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!-- ���� �������� -->
<center><img src="<!--{$smarty.const.URL_DIR}-->img/header/logo.gif"></center>
<!-- ���� �����ޤ� -->

<br>

<!-- ��������� �������� -->
<!--{include_php file=`$smarty.const.MOBILE_HTML_PATH`frontparts/bloc/news.php}-->
<!-- ��������� �����ޤ� -->

<br>

<!-- ���ԥå����å׾��� �������� -->
<hr>
<!--{include_php file=`$smarty.const.MOBILE_HTML_PATH`frontparts/bloc/best5.php}-->
<hr>
<!-- ���ԥå����å׾��� �����ޤ� -->

<br>

<!-- ����˥塼 �������� -->
<!--{1|numeric_emoji}-->���ʥ��ƥ���<br>
<!--{include_php file=`$smarty.const.MOBILE_HTML_PATH`frontparts/bloc/category.php}-->
<a href="products/search.php" accesskey="2"><!--{2|numeric_emoji}-->���ʸ���</a><br>
<!--{if $isLogin eq true}-->
<a href="mypage/refusal.php" accesskey="3"><!--{3|numeric_emoji}-->������</a><br>
<!--{else}-->
<a href="entry/new.php" accesskey="3"><!--{3|numeric_emoji}-->�����Ͽ</a><br>
<!--{/if}-->
<a href="guide/index.php" accesskey="4"><!--{4|numeric_emoji}-->�����ѥ�����</a><br>
<a href="contact/index.php" accesskey="5"><!--{5|numeric_emoji}-->���䤤�礻</a><br>
<a href="mypage/index.php" accesskey="6" utn><!--{6|numeric_emoji}-->MY�ڡ���</a><br>
<!-- ����˥塼 �����ޤ� -->

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
