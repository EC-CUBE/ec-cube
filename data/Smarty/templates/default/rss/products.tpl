<?xml version="1.0" encoding="<!--{$encode}-->"?>
<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title><!--{$arrSiteInfo.shop_name|sfMbConvertEncoding:$encode}--> : <!--{$title|sfMbConvertEncoding:$encode}--></title>
<link><!--{$smarty.const.HTTP_URL}--></link>
<description><!--{$arrSiteInfo.message|sfMbConvertEncoding:$encode}--></description>
<language>ja</language>
<!--{foreach from=$arrProducts item=arrProduct}-->
    <item>
        <title><!--{$arrProduct.name|h|sfMbConvertEncoding:$encode}--></title>
        <link><!--{$smarty.const.HTTP_URL}-->products/detail.php?product_id=<!--{$arrProduct.product_id}--></link>
        <description><![CDATA[
            <div class="hproduct">
                <a href="<!--{$smarty.const.HTTP_URL}-->products/detail.php?product_id=<!--{$arrProduct.product_id}-->" rel="product">
                    <img src="<!--{$arrProduct.main_list_image}-->" alt="<!--{$arrProduct.product_name|h|sfMbConvertEncoding:$encode}-->" class="product-thumb" />
                </a>
                <div class="product-title"><a href="<!--{$smarty.const.HTTP_URL}-->products/detail.php?product_id=<!--{$arrProduct.product_id}-->" rel="product"><!--{$arrProduct.product_name|h|sfMbConvertEncoding:$encode}--></a></div>
                商品コード：<!--{$arrProduct.product_code_max|h|sfMbConvertEncoding:$encode}-->
                <div><!--{$smarty.const.SALE_PRICE_TITLE}-->：
                    <span class="price">
                        <!--{if $arrProduct.price02_min_inctax == $arrProduct.price02_max_inctax}-->
                            <!--{$arrProduct.price02_min_inctax|number_format}-->
                        <!--{else}-->
                            <!--{$arrProduct.price02_min_inctax|number_format}-->〜<!--{$arrProduct.price02_max_inctax|number_format}-->
                        <!--{/if}-->円</span>
                </div>
                <div class="description">
                    <!--{$arrProduct.main_list_comment|h|sfMbConvertEncoding:$encode|nl2br}-->
                </div>
            </div>
        ]]></description>
        <pubDate><!--{$arrProduct.update_date|date_format:"%Y-%m-%dT%T+09:00"}--></pubDate>
    </item>
<!--{/foreach}-->
</channel>
</rss >
