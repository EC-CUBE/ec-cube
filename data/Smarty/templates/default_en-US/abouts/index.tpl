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

<!--{if $objSiteInfo->data.latitude && $objSiteInfo->data.longitude}-->
    <script type="text/javascript">//<![CDATA[
        $(function() {
            $("#maps").css({
                'margin-top': '15px',
                'margin-left': 'auto',
                'margin-right': 'auto',
                'width': '98%',
                'height': '300px'
            });
            var lat = <!--{$objSiteInfo->data.latitude}-->
            var lng = <!--{$objSiteInfo->data.longitude}-->
            if (lat && lng) {
                var latlng = new google.maps.LatLng(lat, lng);
                var mapOptions = {
                    zoom: 15,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var map = new google.maps.Map($("#maps").get(0), mapOptions);
                var marker = new google.maps.Marker({map: map, position: latlng});
            } else {
                $("#maps").remove();
            }
        });
    //]]></script>
<!--{/if}-->
<div id="undercolumn">

    <div id="undercolumn_aboutus">
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <table summary="About this site">
            <col width="20%" />
            <col width="80%" />
            <!--{if strlen($objSiteInfo->data.shop_name)}-->
                <tr>
                    <th>Store name</th>
                    <td><!--{$objSiteInfo->data.shop_name|h}--></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.company_name)}-->
                <tr>
                    <th>Company name</th>
                    <td><!--{$objSiteInfo->data.company_name|h}--></td>
                </tr>
            <!--{/if}-->

            <!--{*
            <!--{if strlen($objSiteInfo->data.zip01)}-->
                <tr>
                    <th>Location</th>
                    <td><!--{$objSiteInfo->data.zip01|h}-->-<!--{$objSiteInfo->data.zip02|h}--><br /><!--{$objSiteInfo->data.addr01|h}--><!--{$objSiteInfo->data.addr02|h}--></td>
                </tr>
            <!--{/if}-->
            *}-->
            <!--{if strlen($objSiteInfo->data.zipcode)}-->
                <tr>
                    <th>Location</th>
                    <td><!--{$objSiteInfo->data.zipcode|h}--><br /><!--{$objSiteInfo->data.addr01|h}--><!--{$objSiteInfo->data.addr02|h}--></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.tel01)}-->
                <tr>
                    <th>Phone number</th>
                    <td><!--{$objSiteInfo->data.tel01|h}-->-<!--{$objSiteInfo->data.tel02|h}-->-<!--{$objSiteInfo->data.tel03|h}--></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.fax01)}-->
                <tr>
                    <th>Fax number</th>
                    <td><!--{$objSiteInfo->data.fax01|h}-->-<!--{$objSiteInfo->data.fax02|h}-->-<!--{$objSiteInfo->data.fax03|h}--></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.email02)}-->
                <tr>
                    <th>E-mail address</th>
                    <td><a href="mailto:<!--{$objSiteInfo->data.email02|escape:'hex'}-->"><!--{$objSiteInfo->data.email02|escape:'hexentity'}--></a></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.business_hour)}-->
                <tr>
                    <th>Business hours</th>
                    <td><!--{$objSiteInfo->data.business_hour|h}--></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.good_traded)}-->
                <tr>
                    <th>Available products</th>
                    <td><!--{$objSiteInfo->data.good_traded|h|nl2br}--></td>
                </tr>
            <!--{/if}-->

            <!--{if strlen($objSiteInfo->data.message)}-->
                <tr>
                    <th>Message</th>
                    <td><!--{$objSiteInfo->data.message|h|nl2br}--></td>
                </tr>
            <!--{/if}-->

        </table>

        <div id="maps"></div>
    </div>
</div>
