<!--{*
 * Copyright ��� 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<div id="pan">
<span class="fs12n">

<a href="<!--{$smarty.const.URL_SITE_TOP}-->">�ȥåץڡ���</a>

<!--{section name=cnt loop=$arrCatName}-->
	<!--{if $smarty.section.cnt.last}-->
	�� <span class="redst"><a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html" class="pan"><!--{$arrCatName[cnt]}--></a></span>
	<!--{elseif $smarty.section.cnt.first}-->
	�� <a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html"><!--{$arrCatName[cnt]}--></a>
	<!--{else}-->
	�� <a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html"><!--{$arrCatName[cnt]}--></a>
	<!--{/if}-->
<!--{sectionelse}-->
	�� <span class="redst">�������</span>
<!--{/section}-->

</span>
</div>
