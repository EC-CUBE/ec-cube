<!--{*
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
 *}-->
 <!--検索該当0の時-->
<table width="570" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr>
		<!--{if $smarty.get.mode == "search"}-->
		<td height="150" align="center" class="fs12">該当件数<strong><span class="red">0件</span></strong>です。<br />
		他の検索キーワードより再度検索をしてください。</td>
		<!--{else}-->
		<td height="150" align="center" class="fs12">現在、商品はございません。</td>
		<!--{/if}-->
	</tr>
</table>
<!--{*
<table width="570" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3" align="center"><img src="<!--{$TPL_DIR}-->img/right_search/subtitle.gif" width="520" height="30" alt="ただいま開催中の特集はこちら" /></td>
	</tr>
	<tr><td height="15"></td></tr>
	<tr>
		<td align="right"><a href="../campaign/nabe.php"><img src="<!--{$TPL_DIR}-->img/right_search/b_01.jpg" width="218" height="117" alt="お鍋特集" border="0" /></a></td>
		<td><img src="<!--{$TPL_DIR}-->img/_.gif" width="20" height="1" alt="" /></td>
		<td><a href="../campaign/christmas.php"><img src="<!--{$TPL_DIR}-->img/right_search/b_02.jpg" width="218" height="117" alt="クリスマス特集" border="0" /></a></td>
	</tr>
</table>
*}-->
<!--検索該当0の時-->
