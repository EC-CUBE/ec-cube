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

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

    <div class="form_area">
        <div id="historyBox">
            <p>
                <em>注文番号</em>：&nbsp;<!--{$tpl_arrOrderData.order_id}--><br />
                <em>購入日時</em>：&nbsp;<!--{$tpl_arrOrderData.create_date|sfDispDBDate}--><br />
                <em>お支払い方法</em>：&nbsp;<!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}-->
            </p>

            <form action="order.php" method="post">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id}-->">
                <input class="btn_reorder btn data-role-none" type="submit" name="submit" value="再注文">
            </form>
        </div>
        <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
            <h3>お届け先<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
        <div class="historyBox">
        <p>
            <!--{if $isMultiple}-->
                    <!--{foreach item=item from=$shippingItem.shipment_item}-->
                        <em>商品コード：&nbsp;</em><!--{$item.productsClass.product_code|h}--><br />
                        <em>商品名：&nbsp;</em>
                                <!--{$item.productsClass.name|h}--><br />
                                <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                    <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                                <!--{/if}-->
                                <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                    <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br />
                                <!--{/if}-->

                        <em>単価：&nbsp;</em><!--{$item.price|sfCalcIncTax:$tpl_arrOrderData.order_tax_rate:$tpl_arrOrderData.order_tax_rule|n2s}-->円<br />
                        <em>数量：&nbsp;</em><!--{$item.quantity}--><br />
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <em>小計</em><!--{$item.total_inctax|n2s}-->円
                        *}-->
                        <br />
                    <!--{/foreach}-->
            <!--{/if}-->

            <em>お名前</em>：&nbsp;<!--{$shippingItem.shipping_name01|h}-->&nbsp;<!--{$shippingItem.shipping_name02|h}--><br />
            <em>お名前(フリガナ)</em>：&nbsp;<!--{$shippingItem.shipping_kana01|h}-->&nbsp;<!--{$shippingItem.shipping_kana02|h}--><br />
            <em>会社名</em>：&nbsp;<!--{$shippingItem.shipping_company_name|h}--><br />
            <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
                <em>国</em>：&nbsp;<!--{$arrCountry[$shippingItem.shipping_country_id]|h}--><br />
                <em>ZIPCODE</em>：&nbsp;<!--{$shippingItem.shipping_zipcode|h}--><br />
            <!--{/if}-->
            <em>郵便番号</em>：&nbsp;〒<!--{$shippingItem.shipping_zip01}-->-<!--{$shippingItem.shipping_zip02}--><br />
            <em>住所</em>：&nbsp;<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--><br />
            <em>電話番号</em>：&nbsp;<!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--><br />
                            <!--{if $shippingItem.shipping_fax01 > 0}-->
            <em>FAX番号</em>：&nbsp;<!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}--><br />
                            <!--{/if}-->
            <em>お届け日</em>：&nbsp;<!--{$shippingItem.shipping_date|default:'指定なし'|h}--><br />
            <em>お届け時間</em>：&nbsp;<!--{$shippingItem.shipping_time|default:'指定なし'|h}--><br />
