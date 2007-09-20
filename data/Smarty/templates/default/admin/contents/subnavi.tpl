<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">新着情報管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'recommend'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./recommend.php" onMouseOver="naviStyleChange('recommend', '#a5a5a5')" <!--{if $tpl_subno != 'recommend'}-->onMouseOut="naviStyleChange('recommend', '#636469')"<!--{/if}--> id="recommend"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">オススメ管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'inquiry'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./inquiry.php" onMouseOver="naviStyleChange('inquiry', '#a5a5a5')" <!--{if $tpl_subno != 'inquiry'}-->onMouseOut="naviStyleChange('inquiry', '#636469')"<!--{/if}--> id="inquiry"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">アンケート管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'campaign'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./campaign.php" onMouseOver="naviStyleChange('campaign', '#a5a5a5')" <!--{if $tpl_subno != 'campaign'}-->onMouseOut="naviStyleChange('campaign', '#636469')"<!--{/if}--> id="campaign"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">キャンペーン管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'file'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./file_manager.php" onMouseOver="naviStyleChange('file', '#a5a5a5')" <!--{if $tpl_subno != 'file'}-->onMouseOut="naviStyleChange('file', '#636469')"<!--{/if}--> id="file"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ファイル管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'csv'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./csv.php" onMouseOver="naviStyleChange('csv', '#a5a5a5')" <!--{if $tpl_subno != 'csv'}-->onMouseOut="naviStyleChange('csv', '#636469')"<!--{/if}--> id="csv"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSV出力項目設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'csv'}-->
		<tr><td class=<!--{if $tpl_subno_csv != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.1}-->" onMouseOver="naviStyleChange('product_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'product'}-->onMouseOut="naviStyleChange('product_sub', '#818287')"<!--{/if}--> id="product_sub"><span class="subnavi_text">商品管理</span></a></td></tr>
		<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'customer'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.2}-->" onMouseOver="naviStyleChange('customer_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'customer'}-->onMouseOut="naviStyleChange('customer_sub', '#818287')"<!--{/if}--> id="customer_sub"><span class="subnavi_text">顧客管理</span></a></td></tr>
		<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'order'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.3}-->" onMouseOver="naviStyleChange('order_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'order'}-->onMouseOut="naviStyleChange('order_sub', '#818287')"<!--{/if}--> id="order_sub"><span class="subnavi_text">受注管理</span></a></td></tr>
		<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'campaign'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.4}-->" onMouseOver="naviStyleChange('campaign_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'campaign'}-->onMouseOut="naviStyleChange('campaign_sub', '#818287')"<!--{/if}--> id="campaign_sub"><span class="subnavi_text">キャンペーン</span></a></td></tr>
		<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'csv_sql'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv_sql.php" onMouseOver="naviStyleChange('csv_sql', '#b7b7b7')" <!--{if $tpl_subno_csv != 'csv_sql'}-->onMouseOut="naviStyleChange('csv_sql', '#818287')"<!--{/if}--> id="csv_sql"><span class="subnavi_text">高度な設定</span></a></td></tr>
		<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--ナビ-->
</table>
