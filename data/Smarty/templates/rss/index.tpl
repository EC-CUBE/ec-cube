<?xml version="1.0" encoding="<!--{$encode}-->"?>
<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<rss version="2.0">

<!--{* channel���� *}-->
<channel>
<title> <!--{$site_title|sf_mb_convert_encoding:$encode}--> </title>
<link> <!--{$smarty.const.SITE_URL}--> </link>
<description> <!--{$description|sf_mb_convert_encoding:$encode}--> </description>
<language>ja</language>
<managingEditor><!--{$email}--></managingEditor>
<webMaster><!--{$email}--></webMaster>
<generator>web shoppings v1.0</generator>
<copyright>(c) COPYRIGHT</copyright>
<category>WEB SHOPPING</category>
<docs>http://backend.userland.com/rss</docs>

	<!--{section name=cnt loop=$arrNews}-->
		
		<!--{* ���Ĥ�item���Ǥ���Ϥ��� *}-->
		<item>
			<!--{if $arrNews[cnt].news_url == '' }-->
				<link> <!--{$smarty.const.SITE_URL}--> </link>
			<!--{else}-->
				<link> <!--{$arrNews[cnt].news_url|escape}--></link>
			<!--{/if}-->
			<title> <!--{ $arrNews[cnt].news_title|sf_mb_convert_encoding:$encode }--> </title>
			<description><!--{$arrNews[cnt].news_comment|truncate:256|sf_mb_convert_encoding:$encode}--></description>
			<!--{* <pubDate><!--{"r"|sf_mktime:$arrNews[cnt].hour:$arrNews[cnt].minute:$arrNews[cnt].second:$arrNews[cnt].month:$arrNews[cnt].day:$arrNews[cnt].year}--></pubDate> *}-->
			<pubDate><!--{$timestamp}--></pubDate>
		</item>
	<!--{/section}-->

</channel>
</rss >
