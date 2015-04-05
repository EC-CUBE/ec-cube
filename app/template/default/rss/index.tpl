<?xml version="1.0" encoding="<!--{$encode}-->"?>
<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
    <channel>
        <title><!--{$site_title|sfMbConvertEncoding:$encode|h}--></title>
        <link><!--{$smarty.const.HTTP_URL}--></link>
        <description><!--{$description|sfMbConvertEncoding:$encode|h}--></description>
        <language>ja</language>
        <managingEditor><!--{$email|h}--></managingEditor>
        <webMaster><!--{$email|h}--></webMaster>
        <generator>web shoppings v1.0</generator>
        <copyright>(c) COPYRIGHT</copyright>
        <category>WEB SHOPPING</category>
        <docs>http://backend.userland.com/rss</docs>

        <!--{section name=cnt loop=$arrNews}-->
            <item>
                <link><!--{$arrNews[cnt].news_url|h}--></link>
                <title><!--{$arrNews[cnt].news_title|sfMbConvertEncoding:$encode|h}--></title>
                <description><!--{$arrNews[cnt].news_comment|truncate:256|sfMbConvertEncoding:$encode|h}--></description>
                <pubDate><!--{$arrNews[cnt].news_date|h}--></pubDate>
            </item>
        <!--{/section}-->

    </channel>
</rss>
