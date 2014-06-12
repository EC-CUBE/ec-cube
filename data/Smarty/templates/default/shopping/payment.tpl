<!--{*
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
 *}-->

<script type="text/javascript">//<![CDATA[
    $(function() {
        if ($('input[name=deliv_id]:checked').val()
            || $('#deliv_id').val()) {
            showForm(true);
        } else {
            showForm(false);
        }
        $('input[id^=deliv_]').click(function() {
            showForm(true);
            var data = {};
            data.mode = 'select_deliv';
            data.deliv_id = $(this).val();
            data['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = '<!--{$transactionid}-->';
            $.ajax({
                type : 'POST',
                url : location.pathname,
                data: data,
                cache : false,
                dataType : 'json',
                error : remoteException,
                success : function(data, dataType) {
                    if (data.error) {
                        remoteException();
                    } else {
                        // 支払い方法の行を生成
                        var payment_tbody = $('#payment tbody');
                        payment_tbody.empty();
                        for (var i in data.arrPayment) {
                            // ラジオボタン
                            <!--{* IE7未満対応のため name と id をベタ書きする *}-->
                            var radio = $('<input type="radio" name="payment_id" id="pay_' + i + '" />')
                                .val(data.arrPayment[i].payment_id);
                            // ラベル
                            var label = $('<label />')
                                .attr('for', 'pay_' + i)
                                .text(data.arrPayment[i].payment_method);
                            // 行
                            var tr = $('<tr />')
                                .append($('<td />')
                                    .addClass('alignC')
                                    .append(radio))
                                .append($('<td />').append(label));

                            // 支払方法の画像が登録されている場合は表示
                            if (data.img_show) {
                                var payment_image = data.arrPayment[i].payment_image;
                                $('th#payment_method').attr('colspan', 3);
                                if (payment_image) {
                                    var img = $('<img />').attr('src', '<!--{$smarty.const.IMAGE_SAVE_URLPATH}-->' + payment_image);
                                    tr.append($('<td />').append(img));
                                } else {
                                    tr.append($('<td />'));
                                }
                            } else {
                                $('th#payment_method').attr('colspan', 2);
                            }

                            tr.appendTo(payment_tbody);
                        }
                        // お届け時間を生成
                        var deliv_time_id_select = $('select[id^=deliv_time_id]');
                        deliv_time_id_select.empty();
                        deliv_time_id_select.append($('<option />').text('指定なし').val(''));
                        for (var i in data.arrDelivTime) {
                            var option = $('<option />')
                                .val(i)
                                .text(data.arrDelivTime[i])
                                .appendTo(deliv_time_id_select);
                        }
                    }
                }
            });
        });

        /**
         * 通信エラー表示.
         */
        function remoteException(XMLHttpRequest, textStatus, errorThrown) {
            alert('通信中にエラーが発生しました。カート画面に移動します。');
            location.href = '<!--{$smarty.const.CART_URL}-->';
        }

        /**
         * 配送方法の選択状態により表示を切り替える
         */
        function showForm(show) {
            if (show) {
                $('#payment, div.delivdate, .select-msg').show();
                $('.non-select-msg').hide();
            } else {
                $('#payment, div.delivdate, .select-msg').hide();
                $('.non-select-msg').show();
            }
        }
    });
//]]></script>
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area">
            <img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_02.jpg" alt="購入手続きの流れ" />
        </p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

            <!--{assign var=key value="deliv_id"}-->
            <!--{if $is_single_deliv}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" id="deliv_id" />
            <!--{else}-->
            <div class="pay_area">
                <h3>配送方法の指定</h3>
                <p>配送方法をご選択ください。</p>

                <!--{if $arrErr[$key] != ""}-->
                <p class="attention"><!--{$arrErr[$key]}--></p>
                <!--{/if}-->
                <table summary="配送方法選択">
                    <col width="20%" />
                    <col width="80%" />
                    <tr>
                        <th class="alignC">選択</th>
                        <th class="alignC" colspan="2">配送方法</th>
                    </tr>
                    <!--{section name=cnt loop=$arrDeliv}-->
                    <tr>
                        <td class="alignC"><input type="radio" id="deliv_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->" value="<!--{$arrDeliv[cnt].deliv_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrDeliv[cnt].deliv_id|sfGetChecked:$arrForm[$key].value}--> />
                        </td>
                        <td>
                            <label for="deliv_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrDeliv[cnt].name|h}--><!--{if $arrDeliv[cnt].remark != ""}--><p><!--{$arrDeliv[cnt].remark|h|nl2br}--></p><!--{/if}--></label>
                        </td>
                    </tr>
                    <!--{/section}-->
                </table>
            </div>
            <!--{/if}-->

            <div class="pay_area">
                <h3>お支払方法の指定</h3>
                <p class="select-msg">お支払方法をご選択ください。</p>
                <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>

                <!--{assign var=key value="payment_id"}-->
                <!--{if $arrErr[$key] != ""}-->
                <p class="attention"><!--{$arrErr[$key]}--></p>
                <!--{/if}-->
                <table summary="お支払方法選択" id="payment">
                    <col width="20%" />
                    <col width="80%" />
                    <thead>
                        <tr>
                            <th class="alignC">選択</th>
                            <th class="alignC" colspan="<!--{if !$img_show}-->2<!--{else}-->3<!--{/if}-->" id="payment_method">お支払方法</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--{section name=cnt loop=$arrPayment}-->
                            <tr>
                            <td class="alignC"><input type="radio" id="pay_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->"  value="<!--{$arrPayment[cnt].payment_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}--> /></td>
                            <td>
                                <label for="pay_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrPayment[cnt].payment_method|h}--><!--{if $arrPayment[cnt].note != ""}--><!--{/if}--></label>
                            </td>
                            <!--{if $img_show}-->
                                <td>
                                    <!--{if $arrPayment[cnt].payment_image != ""}-->
                                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrPayment[cnt].payment_image}-->" />
                                    <!--{/if}-->
                                </td>
                            <!--{/if}-->
                            </tr>
                        <!--{/section}-->
                    </tbody>
                </table>
            </div>

            <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <div class="pay_area02">
                <h3>お届け時間の指定</h3>
                <p class="select-msg">ご希望の方は、お届け時間を選択してください。</p>
                <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>
                <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
                <!--{assign var=index value=$shippingItem.shipping_id}-->
                <div class="delivdate top">
                    <!--{if $is_multiple}-->
                        <span class="st">▼<!--{$shippingItem.shipping_name01}--><!--{$shippingItem.shipping_name02}-->
                        <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}--></span><br/>
                    <!--{/if}-->
                    <!--★お届け日★-->
                    <!--{assign var=key value="deliv_date`$index`"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    お届け日：
                    <!--{if !$arrDelivDate}-->
                        ご指定頂けません。
                    <!--{else}-->
                        <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{assign var=shipping_date_value value=$arrForm[$key].value|default:$shippingItem.shipping_date}-->
                            <!--{html_options options=$arrDelivDate selected=$shipping_date_value}-->
                        </select>&nbsp;
                    <!--{/if}-->
                    <!--★お届け時間★-->
                    <!--{assign var=key value="deliv_time_id`$index`"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    お届け時間：
                    <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="" selected="">指定なし</option>
                        <!--{assign var=shipping_time_value value=$arrForm[$key].value|default:$shippingItem.time_id}-->
                        <!--{html_options options=$arrDelivTime selected=$shipping_time_value}-->
                    </select>
                </div>
                <!--{/foreach}-->
            </div>
            <!--{/if}-->

            <!-- ▼ポイント使用 -->
            <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
                <div class="point_area">
                    <h3>ポイント使用の指定</h3>
                        <p><span class="attention">1ポイントを<!--{$smarty.const.POINT_VALUE|n2s}-->円</span>として使用する事ができます。<br />
                            使用する場合は、「ポイントを使用する」にチェックを入れた後、使用するポイントをご記入ください。
                        </p>
                        <div class="point_announce">
                            <p><span class="user_name"><!--{$name01|h}--> <!--{$name02|h}-->様</span>の、現在の所持ポイントは「<span class="point"><!--{$tpl_user_point|default:0|n2s}-->Pt</span>」です。<br />
                                今回ご購入合計金額：<span class="price"><!--{$arrPrices.subtotal|n2s}-->円</span> <span class="attention">(送料、手数料を含みません。)</span>
                            </p>
                            <ul>
                                <li>
                                <input type="radio" id="point_on" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}--> onclick="eccube.togglePointForm();" /><label for="point_on">ポイントを使用する</label>
                                    <!--{assign var=key value="use_point"}--><br />
                                今回のお買い物で、<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box60" />&nbsp;Ptを使用する。<span class="attention"><!--{$arrErr[$key]}--></span>
                                </li>
                                <li><input type="radio" id="point_off" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}--> onclick="eccube.togglePointForm();" /><label for="point_off">ポイントを使用しない</label></li>
                            </ul>
                    </div>
                </div>
            <!--{/if}-->
            <!-- ▲ポイント使用 -->

            <div class="pay_area02">
                <h3>その他お問い合わせ</h3>
                <p>その他お問い合わせ事項がございましたら、こちらにご入力ください。</p>
                <div>
                    <!--★その他お問い合わせ事項★-->
                    <!--{assign var=key value="message"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <textarea name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="70" rows="8" class="txtarea" wrap="hard"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
                    <p class="attention"> (<!--{$smarty.const.LTEXT_LEN}-->文字まで)</p>
                </div>
            </div>

            <div class="btn_area">
                <ul>
                    <li>
                    <a href="?mode=return">
                        <img class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" border="0" name="back03" id="back03" /></a>
                    </li>
                    <li>
                        <input type="image" class="hover_change_image" src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" name="next" id="next" />
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
