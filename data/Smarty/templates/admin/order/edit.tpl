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
<script type="text/javascript">
<!--
    function fnEdit(customer_id) {
        document.form1.action = '<!--{$smarty.const.URL_DIR}-->admin/customer/edit.php';
        document.form1.mode.value = "edit"
        document.form1['edit_customer_id'].value = customer_id;
        document.form1.submit();
        return false;
    }

    function fnCopyFromOrderData() {
        df = document.form1;

        df.deliv_name01.value = df.order_name01.value;
        df.deliv_name02.value = df.order_name02.value;
        df.deliv_kana01.value = df.order_kana01.value;
        df.deliv_kana02.value = df.order_kana02.value;
        df.deliv_zip01.value = df.order_zip01.value;
        df.deliv_zip02.value = df.order_zip02.value;
        df.deliv_tel01.value = df.order_tel01.value;
        df.deliv_tel02.value = df.order_tel02.value;
        df.deliv_tel03.value = df.order_tel03.value;
        df.deliv_pref.value = df.order_pref.value;
        df.deliv_addr01.value = df.order_addr01.value;
        df.deliv_addr02.value = df.order_addr02.value;
    }

//-->
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode|default:"edit"}-->" />
<input type="hidden" name="order_id" value="<!--{$tpl_order_id}-->" />
<input type="hidden" name="edit_customer_id" value="" />
<input type="hidden" name="anchor_key" value="" />
<input type="hidden" id="add_product_id" name="add_product_id" value="" />
<input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
<input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
<input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
<input type="hidden" id="no" name="no" value="" />
<input type="hidden" id="delete_no" name="delete_no" value="" />

