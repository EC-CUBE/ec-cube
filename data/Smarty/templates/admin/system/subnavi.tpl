<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">メンバー管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'update'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./update.php" onMouseOver="naviStyleChange('update', '#a5a5a5')" <!--{if $tpl_subno != 'update'}-->onMouseOut="naviStyleChange('update', '#636469')"<!--{/if}--> id="update"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">アップデート管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'module'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./module.php" onMouseOver="naviStyleChange('module', '#a5a5a5')" <!--{if $tpl_subno != 'module'}-->onMouseOut="naviStyleChange('module', '#636469')"<!--{/if}--> id="module"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">モジュール管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'bkup'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./bkup.php" onMouseOver="naviStyleChange('bkup', '#a5a5a5')" <!--{if $tpl_subno != 'bkup'}-->onMouseOut="naviStyleChange('bkup', '#636469')"<!--{/if}--> id="bkup"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">バックアップ管理</span></a></td></tr>
	<tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>