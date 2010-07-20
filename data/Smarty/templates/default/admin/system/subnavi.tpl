<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">メンバー管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'bkup'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./bkup.php" onMouseOver="naviStyleChange('bkup', '#a5a5a5')" <!--{if $tpl_subno != 'bkup'}-->onMouseOut="naviStyleChange('bkup', '#636469')"<!--{/if}--> id="bkup"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">バックアップ管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'parameter'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./parameter.php" onMouseOver="naviStyleChange('parameter', '#a5a5a5')" <!--{if $tpl_subno != 'parameter'}-->onMouseOut="naviStyleChange('parameter', '#636469')"<!--{/if}--> id="parameter"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">パラメータ設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'masterdata'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./masterdata.php" onMouseOver="naviStyleChange('masterdata', '#a5a5a5')" <!--{if $tpl_subno != 'masterdata'}-->onMouseOut="naviStyleChange('masterdata', '#636469')"<!--{/if}--> id="masterdata"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">マスタデータ管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'system'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./system.php" onMouseOver="naviStyleChange('system', '#a5a5a5')" <!--{if $tpl_subno != 'system'}-->onMouseOut="naviStyleChange('system', '#636469')"<!--{/if}--> id="system"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">システム情報</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>
