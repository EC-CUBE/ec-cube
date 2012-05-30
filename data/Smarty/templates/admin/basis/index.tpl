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
                    alert('所在地の場所が見つかりません');
                }
            });
        } else {
            alert('所在地の場所が見つかりません');
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

    <h2>基本情報</h2>
    <table summary="基本情報" id="basis-index-basis">
        <tr>
            <th>会社名</th>
            <td>
                <span class="attention"><!--{$arrErr.company_name}--></span>
                <input type="text" name="company_name" value="<!--{$arrForm.company_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.company_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>会社名(フリガナ)</th>
            <td>
                <span class="attention"><!--{$arrErr.company_kana}--></span>
                <input type="text" name="company_kana" value="<!--{$arrForm.company_kana|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.company_kana != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>店名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.shop_name}--></span>
                <input type="text" name="shop_name" value="<!--{$arrForm.shop_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.shop_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>店名(フリガナ)</th>
            <td>
                <span class="attention"><!--{$arrErr.shop_kana}--></span>
                <input type="text" name="shop_kana" value="<!--{$arrForm.shop_kana|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.shop_kana != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>店名(英語表記)</th>
            <td>
                <span class="attention"><!--{$arrErr.shop_name_eng}--></span>
                <input type="text" name="shop_name_eng" value="<!--{$arrForm.shop_name_eng|h}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.shop_name_eng != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> (上限<!--{$smarty.const.MTEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>郵便番号<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.zip01}--></span>
                <span class="attention"><!--{$arrErr.zip02}--></span>
                〒 <input type="text" name="zip01" value="<!--{$arrForm.zip01|h}-->" maxlength="3" size="6" class="box6" style="<!--{if $arrErr.zip01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> - <input type="text" name="zip02" value="<!--{$arrForm.zip02|h}-->" maxlength="4"    size="6" class="box6" style="<!--{if $arrErr.zip02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;">所在地入力</a>
            </td>
        </tr>
        <tr>
            <th>SHOP所在地<span class="attention"> *</span></th>
            <td>
                <p>
                    <span class="attention"><!--{$arrErr.pref}--></span>
                    <select class="top" name="pref" style="<!--{if $arrErr.pref != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" >
                        <option value="" selected="selected">都道府県を選択</option>
                        <!--{html_options options=$arrPref selected=$arrForm.pref}-->
                    </select>
                </p>
                <p>
                    <span class="attention"><!--{$arrErr.addr01}--></span>
                    <input type="text" name="addr01" value="<!--{$arrForm.addr01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.addr01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="addr01" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS1}-->
                </p>
                <p>
                    <span class="attention"><!--{$arrErr.addr02}--></span>
                    <input type="text" name="addr02" value="<!--{$arrForm.addr02|h}-->"    maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.addr02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="addr02" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS2}-->
                </p>
            </td>
        </tr>
        <tr>
            <th>TEL</th>
            <td>
                <span class="attention"><!--{$arrErr.tel01}--></span>
                <input type="text" name="tel01" value="<!--{$arrForm.tel01|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.tel01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="tel02" value="<!--{$arrForm.tel02|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.tel01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="tel03" value="<!--{$arrForm.tel03|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.tel01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th>FAX</th>
            <td>
                <span class="attention"><!--{$arrErr.fax01}--></span>
                <input type="text" name="fax01" value="<!--{$arrForm.fax01|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.fax01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="fax02" value="<!--{$arrForm.fax02|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.fax02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> -
                <input type="text" name="fax03" value="<!--{$arrForm.fax03|h}-->" maxlength="6" size="6" class="box6" style="<!--{if $arrErr.fax03 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th>店舗営業時間</th>
            <td>
                <span class="attention"><!--{$arrErr.business_hour}--></span>
                <input type="text" name="business_hour" value="<!--{$arrForm.business_hour|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="60" class="box60" style="<!--{if $arrErr.business_hour != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>商品注文受付<br />メールアドレス<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.email01}--></span>
                <input type="text" name="email01" value="<!--{$arrForm.email01|h}-->" size="60" class="box60" style="<!--{if $arrErr.email01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th>問い合わせ受付<br />メールアドレス<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.email02}--></span>
                <input type="text" name="email02" value="<!--{$arrForm.email02|h}-->" size="60" class="box60" style="<!--{if $arrErr.email02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
            </td>
        </tr>
        <tr>
            <th>メール送信元<br />メールアドレス<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.email03}--></span>
                <input type="text" name="email03" value="<!--{$arrForm.email03|h}-->" size="60" class="box60" style="<!--{if $arrErr.email03 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
            </td>
        </tr>
        <tr>
            <th>送信エラー受付<br />メールアドレス<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.email04}--></span>
                <input type="text" name="email04" value="<!--{$arrForm.email04|h}-->" size="60" class="box60" style="<!--{if $arrErr.email04 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
            </td>
        </tr>
        <tr>
            <th>取扱商品</th>
            <td>
                <!--{assign var=key value="good_traded"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key]|h}--></textarea>
                <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>メッセージ</th>
            <td>
                <!--{assign var=key value="message"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key]|h}--></textarea>
                <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
            </td>
        </tr>
    </table>

    <h2>定休日設定</h2>
    <table id="basis-index-holiday">
        <tr>
            <th>定休日</th>
            <td>
                <span class="attention"><!--{$arrErr.regular_holiday_ids}--></span>
                <!--{html_checkboxes name="regular_holiday_ids" options=$arrRegularHoliday selected=$arrForm.regular_holiday_ids}-->
            </td>
        </tr>
    </table>

    <h2>SHOP機能</h2>
    <table id="basis-index-func">
        <tr>
            <th>消費税率<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.tax}--></span>
                <input type="text" name="tax" value="<!--{$arrForm.tax|h}-->" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.tax != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> ％
            </td>
        </tr>
        <tr>
            <th>課税規則<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.tax_rule}--></span>
                <!--{html_radios name="tax_rule" options=$arrTAXRULE selected=$arrForm.tax_rule}-->
            </td>
        </tr>
        <tr>
            <th>送料無料条件</th>
            <td>
                <span class="attention"><!--{$arrErr.free_rule}--></span>
                <input type="text" name="free_rule" value="<!--{$arrForm.free_rule|h}-->" maxlength="<!--{$smarty.const.PRICE_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.free_rule != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> 円以上購入時無料
            </td>
        </tr>
        <tr>
            <th>ダウンロード可能日数</th>
            <td>
                <span class="attention"><!--{$arrErr.downloadable_days}--></span>
                <input type="text" name="downloadable_days" value="<!--{$arrForm.downloadable_days|h}-->" maxlength="<!--{$smarty.const.DOWNLOAD_DAYS_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.downloadable_days != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" /> 日間有効
                <input type="checkbox" name="downloadable_days_unlimited" value="1" <!--{if $arrForm.downloadable_days_unlimited == "1"}-->checked<!--{/if}--> onclick="fnCheckLimit('downloadable_days', 'downloadable_days_unlimited', '<!--{$smarty.const.DISABLED_RGB}-->');"/>無制限
            </td>
        </tr>
    </table>

    <h2>地図設定</h2>
    <table>
        <tr>
            <th>緯度/経度情報</th>
            <td>
                <span class="attention"><!--{$arrErr.latitude}--></span>
                <span class="attention"><!--{$arrErr.longitude}--></span>
                緯度: <input type="text" name="latitude" value="<!--{$arrForm.latitude|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{if $arrErr.latitude != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="latitude" />
                経度: <input type="text" name="longitude" value="<!--{$arrForm.longitude|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{if $arrErr.longitude != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" id="longitude" />
                <a class="btn-normal" href="javascript:;" name="codeAddress" id="codeAddress" onclick="">所在地より自動取得</a>
                <a href="#maparea" id="mapAddress">地図で設定</a>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', '<!--{$tpl_mode}-->', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>
<div style="display: none">
    <div id="maparea">
        <div id="maps" style="width: 300px; height: 300px"></div>
        <a class="btn-normal" href="javascript:;" id="inputPoint">この位置を入力</a>
    </div>
</div>
<!--{* ▲登録テーブルここまで *}-->
</form>
