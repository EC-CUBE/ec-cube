<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<!--�ʥ�-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'status'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./status.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">���ơ���������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'status'}-->
		<!--{foreach key=key item=item from=$arrORDERSTATUS}-->
			<tr><td class=<!--{if $key ne $SelectedStatus && $key ne $defaultstatus}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="#" onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<!--{$key}-->');"><span class="subnavi_text"><!--{$item}--></span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<!--{/foreach}-->
	<!--{/if}-->
	<!--�ʥ�-->
</table>