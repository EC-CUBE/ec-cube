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

<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">//<![CDATA[
var map;
var marker;

$(function() {
    var geocoder = new google.maps.Geocoder();

    $("#codeAddress").click(function() {
        var result = true;
        var address = $("#addr01").val() + $("#addr02").val();
        if (geocoder && address) {
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    latlng = results[0].geometry.location;
                    $("#latitude").val(latlng.lat());
                    $("#longitude").val(latlng.lng());
                } else {
                    alert('<!--{t string="tpl_The address was not found._01"}-->');
                }
            });
        } else {
            alert('<!--{t string="tpl_The address was not found._01"}-->');
        }
    });

    $("a#mapAddress").fancybox({onStart: function() {
        var lat = $("#latitude").val();
        var lng = $("#longitude").val();

        var latlng;
        if (lat && lng) {
            latlng = new google.maps.LatLng(lat, lng);
        } else {
            var address = $("#addr01").val() + $("#addr02").val();
            if (geocoder) {
                geocoder.geocode({'address': address}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        latlng = results[0].geometry.location;
                    }
                });
            }
        }

        if (!latlng) {
            // 座標が取得できない場合は北緯35度東経135度から取得
            latlng = new google.maps.LatLng(35, 135);
        }

        var mapOptions = {
            zoom: 15,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        if (!map)
        {
            map = new google.maps.Map($("#maps").get(0), mapOptions);
        }
        else
        {
            map.panTo(latlng);
        }

        if (!marker)
        {
            marker = new google.maps.Marker({map: map, position: latlng});
            marker.setDraggable(true);
        }
        else
        {
            marker.setPosition(latlng);
        }

        // TODO Maker のダブルクリックにも対応したい
        $("#inputPoint").click(function() {
            latlng = marker.getPosition();
            $("#latitude").val(latlng.lat());
            $("#longitude").val(latlng.lng());
            $.fancybox.close();
        });
    }});
});
//]]></script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<!--{* ▼登録テーブルここから *}-->
<div id="basis" class="contents-main">

    <h2><!--{t string="tpl_Basic information_01" escape="none"}--></h2>
    <table summary="<!--{t string="tpl_Basic information_01" escape="none"}-->" id="basis-index-basis">
        <tr>
            <th><!--{t string="tpl_Company name_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.company_name}--></span>
                <input type="text" name="company_name" value="<!--{$arrForm.company_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.company_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Store name<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.shop_name}--></span>
                <input type="text" name="shop_name" value="<!--{$arrForm.shop_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.shop_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Store name (in English)_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.shop_name_eng}--></span>
                <input type="text" name="shop_name_eng" value="<!--{$arrForm.shop_name_eng|h}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.shop_name_eng != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.MTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Postal code_01" escape="none"}--></th>
            <td>
                <!--{* <span class="attention"><!--{$arrErr.zip01}--></span> *}-->
                <!--{* <span class="attention"><!--{$arrErr.zip02}--></span> *}-->
                <span class="attention"><!--{$arrErr.zipcode}--></span>
                
                <!--{* <!--{t string="tpl_Postal code mark_01"}--> <input type="text" name="zip01" value="<!--{$arrForm.zip01|h}-->" maxlength="3" size="6" class="box6" style="<!--{if $arrErr.zip01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> - <input type="text" name="zip02" value="<!--{$arrForm.zip02|h}-->" maxlength="4"    size="6" class="box6" style="<!--{if $arrErr.zip02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> *}-->
                <!--{* <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;"><!--{t string="tpl_Location finder_01"}--></a> *}-->
                <!--{t string="tpl_Postal code mark_01"}--> <input type="text" name="zipcode" value="<!--{$arrForm.zipcode|h}-->" maxlength="10" size="15" class="box10" style="<!--{if $arrErr.zipcode != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_SHOP address<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <p>
                    <span class="attention"><!--{$arrErr.addr01}--></span>
                    <input type="text" name="addr01" value="<!--{$arrForm.addr01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.addr01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="addr01" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS1}-->
                </p>
                <p>
                    <span class="attention"><!--{$arrErr.addr02}--></span>
                    <input type="text" name="addr02" value="<!--{$arrForm.addr02|h}-->"    maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.addr02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="addr02" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS2}-->
                </p>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Phone Number_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.tel01}--></span>
                <input type="text" name="tel01" value="<!--{$arrForm.tel01|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.tel01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="tel02" value="<!--{$arrForm.tel02|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.tel01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="tel03" value="<!--{$arrForm.tel03|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.tel01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_FAX_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.fax01}--></span>
                <input type="text" name="fax01" value="<!--{$arrForm.fax01|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.fax01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="fax02" value="<!--{$arrForm.fax02|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.fax02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="fax03" value="<!--{$arrForm.fax03|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.fax03 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Store business hours_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.business_hour}--></span>
                <input type="text" name="business_hour" value="<!--{$arrForm.business_hour|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.business_hour != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product order receipt e-mail address<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.email01}--></span>
                <input type="text" name="email01" value="<!--{$arrForm.email01|h}-->" size="60" class="box60" style="<!--{if $arrErr.email01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address for receiving inquiries<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.email02}--></span>
                <input type="text" name="email02" value="<!--{$arrForm.email02|h}-->" size="60" class="box60" style="<!--{if $arrErr.email02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address of sender<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.email03}--></span>
                <input type="text" name="email03" value="<!--{$arrForm.email03|h}-->" size="60" class="box60" style="<!--{if $arrErr.email03 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address for receiving sending errors<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.email04}--></span>
                <input type="text" name="email04" value="<!--{$arrForm.email04|h}-->" size="60" class="box60" style="<!--{if $arrErr.email04 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Available products_01"}--></th>
            <td>
                <!--{assign var=key value="good_traded"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea>
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LLTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Message_01"}--></th>
            <td>
                <!--{assign var=key value="message"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea>
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LLTEXT_LEN}--></span>
            </td>
        </tr>
    </table>

    <h2><!--{t string="tpl_Regular holiday settings_01"}--></h2>
    <table id="basis-index-holiday">
        <tr>
            <th><!--{t string="tpl_Regular holiday_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.regular_holiday_ids}--></span>
                <!--{html_checkboxes name="regular_holiday_ids" options=$arrRegularHoliday selected=$arrForm.regular_holiday_ids}-->
            </td>
        </tr>
    </table>

    <h2><!--{t string="tpl_SHOP function_01"}--></h2>
    <table id="basis-index-func">
        <tr>
            <th><!--{t string="tpl_Consumption sales tax rate<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.tax}--></span>
                <input type="text" name="tax" value="<!--{$arrForm.tax|h}-->" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.tax != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> <!--{t string="%"}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Taxation rules<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.tax_rule}--></span>
                <!--{html_radios name="tax_rule" options=$arrTAXRULE selected=$arrForm.tax_rule}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Conditions for free shipping_01"}--> </th>
            <td>
                <span class="attention"><!--{$arrErr.free_rule}--></span>
                <input type="text" name="free_rule" value="<!--{$arrForm.free_rule|h}-->" maxlength="<!--{$smarty.const.PRICE_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.free_rule != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> <!--{t string="tpl_ and above is free!_01"}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Number of days during which download is possible_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.downloadable_days}--></span>
                <input type="text" name="downloadable_days" value="<!--{$arrForm.downloadable_days|h}-->" maxlength="<!--{$smarty.const.DOWNLOAD_DAYS_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.downloadable_days != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" /> <!--{t string="tpl_Days active_01"}-->
                <label><input type="checkbox" name="downloadable_days_unlimited" value="1" <!--{if $arrForm.downloadable_days_unlimited == "1"}-->checked<!--{/if}--> onclick="fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '<!--{$smarty.const.DISABLED_RGB}-->');"/><!--{t string="tpl_No limit_01"}--></label>
            </td>
        </tr>
    </table>

    <h2><!--{t string="tpl_Map settings_01"}--></h2>
    <table>
        <tr>
            <th><!--{t string="tpl_Latitude/longitude information_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.latitude}--></span>
                <span class="attention"><!--{$arrErr.longitude}--></span>
                <!--{t string="tpl_Latitude : _01"}--><input type="text" name="latitude" value="<!--{$arrForm.latitude|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{if $arrErr.latitude != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="latitude" />
                <!--{t string="tpl_Longitude: _01"}--><input type="text" name="longitude" value="<!--{$arrForm.longitude|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{if $arrErr.longitude != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="longitude" />
                <a class="btn-normal" href="javascript:;" name="codeAddress" id="codeAddress" onclick=""><!--{t string="tpl_Automatic retrieval from location_01"}--></a>
                <a href="#maparea" id="mapAddress"><!--{t string="tpl_Set using map_01"}--></a>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', '<!--{$tpl_mode}-->', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
<div style="display: none">
    <div id="maparea">
        <div id="maps" style="width: 300px; height: 300px"></div>
        <a class="btn-normal" href="javascript:;" id="inputPoint"><!--{t string="tpl_Enter this position._01"}--></a>
    </div>
</div>
<!--{* ▲登録テーブルここまで *}-->
</form>
