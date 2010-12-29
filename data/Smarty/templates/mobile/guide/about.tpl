<!--{*
/*
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
 */
*}-->
<center>運営会社紹介</center>

<hr>

<!-- ▼本文 ここから -->
<!--{if $arrSiteInfo.shop_name != ""}-->
[emoji:38]<font color="#800000">店名</font><br>
<!--{$arrSiteInfo.shop_name|h}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.company_name != ""}-->
[emoji:39]<font color="#800000">会社名</font><br>
<!--{$arrSiteInfo.company_name|h}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.zip01 != ""}-->
[emoji:121]<font color="#800000">住所</font><br>
〒<!--{$arrSiteInfo.zip01|h}-->-<!--{$arrSiteInfo.zip02|h}--><br>
<!--{$arrSiteInfo.pref|h}--><!--{$arrSiteInfo.addr01|h}--><!--{$arrSiteInfo.addr02|h}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.tel01 != ""}-->
[emoji:74]<font color="#800000">電話番号</font><br>
<!--{$arrSiteInfo.tel01|h}-->-<!--{$arrSiteInfo.tel02|h}-->-<!--{$arrSiteInfo.tel03|h}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.fax01 != ""}-->
[emoji:76]<font color="#800000">FAX番号</font><br>
<!--{$arrSiteInfo.fax01|h}-->-<!--{$arrSiteInfo.fax02|h}-->-<!--{$arrSiteInfo.fax03|h}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.business_hour != ""}-->
[emoji:176]<font color="#800000">営業時間</font><br>
<!--{$arrSiteInfo.business_hour|h}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.email02 != ""}-->
[emoji:110]<font color="#800000">メールアドレス</font><br>
<a href="mailto:<!--{$arrSiteInfo.email02|h}-->"><!--{$arrSiteInfo.email02|h}--></a><br>
<!--{/if}-->
<!--{if $arrSiteInfo.good_traded != ""}-->
[emoji:72]<font color="#800000">取扱商品</font><br>
<!--{$arrSiteInfo.good_traded|h|nl2br}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.message != ""}-->
[emoji:70]<font color="#800000">メッセージ</font><br>
<!--{$arrSiteInfo.message|h|nl2br}--><br>
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
