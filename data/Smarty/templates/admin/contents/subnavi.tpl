<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<!--�ʥ�-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">����������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'recommend'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./recommend.php" onMouseOver="naviStyleChange('recommend', '#a5a5a5')" <!--{if $tpl_subno != 'recommend'}-->onMouseOut="naviStyleChange('recommend', '#636469')"<!--{/if}--> id="recommend"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�����������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'inquiry'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./inquiry.php" onMouseOver="naviStyleChange('inquiry', '#a5a5a5')" <!--{if $tpl_subno != 'inquiry'}-->onMouseOut="naviStyleChange('inquiry', '#636469')"<!--{/if}--> id="inquiry"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">���󥱡��ȴ���</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'csv'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./csv.php" onMouseOver="naviStyleChange('csv', '#a5a5a5')" <!--{if $tpl_subno != 'csv'}-->onMouseOut="naviStyleChange('csv', '#636469')"<!--{/if}--> id="csv"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSV��������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'csv'}-->
			<tr><td class=<!--{if $tpl_subno_csv != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.1}-->" onMouseOver="naviStyleChange('product', '#a5a5a5')" <!--{if $tpl_subno != 'product'}-->onMouseOut="naviStyleChange('product', '#636469')"<!--{/if}--> id="product"><span class="subnavi_text">���ʴ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'customer'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.2}-->" onMouseOver="naviStyleChange('customer', '#a5a5a5')" <!--{if $tpl_subno != 'customer'}-->onMouseOut="naviStyleChange('customer', '#636469')"<!--{/if}--> id="customer"><span class="subnavi_text">�ܵҴ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'order'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.3}-->" onMouseOver="naviStyleChange('order', '#a5a5a5')" <!--{if $tpl_subno != 'order'}-->onMouseOut="naviStyleChange('order', '#636469')"<!--{/if}--> id="order"><span class="subnavi_text">�������</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'csv_sql'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv_sql.php" onMouseOver="naviStyleChange('csv_sql', '#a5a5a5')" <!--{if $tpl_subno != 'csv_sql'}-->onMouseOut="naviStyleChange('csv_sql', '#636469')"<!--{/if}--> id="csv_sql"><span class="subnavi_text">���٤�����</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--�ʥ�-->
</table>
