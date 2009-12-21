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
<table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="40"><span class="white">順位</span></td>
		<td width="80"><span class="white">商品番号</span></td>
		<td width="300"><span class="white">商品名</span></td>				
		<td width="80"><span class="white">購入件数</span></td>
		<td width="80"><span class="white">点数</span></td>
		<td width="80"><span class="white">単価</span></td>
		<td width="80"><span class="white">金額</span></td>
	</tr>
	
	<!--{section name=cnt loop=$arrResults}-->
	<!--{* 色分け判定 *}-->
	<!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
	<!--{if $type == 0}-->
		<!--{* 偶数行 *}-->
		<!--{assign var=color value="#FFFFFF"}-->
	<!--{else}-->
		<!--{* 奇数行 *}-->
		<!--{assign var=color value="#D1DEFE"}-->
	<!--{/if}-->

	<tr bgcolor="<!--{$color}-->" class="fs12">
		<td align="center"><!--{*順位*}--><!--{$smarty.section.cnt.iteration}--></td>
		<td align="right"><!--{*商品番号*}--><!--{$arrResults[cnt].product_code|escape}--></td>
		<td align="left"><!--{*商品名*}--><!--{$arrResults[cnt].product_name|sfCutString:40|escape}--></td>
		<td align="right"><!--{*購入件数*}--><!--{$arrResults[cnt].order_count}-->件</td>
		<td align="right"><!--{*点数*}--><!--{$arrResults[cnt].products_count}--></td>
		<td align="right"><!--{*単価*}--><!--{$arrResults[cnt].price|number_format}-->円</td>
		<td align="right"><!--{*金額*}--><!--{$arrResults[cnt].total|number_format}-->円</td>
	</tr>
	<!--{/section}-->
	
	<tr bgcolor="#636469" align="center" class="fs12n">
		<td width="40"><span class="white">順位</span></td>
		<td width="80"><span class="white">商品番号</span></td>
		<td width="300"><span class="white">商品名</span></td>				
		<td width="80"><span class="white">購入件数</span></td>				
		<td width="80"><span class="white">点数</span></td>
		<td width="80"><span class="white">単価</span></td>
		<td width="80"><span class="white">金額</span></td>
	</tr>
</table>