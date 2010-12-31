<?xml version="1.0" encoding="<!--{$encode}-->"?>
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
<rss version="2.0">

<!--{* channel要素 *}-->
<channel>
<title><!--{$title|sf_mb_convert_encoding:$encode}--></title>
<shop_name><!--{$arrSiteInfo.shop_name|sf_mb_convert_encoding:$encode}--></shop_name>
<shop_kana><!--{$arrSiteInfo.shop_kana|sf_mb_convert_encoding:$encode}--></shop_kana>
<site_url><!--{$smarty.const.HTTP_URL}--></site_url>
<description><!--{section name=cnt loop=$arrProduct}--><!--{$arrProduct[cnt].product_id|sf_mb_convert_encoding:$encode}-->,<!--{/section}--></description>
<language>ja</language>
<docs>http://backend.userland.com/rss</docs>
<!--{section name=cnt loop=$arrProduct}-->
	<!--{* １つのitem要素を出力する *}-->
	<item>
		<link><!--{$smarty.const.HTTP_URL}-->rss/product.php?product_id=<!--{$arrProduct[cnt].product_id}--></link>
		<!--{foreach key=key item=item from=$arrProductKeys}-->
			<<!--{$item}-->><!--{$arrProduct[cnt][$item]|h|sf_mb_convert_encoding:$encode}--></<!--{$item}-->>
		<!--{/foreach}-->
	</item>
<!--{/section}-->
</channel>
</rss >
