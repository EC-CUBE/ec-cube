<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
    <!--ナビ-->
    <tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ｱﾌﾟﾘｹｰｼｮﾝ管理</span></a></td></tr>
    <tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'settings'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./settings.php" onMouseOver="naviStyleChange('settings', '#a5a5a5')" <!--{if $tpl_subno != 'settings'}-->onMouseOut="naviStyleChange('settings', '#636469')"<!--{/if}--> id="settings"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ｱﾌﾟﾘｹｰｼｮﾝ設定</span></a></td></tr>
    <tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'log'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./log.php" onMouseOver="naviStyleChange('log', '#a5a5a5')" <!--{if $tpl_subno != 'log'}-->onMouseOut="naviStyleChange('log', '#636469')"<!--{/if}--> id="log"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ログ管理</span></a></td></tr>
    <tr><td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <!--ナビ-->
</table>
