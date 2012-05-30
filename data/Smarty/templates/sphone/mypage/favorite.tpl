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

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>
    <!--{if $tpl_linemax > 0}-->

        <!--★インフォメーション★-->
        <div class="information">
            <p><span class="attention"><span id="productscount"><!--{$tpl_linemax}--></span>件</span>のお気に入りがあります。</p>
        </div>

        <!--▼フォームここから -->
        <div class="form_area">

            <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->mypage/favorite.php">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <input type="hidden" name="mode" value="cart" />
                <input type="hidden" name="product_id" value="" />


                <!--▼フォームボックスここから -->
                <div class="formBox">
                    <!--{section name=cnt loop=$arrFavorite max=$dispNumber}-->
                        <!--{assign var=product_id value="`$arrFavorite[cnt].product_id`"}-->

                        <!--▼商品 -->
                        <div class="favoriteBox">
                            <a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->"><img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrFavorite[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="" width="80" height="80" class="photoL productImg" /></a>
                            <div class="favoriteContents clearfix">
                                <h4><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->" class="productName"><!--{$arrFavorite[cnt].name}--></a></h4>
                                <p><span class="mini productPrice"><!--{$smarty.const.SALE_PRICE_TITLE}-->：<!--{if $arrFavorite[cnt].price02_min_inctax == $arrFavorite[cnt].price02_max_inctax}-->
                                    <!--{$arrFavorite[cnt].price02_min_inctax|number_format}-->
                                    <!--{else}-->
                                    <!--{$arrFavorite[cnt].price02_min_inctax|number_format}-->～<!--{$arrFavorite[cnt].price02_max_inctax|number_format}-->
                                    <!--{/if}-->円</span></p>
                                <p class="btn_delete"><img src="<!--{$TPL_URLPATH}-->img/button/btn_delete.png" width="21" height="20" alt="削除" onclick="javascript:fnModeSubmit('delete_favorite','product_id','<!--{$product_id|h}-->');" class="pointer" /></p>
                            </div>
                        </div><!--▲商品 -->
                    <!--{/section}-->
                </div><!-- /.formBox -->

                <!--{if $stock_find_count > 0 && $customer_rank < 51}-->
                    <div class="product-btn">
                        <a rel="external" href="javascript:void(document.form1.submit())" class="btn-cart">カートに入れる</a>
                    </div>
                <!--{/if}-->
            </form>
        </div><!-- /.form_area -->

        <div class="btn_area">
            <!--{if $tpl_linemax > $dispNumber}-->
                <p><a rel="external" href="javascript: void(0);" class="btn_more" id="btn_more_product" onclick="getProducts(5); return false;">もっとみる(＋<!--{$dispNumber}-->件)</a></p>
            <!--{/if}-->
        </div>

    <!--{else}-->
        <div class="form_area">
            <div class="information">
                <p>お気に入りが登録されておりません。</p>
            </div>
        </div><!-- /.form_area -->
    <!--{/if}-->

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

<script>
    var pageNo = 2;
    var url = "<!--{$smarty.const.P_DETAIL_URLPATH}-->";
    var imagePath = "<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/";
    var statusImagePath = "<!--{$TPL_URLPATH}-->";

    function getProducts(limit) {
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
            url: "<!--{$smarty.const.ROOT_URLPATH}-->mypage/favorite.php",
            data: postData,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                var productStatus = result.productStatus;
                for (var j = 0; j < i; j++) {
                    if (result[j] != null) {
                        var product = result[j];
                        var productHtml = "";
                        var maxCnt = $(".favoriteBox").length - 1;
                        var productEl = $(".favoriteBox").get(maxCnt);
                        productEl = $(productEl).clone(true).insertAfter(productEl);
                        maxCnt++;

                        //商品写真をセット
                        $($(".favoriteBox img.productImg").get(maxCnt)).attr({
                            src: imagePath + product.main_list_image,
                            alt: product.name
                        });

                        //商品名をセット
                        $($(".favoriteBox a.productName").get(maxCnt)).text(product.name);
                        $($(".favoriteBox a.productName").get(maxCnt)).attr("href", url + product.product_id);

                        //販売価格をセット
                        var price = $($(".favoriteBox span.productPrice").get(maxCnt));
                        //販売価格をクリア
                        price.empty();
                        var priceVale = "";
                        //販売価格が範囲か判定
                        if (product.price02_min == product.price02_max) {
                            priceVale = "<!--{$smarty.const.SALE_PRICE_TITLE}-->：" + product.price02_min_tax_format + '円';
                        } else {
                            priceVale = "<!--{$smarty.const.SALE_PRICE_TITLE}-->：" + product.price02_min_tax_format + '～' + product.price02_max_tax_format + '円';
                        }
                        price.append(priceVale);

                        //削除ボタンをセット
                        $($(".favoriteBox p.btn_delete a").get(maxCnt)).attr("href", "javascript:fnModeSubmit('delete_favorite','product_id','" + product.product_id + "');");

                    }
                }
                pageNo++;

                //すべての商品を表示したか判定
                if (parseInt($("#productscount").text()) <= $(".favoriteBox").length) {
                    $("#btn_more_product").hide();
                }
                $.mobile.hidePageLoadingMsg();
            }
        });
    }
</script>
