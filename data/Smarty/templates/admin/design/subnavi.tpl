<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--�ʥ�-->
	<tr><td class=<!--{if $tpl_subno != 'layout'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('layout', '#a5a5a5')" <!--{if $tpl_subno != 'layout'}-->onMouseOut="naviStyleChange('layout', '#636469')"<!--{/if}--> id="layout"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�쥤����������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'main_edit'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./main_edit.php" onMouseOver="naviStyleChange('main_edit', '#a5a5a5')" <!--{if $tpl_subno != 'main_edit'}-->onMouseOut="naviStyleChange('main_edit', '#636469')"<!--{/if}--> id="main_edit"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�ڡ����ܺ�����</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'bloc'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./bloc.php"	onMouseOver="naviStyleChange('bloc', '#a5a5a5')" <!--{if $tpl_subno != 'bloc'}-->onMouseOut="naviStyleChange('bloc', '#636469')"<!--{/if}--> id="bloc"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�֥�å��Խ�</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'header'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./header.php"	onMouseOver="naviStyleChange('header', '#a5a5a5')" <!--{if $tpl_subno != 'header'}-->onMouseOut="naviStyleChange('header', '#636469')"<!--{/if}--> id="header"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�͎����ގ�/�̎���������</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'css'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./css.php"	onMouseOver="naviStyleChange('css', '#a5a5a5')" <!--{if $tpl_subno != 'css'}-->onMouseOut="naviStyleChange('css', '#636469')"<!--{/if}--> id="css"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSS�Խ�</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>

	<tr><td class=<!--{if $tpl_subno != 'template'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./template.php"	onMouseOver="naviStyleChange('template', '#a5a5a5')" <!--{if $tpl_subno != 'template'}-->onMouseOut="naviStyleChange('template', '#636469')"<!--{/if}--> id="template"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">�ƥ�ץ졼��</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'template'}-->
			<tr><td class=<!--{if $tpl_subno_template != 'top'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.1}-->"	onMouseOver="naviStyleChange('top', '#a5a5a5')" <!--{if $tpl_subno != 'top'}-->onMouseOut="naviStyleChange('top', '#818287')"<!--{/if}--> id="top"><span class="subnavi_text">TOP�ڡ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.2}-->"	onMouseOver="naviStyleChange('product', '#a5a5a5')" <!--{if $tpl_subno != 'product'}-->onMouseOut="naviStyleChange('product', '#818287')"<!--{/if}--> id="product"><span class="subnavi_text">���ʰ����ڡ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'detail'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.3}-->"	onMouseOver="naviStyleChange('detail', '#a5a5a5')" <!--{if $tpl_subno != 'detail'}-->onMouseOut="naviStyleChange('detail', '#818287')"<!--{/if}--> id="detail"><span class="subnavi_text">���ʾܺ٥ڡ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'mypage'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.4}-->"	onMouseOver="naviStyleChange('mypage', '#a5a5a5')" <!--{if $tpl_subno != 'mypage'}-->onMouseOut="naviStyleChange('mypage', '#818287')"<!--{/if}--> id="mypage"><span class="subnavi_text">MY�ڡ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>



			<tr><td class=<!--{if $tpl_subno_csv != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.1}-->" onMouseOver="naviStyleChange('product_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'product'}-->onMouseOut="naviStyleChange('product_sub', '#818287')"<!--{/if}--> id="product_sub"><span class="subnavi_text">���ʴ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'customer'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.2}-->" onMouseOver="naviStyleChange('customer_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'customer'}-->onMouseOut="naviStyleChange('customer_sub', '#818287')"<!--{/if}--> id="customer_sub"><span class="subnavi_text">�ܵҴ���</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_csv != 'order'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.3}-->" onMouseOver="naviStyleChange('order_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'order'}-->onMouseOut="naviStyleChange('order_sub', '#818287')"<!--{/if}--> id="order_sub"><span class="subnavi_text">�������</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'mypage'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv_sql.php" onMouseOver="naviStyleChange('csv_sql', '#b7b7b7')" <!--{if $tpl_subno_csv != 'csv_sql'}-->onMouseOut="naviStyleChange('csv_sql', '#818287')"<!--{/if}--> id="csv_sql"><span class="subnavi_text">���٤�����</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			
	<!--{/if}-->
	
	<!--�ʥ�-->
</table>