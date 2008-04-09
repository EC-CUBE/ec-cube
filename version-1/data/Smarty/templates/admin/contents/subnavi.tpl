<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">新着情報管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'recommend'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./recommend.php" onMouseOver="naviStyleChange('recommend', '#a5a5a5')" <!--{if $tpl_subno != 'recommend'}-->onMouseOut="naviStyleChange('recommend', '#636469')"<!--{/if}--> id="recommend"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">オススメ管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'inquiry'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./inquiry.php" onMouseOver="naviStyleChange('inquiry', '#a5a5a5')" <!--{if $tpl_subno != 'inquiry'}-->onMouseOut="naviStyleChange('inquiry', '#636469')"<!--{/if}--> id="inquiry"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">アンケート管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'campaign'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./campaign.php" onMouseOver="naviStyleChange('campaign', '#a5a5a5')" <!--{if $tpl_subno != 'campaign'}-->onMouseOut="naviStyleChange('campaign', '#636469')"<!--{/if}--> id="campaign"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">キャンペーン管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'file'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./file_manager.php" onMouseOver="naviStyleChange('file', '#a5a5a5')" <!--{if $tpl_subno != 'file'}-->onMouseOut="naviStyleChange('file', '#636469')"<!--{/if}--> id="file"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ファイル管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'csv'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./csv.php" onMouseOver="naviStyleChange('csv', '#a5a5a5')" <!--{if $tpl_subno != 'csv'}-->onMouseOut="naviStyleChange('csv', '#636469')"<!--{/if}--> id="csv"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSV出力項目設定</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'csv'}-->
		<!--{foreach key=key item=item from=$arrSubnavi}-->
			<tr><td class=<!--{if $tpl_subno_csv != $item}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$item}-->" onMouseOver="naviStyleChange('<!--{$item}-->_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != $item}-->onMouseOut="naviStyleChange('<!--{$item}-->_sub', '#818287')"<!--{/if}--> id="<!--{$item}-->_sub"><span class="subnavi_text"><!--{$arrSubnaviName[$key]}--></span></a></td></tr>
			
			<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<!--{/foreach}-->
		<tr><td class=<!--{if $tpl_subno_csv != 'csv_sql'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv_sql.php" onMouseOver="naviStyleChange('csv_sql', '#b7b7b7')" <!--{if $tpl_subno_csv != 'csv_sql'}-->onMouseOut="naviStyleChange('csv_sql', '#818287')"<!--{/if}--> id="csv_sql"><span class="subnavi_text">高度な設定</span></a></td></tr>
		<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--ナビ-->
</table>
