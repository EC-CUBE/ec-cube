<?xml version="1.0" encoding="<!--{$encode}-->"?>
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<rss version="2.0">

<!--{* channel���� *}-->
<channel>
<title> <!--{$site_title|sf_mb_convert_encoding:$encode}--> </title>
<link> http://<!--{$smarty.server.HTTP_HOST}--> </link>
<description> <!--{$description|sf_mb_convert_encoding:$encode}--> </description>
<language>ja</language>
<managingEditor><!--{$email}--></managingEditor>
<webMaster><!--{$email}--></webMaster>
<copyright>(c) COPYRIGHT</copyright>
<docs>http://backend.userland.com/rss</docs>
	<!--{section name=cnt loop=$arrProduct}-->
		<!--{* ���Ĥ�item���Ǥ���Ϥ��� *}-->
		<item>
			<link> http://<!--{$smarty.server.HTTP_HOST}-->/rss/product.php</link>
			<title> <!--{ $arrProduct[cnt].news_title|sf_mb_convert_encoding:$encode }--> </title>
			<description><!--{$arrProduct[cnt].news_comment|truncate:256|sf_mb_convert_encoding:$encode}--></description>
			<pubDate><!--{"r"|sf_mktime:$arrProduct[cnt].hour:$arrProduct[cnt].minute:$arrProduct[cnt].second:$arrProduct[cnt].month:$arrProduct[cnt].day:$arrProduct[cnt].year}--></pubDate>
		</item>
	<!--{/section}-->
</channel>
</rss >