</p>
</div>

        <!--{/foreach}-->

        <div class="formBox">
            <!--▼カートの中の商品一覧 -->
            <div class="cartinarea clearfix">

                <!--▼商品 -->
                <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                    <div>
                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$orderDetail.main_list_image|sfNoImageMainList|h}-->" style="max-width: 80px;max-height: 80px;" alt="<!--{$orderDetail.product_name|h}-->" class="photoL" />
                        <div class="cartinContents">
                            <div>
                                <p><em><!--→商品名--><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}--> rel="external"><!--{$orderDetail.product_name|h}--></a><!--←商品名--></em></p>
                                <p>
                                    <!--→金額-->
                                    <!--{assign var=price value=`$orderDetail.price`}-->
                                    <!--{assign var=quantity value=`$orderDetail.quantity`}-->
                                    <span class="mini">価格:</span><!--{$price|n2s|h}-->円<!--←金額-->
                                </p>

                                <!--→商品種別-->
                                <!--{if $orderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                                    <p id="downloadable">
                                        <!--{if $orderDetail.is_downloadable}-->
                                            <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$tpl_arrOrderData.order_id}-->&amp;product_id=<!--{$orderDetail.product_id}-->&amp;product_class_id=<!--{$orderDetail.product_class_id}-->" rel="external">ダウンロード</a><br />
                                        <!--{else}-->
                                            <!--{if $orderDetail.payment_date == "" && $orderDetail.effective == "0"}-->
                                                <!--{$arrProductType[$orderDetail.product_type_id]}--><br />（入金確認中）
                                            <!--{else}-->
                                                <!--{$arrProductType[$orderDetail.product_type_id]}--><br />（期限切れ）
                                            <!--{/if}-->
                                        <!--{/if}-->
                                    </p>
                                <!--{/if}-->
                                <!--←商品種別-->
                            </div>
                            <!--{assign var=tax_rate value=`$orderDetail.tax_rate`}-->
                            <!--{assign var=tax_rule value=`$orderDetail.tax_rule`}-->
                            <ul>
                                <li><span class="mini">数量：</span><!--{$quantity|h}--></li>
                                <li class="result"><span class="mini">小計：</span><!--{$price|sfCalcIncTax:$tax_rate:$tax_rule|sfMultiply:$quantity|n2s}-->円</li>
                            </ul>
                        </div>
                    </div>
                <!--{/foreach}-->
                <!--▲商品 -->

            </div><!--{* /.cartinarea *}-->
            <!--▲ カートの中の商品一覧 -->

            <div class="total_area">
                <div><span class="mini">小計：</span><!--{$tpl_arrOrderData.subtotal|n2s}-->円</div>
                <!--{if $tpl_arrOrderData.use_point > 0}-->
                    <div><span class="mini">ポイント値引き：</span>&minus;<!--{$tpl_arrOrderData.use_point|n2s}-->円</div>
                <!--{/if}-->
                <!--{if $tpl_arrOrderData.discount != '' && $tpl_arrOrderData.discount > 0}-->
                    <div><span class="mini">値引き：</span>&minus;<!--{$tpl_arrOrderData.discount|n2s}-->円</div>
                <!--{/if}-->
                <div><span class="mini">送料：</span><!--{$tpl_arrOrderData.deliv_fee|n2s}-->円</div>
                <div><span class="mini">手数料：</span><!--{$tpl_arrOrderData.charge|n2s}-->円</div>
                <div><span class="mini">合計：</span><span class="price fb"><!--{$tpl_arrOrderData.payment_total|n2s}-->円</span></div>
                <div><span class="mini">今回加算ポイント：</span><!--{$tpl_arrOrderData.add_point|n2s|default:0}-->Pt</div>
            </div>
        </div><!-- /.formBox -->

        <!--▼メール一覧 -->
        <div class="formBox">

            <div class="box_header">
                メール配信履歴一覧
            </div>
            <!--{section name=cnt loop=$tpl_arrMailHistory}-->
                <!--▼メール -->
                <div class="arrowBox">
                    <p>配信日：<!--{$tpl_arrMailHistory[cnt].send_date|sfDispDBDate|h}--><br />
                        <!--{assign var=key value="`$tpl_arrMailHistory[cnt].template_id`"}-->
                        通知メール：<!--{$arrMAILTEMPLATE[$key]|h}--></p>
                    <p><a href="javascript:;" onclick="getMailDetail(<!--{$tpl_arrMailHistory[cnt].send_id}-->)" rel="external"><!--{$tpl_arrMailHistory[cnt].subject|h}--></a></p>
                </div>
                <!--▲メール -->
            <!--{/section}-->
        </div><!-- /.formBox -->
        <!--▲メール一覧 -->

        <p><a rel="external" class="btn_more" href="./<!--{$smarty.const.DIR_INDEX_PATH}-->">購入履歴一覧に戻る</a></p>

    </div><!-- /.form_area -->

</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

<script>
    function getMailDetail(send_id) {
        eccube.showLoading();
        $.ajax({
            type: "GET",
            url: "<!--{$smarty.const.ROOT_URLPATH}-->mypage/mail_view.php",
            data: "mode=getDetail&send_id=" + send_id,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                eccube.hideLoading();
            },
            success: function(result){
                var dialog = $("#mail-dialog");

                //件名をセット
                $("#mail-dialog-title").remove();
                dialog.find(".dialog-content").append(
                    $('<h3 id="mail-dialog-title">').text(result[0].subject)
                );

                //本文をセット
                $("#mail-dialog-body").remove();
                dialog.find(".dialog-content").append(
                    $('<div id="mail-dialog-body">')
                        .html(result[0].mail_body.replace(/\n/g,"<br />"))
                        .css('font-family', 'monospace')
                );

                //ダイアログをモーダルウィンドウで表示
                $.colorbox({inline: true, href: dialog, onOpen: function(){
                    dialog.show().css('width', String($('body').width() * 0.9) + 'px');
                }, onComplete: function(){
                    eccube.hideLoading();
                }, onClosed: function(){
                    dialog.hide();
                }});
            }
        });
    }
</script>

<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`frontparts/dialog_modal.tpl" dialog_id="mail-dialog" dialog_title="メール詳細"}-->
