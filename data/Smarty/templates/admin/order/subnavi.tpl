<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<!--�ʥ�-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'status'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./status.php" onMouseOver="naviStyleChange('status', '#a5a5a5')" <!--{if $tpl_subno != 'status'}-->onMouseOut="naviStyleChange('status', '#636469')"<!--{/if}--> id="status"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">���ơ���������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'status'}-->
		<!--{foreach key=key item=item from=$arrORDERSTATUS}-->
			<tr><td class=<!--{if $key ne $SelectedStatus && $key ne $defaultstatus}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="#" onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<!--{$key}-->' );" onMouseOver="naviStyleChange('sub_status<!--{$key}-->', '#b7b7b7')" <!--{if $key ne $SelectedStatus && $key ne $defaultstatus}-->onMouseOut="naviStyleChange('sub_status<!--{$key}-->', '#818287')"<!--{/if}--> id="sub_status<!--{$key}-->"><span class="subnavi_text"><!--{$item}--></span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<!--{/foreach}-->
	<!--{/if}-->
	<!--�ʥ�-->
</table>