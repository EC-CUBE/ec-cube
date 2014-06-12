<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<section id="undercolumn">
    <!--☆当サイトについて -->
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <dl class="form_info">
        <!--{if strlen($arrSiteInfo.shop_name)}-->
            <dt>店名</dt>
            <dd><!--{$arrSiteInfo.shop_name|h}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.company_name)}-->
            <dt>会社名</dt>
            <dd><!--{$arrSiteInfo.company_name|h}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.zip01)}-->
            <dt>所在地</dt>
            <dd>〒<!--{$arrSiteInfo.zip01|h}-->-<!--{$arrSiteInfo.zip02|h}--><br />
                <!--{$arrPref[$arrSiteInfo.pref]|h}--><!--{$arrSiteInfo.addr01|h}--><!--{$arrSiteInfo.addr02|h}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.tel01)}-->
            <dt>電話番号</dt>
            <dd><!--{$arrSiteInfo.tel01|h}-->-<!--{$arrSiteInfo.tel02|h}-->-<!--{$arrSiteInfo.tel03|h}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.fax01)}-->
            <dt>FAX番号</dt>
            <dd><!--{$arrSiteInfo.fax01|h}-->-<!--{$arrSiteInfo.fax02|h}-->-<!--{$arrSiteInfo.fax03|h}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.email02)}-->
            <dt>メールアドレス</dt>
            <dd><a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->" rel="external"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.business_hour)}-->
            <dt>営業時間</dt>
            <dd><!--{$arrSiteInfo.business_hour|h}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.good_traded)}-->
            <dt>取扱商品</dt>
            <dd><!--{$arrSiteInfo.good_traded|h|nl2br}--></dd>
        <!--{/if}-->

        <!--{if strlen($arrSiteInfo.message)}-->
            <dt>メッセージ</dt>
            <dd><!--{$arrSiteInfo.message|h|nl2br}--></dd>
        <!--{/if}-->
    </dl>

    <!--{if strlen($arrSiteInfo.latitude) >= 1 && strlen($arrSiteInfo.longitude) >= 1}-->
        <script src="//maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript">//<![CDATA[
            $(function() {
                $("#maps").css({
                    'margin-top': '15px',
                    'margin-left': 'auto',
                    'margin-right': 'auto',
                    'width': '98%',
                    'height': '300px'
                });
                var lat = "<!--{$arrSiteInfo.latitude|escape:'javascript'}-->";
                var lng = "<!--{$arrSiteInfo.longitude|escape:'javascript'}-->";
                var latlng = new google.maps.LatLng(lat, lng);
                var mapOptions = {
                    zoom: 15,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var map = new google.maps.Map($("#maps").get(0), mapOptions);
                var marker = new google.maps.Marker({map: map, position: latlng});
            });
        //]]></script>
        <div id="maps"></div>
    <!--{/if}-->
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

