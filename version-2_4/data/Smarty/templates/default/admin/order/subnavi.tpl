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
    <tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">受注管理</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'add'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./edit.php?mode=add" onMouseOver="naviStyleChange('add', '#a5a5a5')" <!--{if $tpl_subno !='add'}-->onMouseOut="naviStyleChange('add', '#636469')"<!--{/if}--> id="add"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">新規受注入力</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <tr><td class=<!--{if $tpl_subno != 'status'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./status.php" onMouseOver="naviStyleChange('status', '#a5a5a5')" <!--{if $tpl_subno != 'status'}-->onMouseOut="naviStyleChange('status', '#636469')"<!--{/if}--> id="status"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ステータス管理</span></a></td></tr>
    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
    <!--{if $tpl_subno == 'status'}-->
        <!--{foreach key=key item=item from=$arrORDERSTATUS}-->
            <tr><td class=<!--{if $key ne $SelectedStatus && $key ne $defaultstatus}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="#" onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<!--{$key}-->' );" onMouseOver="naviStyleChange('status_sub<!--{$key}-->', '#b7b7b7')" <!--{if $key ne $SelectedStatus && $key ne $defaultstatus}-->onMouseOut="naviStyleChange('status_sub<!--{$key}-->', '#818287')"<!--{/if}--> id="status_sub<!--{$key}-->"><span class="subnavi_text"><!--{$item}--></span></a></td></tr>
            <tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
        <!--{/foreach}-->
    <!--{/if}-->
    <!--ナビ-->
</table>