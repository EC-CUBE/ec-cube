<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>本会員登録完了</center>

<hr>

本登録が完了いたしました。<br>
それではショッピングをお楽しみください。<br>

<br>

今後ともご愛顧賜りますようよろしくお願い申し上げます。<br>

<br>

<!--{$arrSiteInfo.company_name|escape}--><br>
TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--> <!--{if $arrSiteInfo.business_hour != ""}-->（受付時間/<!--{$arrSiteInfo.business_hour}-->）<!--{/if}--><br>
E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a><br>

<br>

<!--{if !$tpl_cart_empty}-->
<a href="<!--{$smarty.const.MOBILE_URL_DIR}-->shopping/deliv.php">ご注文手続きへ進む</a><br>
<!--{/if}-->

<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページに戻る</a><br>

<br>
<hr>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
