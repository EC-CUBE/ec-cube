使用されている？
<div id="pan">
<span class="fs12n">

<a href="<!--{$smarty.const.URL_SITE_TOP}-->">トップページ</a>

<!--{section name=cnt loop=$arrCatName}-->
	<!--{if $smarty.section.cnt.last}-->
	＞ <span class="redst"><a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html" class="pan"><!--{$arrCatName[cnt]}--></a></span>
	<!--{elseif $smarty.section.cnt.first}-->
	＞ <a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html"><!--{$arrCatName[cnt]}--></a>
	<!--{else}-->
	＞ <a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html"><!--{$arrCatName[cnt]}--></a>
	<!--{/if}-->
<!--{sectionelse}-->
	＞ <span class="redst">検索結果</span>
<!--{/section}-->

</span>
</div>
