<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>運営会社紹介</center>

<hr>

<!-- ▼本文 ここから -->
<!--{if $arrSiteInfo.shop_name != ""}-->
[emoji:38]<font color="#800000">店名</font><br>
<!--{$arrSiteInfo.shop_name|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.company_name != ""}-->
[emoji:39]<font color="#800000">会社名</font><br>
<!--{$arrSiteInfo.company_name|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.zip01 != ""}-->
[emoji:121]<font color="#800000">住所</font><br>
〒<!--{$arrSiteInfo.zip01|escape}-->-<!--{$arrSiteInfo.zip02|escape}--><br>
<!--{$arrSiteInfo.pref|escape}--><!--{$arrSiteInfo.addr01|escape}--><!--{$arrSiteInfo.addr02|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.tel01 != ""}-->
[emoji:74]<font color="#800000">電話番号</font><br>
<!--{$arrSiteInfo.tel01|escape}-->-<!--{$arrSiteInfo.tel02|escape}-->-<!--{$arrSiteInfo.tel03|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.fax01 != ""}-->
[emoji:76]<font color="#800000">FAX番号</font><br>
<!--{$arrSiteInfo.fax01|escape}-->-<!--{$arrSiteInfo.fax02|escape}-->-<!--{$arrSiteInfo.fax03|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.business_hour != ""}-->
[emoji:176]<font color="#800000">営業時間</font><br>
<!--{$arrSiteInfo.business_hour|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.email02 != ""}-->
[emoji:110]<font color="#800000">メールアドレス</font><br>
<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a><br>
<!--{/if}-->
<!--{if $arrSiteInfo.good_traded != ""}-->
[emoji:72]<font color="#800000">取扱商品</font><br>
<!--{$arrSiteInfo.good_traded|escape|nl2br}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.message != ""}-->
[emoji:70]<font color="#800000">メッセージ</font><br>
<!--{$arrSiteInfo.message|escape|nl2br}--><br>
<!--{/if}-->
<!-- ▲本文 ここまで -->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
