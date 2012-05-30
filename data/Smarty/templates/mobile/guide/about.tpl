<!--{*
/*
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
 */
*}-->

<!--{strip}-->
    <!--{if $arrSiteInfo.shop_name != ""}-->
        [emoji:38]店名<br>
        <!--{$arrSiteInfo.shop_name|h}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.company_name != ""}-->
        <br>
        [emoji:39]会社名<br>
        <!--{$arrSiteInfo.company_name|h}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.zip01 != ""}-->
        <br>
        [emoji:121]所在地<br>
        〒<!--{$arrSiteInfo.zip01|h}-->-<!--{$arrSiteInfo.zip02|h}--><br>
        <!--{$arrPref[$arrSiteInfo.pref]}--><!--{$arrSiteInfo.addr01|h}--><!--{$arrSiteInfo.addr02|h}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.tel01 != ""}-->
        <br>
        [emoji:74]電話番号<br>
        <!--{$arrSiteInfo.tel01|h}-->-<!--{$arrSiteInfo.tel02|h}-->-<!--{$arrSiteInfo.tel03|h}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.fax01 != ""}-->
        <br>
        [emoji:76]FAX番号<br>
        <!--{$arrSiteInfo.fax01|h}-->-<!--{$arrSiteInfo.fax02|h}-->-<!--{$arrSiteInfo.fax03|h}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.business_hour != ""}-->
        <br>
        [emoji:176]営業時間<br>
        <!--{$arrSiteInfo.business_hour|h}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.email02 != ""}-->
        <br>
        [emoji:110]メールアドレス<br>
        <a href="mailto:<!--{$arrSiteInfo.email02|h}-->"><!--{$arrSiteInfo.email02|h}--></a><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.good_traded != ""}-->
        <br>
        [emoji:72]取扱商品<br>
        <!--{$arrSiteInfo.good_traded|h|nl2br}--><br>
    <!--{/if}-->
    <!--{if $arrSiteInfo.message != ""}-->
        <br>
        [emoji:70]メッセージ<br>
        <!--{$arrSiteInfo.message|h|nl2br}--><br>
    <!--{/if}-->
<!--{/strip}-->
