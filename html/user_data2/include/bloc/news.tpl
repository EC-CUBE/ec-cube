<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<table width="400" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/top/news.jpg" width="400" height="29" alt="�������"></td>
	</tr>
	<tr>
		<td colspan="3"><span class="fs10">������ ��������<a href="<!--{$smarty.const.URL_DIR}-->rss/index.php" target="_blank">RSS</a>���ۿ����Ƥ��ޤ��������� </span></td>
	</tr>
	<tr>
		<td height="10"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="16" height="1" alt=""></td>
		<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="90" height="1" alt=""></td>
		<td><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="294" height="1" alt=""></td>
	</tr>

	<!--{section name=data loop=$arrNews}-->
	<tr valign="top">
		<td><img src="<!--{$smarty.const.URL_DIR}-->img/top/news_icon.gif" width="16" height="16" alt=""></td>
		<td class="fs10"><!--{$arrNews[data].news_date_disp|escape}--></td>
		<td class="fs10"><!--{if $arrNews[data].news_url}--><a href="<!--{$arrNews[data].news_url}-->" <!--{if $arrNews[data].link_method eq "2"}-->target="_blank"<!--{/if}--> ><!--{/if}--><!--{$arrNews[data].news_title|escape|nl2br}--><!--{if $arrNews[data].news_url}--></a><!--{/if}--><br/><!--{$arrNews[data].news_comment|escape|nl2br}--></td>
	</tr>
	<!--{if !$smarty.section.data.last}-->
	<tr><td colspan="3" height="20"><img src="<!--{$smarty.const.URL_DIR}-->img/common/line_400.gif" width="400" height="1" alt=""></td></tr>
	<!--{/if}-->
	<!--{/section}-->

	<tr><td height="35" colspan="3"></td></tr>
</table>