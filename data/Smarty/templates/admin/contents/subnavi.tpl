<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<!--�ʥ�-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">����������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'recommend'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./recommend.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�����������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'inquiry'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./inquiry.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">���󥱡��ȴ���</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'csv'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./csv.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSV��������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'csv'}-->
			<tr><td class=<!--{if $tpl_subno_csv != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.1}-->"><span class="subnavi_text">���ʴ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'customer'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.2}-->"><span class="subnavi_text">�ܵҴ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'order'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.3}-->"><span class="subnavi_text">�������</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'csv_sql'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv_sql.php"><span class="subnavi_text">���٤�����</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--�ʥ�-->
</table>
