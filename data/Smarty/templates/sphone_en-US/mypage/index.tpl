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
        <!--{if $tpl_navi != ""}-->
            <!--{include file=$tpl_navi}-->
        <!--{else}-->
            <!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
        <!--{/if}-->

        <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->mypage/index.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="order_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

            <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>
            <!--{if $objNavi->all_row > 0}-->

                <!--★インフォメーション★-->
                <div class="information">
                    <p>There are <span class="attention"><span id="historycount"><!--{$objNavi->all_row}--></span> items</span> in your purchase history.</p>
                </div>

                <div class="form_area">

                    <!--▼フォームボックスここから -->
                    <div class="formBox">
                        <!--{section name=cnt loop=$arrOrder max=$dispNumber}-->
                            <!--▼商品 -->
                            <div class="arrowBox">
                                <p>
                                    <em>Order number:</em><span class="order_id"><!--{$arrOrder[cnt].order_id}--></span><br />
                                    <em>Date and time of purchase:</em><span class="create_date"><!--{$arrOrder[cnt].create_date|sfDispDBDate}--></span><br />
                                    <em>Payment method:</em><span class="payment_id"><!--{$arrOrder[cnt].payment_method|h}--></span><br />
                                    <em>Total amount:</em>&#036; <span class="payment_total"><!--{$arrOrder[cnt].payment_total|number_format}--></span><br />
                                    <em>Order status:</em>
                                    <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                                        <!--{assign var=order_status_id value="`$arrOrder[cnt].status`"}-->
                                        <!--{if $order_status_id != $smarty.const.ORDER_PENDING }-->
                                        <span class="order_status"><!--{$arrCustomerOrderStatus[$order_status_id]|h}--></span><br />
                                        <!--{else}-->
                                        <span class="order_status attention"><!--{$arrCustomerOrderStatus[$order_status_id]|h}--></span><br />
                                        <!--{/if}-->
                                    <!--{/if}-->
                                </p>
                                <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder[cnt].order_id}-->" rel="external"></a>
                            </div>
                            <!--▲商品 -->
                        <!--{/section}-->
                    </div><!-- /.formBox -->
                </div><!-- /.form_area-->
                <div class="btn_area">
                    <!--{if $objNavi->all_row > $dispNumber}-->
                        <p><a href="javascript: void(0);" class="btn_more" id="btn_more_history" onClick="getHistory(5); return false;" rel="external">View more (+<!--{$dispNumber}--> items)</a></p>
                    <!--{/if}-->
                </div>
            <!--{else}-->
                <div class="form_area">
                    <div class="information">
                        <p>There is no purchase history.</p>
                    </div>
                </div><!-- /.form_area-->
            <!--{/if}-->
        </form>
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
    var pageNo = 2;
    var url = "<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php";
    var statusImagePath = "<!--{$TPL_URLPATH}-->";

    function getHistory(limit) {
        $.mobile.showPageLoadingMsg();
        var i = limit;
        //送信データを準備
        var postData = {};
        $('#form1').find(':input').each(function(){
            postData[$(this).attr('name')] = $(this).val();
        });
        postData["mode"] = "getList";
        postData["pageno"] = pageNo;
        postData["disp_number"] = i;

        $.ajax({
            type: "POST",
            url: "<!--{$smarty.const.ROOT_URLPATH}-->mypage/index.php",
            data: postData,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                for (var j = 0; j < i; j++) {
                    if (result[j] != null) {
                        var history = result[j];
                        var historyHtml = "";
                        var maxCnt = $(".arrowBox").length - 1;
                        var historyEl = $(".arrowBox").get(maxCnt);
                        historyEl = $(historyEl).clone(true).insertAfter(historyEl);
                        maxCnt++;

                        //注文番号をセット
                        $($(".arrowBox span.order_id").get(maxCnt)).text(history.order_id);
                        //購入日時をセット
                        $($(".arrowBox span.create_date").get(maxCnt)).text(history.create_date);
                        //支払い方法をセット
                        $($(".arrowBox span.payment_id").get(maxCnt)).text(history.payment_method);
                        //合計金額をセット
                        $($(".arrowBox span.payment_total").get(maxCnt)).text(history.payment_total);
                        //履歴URLをセット
                        $($(".arrowBox a").get(maxCnt)).attr("href", url + "?order_id=" + history.order_id);
                    }
                }
                pageNo++;

                //すべての商品を表示したか判定
                if (parseInt($("#historycount").text()) <= $(".arrowBox").length) {
                    $("#btn_more_history").hide();
                }
                $.mobile.hidePageLoadingMsg();
            }
        });
    }
</script>
