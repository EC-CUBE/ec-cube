<?xml version="1.0" encoding="<!--{$encode}-->"?>
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<rss version="2.0">

<!--{* channel要素 *}-->
<channel>
<site_name> <!--{$arrSiteInfo.site_name|sf_mb_convert_encoding:$encode}--> </site_name>
<link> <!--{$smarty.const.SITE_URL}--> </link>
<description> <!--{$description|sf_mb_convert_encoding:$encode}--> </description>
<language>ja</language>
<managingEditor><!--{$email}--></managingEditor>
<webMaster><!--{$email}--></webMaster>
<copyright>(c) COPYRIGHT</copyright>
<docs>http://backend.userland.com/rss</docs>
	<!--{section name=cnt loop=$arrProduct}-->
		<!--{* １つのitem要素を出力する *}-->
		<item>
			<link> <!--{$smarty.const.SITE_URL}-->rss/product.php?product_id=<!--{$arrProduct[cnt].product_id}--></link>
			<product_id><!--{$arrProduct[cnt].product_id|sf_mb_convert_encoding:$encode}--></product_id>
			<product_name> <!--{ $arrProduct[cnt].product_name|sf_mb_convert_encoding:$encode }--> </product_name>
			<description><!--{$arrProduct[cnt].product_id|truncate:256|sf_mb_convert_encoding:$encode}--></description>
		</item>
	<!--{/section}-->
</channel>
</rss >
