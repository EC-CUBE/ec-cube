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
	<tr><td class=<!--{if !($arrForm.page.value == 'term' || $arrForm.page.value == '')}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php?page=term" onMouseOver="naviStyleChange('term', '#a5a5a5')" <!--{if !($arrForm.page.value == 'term' || $arrForm.page.value == '')}-->onMouseOut="naviStyleChange('term', '#636469')"<!--{/if}--> id="term"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">期間別集計</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if !($arrForm.page.value == 'products')}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php?page=products" onMouseOver="naviStyleChange('products', '#a5a5a5')" <!--{if !($arrForm.page.value == 'products')}-->onMouseOut="naviStyleChange('products', '#636469')"<!--{/if}--> id="products"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品別集計</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if !($arrForm.page.value == 'age')}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php?page=age" onMouseOver="naviStyleChange('age', '#a5a5a5')" <!--{if !($arrForm.page.value == 'age')}-->onMouseOut="naviStyleChange('age', '#636469')"<!--{/if}--> id="age"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">年代別集計</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if !($arrForm.page.value == 'job')}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php?page=job" onMouseOver="naviStyleChange('job', '#a5a5a5')" <!--{if !($arrForm.page.value == 'job')}-->onMouseOut="naviStyleChange('job', '#636469')"<!--{/if}--> id="job"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">職業別集計</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if !($arrForm.page.value == 'member')}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php?page=member" onMouseOver="naviStyleChange('member', '#a5a5a5')" <!--{if !($arrForm.page.value == 'member')}-->onMouseOut="naviStyleChange('member', '#636469')"<!--{/if}--> id="member"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">会員別集計</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>