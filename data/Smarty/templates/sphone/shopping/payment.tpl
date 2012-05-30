<!--{*
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
                        var payment = $('#payment');
                        payment.empty();
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
                            var li = $('<li />')
                                .append($('<td />')
                                .addClass('centertd')
                                .append(radio)
                                .append(label));

                            li.appendTo(payment);
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

        $('#etc')
            .css('font-size', '100%')
            .autoResizeTextAreaQ({
                'max_rows': 50,
                'extra_rows': 0
            });
    });
//]]></script>

<!--▼コンテンツここから -->
<section id="undercolumn">

    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->shopping/payment.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

        <!--★インフォメーション★-->
        <div class="information end">
            <p>各項目を選択してください。</p>
        </div>

        <!--★配送方法の指定★-->
        <!--{assign var=key value="deliv_id"}-->
        <!--{if $is_single_deliv}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" id="deliv_id" />
        <!--{else}-->
            <section class="pay_area">
                <h3 class="subtitle">配送方法の指定</h3>
                <!--{if $arrErr[$key] != ""}-->
                    <p class="attention"><!--{$arrErr[$key]}--></p>
                <!--{/if}-->
                <ul>
                    <!--{section name=cnt loop=$arrDeliv}-->
                        <li>
                            <input type="radio" id="deliv_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->"  value="<!--{$arrDeliv[cnt].deliv_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrDeliv[cnt].deliv_id|sfGetChecked:$arrForm[$key].value}--> class="data-role-none" />
                            <label for="deliv_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrDeliv[cnt].name|h}--><!--{if $arrDeliv[cnt].remark != ""}--><p><!--{$arrDeliv[cnt].remark|h}--></p><!--{/if}--></label>
                        </li>
                    <!--{/section}-->
                </ul>
            </section>
        <!--{/if}-->

        <!--★インフォメーション★-->
        <section class="pay_area">
            <h3 class="subtitle">お支払方法の指定</h3>
            <!--{assign var=key value="payment_id"}-->
            <!--{if $arrErr[$key] != ""}-->
                <p class="attention"><!--{$arrErr[$key]}--></p>
            <!--{/if}-->
            <p class="non-select-msg information">まずはじめに、配送方法を選択ください。</p>
            <ul id="payment">
                <!--{section name=cnt loop=$arrPayment}-->
                    <li>
                        <input type="radio" id="pay_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}--> class="data-role-none" />
                        <label for="pay_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrPayment[cnt].payment_method|h}--><!--{if $arrPayment[cnt].note != ""}--><!--{/if}--></label>
                        <!--{if $img_show}-->
                            <!--{if $arrPayment[cnt].payment_image != ""}-->
                                <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrPayment[cnt].payment_image}-->" />
                            <!--{/if}-->
                        <!--{/if}-->
                    </li>
                <!--{/section}-->
            </ul>
        </section>


        <!--★お届け時間の指定★-->
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <section class="pay_area02">
                <h3 class="subtitle">お届け時間の指定</h3>

                <div class="form_area">
                    <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
                        <!--{assign var=index value=$shippingItem.shipping_id}-->

                        <!--▼フォームボックスここから -->
                        <!--{if $is_multiple}-->
                            <div class="formBox"><!--{* FIXME *}-->
                                <div class="box_header">
                                    お届け先<!--{$smarty.foreach.shippingItem.iteration}-->
                                </div>
                                <div class="innerBox">
                                    <!--{$shippingItem.shipping_name01}--><!--{$shippingItem.shipping_name02}--><br />
                                    <span class="mini"><!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}--></span>
                                </div>
                        <!--{else}-->
                            <div class="time_select"><!--{* FIXME *}-->
                        <!--{/if}-->

                            <div class="btn_area_btm">
                                <!--★お届け日★-->
                                <!--{assign var=key value="deliv_date`$index`"}-->
                                <span class="attention"><!--{$arrErr[$key]}--></span>
                                <!--{if !$arrDelivDate}-->
                                    ご指定頂けません。
                                <!--{else}-->
                                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="boxLong top data-role-none">
                                        <option value="" selected="">お届け日：指定なし</option>
                                        <!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
                                    </select>
                                <!--{/if}-->

                                <!--★お届け時間★-->
                                <!--{assign var=key value="deliv_time_id`$index`"}-->
                                <span class="attention"><!--{$arrErr[$key]}--></span>
                                <select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="boxLong data-role-none">
                                    <option value="" selected="">お届け時間：指定なし</option>
                                    <!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
                                </select>
                            </div>
                        </div><!-- /.formBox --><!-- /.time_select --><!--{* FIXME *}-->
                    <!--{/foreach}-->

                </div><!-- /.form_area -->
            </section>
        <!--{/if}-->

        <!--★ポイント使用の指定★-->
        <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
            <section class="point_area">
                <h3 class="subtitle">ポイント使用の指定</h3>

                    <div class="form_area">
                        <p class="fb"><span class="point">1ポイントを1円</span>として使用する事ができます。</p>
                        <div class="point_announce">
                            <p>現在の所持ポイントは「<span class="price"><!--{$tpl_user_point|default:0|number_format}-->Pt</span>」です。<br />
                            今回ご購入合計金額：<span class="price"><!--{$arrPrices.subtotal|number_format}-->円</span> (送料、手数料を含みません。)</p>
                        </div>

                        <!--▼ポイントフォームボックスここから -->
                        <div class="formBox">
                            <div class="innerBox fb">
                                <p>
                                    <input type="radio" id="point_on" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}--> onchange="fnCheckInputPoint();" class="data-role-none" />
                                    <label for="point_on">ポイントを使用する</label>
                                </p>
                                <!--{assign var=key value="use_point"}-->
                                <p class="check_point"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box_point data-role-none" />ポイントを使用する。<span class="attention"><!--{$arrErr[$key]}--></span></p>
                            </div>
                        <div class="innerBox fb">
                            <input type="radio" id="point_off" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}--> onchange="fnCheckInputPoint();" class="data-role-none" />
                            <label for="point_off">ポイントを使用しない</label>
                        </div>
                    </div><!-- /.formBox -->
                </div><!-- /.form_area -->
            </section>
        <!--{/if}-->

        <!--★その他お問い合わせ★-->
        <section class="contact_area">
            <h3 class="subtitle">その他お問い合わせ</h3>
            <div class="form_area">
                <p>その他お問い合わせ事項がございましたら、こちらにご入力ください。</p>

                <!--{assign var=key value="message"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" id="etc" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="62" rows="8" class="textarea data-role-none" wrap="hard"><!--{$arrForm[$key].value|h}--></textarea><br />
            </div><!--▲form_area -->
        </section>

        <!--★ボタン★-->
        <div class="btn_area">
            <ul class="btn_btm">
                <li><a rel="external" href="javascript:void(document.form1.submit());" class="btn">確認ページへ</a></li>
                <li><a rel="external" href="?mode=return" class="btn_back">戻る</a></li>
            </ul>
        </div>

    </form>
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
<!--▲コンテンツここまで -->
