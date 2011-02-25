<!--{*
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
 *}-->
<!--▼CONTENTS-->
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
                            var radio = $('<input type="radio" />')
                                .attr('name', 'payment_id')
                                .attr('id', 'pay_' + i)
                                .val(data.arrPayment[i].payment_id);
                            // ラベル
                            var label = $('<label />')
                                .attr('for', 'pay_' + i)
                                .text(data.arrPayment[i].payment_method);
                            // 行
                            var tr = $('<tr />')
                                .append($('<td />')
                                        .addClass('centertd')
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
            location.href = '<!--{$smarty.const.CART_URLPATH}-->';
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
//]]>
</script>
<div id="under02column">
    <div id="under02column_shopping">
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <!--{assign var=key value="deliv_id"}-->
            <!--{if $is_single_deliv}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" id="deliv_id" />
            <!--{else}-->
            <div class="payarea">
                <h3>配送方法の指定</h3>
                <p>配送方法をご選択ください。</p>

                <!--{if $arrErr[$key] != ""}-->
                <p class="attention"><!--{$arrErr[$key]}--></p>
                <!--{/if}-->
                <table summary="配送方法選択">
                    <tr>
                        <th>選択</th>
                        <th colspan="2">配送方法</th>
                    </tr>
                    <!--{section name=cnt loop=$arrDeliv}-->
                    <tr>
                        <td class="centertd"><input type="radio" id="deliv_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->"  value="<!--{$arrDeliv[cnt].deliv_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrDeliv[cnt].deliv_id|sfGetChecked:$arrForm[$key].value}--> />
                        </td>
                        <td>
                            <label for="deliv_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrDeliv[cnt].name|h}--><!--{if $arrDeliv[cnt].remark != ""}--><p><!--{$arrDeliv[cnt].remark|h}--></p><!--{/if}--></label>
                        </td>
                    </tr>
                    <!--{/section}-->
                </table>
            </div>
            <!--{/if}-->

            <div class="payarea">
                <h3>お支払方法の指定</h3>
                <p class="select-msg">お支払方法をご選択ください。</p>
                <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>

                <!--{assign var=key value="payment_id"}-->
                <!--{if $arrErr[$key] != ""}-->
                <p class="attention"><!--{$arrErr[$key]}--></p>
                <!--{/if}-->
                <table summary="お支払方法選択" id="payment">
                  <thead>
                    <tr>
                        <th>選択</th>
                        <th colspan="<!--{if !$img_show}-->2<!--{else}-->3<!--{/if}-->" id="payment_method">お支払方法</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!--{section name=cnt loop=$arrPayment}-->
                    <tr>
                        <td class="centertd"><input type="radio" id="pay_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}--> />
                        </td>
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
            <div class="payarea02">
                <h3>お届け時間の指定</h3>
                <p class="select-msg">ご希望の方は、お届け時間を選択してください。</p>
                <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>
                <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
                <!--{assign var=index value=$smarty.foreach.shippingItem.index}-->
                <!--{if $is_multiple}-->
                <div class="delivdate">
                        &nbsp;<!--{$shippingItem.shipping_name01}--><!--{$shippingItem.shipping_name02}--><br />
                        &nbsp;<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}-->
                </div>
                <!--{/if}-->
                <div class="delivdate">
                    <!--★お届け日★-->
                    <!--{assign var=key value="deliv_date`$index`"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <em>お届け日：</em>
                    <!--{if !$arrDelivDate}-->
                        ご指定頂けません。
                    <!--{else}-->
                        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
                        </select>
                    <!--{/if}-->
                </div>
                <div class="delivdate">
                    <!--★お届け時間★-->
                    <!--{assign var=key value="deliv_time_id`$index`"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <em>お届け時間：</em>
                    <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="" selected="">指定なし</option>
                        <!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
                    </select>
                </div>
                <!--{/foreach}-->
            </div>
            <!--{/if}-->

            <div class="payarea02">
                <h3>その他お問い合わせ</h3>
                <p>その他お問い合わせ事項がございましたら、こちらにご入力ください。</p>
                <div>
                    <!--★その他お問い合わせ事項★-->
                    <!--{assign var=key value="message"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <textarea name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="80" rows="8" class="area660" wrap="hard"><!--{$arrForm[$key].value|h}--></textarea>
                </div>
                <div>
                    <span class="attention"> (<!--{$smarty.const.LTEXT_LEN}-->文字まで)</span>
                </div>
            </div>

            <!-- ▼ポイント使用 ここから -->
            <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
                <div class="pointarea">
                    <h3>ポイント使用の指定</h3>

                        <p><span class="attention">1ポイントを1円</span>として使用する事ができます。<br />
                            使用する場合は、「ポイントを使用する」にチェックを入れた後、使用するポイントをご記入ください。</p>
                    <div>
                        <p><!--{$name01|h}--> <!--{$name02|h}-->様の、現在の所持ポイントは「<em><!--{$tpl_user_point|default:0}-->Pt</em>」です。</p>
                        <p>今回ご購入合計金額：<span class="price"><!--{$arrPrices.subtotal|number_format}-->円</span> <span class="attention">(送料、手数料を含みません。)</span></p>
                        <ul>
                            <li><input type="radio" id="point_on" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}--> onclick="fnCheckInputPoint();" /><label for="point_on">ポイントを使用する</label></li>
                             <!--{assign var=key value="use_point"}-->

                             <li class="underline">今回のお買い物で、<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box60" />&nbsp;ポイントを使用する。<span class="attention"><!--{$arrErr[$key]}--></span></li>
                             <li><input type="radio" id="point_off" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}--> onclick="fnCheckInputPoint();" /><label for="point_off">ポイントを使用しない</label></li>
                        </ul>
                    </div>
                </div>
            <!--{/if}-->
            <!-- ▲ポイント使用 ここまで -->

            <div class="tblareabtn">
                <a href="<!--{$tpl_back_url|h}-->" class="spbtn spbtn-medeum">
                    戻る</a>&nbsp;
                <input type="submit" value="次へ" class="spbtn spbtn-shopping" width="130" height="30" alt="次へ" name="next" id="next" />
            </div>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
