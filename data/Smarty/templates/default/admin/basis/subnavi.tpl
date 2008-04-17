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
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5', '#000000')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">SHOPマスタ</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'tradelaw'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./tradelaw.php" onMouseOver="naviStyleChange('tradelaw', '#a5a5a5')" <!--{if $tpl_subno != 'tradelaw'}-->onMouseOut="naviStyleChange('tradelaw', '#636469')"<!--{/if}--> id="tradelaw"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">特定商取引法</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'delivery'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./delivery.php" onMouseOver="naviStyleChange('delivery', '#a5a5a5')" <!--{if $tpl_subno != 'delivery'}-->onMouseOut="naviStyleChange('delivery', '#636469')"<!--{/if}--> id="delivery"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">配送設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'payment'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./payment.php" onMouseOver="naviStyleChange('payment', '#a5a5a5')" <!--{if $tpl_subno != 'payment'}-->onMouseOut="naviStyleChange('payment', '#636469')"<!--{/if}--> id="payment"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">支払方法設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'point'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./point.php" onMouseOver="naviStyleChange('point', '#a5a5a5')" <!--{if $tpl_subno != 'point'}-->onMouseOut="naviStyleChange('point', '#636469')"<!--{/if}--> id="point"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ポイント設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'mail'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./mail.php" onMouseOver="naviStyleChange('mail_id', '#a5a5a5')" <!--{if $tpl_subno != 'mail'}-->onMouseOut="naviStyleChange('mail_id', '#636469')"<!--{/if}--> id="mail_id"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">メール設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'seo'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./seo.php" onMouseOver="naviStyleChange('seo', '#a5a5a5')" <!--{if $tpl_subno != 'seo'}-->onMouseOut="naviStyleChange('seo', '#636469')"<!--{/if}--> id="seo"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">SEO管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'kiyaku'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./kiyaku.php" onMouseOver="naviStyleChange('kiyaku', '#a5a5a5')" <!--{if $tpl_subno != 'kiyaku'}-->onMouseOut="naviStyleChange('kiyaku', '#636469')"<!--{/if}--> id="kiyaku"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">会員規約設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'zip_install'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="#" onclick="win03('<!--{$smarty.const.URL_DIR}-->admin/basis/zip_install.php', 'install', '750', '350');" onMouseOver="naviStyleChange('zip_install', '#a5a5a5')" <!--{if $tpl_subno != 'zip_install'}-->onMouseOut="naviStyleChange('zip_install', '#636469')"<!--{/if}--> id="zip_install"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">郵便番号DB登録</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'control'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./control.php" onMouseOver="naviStyleChange('control', '#a5a5a5')" <!--{if $tpl_subno != 'control'}-->onMouseOut="naviStyleChange('control', '#636469')"<!--{/if}--> id="control"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">サイト管理設定</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'holiday'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./holiday.php" onMouseOver="naviStyleChange('holiday', '#a5a5a5')" <!--{if $tpl_subno != 'holiday'}-->onMouseOut="naviStyleChange('holiday', '#636469')"<!--{/if}--> id="holiday"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">定休日管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>
