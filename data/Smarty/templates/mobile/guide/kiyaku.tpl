<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>�����ѵ���(<!--{$tpl_kiyaku_index+1}-->/<!--{$tpl_kiyaku_last_index+1}-->)</center>

<hr>

<!-- ����ʸ �������� -->
<font color="#ff0000"><!--{$tpl_kiyaku_title|escape}--></font><br>
<!--{$tpl_kiyaku_text|escape}--><br>
<!-- ����ʸ �����ޤ� -->

<!--{if !$tpl_kiyaku_is_first || !$tpl_kiyaku_is_last}-->
<br>
<!--{if !$tpl_kiyaku_is_first}-->
<a href="kiyaku.php?page=<!--{$tpl_kiyaku_index-1}-->" accesskey="1"><!--{1|numeric_emoji}-->���</a><br>
<!--{/if}-->
<!--{if !$tpl_kiyaku_is_last}-->
<a href="kiyaku.php?page=<!--{$tpl_kiyaku_index+1}-->" accesskey="2"><!--{2|numeric_emoji}-->�ʤ�</a><br>
<!--{/if}-->
<!--{/if}-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
