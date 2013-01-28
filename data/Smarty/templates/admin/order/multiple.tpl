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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
$(function() {

    var product_class_id = window.opener.jQuery('input[id^=product_class_id]');
    var product_code = window.opener.jQuery('input[id^=product_code]');
    var product_name = window.opener.jQuery('input[id^=product_name]');
    var classcategory_name1 = window.opener.jQuery('input[id^=classcategory_name1]');
    var classcategory_name2 = window.opener.jQuery('input[id^=classcategory_name2]');
    var price = window.opener.jQuery('input[id^=price]');
    var quantity = window.opener.jQuery('input[id^=quantity]');
    var shipping_id = window.opener.jQuery('input[id^=shipping_id]');
    var shipping_name01 = window.opener.jQuery('input[name^=shipping_name01]');
    var shipping_name02 = window.opener.jQuery('input[name^=shipping_name02]');
    var shipping_pref = window.opener.jQuery('select[name^=shipping_pref] option:selected');
    var shipping_addr01 = window.opener.jQuery('input[name^=shipping_addr01]');
    var shipping_addr02 = window.opener.jQuery('input[name^=shipping_addr02]');

    // 都道府県の入力チェック
    shipping_pref.each(function() {
        if (!$(this).val()) {
            alert('<!--{t string="tpl_The delivery destination has not been entered._01"}-->');
            window.close();
            return;
        }
    });

    var index = 0;
    for (var i = 0; i < product_class_id.length; i++) {

        for (var j = 0; j < $(quantity[i]).val(); j++) {

            // 表示商品名
            var dispname = '';

            // 商品規格ID
            var idfield = $('<input type="hidden" name="multiple_product_class_id[' + index + ']" value="' + $(product_class_id[i]).val() + '" />"');

            // 商品コード
            var codefield = $('<input type="hidden" name="multiple_product_code['+ index + ']" value="' + $(product_code[i]).val() + '" />');

            // 商品名
            var namefield = $('<input type="hidden" name="multiple_product_name[' + index + ']" value="' + $(product_name[i]).val() + '" />');
            dispname = $(product_name[i]).val();

            // 規格1
            var class1field = $('<input type="hidden" name="multiple_classcategory_name1[' + index + ']" value="' + $(classcategory_name1[i]).val() + '" />');
            if ($(classcategory_name1[i]).val() != '') {
                dispname += '<br />' + $(classcategory_name1[i]).val();
            }

            // 規格2
            var class2field = $('<input type="hidden" name="multiple_classcategory_name2[' + index + ']" value="' + $(classcategory_name2[i]).val() + '" />');
            if ($(classcategory_name2[i]).val() != '') {
                dispname += '<br />' + $(classcategory_name2[i]).val();
            }

            // 単価
            var pricefield = $('<input type="hidden" name="multiple_price[' + index + ']" value="' + $(price[i]).val() + '" />');

            // 数量
            var qfield = $('<input type="text" name="multiple_quantity[' + index + ']" size="4" value="1" />');

            // 数量と hidden を設定
            var q = $('<td />').addClass('center')
                .append(idfield)
                .append(codefield)
                .append(namefield)
                .append(class1field)
                .append(class2field)
                .append(pricefield)
                .append(qfield);

            // お届け先
            var select = $('<select name="multiple_shipping_id[' + index + ']" />');
            var s = $('<td />').append(select);

            // 行を生成
            var tr = $('<tr />')
                .append($('<td />').text($(product_code[i]).val()))
                .append($('<td />').html(dispname))
                .append(q)
                .append(s);

            jQuery(tr).appendTo('tbody');
            index++;
        }
    }

    // プルダウンを生成
    for (var i = 0; i < shipping_id.length; i++) {
        var text = $(shipping_name01[i]).val() + $(shipping_name02[i]).val()
            + ' ' + $(shipping_pref[i]).text()
            + $(shipping_addr01[i]).val() + $(shipping_addr02[i]).val();

        var option = $('<option value="' + $(shipping_id[i]).val() + '">' + text + '</option>');
        $('select').append(option);
    }
});

function func_submit() {
    var err_text = '';
    var fm = window.opener.document.form1;

    fm.mode.value = 'multiple_set_to';
    fm.anchor_key.value = 'shipping';

    var div = $('<div />');
    $('input[name^=multiple_], select[name^=multiple_]').each(function() {
        // TODO タグをベタ書きにしないと, innerHTML で value が空になってしまう
        $(div).append('<input type="hidden" name="'
            + $(this).attr('name')
            + '" value="' + $(this).val() + '" />');
    });

    // window.opener に対する append は IE で動作しない
    window.opener.jQuery('#multiple').html(div.html());
    fm.submit();
    window.close();
    return true;
}
//-->
</script>

<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input name="mode" type="hidden" value="search" />
<input name="anchor_key" type="hidden" value="" />
<input name="search_pageno" type="hidden" value="" />
<input name="no" type="hidden" value="<!--{$tpl_no}-->" />
<table summary="Delivery information" class="list">
    <thead>
        <tr>
            <th><!--{t string="tpl_Product code_01"}--></th>
            <th><!--{t string="tpl_Product name_01"}-->/<!--{t string="tpl_Standard 1_01"}-->/<!--{t string="tpl_Standard 2_01"}--></th>
            <th><!--{t string="tpl_Quantity_01"}--></th>
            <th><!--{t string="tpl_Delivery destination_01"}--></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="btn-area">
    <ul>
        <li><a class="btn-action" href="javascript:;" onclick="func_submit(); return false;"><span class="btn-next"><!--{t string="tpl_Confirm_01"}--></span></a></li>
    </ul>
</div>
</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
