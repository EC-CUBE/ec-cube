<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'layout'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('layout', '#a5a5a5')" <!--{if $tpl_subno != 'layout'}-->onMouseOut="naviStyleChange('layout', '#636469')"<!--{/if}--> id="layout"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">レイアウト設定</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'main_edit'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./main_edit.php" onMouseOver="naviStyleChange('main_edit', '#a5a5a5')" <!--{if $tpl_subno != 'main_edit'}-->onMouseOut="naviStyleChange('main_edit', '#636469')"<!--{/if}--> id="main_edit"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ページ詳細設定</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'bloc'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./bloc.php"	onMouseOver="naviStyleChange('bloc', '#a5a5a5')" <!--{if $tpl_subno != 'bloc'}-->onMouseOut="naviStyleChange('bloc', '#636469')"<!--{/if}--> id="bloc"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ブロック編集</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'header'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./header.php"	onMouseOver="naviStyleChange('header', '#a5a5a5')" <!--{if $tpl_subno != 'header'}-->onMouseOut="naviStyleChange('header', '#636469')"<!--{/if}--> id="header"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ﾍｯﾀﾞｰ/ﾌｯﾀｰ設定</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'css'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./css.php"	onMouseOver="naviStyleChange('css', '#a5a5a5')" <!--{if $tpl_subno != 'css'}-->onMouseOut="naviStyleChange('css', '#636469')"<!--{/if}--> id="css"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSS編集</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>

	<tr><td class=<!--{if $tpl_subno != 'template'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./template.php"	onMouseOver="naviStyleChange('template', '#a5a5a5')" <!--{if $tpl_subno != 'template'}-->onMouseOut="naviStyleChange('template', '#636469')"<!--{/if}--> id="template"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">テンプレート設定</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'template'}-->
		<tr><td class=<!--{if $tpl_subno_template != 'top'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.title.1}-->"	onMouseOver="naviStyleChange('top', '#b7b7b7')" <!--{if $tpl_subno_template != 'top'}-->onMouseOut="naviStyleChange('top', '#818287')"<!--{/if}--> id="top"><span class="subnavi_text">TOPページ</span></a></td></tr>
		<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_template != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.title.2}-->"	onMouseOver="naviStyleChange('product', '#b7b7b7')" <!--{if $tpl_subno_template != 'product'}-->onMouseOut="naviStyleChange('product', '#818287')"<!--{/if}--> id="product"><span class="subnavi_text">商品一覧ページ</span></a></td></tr>
		<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_template != 'detail'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.title.3}-->"	onMouseOver="naviStyleChange('detail', '#b7b7b7')" <!--{if $tpl_subno_template != 'detail'}-->onMouseOut="naviStyleChange('detail', '#818287')"<!--{/if}--> id="detail"><span class="subnavi_text">商品詳細ページ</span></a></td></tr>
		<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_template != 'mypage'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.title.4}-->"	onMouseOver="naviStyleChange('mypage', '#b7b7b7')" <!--{if $tpl_subno_template != 'mypage'}-->onMouseOut="naviStyleChange('mypage', '#818287')"<!--{/if}--> id="mypage"><span class="subnavi_text">MYページ</span></a></td></tr>
		<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_template != 'upload'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./upload.php" onMouseOver="naviStyleChange('upload', '#b7b7b7')" <!--{if $tpl_subno_template != 'upload'}-->onMouseOut="naviStyleChange('upload', '#818287')"<!--{/if}--> id="upload"><span class="subnavi_text">アップロード</span></a></td></tr>
		<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--ナビ-->
</table>