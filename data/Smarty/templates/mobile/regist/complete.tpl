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
    本登録が完了いたしました。<br>
    それではショッピングをお楽しみください。<br>
    <br>

    今後ともご愛顧賜りますようよろしくお願い申し上げます。<br>
    <br>

    <!--{$arrSiteInfo.company_name|h}--><br>
    TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--> <!--{if $arrSiteInfo.business_hour != ""}-->（受付時間/<!--{$arrSiteInfo.business_hour}-->）<!--{/if}--><br>
    E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|h}-->"><!--{$arrSiteInfo.email02|h}--></a><br>


    <!--{if !$tpl_cart_empty}-->
        <br>
        <a href="<!--{$smarty.const.DELIV_URLPATH|h}-->">ご注文手続きへ進む</a><br>
        <br>
    <!--{/if}-->
<!--{/strip}-->
