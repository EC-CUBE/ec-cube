<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
    <!--ナビ-->
    <tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">購入商品一覧</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'settings'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./settings.php" onMouseOver="naviStyleChange('settings', '#a5a5a5')" <!--{if $tpl_subno != 'settings'}-->onMouseOut="naviStyleChange('settings', '#636469')"<!--{/if}--> id="settings"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">認証キー設定</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'log'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./log.php" onMouseOver="naviStyleChange('log', '#a5a5a5')" <!--{if $tpl_subno != 'log'}-->onMouseOut="naviStyleChange('log', '#636469')"<!--{/if}--> id="log"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ログ管理</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <!--ナビ-->
</table>