<div id="order" class="contents-main">
    <!--{* ペイジェントモジュール連携用 *}-->
    <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_paygent/paygent_order.tpl}-->
    <!--{if file_exists($path)}-->
        <!--{include file=$path}-->
    <!--{/if}-->

                        <!--{* GMOPG連携用 *}-->
                        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_gmopg/templates/order_edit.tpl}-->
                        <!--{if file_exists($path)}-->
                            <!--{include file=$path}-->
                        <!--{/if}-->

                        <!--{* SPS連携用 *}-->
                        <!--{assign var=sps_path value=`$smarty.const.MODULE_PATH`mdl_sps/templates/sps_request.tpl}-->
                        <!--{if file_exists($sps_path) && $paymentType[0].module_code == $smarty.const.MDL_SPS_CODE}-->
                            <!--{include file=$sps_path}-->
                        <!--{/if}-->

                        <!--{* ペイジェントモジュール連携用 *}-->
                        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_paygent/paygent_order.tpl}-->
                        <!--{if file_exists($path)}-->
                            <!--{include file=$path}-->
                        <!--{/if}-->

    <!--▼お客様情報ここから-->
    <table class="form">
        <!--{if $tpl_mode != 'add'}-->
        <tr>
            <th>帳票出力</th>
            <td><a href="./" onClick="win02('pdf.php?order_id=<!--{$arrForm.order_id.value}-->','pdf','1000','800'); return false;">帳票を出力するにはこちらをクリックして下さい。</a></td>
        </tr>
        <!--{/if}-->
        <tr>
            <th>注文番号</th>
            <td><!--{$arrForm.order_id.value}--></td>
        </tr>
        <tr>
            <th>受注日</th>
            <td><!--{$arrForm.create_date.value|sfDispDBDate}--></td>
            <input type="hidden" name="create_date" value="<!--{$arrForm.create_date.value}-->" />
        </tr>
        <tr>
            <th>対応状況</th>
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
            <td><!--{$arrForm.payment_date.value|sfDispDBDate|default:"未入金"}--></td>
        </tr>
        <tr>
            <th>発送日</th>
            <td><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"}--></td>
        </tr>
    </table>

    <!--{foreach key=key item=item from=$arrSearchHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
    <!--{/foreach}-->
    <h2>お客様情報
        <!--{if $tpl_mode == 'add'}-->
            <input type="button" name="address_input" value="顧客検索" onclick="fnOpenWindow('<!--{$smarty.const.SITE_URL}-->admin/customer/search_customer.php','search','500','650'); return false;" />
        <!--{/if}-->
    </h2>
    <table class="form">
        <tr>
            <th>顧客ID</th>
            <td>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <!--{$arrForm.customer_id.value}-->
                    <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id.value}-->" />
                <!--{else}-->
                   (非会員)
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>顧客名</th>
            <td>
                <!--{assign var=key1 value="order_name01"}-->
                <!--{assign var=key2 value="order_name02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>顧客名(カナ)</th>
            <td>
                <!--{assign var=key1 value="order_kana01"}-->
                <!--{assign var=key2 value="order_kana02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>
                <!--{assign var=key1 value="order_email"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="30" class="box30" />
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
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th>住所</th>
            <td>
                <!--{assign var=key1 value="order_zip01"}-->
                <!--{assign var=key2 value="order_zip02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                〒
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                 -
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                <input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01');" /><br />
                <!--{assign var=key value="order_pref"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="">都道府県を選択</option>
                    <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                </select><br />
                <!--{assign var=key value="order_addr01"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
                <!--{assign var=key value="order_addr02"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th>備考</th>
            <td><!--{$arrForm.message.value|escape|nl2br}--></td>
        </tr>
        <tr>
            <th>現在ポイント</th>
            <td>
                <!--{if $arrForm.customer_id > 0}-->
                    <!--{$arrForm.point.value|number_format}-->
                     pt
                <!--{else}-->
                    (非会員)
            <!--{/if}-->
            </td>
        </tr>
    </table>
    <!--▲お客様情報ここまで-->

    <!--▼お届け先情報ここから-->
    <h2>お届け先情報
        <input type="button" name="input_from_order_data" value="上記お客様情報をコピー" onclick="fnCopyFromOrderData();" />
    </h2>
    <table class="form">
        <tr>
            <th>お名前</th>
            <td>
                <!--{assign var=key1 value="deliv_name01"}-->
                <!--{assign var=key2 value="deliv_name02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>お名前(カナ)</th>
            <td>
                <!--{assign var=key1 value="deliv_kana01"}-->
                <!--{assign var=key2 value="deliv_kana02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>TEL</th>
            <td>
                <!--{assign var=key1 value="deliv_tel01"}-->
                <!--{assign var=key2 value="deliv_tel02"}-->
                <!--{assign var=key3 value="deliv_tel03"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th>住所</th>
            <td>
                <!--{assign var=key1 value="deliv_zip01"}-->
                <!--{assign var=key2 value="deliv_zip02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                〒
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                 -
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                <input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'deliv_zip01', 'deliv_zip02', 'deliv_pref', 'deliv_addr01');" /><br />
                <!--{assign var=key value="deliv_pref"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="">都道府県を選択</option>
                    <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                </select><br />
                <!--{assign var=key value="deliv_addr01"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
                <!--{assign var=key value="deliv_addr02"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            </td>
        </tr>
    </table>
    <!--▲お届け先情報ここまで-->

    <!--▼受注商品情報ここから-->
    <h2 id="order_products">
        <a name="order_products">受注商品情報</a>
        <input type="button" name="cheek" value="計算結果の確認" onclick="fnModeSubmit('cheek','anchor_key','order_products');" />
        <input type="button" name="add_product" value="商品の追加" onclick="win03('<!--{$smarty.const.URL_DIR}-->admin/order/product_select.php<!--{if $tpl_order_id}-->?order_id=<!--{$tpl_order_id}--><!--{/if}-->', 'search', '500', '500'); " />
    </h2>
    <!--{if $arrErr.product_id || $arrErr.quantity || $arrErr.price}-->
        <span class="attention"><!--{$arrErr.product_id}--></span>
        <span class="attention"><!--{$arrErr.quantity}--></span>
        <span class="attention"><!--{$arrErr.price}--></span>
    <!--{/if}-->
    <table class="list" id="order-edit-products">
        <tr>
            <th class="id">商品コード</th>
            <th class="name">商品名/規格1/規格2</th>
            <th class="price">単価</th>
            <th class="qty">数量</th>
            <th class="price">税込み価格</th>
            <th class="price">小計</th>
        </tr>
        <!--{section name=cnt loop=$arrForm.quantity.value}-->
        <!--{assign var=key value="`$smarty.section.cnt.index`"}-->
        <tr>
            <td>
                <!--{$arrForm.product_code.value[$key]|escape}-->
                <input type="hidden" name="product_code[<!--{$key}-->]" value="<!--{$arrForm.product_code.value[$key]}-->" id="product_code_<!--{$key}-->" />
            </td>
            <td>
                <!--{$arrForm.product_name.value[$key]|escape}-->/<!--{$arrForm.classcategory_name1.value[$key]|escape|default:"(なし)"}-->/<!--{$arrForm.classcategory_name2.value[$key]|escape|default:"(なし)"}-->
                <input type="hidden" name="product_name[<!--{$key}-->]" value="<!--{$arrForm.product_name.value[$key]}-->" id="product_name_<!--{$key}-->" />
                <input type="hidden" name="classcategory_name1[<!--{$key}-->]" value="<!--{$arrForm.classcategory_name1.value[$key]}-->" id="classcategory_name1_<!--{$key}-->" />
                <input type="hidden" name="classcategory_name2[<!--{$key}-->]" value="<!--{$arrForm.classcategory_name2.value[$key]}-->" id="classcategory_name2_<!--{$key}-->" />
                <br />
                <input type="button" name="change" value="変更" onclick="win03('<!--{$smarty.const.URL_DIR}-->admin/order/product_select.php?no=<!--{$key}--><!--{if $tpl_order_id}-->&order_id=<!--{$tpl_order_id}--><!--{/if}-->', 'search', '500', '500'); " />
                <!--{if $product_count > 1}-->
                    <input type="button" name="delete" value="削除" onclick="fnSetFormVal('form1', 'delete_no', <!--{$key}-->); fnModeSubmit('delete_product','anchor_key','order_products');" />
                <!--{/if}-->
            <input type="hidden" name="product_id[<!--{$key}-->]" value="<!--{$arrForm.product_id.value[$key]}-->" id="product_id_<!--{$key}-->" />
            <input type="hidden" name="product_class_id[<!--{$key}-->]" value="<!--{$arrForm.product_class_id.value[$key]}-->" id="product_class_id_<!--{$key}-->" />
            <input type="hidden" name="point_rate[<!--{$key}-->]" value="<!--{$arrForm.point_rate.value[$key]}-->" id="point_rate_<!--{$key}-->" />
            </td>
            <td align="center"><input type="text" name="price[<!--{$key}-->]" value="<!--{$arrForm.price.value[$key]|escape}-->" size="6" class="box6" maxlength="<!--{$arrForm.price.length}-->" id="price_<!--{$key}-->"/> 円</td>
            <td align="center"><input type="text" name="quantity[<!--{$key}-->]" value="<!--{$arrForm.quantity.value[$key]|escape}-->" size="3" class="box3" maxlength="<!--{$arrForm.quantity.length}-->"/></td>
            <!--{assign var=price value=`$arrForm.price.value[$key]`}-->
            <!--{assign var=quantity value=`$arrForm.quantity.value[$key]`}-->
            <td><!--{$price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</td>
            <td><!--{$price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
        </tr>
        <!--{/section}-->
        <tr>
            <th colspan="5" class="right">小計</th>
            <td class="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
        </tr>
        <tr>
            <th colspan="5" class="right">値引</th>
            <td class="right">
                <!--{assign var=key value="discount"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                 円
            </td>
        </tr>
        <tr>
            <th colspan="5" class="right">送料</th>
            <td class="right">
                <!--{assign var=key value="deliv_fee"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                 円
            </td>
        </tr>
        <tr>
            <th colspan="5" class="right">手数料</th>
            <td class="right">
                <!--{assign var=key value="charge"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                 円
            </td>
        </tr>
        <tr>
            <th colspan="5" class="right">合計</th>
            <td class="right">
                <span class="attention"><!--{$arrErr.total}--></span>
                <!--{$arrForm.total.value|number_format}--> 円
            </td>
        </tr>
        <tr>
            <th colspan="5" class="right">お支払い合計</th>
            <td class="right">
                <span class="attention"><!--{$arrErr.payment_total}--></span>
                <!--{$arrForm.payment_total.value|number_format}-->
                 円
            </td>
        </tr>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th colspan="5" class="right">使用ポイント</th>
                <td class="right">
                    <!--{assign var=key value="use_point"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape|default:0}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                     pt
                </td>
            </tr>
            <!--{if $arrForm.birth_point.value > 0}-->
            <tr>
                <th colspan="5" class="right">お誕生日ポイント</th>
                <td class="right">
                    <!--{$arrForm.birth_point.value|number_format}-->
                     pt
                </td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="right">加算ポイント</th>
                <td class="right">
                    <!--{$arrForm.add_point.value|number_format|default:0}-->
                     pt
                </td>
            </tr>
        <!--{/if}-->
    </table>

    <table class="form">
        <tr>
            <th>お支払方法<br /><span class="attention">(お支払方法の変更に伴う手数料の変更は手動にてお願いします。)</span></th>
            <td>
                <!--{assign var=key value="payment_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('payment','anchor_key','order_products');">
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
            <th>お届け時間</th>
            <td>
                <!--{assign var=key value="deliv_time_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="0">指定無し</option>
                    <!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>お届け日</th>
            <td>
                <!--{assign var=key value="deliv_date"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input
                    name="<!--{$key|escape}-->"
                    value="<!--{$arrForm[$key].value|escape}-->"
                    style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
                    size="30"
                    maxlength="<!--{$arrForm[$key].length}-->"
                >
            </td>
        </tr>
        <tr>
            <th>メモ</th>
            <td>
                <!--{assign var=key value="note"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area80" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea></td>
            </td>
        </tr>
    </table>
    <!--▲受注商品情報ここまで-->

    <div class="btn">
        <!--{if count($arrSearchHidden) > 0}-->
        <button type="button" onclick="fnChangeAction('<!--{$smarty.const.URL_SEARCH_ORDER}-->'); fnModeSubmit('search','',''); return false;"><span>検索画面に戻る</span></button>
        <!--{/if}-->
        <button type="submit" onclick="return fnConfirm();"><span>この内容で登録する</span></button>
    </div>

</div>
</form>
