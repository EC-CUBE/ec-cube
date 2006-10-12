<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼リンクここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<!--{if $tpl_page_category != "abouts"}-->
			<td><a href="/abouts/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/side/about_on.jpg','about');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/side/about.jpg','about');"><img src="<!--{$smarty.const.URL_DIR}-->img/side/about.jpg" width="166" height="30" alt="当サイトについて" border="0" name="about"></a></td>
		<!--{else}-->
			<td><a href="/abouts/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/side/about_on.jpg" width="166" height="30" alt="当サイトについて" border="0" name="about"></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category != "contact"}-->
			<td><a href="/contact/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/side/contact_on.jpg','contact');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/side/contact.jpg','contact');"><img src="<!--{$smarty.const.URL_DIR}-->img/side/contact.jpg" width="166" height="30" alt="お問い合わせ" border="0" name="contact"></a></td>
		<!--{else}-->
			<td><a href="/contact/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/side/contact_on.jpg" width="166" height="30" alt="お問い合わせ" border="0" name="contact"></a><td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_page_category != "order"}-->
			<td><a href="/order/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/side/low_on.jpg','low');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/side/low.jpg','low');"><img src="<!--{$smarty.const.URL_DIR}-->img/side/low.jpg" width="166" height="30" alt="特定商取引に関する法律" border="0" name="low"></a></td>
		<!--{else}-->
			<td><a href="/order/index.php"><img src="<!--{$smarty.const.URL_DIR}-->img/side/low_on.jpg" width="166" height="30" alt="特定商取引に関する法律" border="0" name="low"></a></td>
		<!--{/if}-->
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲リンクここまで-->