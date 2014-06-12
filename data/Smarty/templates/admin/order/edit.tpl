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

<script type="text/javascript">
<!--
    function fnEdit(customer_id) {
        document.form1.action = '<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/edit.php';
        document.form1.mode.value = "edit"
        document.form1['customer_id'].value = customer_id;
        document.form1.submit();
        return false;
    }

    function fnCopyFromOrderData() {
        df = document.form1;

        // お届け先名のinputタグのnameを取得
        var shipping_data = $('input[name^=shipping_name01]').attr('name');
        var shipping_slt  = shipping_data.split("shipping_name01");

        var shipping_key = "[0]";
        if(shipping_slt.length > 1) {
            shipping_key = shipping_slt[1];
        }

        df['shipping_name01'+shipping_key].value = df.order_name01.value;
        df['shipping_name02'+shipping_key].value = df.order_name02.value;
        df['shipping_kana01'+shipping_key].value = df.order_kana01.value;
        df['shipping_kana02'+shipping_key].value = df.order_kana02.value;
        df['shipping_company_name'+shipping_key].value = df.order_company_name.value;
        df['shipping_zip01'+shipping_key].value = df.order_zip01.value;
        df['shipping_zip02'+shipping_key].value = df.order_zip02.value;
        df['shipping_tel01'+shipping_key].value = df.order_tel01.value;
        df['shipping_tel02'+shipping_key].value = df.order_tel02.value;
        df['shipping_tel03'+shipping_key].value = df.order_tel03.value;
        df['shipping_fax01'+shipping_key].value = df.order_fax01.value;
        df['shipping_fax02'+shipping_key].value = df.order_fax02.value;
        df['shipping_fax03'+shipping_key].value = df.order_fax03.value;
        <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
            df['shipping_country_id'+shipping_key].value = df.order_country_id.value;
            df['shipping_zipcode'+shipping_key].value = df.order_zipcode.value;
        <!--{/if}-->
        df['shipping_addr01'+shipping_key].value = df.order_addr01.value;
        df['shipping_addr02'+shipping_key].value = df.order_addr02.value;
        df['shipping_pref'+shipping_key].value = df.order_pref.value;
    }

    function fnFormConfirm() {
        if (eccube.doConfirm()) {
            document.form1.submit();
        }
    }

    function fnMultiple() {
        eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/multiple.php', 'multiple', '600', '500', {menubar:'no'});
        document.form1.anchor_key.value = "shipping";
        document.form1.mode.value = "multiple";
        document.form1.submit();
        return false;
    }

    function fnAppendShipping() {
        document.form1.anchor_key.value = "shipping";
        document.form1.mode.value = "append_shipping";
        document.form1.submit();
        return false;
    }

    $(document).ready(function() {
        var shipping_quantity = '<!--{$tpl_shipping_quantity|escape:javascript}-->';
        if (shipping_quantity > 1){
            $("input[name^='quantity[']").attr("disabled","disabled");
        }
    });

    function quantityCopyForSingleShipping(product_index){
        var product_index = parseInt(product_index);
        var input_quantity = $('input[name^="quantity[' + product_index + ']"]').val();
        $('input[name^="shipment_quantity[<!--{$top_shipping_id}-->][' + product_index + ']"]').val(input_quantity);
    }

