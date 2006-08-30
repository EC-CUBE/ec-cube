<div id="pan">
<span class="fs12n">

<a href="<!--{$smarty.const.SITE_URL}-->">¥È¥Ã¥×¥Ú¡¼¥¸</a>

<!--{section name=cnt loop=$arrCatName}-->
	<!--{if $smarty.section.cnt.last}-->
	¡ä <span class="redst"><a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html" class="pan"><!--{$arrCatName[cnt]}--></a></span>
	<!--{elseif $smarty.section.cnt.first}-->
	¡ä <a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html"><!--{$arrCatName[cnt]}--></a>
	<!--{else}-->
	¡ä <a href="<!--{$smarty.const.LIST_C_HTML}--><!--{$arrCatID[cnt]}-->.html"><!--{$arrCatName[cnt]}--></a>
	<!--{/if}-->
<!--{sectionelse}-->
	¡ä <span class="redst">¸¡º÷·ë²Ì</span>
<!--{/section}-->

</span>
</div>
