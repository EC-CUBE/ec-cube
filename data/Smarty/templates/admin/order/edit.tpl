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
        document.form1.action = '<!--{$smarty.const.URL_PATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/edit.php';
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

    function fnFormConfirm() {
        if (fnConfirm()) {
            document.form1.submit();
        }
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

    <!--▼お客様情報ここから-->
    <table class="form">
        <!--{if $tpl_mode != 'add'}-->
        <tr>
            <th>帳票出力</th>
            <td><a class="btn-normal" href="javascript:;" onclick="win02('pdf.php?order_id=<!--{$arrForm.order_id.value}-->','pdf','1000','800'); return false;">帳票出力</a></td>
        </tr>
        <!--{/if}-->
        <tr>
            <th>注文番号</th>
            <td><!--{$tpl_order_id|h}--></td>
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
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/foreach}-->
    <h2>お客様情報
        <!--{if $tpl_mode == 'add'}-->
            <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnOpenWindow('<!--{$smarty.const.HTTP_URL}--><!--{$smarty.const.ADMIN_DIR}-->customer/search_customer.php','search','600','650'); return false;">顧客検索</a>
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
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>顧客名(カナ)</th>
            <td>
                <!--{assign var=key1 value="order_kana01"}-->
                <!--{assign var=key2 value="order_kana02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
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
            <th>住所</th>
            <td>
                <!--{assign var=key1 value="order_zip01"}-->
                <!--{assign var=key2 value="order_zip02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                〒
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                 -
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URL_PATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01');">住所入力</a><br />
                <!--{assign var=key value="order_pref"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="">都道府県を選択</option>
                    <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                </select><br />
                <!--{assign var=key value="order_addr01"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
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

    <!--▼受注商品情報ここから-->
    <h2 id="order_products">
        <a name="order_products">受注商品情報</a>
        <a class="btn-normal" href="javascript:;" name="cheek" onclick="fnModeSubmit('cheek','anchor_key','order_products');">計算結果の確認</a>
        <a class="btn-normal" href="javascript:;" name="add_product" onclick="win03('<!--{$smarty.const.URL_PATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php<!--{if $tpl_order_id}-->?order_id=<!--{$tpl_order_id}--><!--{/if}-->', 'search', '600', '500'); ">商品の追加</a>
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
                <!--{$arrForm.product_code.value[$key]|h}-->
                <input type="hidden" name="product_code[<!--{$key}-->]" value="<!--{$arrForm.product_code.value[$key]}-->" id="product_code_<!--{$key}-->" />
            </td>
            <td>
                <!--{$arrForm.product_name.value[$key]|h}-->/<!--{$arrForm.classcategory_name1.value[$key]|default:"(なし)"|h}-->/<!--{$arrForm.classcategory_name2.value[$key]|default:"(なし)"|h}-->
                <input type="hidden" name="product_name[<!--{$key}-->]" value="<!--{$arrForm.product_name.value[$key]}-->" id="product_name_<!--{$key}-->" />
                <input type="hidden" name="classcategory_name1[<!--{$key}-->]" value="<!--{$arrForm.classcategory_name1.value[$key]}-->" id="classcategory_name1_<!--{$key}-->" />
                <input type="hidden" name="classcategory_name2[<!--{$key}-->]" value="<!--{$arrForm.classcategory_name2.value[$key]}-->" id="classcategory_name2_<!--{$key}-->" />
                <br />
                <a class="btn-normal" href="javascript:;" name="change" onclick="win03('<!--{$smarty.const.URL_PATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?no=<!--{$key}--><!--{if $tpl_order_id}-->&order_id=<!--{$tpl_order_id}--><!--{/if}-->', 'search', '600', '500');">変更</a>
                <!--{if $product_count > 1}-->
                    <a class="btn-normal" href="javascript:;" name="delete" onclick="fnSetFormVal('form1', 'delete_no', <!--{$key}-->); fnModeSubmit('delete_product','anchor_key','order_products');">削除</a>
                <!--{/if}-->
            <input type="hidden" name="product_type_id[<!--{$key}-->]" value="<!--{$arrForm.product_type_id.value[$key]}-->" id="product_type_id_<!--{$key}-->" />
            <input type="hidden" name="product_id[<!--{$key}-->]" value="<!--{$arrForm.product_id.value[$key]}-->" id="product_id_<!--{$key}-->" />
            <input type="hidden" name="product_class_id[<!--{$key}-->]" value="<!--{$arrForm.product_class_id.value[$key]}-->" id="product_class_id_<!--{$key}-->" />
            <input type="hidden" name="point_rate[<!--{$key}-->]" value="<!--{$arrForm.point_rate.value[$key]}-->" id="point_rate_<!--{$key}-->" />
            </td>
            <td align="center"><input type="text" name="price[<!--{$key}-->]" value="<!--{$arrForm.price.value[$key]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm.price.length}-->" id="price_<!--{$key}-->"/> 円</td>
            <td align="center"><input type="text" name="quantity[<!--{$key}-->]" value="<!--{$arrForm.quantity.value[$key]|h}-->" size="3" class="box3" maxlength="<!--{$arrForm.quantity.length}-->"/></td>
            <!--{assign var=price value=`$arrForm.price.value[$key]`}-->
            <!--{assign var=quantity value=`$arrForm.quantity.value[$key]`}-->
            <td class="right"><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</td>
            <td class="right"><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
        </tr>
        <!--{/section}-->
        <tr>
            <th colspan="5" class="column right">小計</th>
            <td class="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
        </tr>
        <tr>
            <th colspan="5" class="column right">値引</th>
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
                <!--{$arrForm.total.value|number_format}--> 円
            </td>
        </tr>
        <tr>
            <th colspan="5" class="column right">お支払い合計</th>
            <td class="right">
                <span class="attention"><!--{$arrErr.payment_total}--></span>
                <!--{$arrForm.payment_total.value|number_format}-->
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
                    <!--{$arrForm.birth_point.value|number_format}-->
                     pt
                </td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="column right">加算ポイント</th>
                <td class="right">
                    <!--{$arrForm.add_point.value|number_format|default:0}-->
                     pt
                </td>
            </tr>
        <!--{/if}-->
    </table>

    <!--▼お届け先情報ここから-->
    <h2>お届け先情報
        <a class="btn-normal" href="javascript:;" name="input_from_order_data" onclick="fnCopyFromOrderData();">お客様情報へお届けする</a>
        <a class="btn-normal" href="javascript:;" name="input_from_order_data" onclick="fnCopyFromOrderData();">お届け先を新規追加</a>
        <a class="btn-normal" href="javascript:;" name="input_from_order_data" onclick="fnCopyFromOrderData();">複数のお届け先を指定する</a>
    </h2>

    <!--{assign var=key value="shipping_quantity"}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />

    <!--{section name=shipping loop=$arrForm.shipping_quantity.value}-->
    <!--{assign var=shipping_index value="`$smarty.section.shipping.index`"}-->

    <!--{assign var=shipping_id value=$arrShippingIds[$shipping_index]}-->
    <!--{if $arrForm.shipping_quantity.value > 1}-->
    <h3>お届け先<!--{$smarty.section.shipping.iteration}--></h3
    <!--{/if}-->

    <!--{if $arrForm.shipping_quantity.value > 1}-->
    <!--{assign var=product_quantity value="shipping_product_quantity_`$shipping_id`"}-->
    <input type="hidden" name="<!--{$product_quantity}-->" value="<!--{$arrForm[$product_quantity].value|h}-->" />
    <table class="list" id="order-edit-products">
      <tr>
        <th class="id">商品コード</th>
        <th class="name">商品名/規格1/規格2</th>
        <th class="price">単価</th>
        <th class="qty">数量</th>
      </tr>
      <!--{section name=item loop=$arrForm[$product_quantity].value}-->
      <!--{assign var=item_index value="`$smarty.section.item.index`"}-->
      <!--{assign var=product_class_id value=$arrProductClassIds[$shipping_index][$item_index]}-->
      <tr>
        <td>
          <!--{assign var=key value="product_code_`$shipping_id`_`$product_class_id`"}-->
          <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
          <!--{$arrForm[$key].value|h}-->
        </td>
        <td>
          <!--{assign var=key1 value="product_name_`$shipping_id`_`$product_class_id`"}-->
          <!--{assign var=key2 value="classcategory_name1_`$shipping_id`_`$product_class_id`"}-->
          <!--{assign var=key3 value="classcategory_name2_`$shipping_id`_`$product_class_id`"}-->
          <input type="hidden" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" />
          <input type="hidden" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" />
          <input type="hidden" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" />
          <!--{$arrForm[$key1].value|h}-->/<!--{$arrForm[$key2].value|default:"(なし)"|h}-->/<!--{$arrForm[$key3].value|default:"(なし)"|h}-->
        </td>
        <td class="right">
          <!--{assign var=key value="price_`$shipping_id`_`$product_class_id`"}-->
          <!--{$arrForm[$key].value|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
          <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
        </td>
        <td class="right">
          <!--{assign var=key value="quantity_`$shipping_id`_`$product_class_id`"}-->
          <!--{$arrForm[$key].value|h}-->
          <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
        </td>
      </tr>
      <!--{/section}-->
    </table>
    <!--{/if}-->

    <table class="form">
        <tr>
            <th>お名前</th>
            <td>
                <!--{assign var=key1 value="shipping_name01_`$shipping_id`"}-->
                <!--{assign var=key2 value="shipping_name02_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>お名前(カナ)</th>
            <td>
                <!--{assign var=key1 value="shipping_kana01_`$shipping_id`"}-->
                <!--{assign var=key2 value="shipping_kana02_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th>TEL</th>
            <td>
                <!--{assign var=key1 value="shipping_tel01_`$shipping_id`"}-->
                <!--{assign var=key2 value="shipping_tel02_`$shipping_id`"}-->
                <!--{assign var=key3 value="shipping_tel03_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th>住所</th>
            <td>
                <!--{assign var=key1 value="shipping_zip01_`$shipping_id`"}-->
                <!--{assign var=key2 value="shipping_zip02_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                〒
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                 -
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URL_PATH}-->', 'shipping_zip01', 'shipping_zip02', 'shipping_pref', 'shipping_addr01');">住所入力</a><br />
                <!--{assign var=key value="shipping_pref_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="">都道府県を選択</option>
                    <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                </select><br />
                <!--{assign var=key value="shipping_addr01_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
                <!--{assign var=key value="shipping_addr02_`$shipping_id`"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th>お届け時間</th>
            <td>
                <!--{assign var=key value="time_id_`$shipping_id`"}-->
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
              <!--{assign var=key1 value="shipping_date_year_`$shipping_id`"}-->
              <!--{assign var=key2 value="shipping_date_month_`$shipping_id`"}-->
              <!--{assign var=key3 value="shipping_date_day_`$shipping_id`"}-->
              <span class="attention"><!--{$arrErr[$key1]}--></span>
              <span class="attention"><!--{$arrErr[$key2]}--></span>
              <span class="attention"><!--{$arrErr[$key3]}--></span>
              <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
              <!--{html_options options=$arrYearShippingDate selected=$arrForm[$key1].value|default:""}-->
              </select>年
              <select name="<!--{$key2}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->">
              <!--{html_options options=$arrMonthShippingDate selected=$arrForm[$key2].value|default:""}-->
              </select>月
              <select name="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
              <!--{html_options options=$arrDayShippingDate selected=$arrForm[$key3].value|default:""}-->
              </select>日
            </td>
        </tr>

    </table>
    <!--{/section}-->
    <!--▲お届け先情報ここまで-->

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
            <th>メモ</th>
            <td>
                <!--{assign var=key value="note"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area80" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea></td>
            </td>
        </tr>
    </table>
    <!--▲受注商品情報ここまで-->

    <div class="btn-area">
      <ul>
        <!--{if count($arrSearchHidden) > 0}-->
        <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URL_PATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--{/if}-->
        <li><a class="btn-action" href="javascript:;" onclick="return fnFormConfirm();"><span class="btn-next">この内容で登録する</span></a></li>
      </ul>
    </div>
  </div>
</form>