//-->
</script>
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="<!--{$tpl_mode|default:"edit"|h}-->" />
    <input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
    <input type="hidden" name="edit_customer_id" value="" />
    <input type="hidden" name="anchor_key" value="" />
    <input type="hidden" id="add_product_id" name="add_product_id" value="" />
    <input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
    <input type="hidden" id="select_shipping_id" name="select_shipping_id" value="" />
    <input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
    <input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
    <input type="hidden" id="no" name="no" value="" />
    <input type="hidden" id="delete_no" name="delete_no" value="" />
    <!--{foreach key=key item=item from=$arrSearchHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->

    <div id="order" class="contents-main">

        <!--▼お客様情報ここから-->
        <table class="form">
            <!--{if $tpl_mode != 'add'}-->
            <tr>
                <th>帳票出力</th>
                <td><a class="btn-normal" href="javascript:;" onclick="eccube.openWindow('pdf.php?order_id=<!--{$arrForm.order_id.value|h}-->','pdf_input','615','650'); return false;">帳票出力</a></td>
            </tr>
            <!--{/if}-->
            <tr>
                <th>注文番号</th>
                <td><!--{$arrForm.order_id.value|h}--></td>
            </tr>
            <tr>
                <th>受注日</th>
                <td><!--{$arrForm.create_date.value|sfDispDBDate|h}--><input type="hidden" name="create_date" value="<!--{$arrForm.create_date.value|h}-->" /></td>
            </tr>
            <tr>
                <th>対応状況<span class="attention"> *</span></th>
                <td>
                    <!--{assign var=key value="status"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="">選択してください</option>
                        <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                    </select><br />
                    <!--{if $smarty.get.mode != 'add'}-->
                        <span class="attention">※ <!--{$arrORDERSTATUS[$smarty.const.ORDER_CANCEL]}-->に変更時には、在庫数を手動で戻してください。</span>
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>入金日</th>
                <td><!--{$arrForm.payment_date.value|sfDispDBDate|default:"未入金"|h}--></td>
            </tr>
            <tr>
                <th>発送日</th>
                <td><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"|h}--></td>
            </tr>
        </table>

        <h2>注文者情報
            <!--{if $tpl_mode == 'add'}-->
                <a class="btn-normal" href="javascript:;" name="address_input" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/search_customer.php','search','600','650',{resizable:'no',focus:false}); return false;">会員検索</a>
            <!--{/if}-->
        </h2>
        <table class="form">
            <tr>
                <th>会員ID</th>
                <td>
                    <!--{if $arrForm.customer_id.value > 0}-->
                        <!--{$arrForm.customer_id.value|h}-->
                        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id.value|h}-->" />
                    <!--{else}-->
                        (非会員)
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>お名前<span class="attention"> *</span></th>
                <td>
                    <!--{assign var=key1 value="order_name01"}-->
                    <!--{assign var=key2 value="order_name02"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                </td>
            </tr>
            <tr>
                <th>お名前(フリガナ)</th>
                <td>
                    <!--{assign var=key1 value="order_kana01"}-->
                    <!--{assign var=key2 value="order_kana02"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                </td>
            </tr>
            <tr>
                <th>会社名</th>
                <td>
                    <!--{assign var=key1 value="order_company_name"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="30" class="box30" />
                </td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td>
                    <!--{assign var=key1 value="order_email"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="30" class="box30" />
                </td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>
                    <!--{assign var=key1 value="order_tel01"}-->
                    <!--{assign var=key2 value="order_tel02"}-->
                    <!--{assign var=key3 value="order_tel03"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                    <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
                </td>
            </tr>
            <tr>
                <th>FAX</th>
                <td>
                    <!--{assign var=key1 value="order_fax01"}-->
                    <!--{assign var=key2 value="order_fax02"}-->
                    <!--{assign var=key3 value="order_fax03"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                    <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
                </td>
            </tr>
            <tr>
                <th>性別</th>
                <td>
                    <!--{assign var=key1 value="order_sex"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                    <!--{html_radios name=$key1 options=$arrSex selected=$arrForm[$key1].value}-->
                    </span>
                </td>
            </tr>
            <tr>
                <th>職業</th>
                <td>
                    <!--{assign var=key1 value="order_job"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                    <select name="<!--{$key1}-->">
	                    <option value="" selected="selected">選択してください</option>
	                    <!--{html_options options=$arrJob selected=$arrForm[$key1].value}-->
                    </select>
                    </span>
                </td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td>
                    <!--{assign var=key1 value="order_birth_year"}-->
                    <!--{assign var=key2 value="order_birth_month"}-->
                    <!--{assign var=key3 value="order_birth_day"}-->
                    <!--{assign var=errBirth value="`$arrErr.$key1``$arrErr.$key2``$arrErr.$key3`"}-->
	                <!--{if $errBirth}-->
	                    <div class="attention"><!--{$errBirth}--></div>
	                <!--{/if}-->
                    <select name="<!--{$key1}-->" style="<!--{$errBirth|sfGetErrorColor}-->">
                        <!--{html_options options=$arrBirthYear selected=$arrForm[$key1].value|default:""}-->
                    </select>年
                    <select name="<!--{$key2}-->" style="<!--{$errBirth|sfGetErrorColor}-->">
                        <!--{html_options options=$arrBirthMonth selected=$arrForm[$key2].value|default:""}-->
                    </select>月
                    <select name="<!--{$key3}-->" style="<!--{$errBirth|sfGetErrorColor}-->">
                        <!--{html_options options=$arrBirthDay selected=$arrForm[$key3].value|default:""}-->
                    </select>日
                </td>
            </tr>

            <!--{assign var=key1 value="order_country_id"}-->
            <!--{assign var=key2 value="order_zipcode"}-->
            <!--{if !$smarty.const.FORM_COUNTRY_ENABLE}-->
            <input type="hidden" name="<!--{$key1}-->" value="<!--{$smarty.const.DEFAULT_COUNTRY_ID}-->" />
            <!--{else}-->
                <tr>
                    <th>国</th>
                    <td>
                        <span class="attention"><!--{$arrErr[$key1]}--></span>
                        <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                                <option value="" selected="selected">国を選択</option>
                                <!--{html_options options=$arrCountry selected=$arrForm[$key1].value|default:$smarty.const.DEFAULT_COUNTRY_ID}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>ZIP CODE</th>
                    <td>
                        <span class="attention"><!--{$arrErr[$key2]}--></span>
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->;" size="15" class="box15"/>
                    </td>
                </tr>
            <!--{/if}-->

            <tr>
                <th>住所</th>
                <td>
                    <!--{assign var=key1 value="order_zip01"}-->
                    <!--{assign var=key2 value="order_zip02"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                    〒
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                    -
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                    <a class="btn-normal" href="javascript:;" name="address_input" onclick="eccube.getAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;">住所入力</a><br />
                    <!--{assign var=key value="order_pref"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <select class="top" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="" selected="">都道府県を選択</option>
                        <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                    </select><br />
                    <!--{assign var=key value="order_addr01"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60 top" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
                    <!--{assign var=key value="order_addr02"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td><!--{$arrForm.message.value|h|nl2br}--></td>
            </tr>
            <tr>
                <th>現在ポイント</th>
                <td>
                    <!--{if $arrForm.customer_id.value > 0}-->
                        <!--{$arrForm.customer_point.value|n2s}-->
                        pt
                    <!--{else}-->
                        (非会員)
                <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>端末種別</th>
                <td><!--{$arrDeviceType[$arrForm.device_type_id.value]|h}--></td>
            </tr>

        </table>
        <!--▲お客様情報ここまで-->

        <!--▼受注商品情報ここから-->
        <a name="order_products"></a>
        <h2 id="order_products">
            受注商品情報
            <a class="btn-normal" href="javascript:;" name="recalculate" onclick="eccube.setModeAndSubmit('recalculate','anchor_key','order_products');">計算結果の確認</a>
            <!--{if $tpl_shipping_quantity <= 1}-->
                <a class="btn-normal" href="javascript:;" name="add_product" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?order_id=<!--{$arrForm.order_id.value|h}-->&amp;shipping_id=<!--{$top_shipping_id}-->', 'search', '615', '500', {menubar:'no'}); return false;">商品の追加</a>
            <!--{/if}-->
        </h2>

        <!--{if $arrErr.product_id}-->
            <span class="attention">※ 商品が選択されていません。</span>
        <!--{/if}-->

        <table class="list order-edit-products">
            <tr>
                <th class="id">商品コード</th>
                <th class="name">商品名/規格1/規格2</th>
                <th class="price">単価</th>
                <th class="qty">数量</th>
                <th class="price">税込み価格</th>
                <th class="price">小計</th>
            </tr>
            <!--{section name=cnt loop=$arrForm.quantity.value}-->
            <!--{assign var=product_index value="`$smarty.section.cnt.index`"}-->
                <tr>
                    <td class="center">
                        <!--{$arrForm.product_code.value[$product_index]|h}-->
                        <input type="hidden" name="product_code[<!--{$product_index}-->]" value="<!--{$arrForm.product_code.value[$product_index]|h}-->" id="product_code_<!--{$product_index}-->" />
                    </td>
                    <td class="center">
                        <!--{$arrForm.product_name.value[$product_index]|h}-->/<!--{$arrForm.classcategory_name1.value[$product_index]|default:"(なし)"|h}-->/<!--{$arrForm.classcategory_name2.value[$product_index]|default:"(なし)"|h}-->
                        <input type="hidden" name="product_name[<!--{$product_index}-->]" value="<!--{$arrForm.product_name.value[$product_index]|h}-->" id="product_name_<!--{$product_index}-->" />
                        <input type="hidden" name="classcategory_name1[<!--{$product_index}-->]" value="<!--{$arrForm.classcategory_name1.value[$product_index]|h}-->" id="classcategory_name1_<!--{$product_index}-->" />
                        <input type="hidden" name="classcategory_name2[<!--{$product_index}-->]" value="<!--{$arrForm.classcategory_name2.value[$product_index]|h}-->" id="classcategory_name2_<!--{$product_index}-->" />
                        <br />
                        <!--{if $tpl_shipping_quantity <= 1}-->
                            <a class="btn-normal" href="javascript:;" name="change" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?no=<!--{$product_index}-->&amp;order_id=<!--{$arrForm.order_id.value|h}-->&amp;shipping_id=<!--{$top_shipping_id}-->', 'search', '615', '500', {menubar:'no'}); return false;">変更</a>
                            <!--{if count($arrForm.quantity.value) > 1}-->
                                <a class="btn-normal" href="javascript:;" name="delete" onclick="eccube.setValue('delete_no', <!--{$product_index}-->, 'form1'); eccube.setValue('select_shipping_id', '<!--{$top_shipping_id}-->', 'form1'); eccube.setModeAndSubmit('delete_product','anchor_key','order_products'); return false;">削除</a>
                            <!--{/if}-->
                        <!--{/if}-->
                    <input type="hidden" name="product_type_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_type_id.value[$product_index]|h}-->" id="product_type_id_<!--{$product_index}-->" />
                    <input type="hidden" name="product_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_id.value[$product_index]|h}-->" id="product_id_<!--{$product_index}-->" />
                    <input type="hidden" name="product_class_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_class_id.value[$product_index]|h}-->" id="product_class_id_<!--{$product_index}-->" />
                    <input type="hidden" name="point_rate[<!--{$product_index}-->]" value="<!--{$arrForm.point_rate.value[$product_index]|h}-->" id="point_rate_<!--{$product_index}-->" />
                    </td>
                    <td class="center">
                        <!--{assign var=key value="price"}-->
                        <span class="attention"><!--{$arrErr[$key][$product_index]}--></span>
                        <input type="text" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$product_index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$product_index}-->" /> 円
                    </td>
                    <td class="center">
                        <!--{assign var=key value="quantity"}-->
                        <span class="attention"><!--{$arrErr[$key][$product_index]}--></span>
                        <input type="text" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" size="3" class="box3" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$product_index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$product_index}-->"  onChange="quantityCopyForSingleShipping('<!--{$product_index}-->')" />
                    </td>
                    <!--{assign var=price value=`$arrForm.price.value[$product_index]`}-->
                    <!--{assign var=quantity value=`$arrForm.quantity.value[$product_index]`}-->
                    <!--{assign var=tax_rate value=`$arrForm.tax_rate.value[$product_index]`}-->
                    <!--{assign var=tax_rule value=`$arrForm.tax_rule.value[$product_index]`}-->
                    <input type="hidden" name="tax_rule[<!--{$product_index}-->]" value="<!--{$arrForm.tax_rule.value[$product_index]|h}-->" id="tax_rule_<!--{$product_index}-->" />
    
                    <td class="right">
                        <!--{$price|sfCalcIncTax:$tax_rate:$tax_rule|n2s}--> 円<br />
                        <!--{assign var=key value="tax_rate"}-->
                        <span class="attention"><!--{$arrErr[$key][$product_index]}--></span>
                        税率<input type="text" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" size="3" class="box3" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$product_index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$product_index}-->" />%
                    </td>
                    <td class="right"><!--{$price|sfCalcIncTax:$tax_rate:$tax_rule|sfMultiply:$quantity|n2s}-->円</td>
                </tr>
            <!--{/section}-->
            <tr>
                <th colspan="5" class="column right">小計</th>
                <td class="right"><!--{$arrForm.subtotal.value|default:0|n2s}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="column right">値引き</th>
                <td class="right">
                    <!--{assign var=key value="discount"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                    円
                </td>
            </tr>
            <tr>
                <th colspan="5" class="column right">送料</th>
                <td class="right">
                    <!--{assign var=key value="deliv_fee"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                    円
                </td>
            </tr>
            <tr>
                <th colspan="5" class="column right">手数料</th>
                <td class="right">
                    <!--{assign var=key value="charge"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                    円
                </td>
            </tr>
            <tr>
                <th colspan="5" class="column right">合計</th>
                <td class="right">
                    <span class="attention"><!--{$arrErr.total}--></span>
                    <!--{$arrForm.total.value|default:0|n2s}--> 円
                </td>
            </tr>
            <tr>
                <th colspan="5" class="column right">お支払い合計</th>
                <td class="right">
                    <span class="attention"><!--{$arrErr.payment_total}--></span>
                    <!--{$arrForm.payment_total.value|default:0|n2s}-->
                    円
                </td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
                <tr>
                    <th colspan="5" class="column right">使用ポイント</th>
                    <td class="right">
                        <!--{assign var=key value="use_point"}-->
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:0|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                        pt
                    </td>
                </tr>
                <!--{if $arrForm.birth_point.value > 0}-->
                    <tr>
                        <th colspan="5" class="column right">お誕生日ポイント</th>
                        <td class="right">
                            <!--{assign var=key value="birth_point"}-->
                            <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->"/>
                            <!--{$arrForm.birth_point.value|n2s}-->
                            pt
                        </td>
                    </tr>
                <!--{/if}-->
                <tr>
                    <th colspan="5" class="column right">加算ポイント</th>
                    <td class="right">
                        <!--{$arrForm.add_point.value|default:0|n2s}-->
                        pt
                    </td>
                </tr>
            <!--{/if}-->
        </table>
        <!--▼お届け先情報ここから-->
        <a name="shipping"></a>
        <h2>お届け先情報
            <!--{if $tpl_shipping_quantity <= 1}-->
                <a class="btn-normal" href="javascript:;" onclick="fnCopyFromOrderData();">お客様情報へお届けする</a>
            <!--{/if}-->
            <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                <a class="btn-normal" href="javascript:;"  onclick="fnAppendShipping();">お届け先を新規追加</a>
                <a class="btn-normal" href="javascript:;" onclick="fnMultiple();">複数のお届け先を指定する</a>
            <!--{/if}-->
        </h2>

        <!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
            <!--{if $tpl_shipping_quantity > 1}-->
                <h3>お届け先<!--{$smarty.foreach.shipping.iteration}--></h3>
            <!--{/if}-->
            <!--{assign var=key value="shipping_id"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|default:"0"|h}-->" id="<!--{$key}-->_<!--{$shipping_index}-->" />
            <!--{if $tpl_shipping_quantity > 1}-->
                <h2>届け先商品情報&nbsp;<a class="btn-normal" href="javascript:;" name="add_product" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?order_id=<!--{$arrForm.order_id.value|h}-->&shipping_id=<!--{$shipping_index}-->', 'search', '615', '500', {menubar:'no'}); return false;">商品の追加</a>
                </h2>

                <!--{if count($arrShipping.shipment_product_class_id) > 0}-->
                    <table class="list order-edit-products">
                        <tr>
                            <th class="id">商品コード</th>
                            <th class="name">商品名/規格1/規格2</th>
                            <th class="price">単価</th>
                            <th class="qty">数量</th>
                        </tr>
                        <!--{section name=item loop=$arrShipping.shipment_product_class_id|@count}-->
                            <!--{assign var=item_index value="`$smarty.section.item.index`"}-->

                            <tr>
                                <td class="center">
                                    <!--{assign var=key value="shipment_product_class_id"}-->
                                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                                    <!--{assign var=key value="shipment_product_code"}-->
                                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                                    <!--{$arrShipping[$key][$item_index]|h}-->
                                </td>
                                <td class="center">
                                    <!--{assign var=key1 value="shipment_product_name"}-->
                                    <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                                    <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                                    <input type="hidden" name="<!--{$key1}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key1][$item_index]|h}-->" />
                                    <input type="hidden" name="<!--{$key2}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key2][$item_index]|h}-->" />
                                    <input type="hidden" name="<!--{$key3}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key3][$item_index]|h}-->" />
                                    <!--{$arrShipping[$key1][$item_index]|h}-->/<!--{$arrShipping[$key2][$item_index]|default:"(なし)"|h}-->/<!--{$arrShipping[$key3][$item_index]|default:"(なし)"|h}-->
                                    <br />
                                    <a class="btn-normal" href="javascript:;" name="change" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?no=<!--{$item_index}-->&amp;order_id=<!--{$arrForm.order_id.value|h}-->&amp;shipping_id=<!--{$shipping_index}-->', 'search', '615', '500', {menubar:'no'}); return false;">変更</a>
                                    <!--{if $arrShipping.shipment_product_class_id|@count > 1}-->
                                    <a class="btn-normal" href="javascript:;" name="delete" onclick="eccube.setValue('delete_no', <!--{$item_index}-->, 'form1'); eccube.setValue('select_shipping_id', <!--{$shipping_index}-->, 'form1'); eccube.setModeAndSubmit('delete_product','anchor_key','order_products'); return false;">削除</a>
                                    <!--{/if}-->
                                </td>
                                <td class="right">
                                    <!--{assign var=key value="shipment_price"}-->
                                    <!--{$arrShipping[$key][$item_index]|n2s}-->円
                                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                                </td>
                                <td class="center">
                                    <!--{assign var=key value="shipment_quantity"}-->
                                    <span class="attention"><!--{$arrErr[$key][$shipping_index][$item_index]}--></span>
                                    <input type="text" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" size="3" class="box3" maxlength="<!--{$arrForm[$key].length}-->" />
                                </td>
                            </tr>
                        <!--{/section}-->
                    </table>
                <!--{/if}-->
            <!--{else}-->
                <!-- 配送先が１つでも、shipment_itemを更新するために必要 -->

                <!--{section name=item loop=$arrShipping.shipment_product_class_id|@count}-->
                    <!--{assign var=item_index value="`$smarty.section.item.index`"}-->
                    <!--{assign var=key value="shipment_product_class_id"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                    <!--{assign var=key value="shipment_product_code"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                    <!--{assign var=key1 value="shipment_product_name"}-->
                    <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                    <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                    <input type="hidden" name="<!--{$key1}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key1][$item_index]|h}-->" />
                    <input type="hidden" name="<!--{$key2}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key2][$item_index]|h}-->" />
                    <input type="hidden" name="<!--{$key3}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key3][$item_index]|h}-->" />
                    <!--{assign var=key value="shipment_price"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                    <!--{assign var=key value="shipment_quantity"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                <!--{/section}-->
            <!--{/if}-->

            <table class="form">
                <tr>
                    <th>お名前</th>
                    <td>
                        <!--{assign var=key1 value="shipping_name01"}-->
                        <!--{assign var=key2 value="shipping_name02"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="15" class="box15" />
                        <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="15" class="box15" />
                    </td>
                </tr>
                <tr>
                    <th>お名前(フリガナ)</th>
                    <td>
                        <!--{assign var=key1 value="shipping_kana01"}-->
                        <!--{assign var=key2 value="shipping_kana02"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="15" class="box15" />
                        <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="15" class="box15" />
                    </td>
                </tr>
                <tr>
                    <th>会社名</th>
                    <td>
                        <!--{assign var=key1 value="shipping_company_name"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="30" class="box30" />
                    </td>
                </tr>
                <tr>
                    <th>TEL</th>
                    <td>
                        <!--{assign var=key1 value="shipping_tel01"}-->
                        <!--{assign var=key2 value="shipping_tel02"}-->
                        <!--{assign var=key3 value="shipping_tel03"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key3}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key3]|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                    </td>
                </tr>
                <tr>
                    <th>FAX</th>
                    <td>
                        <!--{assign var=key1 value="shipping_fax01"}-->
                        <!--{assign var=key2 value="shipping_fax02"}-->
                        <!--{assign var=key3 value="shipping_fax03"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key3}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key3]|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                    </td>
                </tr>
                <!--{assign var=key1 value="shipping_country_id"}-->
                <!--{assign var=key2 value="shipping_zipcode"}-->
                <!--{if !$smarty.const.FORM_COUNTRY_ENABLE}-->
                <input type="hidden" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$smarty.const.DEFAULT_COUNTRY_ID}-->" />
                <!--{else}-->
                <tr>
                    <th>国</th>
                    <td>
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <select name="<!--{$key1}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->">
                                <!--{html_options options=$arrCountry selected=$arrShipping[$key1]|default:$smarty.const.DEFAULT_COUNTRY_ID}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>ZIP CODE</th>
                    <td>
                        <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->;" size="15" class="box15"/>
                    </td>
                </tr>
                <!--{/if}-->
                <tr>
                    <th>住所</th>
                    <td>
                        <!--{assign var=key1 value="shipping_zip01"}-->
                        <!--{assign var=key2 value="shipping_zip02"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--><!--{$arrErr[$key2][$shipping_index]}--></span>
                        〒
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                        -
                        <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                        <a class="btn-normal" href="javascript:;" name="address_input" onclick="eccube.getAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01[<!--{$shipping_index}-->]', 'shipping_zip02[<!--{$shipping_index}-->]', 'shipping_pref[<!--{$shipping_index}-->]', 'shipping_addr01[<!--{$shipping_index}-->]'); return false;">住所入力</a><br />
                        <!--{assign var=key value="shipping_pref"}-->
                        <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                        <select class="top" name="<!--{$key}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->">
                            <option value="" selected="">都道府県を選択</option>
                            <!--{html_options options=$arrPref selected=$arrShipping[$key]}-->
                        </select><br />
                        <!--{assign var=key value="shipping_addr01"}-->
                        <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|h}-->" size="60" class="box60 top" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->" /><br />
                        <!--{assign var=key value="shipping_addr02"}-->
                        <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->" />
                    </td>
                </tr>
                <tr>
                    <th>お届け時間</th>
                    <td>
                        <!--{assign var=key value="time_id"}-->
                        <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                        <select name="<!--{$key}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->">
                            <option value="">指定無し</option>
                            <!--{html_options options=$arrDelivTime selected=$arrShipping[$key]}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>お届け日</th>
                    <td>
                        <!--{assign var=key1 value="shipping_date_year"}-->
                        <!--{assign var=key2 value="shipping_date_month"}-->
                        <!--{assign var=key3 value="shipping_date_day"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
                        <select name="<!--{$key1}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrYearShippingDate selected=$arrShipping[$key1]|default:""}-->
                        </select>年
                        <select name="<!--{$key2}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrMonthShippingDate selected=$arrShipping[$key2]|default:""}-->
                        </select>月
                        <select name="<!--{$key3}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key3][$shipping_index]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrDayShippingDate selected=$arrShipping[$key3]|default:""}-->
                        </select>日
                    </td>
                </tr>

            </table>
        <!--{/foreach}-->
        <!--▲お届け先情報ここまで-->

        <a name="deliv"></a>
        <table class="form">
            <tr>
                <th>配送業者<span class="attention"> *</span><br /><span class="attention">(配送業者の変更に伴う送料の変更は手動にてお願いします。)</span></th>
                <td>
                    <!--{assign var=key value="deliv_id"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="eccube.setModeAndSubmit('deliv','anchor_key','deliv');">
                        <option value="" selected="">選択してください</option>
                        <!--{html_options options=$arrDeliv selected=$arrForm[$key].value}-->
                    </select>
                </td>
            </tr>
            <tr>
                <th>お支払方法<span class="attention"> *</span><br /><span class="attention">(お支払方法の変更に伴う手数料の変更は手動にてお願いします。)</span></th>
                <td>
                    <!--{assign var=key value="payment_id"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="eccube.setModeAndSubmit('payment','anchor_key','deliv');">
                        <option value="" selected="">選択してください</option>
                        <!--{html_options options=$arrPayment selected=$arrForm[$key].value}-->
                    </select>
                </td>
            </tr>

            <!--{if $arrForm.payment_info|@count > 0}-->
            <tr>
                <th><!--{$arrForm.payment_type}-->情報</th>
                <td>
                    <!--{foreach key=key item=item from=$arrForm.payment_info}-->
                    <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
            <!--{/if}-->

            <tr>
                <th>メモ</th>
                <td>
                    <!--{assign var=key value="note"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area80" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
                </td>
            </tr>
        </table>
        <!--▲受注商品情報ここまで-->

        <div class="btn-area">
            <ul>
                <!--{if count($arrSearchHidden) > 0}-->
                <li><a class="btn-action" href="javascript:;" onclick="eccube.changeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->'); eccube.setModeAndSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
                <!--{/if}-->
                <li><a class="btn-action" href="javascript:;" onclick="return fnFormConfirm(); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            </ul>
        </div>
    </div>
    <div id="multiple"></div>
</form>
