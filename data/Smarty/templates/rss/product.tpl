<?xml version="1.0" encoding="<!--{$encode}-->"?>
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<rss version="2.0">

<!--{* channel要素 *}-->
<channel>
<title><!--{$title|sf_mb_convert_encoding:$encode}--></title>
<shop_name><!--{$arrSiteInfo.shop_name|sf_mb_convert_encoding:$encode}--></shop_name>
<shop_kana><!--{$arrSiteInfo.shop_kana|sf_mb_convert_encoding:$encode}--></shop_kana>
<site_url><!--{$smarty.const.SITE_URL}--></site_url>
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
