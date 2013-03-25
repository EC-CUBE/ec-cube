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

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

    <div class="form_area">
        <div id="historyBox">
            <p>
                <em>Order number</em>:&nbsp;<!--{$tpl_arrOrderData.order_id}--><br />
                <em>Date/time of purchase</em>:&nbsp;<!--{$tpl_arrOrderData.create_date|sfDispDBDate}--><br />
                <em>Payment method</em>:&nbsp;<!--{$tpl_arrOrderData.payment_method|h}-->
                <!--{if $tpl_arrOrderData.deliv_time_id != ""}-->
                    <br />
                    <em>Delivery time</em>:&nbsp;</strong><!--{$arrDelivTime[$tpl_arrOrderData.deliv_time_id]|h}-->
                <!--{/if}-->
                <!--{if $tpl_arrOrderData.deliv_date != ""}-->
                    <br />
                    <em>Delivery date</em>:&nbsp;</strong><!--{$tpl_arrOrderData.deliv_date|h}-->
                <!--{/if}-->
            </p>

            <form action="order.php" method="post">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id}-->">
                <input class="btn_reorder btn data-role-none" type="submit" name="submit" value="Reorder">
            </form>
        </div>

        <div class="formBox">
            <!--▼カートの中の商品一覧 -->
            <div class="cartinarea clearfix">

                <!--▼商品 -->
                <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                    <div>
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$orderDetail.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$orderDetail.product_name|h}-->" class="photoL" />
                        <div class="cartinContents">
                            <div>
                                <p><em><!--→商品名--><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}--> rel="external"><!--{$orderDetail.product_name|h}--></a><!--←商品名--></em></p>
                                <p>
                                    <!--→金額-->
                                    <!--{assign var=price value=`$orderDetail.price`}-->
                                    <!--{assign var=quantity value=`$orderDetail.quantity`}-->
                                    <span class="mini">Price:</span>&#036; <!--{$price|number_format|h}--><!--←金額-->
                                </p>

                                <!--→商品種別-->
                                <!--{if $orderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                                    <p id="downloadable">
                                        <!--{if $orderDetail.is_downloadable}-->
                                            <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$tpl_arrOrderData.order_id}-->&amp;product_id=<!--{$orderDetail.product_id}-->&amp;product_class_id=<!--{$orderDetail.product_class_id}-->" rel="external">Download</a><br />
                                        <!--{else}-->
                                            <!--{if $orderDetail.payment_date == "" && $orderDetail.effective == "0"}-->
                                                <!--{$arrProductType[$orderDetail.product_type_id]}--><br />(Transaction being confirmed)
                                            <!--{else}-->
                                                <!--{$arrProductType[$orderDetail.product_type_id]}--><br />(Expired)
                                            <!--{/if}-->
                                        <!--{/if}-->
                                    </p>
                                <!--{/if}-->
                                <!--←商品種別-->
                            </div>

                            <ul>
                                <li><span class="mini">Quantity:</span><!--{$quantity|h}--></li>
                                <li class="result"><span class="mini">Subtotal:</span>&#036; <!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}--></li>
                            </ul>
                        </div>
                    </div>
                <!--{/foreach}-->
                <!--▲商品 -->

            </div><!--{* /.cartinarea *}-->
            <!--▲ カートの中の商品一覧 -->

            <div class="total_area">
                <div><span class="mini">Subtotal:</span>&#036; <!--{$tpl_arrOrderData.subtotal|number_format}--></div>
                <!--{if $tpl_arrOrderData.use_point > 0}-->
                    <div><span class="mini">Point discount:</span>&#036; &minus;<!--{$tpl_arrOrderData.use_point|number_format}--></div>
                <!--{/if}-->
                <!--{if $tpl_arrOrderData.discount != '' && $tpl_arrOrderData.discount > 0}-->
                    <div><span class="mini">Discount:</span>&#036; &minus;<!--{$tpl_arrOrderData.discount|number_format}--></div>
                <!--{/if}-->
                <div><span class="mini">Shipping fee:</span>&#036; <!--{$tpl_arrOrderData.deliv_fee|number_format}--></div>
                <div><span class="mini">Processing fee:</span>&#036; <!--{$tpl_arrOrderData.charge|number_format}--></div>
                <div><span class="mini">Total:</span>&#036; <span class="price fb"><!--{$tpl_arrOrderData.payment_total|number_format}--></span></div>
                <div><span class="mini">Points added at this time:</span><!--{$tpl_arrOrderData.add_point|number_format|default:0}-->Pts</div>
            </div>
        </div><!-- /.formBox -->

        <!--▼メール一覧 -->
        <div class="formBox">

            <div class="box_header">
                E-mail delivery history list
            </div>
            <!--{section name=cnt loop=$tpl_arrMailHistory}-->
                <!--▼メール -->
                <div class="arrowBox">
                    <p>Delivery date:<!--{$tpl_arrMailHistory[cnt].send_date|sfDispDBDate|h}--><br />
                        <!--{assign var=key value="`$tpl_arrMailHistory[cnt].template_id`"}-->
                        Notification e-mail:<!--{$arrMAILTEMPLATE[$key]|h}--></p>
                    <p><a href="javascript:;" onclick="getMailDetail(<!--{$tpl_arrMailHistory[cnt].send_id}-->)" rel="external"><!--{$tpl_arrMailHistory[cnt].subject|h}--></a></p>
                </div>
                <!--▲メール -->
            <!--{/section}-->
        </div><!-- /.formBox -->
        <!--▲メール一覧 -->

        <p><a rel="external" class="btn_more" href="./<!--{$smarty.const.DIR_INDEX_PATH}-->">Return</a></p>

    </div><!-- /.form_area -->

</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="Enter keywords" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->

<script>
    function getMailDetail(send_id) {
        $.mobile.showPageLoadingMsg();
        $.ajax({
            type: "GET",
            url: "<!--{$smarty.const.ROOT_URLPATH}-->mypage/mail_view.php",
            data: "mode=getDetail&send_id=" + send_id,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                var maxCnt = 0;
                $("#windowcolumn h2").text('E-mail details');
                $("#windowcolumn a[data-rel=back]").text('Return');
                $($("#windowcolumn dl.view_detail dt").get(maxCnt)).text(result[0].subject);
                $($("#windowcolumn dl.view_detail dd").get(maxCnt)).html(result[0].mail_body.replace(/\n/g,"<br />"));
                $("#windowcolumn dl.view_detail dd").css('font-family', 'monospace');
                $.mobile.changePage('#windowcolumn', {transition: "slideup"});
                //ダイアログが開き終わるまで待機
                setTimeout( function() {
                                loadingState = 0;
                                $.mobile.hidePageLoadingMsg();
                }, 1000);
            }
        });
    }
</script>
