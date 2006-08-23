<?xml version="1.0" encoding="<!--{$encode}-->"?>
<rss version="2.0">

<!--{* channel要素 *}-->
<channel>
<title> <!--{$site_title|sf_mb_convert_encoding:$encode}--> </title>
<link> http://<!--{$smarty.server.HTTP_HOST}--> </link>
<description> <!--{$description|sf_mb_convert_encoding:$encode}--> </description>
<language>ja</language>
<managingEditor><!--{$email}--></managingEditor>
<webMaster><!--{$email}--></webMaster>
<generator>web shoppings v1.0</generator>
<copyright>(c) COPYRIGHT </copyright>
<category>WEB SHOPPING</category>
<docs>http://backend.userland.com/rss</docs>

	<!--{section name=cnt loop=$arrNews}-->
		
		<!--{* １つのitem要素を出力する *}-->
		<item>
			<!--{if $arrNews[cnt].news_url == '' }-->
				<link> http://<!--{$smarty.server.HTTP_HOST}--> </link>
			<!--{else}-->
				<link> <!--{$arrNews[cnt].news_url|escape}--></link>
			<!--{/if}-->
			<title> <!--{ $arrNews[cnt].news_title|sf_mb_convert_encoding:$encode }--> </title>
			<description><!--{$arrNews[cnt].news_comment|truncate:256|sf_mb_convert_encoding:$encode}--></description>
			<pubDate><!--{"r"|sf_mktime:$arrNews[cnt].hour:$arrNews[cnt].minute:$arrNews[cnt].second:$arrNews[cnt].month:$arrNews[cnt].day:$arrNews[cnt].year}--></pubDate>
		</item>
	<!--{/section}-->

</channel>
</rss >
