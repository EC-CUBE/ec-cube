<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'layout'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('layout', '#a5a5a5')" <!--{if $tpl_subno != 'layout'}-->onMouseOut="naviStyleChange('layout', '#636469')"<!--{/if}--> id="layout"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">レイアウト設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'main_edit'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./main_edit.php" onMouseOver="naviStyleChange('main_edit', '#a5a5a5')" <!--{if $tpl_subno != 'main_edit'}-->onMouseOut="naviStyleChange('main_edit', '#636469')"<!--{/if}--> id="main_edit"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ページ詳細設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'bloc'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./bloc.php"	onMouseOver="naviStyleChange('bloc', '#a5a5a5')" <!--{if $tpl_subno != 'bloc'}-->onMouseOut="naviStyleChange('bloc', '#636469')"<!--{/if}--> id="bloc"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ブロック編集</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'header'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./header.php"	onMouseOver="naviStyleChange('header', '#a5a5a5')" <!--{if $tpl_subno != 'header'}-->onMouseOut="naviStyleChange('header', '#636469')"<!--{/if}--> id="header"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ﾍｯﾀﾞｰ/ﾌｯﾀｰ設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'css'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./css.php"	onMouseOver="naviStyleChange('css', '#a5a5a5')" <!--{if $tpl_subno != 'css'}-->onMouseOut="naviStyleChange('css', '#636469')"<!--{/if}--> id="css"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">CSS編集</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>

	<tr><td class=<!--{if $tpl_subno != 'template'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./template.php"	onMouseOver="naviStyleChange('template', '#a5a5a5')" <!--{if $tpl_subno != 'template'}-->onMouseOut="naviStyleChange('template', '#636469')"<!--{/if}--> id="template"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">テンプレート</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--{if $tpl_subno == 'template'}-->
			<tr><td class=<!--{if $tpl_subno_template != 'top'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.1}-->"	onMouseOver="naviStyleChange('top', '#a5a5a5')" <!--{if $tpl_subno != 'top'}-->onMouseOut="naviStyleChange('top', '#818287')"<!--{/if}--> id="top"><span class="subnavi_text">TOPページ</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.2}-->"	onMouseOver="naviStyleChange('product', '#a5a5a5')" <!--{if $tpl_subno != 'product'}-->onMouseOut="naviStyleChange('product', '#818287')"<!--{/if}--> id="product"><span class="subnavi_text">商品一覧ページ</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'detail'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.3}-->"	onMouseOver="naviStyleChange('detail', '#a5a5a5')" <!--{if $tpl_subno != 'detail'}-->onMouseOut="naviStyleChange('product', '#818287')"<!--{/if}--> id="detail"><span class="subnavi_text">商品詳細ページ</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
			<tr><td class=<!--{if $tpl_subno_template != 'mypage'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./template.php?tpl_subno_template=<!--{$arrSubnavi.4}-->"	onMouseOver="naviStyleChange('mypage', '#a5a5a5')" <!--{if $tpl_subno != 'mypage'}-->onMouseOut="naviStyleChange('mypage', '#818287')"<!--{/if}--> id="mypage"><span class="subnavi_text">MYページ</span></a></td></tr>
			<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--ナビ-->
</table>