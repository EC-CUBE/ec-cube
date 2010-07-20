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
<!-- ★ ページャここから ★-->
<table border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td>
		<table border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/contents/arrow_left_top.jpg" width="36" height="2" alt=""></td>
				<td background="<!--{$TPL_DIR}-->img/contents/number_top_bg.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="2" alt=""></td>
				<td><img src="<!--{$TPL_DIR}-->img/contents/arrow_right_top.jpg" width="37" height="2" alt=""></td>
			</tr>
			<tr>
				<td background="<!--{$TPL_DIR}-->img/contents/arrow_left_bg.jpg"><a href=<!--{$smarty.server.PHP_SELF|escape}--> onclick="fnNaviSearchPage(<!--{$arrPagenavi.before}-->, '<!--{$arrPagenavi.mode}-->'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/contents/arrow_left_on.jpg','arrow_left');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/contents/arrow_left.jpg','arrow_left');"><img src="<!--{$TPL_DIR}-->img/contents/arrow_left.jpg" width="36" height="17" alt="" border="0" name="arrow_left" id="arrow_left"></a></td>
				<td bgcolor="#393a48">
				<table border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<!--{foreach from=$arrPagenavi.arrPageno key="key" item="item"}-->
						<td><img src="<!--{$TPL_DIR}-->img/contents/number_line.jpg" width="2" height="17" alt="" ></td>
						<td class=<!--{if $arrPagenavi.now_page == $item}-->"number-on"<!--{else}-->"number"<!--{/if}-->><a href=<!--{$smarty.server.PHP_SELF|escape}--> onclick="fnNaviSearchPage(<!--{$item}-->, '<!--{$arrPagenavi.mode}-->'); return false;"><!--{$item}--></a></td>
						<td><img src="<!--{$TPL_DIR}-->img/contents/number_line.jpg" width="2" height="17" alt=""></td>
						<!--{/foreach}-->
					</tr>
				</table>
				</td>
				<td background="<!--{$TPL_DIR}-->img/contents/arrow_right_bg.jpg"><a href=<!--{$smarty.server.PHP_SELF|escape}--> onclick="fnNaviSearchPage(<!--{$arrPagenavi.next}-->, '<!--{$arrPagenavi.mode}-->'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/contents/arrow_right_on.jpg','arrow_right');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/contents/arrow_right.jpg','arrow_right');"><img src="<!--{$TPL_DIR}-->img/contents/arrow_right.jpg" width="37" height="17" alt="" border="0" name="arrow_right" id="arrow_right"></a></td>
			</tr>
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/contents/arrow_left_bottom.jpg" width="36" height="3" alt=""></td>
				<td background="<!--{$TPL_DIR}-->img/contents/number_bottom_bg.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="3" alt=""></td>
				<td><img src="<!--{$TPL_DIR}-->img/contents/arrow_right_bottom.jpg" width="37" height="3" alt=""></td>
			</tr>
		</table>
		</td>
		<td><img src="<!--{$TPL_DIR}-->img/contents/search_right.gif" width="19" height="22" alt=""></td>
	</tr>
</table>
<!-- ★ ページャここまで ★-->
